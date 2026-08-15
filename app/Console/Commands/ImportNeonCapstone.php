<?php

namespace App\Console\Commands;

use App\Models\Lecturer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class ImportNeonCapstone extends Command
{
    private int $syntheticPeerReviewTemplates = 0;

    private int $syntheticPeerReviewIndicators = 0;

    private int $repairedPeerReviews = 0;

    protected $signature = 'capstone:import-neon
                            {dump : Path to the Neon custom-format PostgreSQL dump}
                            {--apply : Commit the import; without this option only a dry-run is performed}
                            {--pg-restore= : Explicit path to pg_restore}';

    protected $description = 'Import legacy Neon Capstone data into the prefixed Capstone schema';

    /**
     * Source tables in foreign-key-safe insertion order.
     * The groups.title_id cycle is deferred until titles have been inserted.
     *
     * @var array<int, string>
     */
    private const SOURCE_TABLES = [
        'periods',
        'locations',
        'assessment_component_templates',
        'peer_review_indicator_templates',
        'document_types',
        'stakeholders',
        'assessment_components',
        'period_assessment_components',
        'peer_review_indicators',
        'period_peer_review_indicators',
        'phase_document_requirements',
        'groups',
        'titles',
        'stakeholder_title',
        'group_members',
        'group_invitations',
        'group_supervisor_proposals',
        'supervisions',
        'bids',
        'join_requests',
        'period_registrations',
        'student_peer_review_status',
        'documents',
        'schedules',
        'seminar_schedules',
        'seminar_evaluations',
        'ta_submissions',
        'ta_defense_schedules',
        'ta_defense_examiners',
        'ta_defense_schedule_student',
        'ta_defense_evaluations',
        'expo_events',
        'expo_registrations',
        'expo_student_documents',
        'evaluations',
        'peer_reviews',
        'grade_consistency_checks',
        'finalization_audits',
        'title_approval_audits',
        'title_deletion_audits',
        'digital_signatures',
        'notifications',
        'audit_logs',
        'bimbingan_sempro_scores',
        'bimbingan_ta_scores',
        'expo_scores',
        'milestone_scores',
        'nilai_dosen_scores',
        'sempro_scores',
        'sidang_ta_scores',
    ];

    public function handle(): int
    {
        try {
            $dumpPath = $this->resolveDumpPath((string) $this->argument('dump'));
            $pgRestore = $this->resolvePgRestore();
            $source = ['users' => $this->readDumpTable($pgRestore, $dumpPath, 'users')];

            foreach (self::SOURCE_TABLES as $table) {
                $source[$table] = $this->readDumpTable($pgRestore, $dumpPath, $table);
            }

            $this->repairLegacyPeerReviewData($source);

            $prepared = $this->prepareImport($source, allowPlannedLecturers: true);
            $this->renderAnalysis($prepared);

            if (! $this->option('apply')) {
                $this->info('Dry-run selesai. Tidak ada data database yang diubah.');

                return self::SUCCESS;
            }

            if ($this->input->isInteractive()
                && ! $this->confirm('Impor seluruh data Capstone dalam satu transaksi?', false)) {
                $this->warn('Import dibatalkan.');

                return self::SUCCESS;
            }

            $result = $this->import($source);

            $this->newLine();
            $this->info('Import Capstone Neon selesai dan transaksi berhasil di-commit.');
            $this->table(['Verifikasi', 'Jumlah'], [
                ['Total row Capstone', $result['rows']],
                ['Tabel sumber yang diproses', count(self::SOURCE_TABLES)],
                ['Group member terhubung ke user', $result['linked_group_members']],
                ['Akun sumber yang sudah diklaim SSO', $result['sso_linked_users']],
                ['Profile aktor dosen legacy dibuat', $result['supplemental_lecturers']],
                ['Foreign key yatim', $result['orphaned_foreign_keys']],
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function resolveDumpPath(string $dump): string
    {
        $dumpPath = realpath($dump);

        if ($dumpPath === false || ! is_file($dumpPath)) {
            throw new RuntimeException("Dump tidak ditemukan: {$dump}");
        }

        return $dumpPath;
    }

    private function resolvePgRestore(): string
    {
        $explicit = trim((string) $this->option('pg-restore'));

        if ($explicit !== '') {
            $path = realpath($explicit);
            if ($path === false || ! is_file($path)) {
                throw new RuntimeException("pg_restore tidak ditemukan: {$explicit}");
            }

            return $path;
        }

        $found = (new ExecutableFinder)->find('pg_restore');
        if ($found) {
            return $found;
        }

        foreach ([
            'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_restore.exe',
            'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_restore.exe',
            'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_restore.exe',
        ] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('pg_restore tidak tersedia. Gunakan opsi --pg-restore.');
    }

    /**
     * @return array{columns:array<int, string>, rows:array<int, array<string, string|null>>}
     */
    private function readDumpTable(string $pgRestore, string $dumpPath, string $table): array
    {
        $process = new Process([
            $pgRestore,
            '--data-only',
            "--table={$table}",
            '--file=-',
            $dumpPath,
        ]);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                "pg_restore gagal membaca {$table}: ".trim($process->getErrorOutput())
            );
        }

        return $this->parseCopy($process->getOutput(), $table);
    }

    /**
     * @return array{columns:array<int, string>, rows:array<int, array<string, string|null>>}
     */
    private function parseCopy(string $sql, string $table): array
    {
        $columns = [];
        $rows = [];
        $insideCopy = false;
        $headerFound = false;
        $pattern = '/^COPY public\\.'.preg_quote($table, '/').' \\(([^)]+)\\) FROM stdin;$/';

        foreach (preg_split('/\R/', $sql) ?: [] as $line) {
            if (! $insideCopy) {
                if (! preg_match($pattern, $line, $matches)) {
                    continue;
                }

                $columns = array_map('trim', explode(',', $matches[1]));
                $headerFound = true;
                $insideCopy = true;

                continue;
            }

            if ($line === '\\.') {
                $insideCopy = false;
                break;
            }

            $values = explode("\t", $line);
            if (count($values) !== count($columns)) {
                throw new RuntimeException("Jumlah kolom data {$table} tidak sesuai header COPY.");
            }

            $decoded = array_map(fn (string $value) => $this->decodeCopyValue($value), $values);
            $rows[] = array_combine($columns, $decoded);
        }

        if (! $headerFound) {
            throw new RuntimeException("TABLE DATA public.{$table} tidak ditemukan dalam dump.");
        }

        if ($insideCopy) {
            throw new RuntimeException("Data COPY {$table} tidak ditutup dengan benar.");
        }

        return ['columns' => $columns, 'rows' => $rows];
    }

    private function decodeCopyValue(string $value): ?string
    {
        if ($value === '\\N') {
            return null;
        }

        $decoded = '';
        $length = strlen($value);

        for ($index = 0; $index < $length; $index++) {
            if ($value[$index] !== '\\') {
                $decoded .= $value[$index];

                continue;
            }

            $index++;
            if ($index >= $length) {
                $decoded .= '\\';

                break;
            }

            $escape = $value[$index];
            $simple = [
                'b' => "\x08",
                'f' => "\x0c",
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                'v' => "\x0b",
                '\\' => '\\',
            ];

            if (array_key_exists($escape, $simple)) {
                $decoded .= $simple[$escape];

                continue;
            }

            if ($escape >= '0' && $escape <= '7') {
                $octal = $escape;
                while (strlen($octal) < 3
                    && $index + 1 < $length
                    && $value[$index + 1] >= '0'
                    && $value[$index + 1] <= '7') {
                    $octal .= $value[++$index];
                }
                $decoded .= chr(octdec($octal));

                continue;
            }

            if ($escape === 'x') {
                $hex = '';
                while (strlen($hex) < 2
                    && $index + 1 < $length
                    && ctype_xdigit($value[$index + 1])) {
                    $hex .= $value[++$index];
                }

                if ($hex !== '') {
                    $decoded .= chr(hexdec($hex));

                    continue;
                }
            }

            $decoded .= $escape;
        }

        return $decoded;
    }

    /**
     * The legacy schema stored period peer-review templates in the assessment
     * template table and left peer_reviews.indicator_id empty. Materialize the
     * compatibility rows required by the stricter prefixed schema.
     *
     * @param  array<string, array{columns:array<int, string>, rows:array<int, array<string, string|null>>}>  $source
     */
    private function repairLegacyPeerReviewData(array &$source): void
    {
        $assessmentTemplates = collect($source['assessment_component_templates']['rows'])
            ->keyBy('id');
        $peerTemplates = collect($source['peer_review_indicator_templates']['rows'])
            ->keyBy('id');
        $periodIndicators = collect($source['period_peer_review_indicators']['rows'])
            ->keyBy('id');
        $peerIndicators = collect($source['peer_review_indicators']['rows'])
            ->keyBy('id');

        foreach ($periodIndicators as $periodIndicator) {
            $templateId = $periodIndicator['template_id'];
            $template = $assessmentTemplates->get($templateId);

            if (! $template) {
                throw new RuntimeException(
                    "Template peer review legacy {$templateId} tidak ditemukan di dump."
                );
            }

            if (! $peerTemplates->has($templateId)) {
                $compatibilityTemplate = [
                    'id' => $template['id'],
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'weight' => $template['weight'],
                    'is_active' => $template['is_active'],
                    'created_by' => $template['created_by'],
                    'sort_order' => $template['sort_order'],
                    'created_at' => $template['created_at'],
                    'updated_at' => $template['updated_at'],
                ];
                $source['peer_review_indicator_templates']['rows'][] = $compatibilityTemplate;
                $peerTemplates->put($templateId, $compatibilityTemplate);
                $this->syntheticPeerReviewTemplates++;
            }

            $indicatorId = $periodIndicator['id'];
            if (! $peerIndicators->has($indicatorId)) {
                $compatibilityIndicator = [
                    'id' => $indicatorId,
                    'period_id' => $periodIndicator['period_id'],
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'weight' => $template['weight'],
                    'sort_order' => $periodIndicator['sort_order'],
                    'created_at' => $periodIndicator['created_at'],
                    'updated_at' => $periodIndicator['updated_at'],
                ];
                $source['peer_review_indicators']['rows'][] = $compatibilityIndicator;
                $peerIndicators->put($indicatorId, $compatibilityIndicator);
                $this->syntheticPeerReviewIndicators++;
            }
        }

        foreach ($source['peer_reviews']['rows'] as &$review) {
            if ($review['indicator_id'] !== null) {
                continue;
            }

            $periodIndicatorId = $review['period_indicator_id'];
            if ($periodIndicatorId === null || ! $peerIndicators->has($periodIndicatorId)) {
                throw new RuntimeException(
                    "Peer review {$review['id']} tidak memiliki period indicator yang valid."
                );
            }

            $review['indicator_id'] = $periodIndicatorId;
            $this->repairedPeerReviews++;
        }
        unset($review);
    }

    /**
     * @param  array<string, array{columns:array<int, string>, rows:array<int, array<string, string|null>>}>  $source
     * @return array<string, mixed>
     */
    private function prepareImport(array $source, bool $allowPlannedLecturers = false): array
    {
        $sourceUserIds = collect($source['users']['rows'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $userMap = DB::table('users')
            ->whereIn('id', $sourceUserIds)
            ->pluck('id', 'id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $studentMap = DB::table('students')
            ->whereIn('user_id', $sourceUserIds)
            ->pluck('id', 'user_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $lecturerMap = DB::table('lecturers')
            ->whereIn('user_id', $sourceUserIds)
            ->pluck('id', 'user_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $missingUsers = $sourceUserIds
            ->reject(fn (int $id) => array_key_exists($id, $userMap))
            ->values();

        if ($missingUsers->isNotEmpty()) {
            throw new RuntimeException(
                'User Neon belum lengkap di database tujuan. ID yang hilang: '
                .$missingUsers->take(20)->implode(', ')
            );
        }

        $identityMaps = [
            'users' => $userMap,
            'students' => $studentMap,
            'lecturers' => $lecturerMap,
        ];
        $preparedTables = [];
        $tableStats = [];
        $mappingErrors = [];
        $schemaErrors = [];
        $plannedLecturerUserIds = [];

        foreach (self::SOURCE_TABLES as $sourceTable) {
            $targetTable = $this->targetTable($sourceTable);

            if (! Schema::hasTable($targetTable)) {
                throw new RuntimeException("Tabel tujuan {$targetTable} tidak ditemukan.");
            }

            $targetColumns = Schema::getColumnListing($targetTable);
            $targetColumnDefinitions = collect(Schema::getColumns($targetTable))->keyBy('name');
            $unsupportedColumns = array_values(array_diff(
                $source[$sourceTable]['columns'],
                $targetColumns
            ));

            if ($unsupportedColumns !== []) {
                throw new RuntimeException(
                    "Kolom sumber {$sourceTable} tidak tersedia di {$targetTable}: "
                    .implode(', ', $unsupportedColumns)
                );
            }

            $foreignKeys = Schema::getForeignKeys($targetTable);
            $rows = [];

            foreach ($source[$sourceTable]['rows'] as $sourceRow) {
                $row = array_intersect_key($sourceRow, array_flip($targetColumns));

                foreach ($foreignKeys as $foreignKey) {
                    if (count($foreignKey['columns']) !== 1
                        || count($foreignKey['foreign_columns']) !== 1) {
                        continue;
                    }

                    $column = $foreignKey['columns'][0];
                    $foreignTable = $foreignKey['foreign_table'];
                    $value = $row[$column] ?? null;

                    if ($value === null || ! isset($identityMaps[$foreignTable])) {
                        continue;
                    }

                    if (! array_key_exists($value, $identityMaps[$foreignTable])) {
                        if ($foreignTable === 'lecturers' && $allowPlannedLecturers) {
                            $plannedLecturerUserIds[(int) $value] = true;

                            continue;
                        }

                        $mappingErrors["{$targetTable}.{$column}:{$value}"] =
                            "{$targetTable}.{$column}={$value} tidak memiliki mapping {$foreignTable}.";

                        continue;
                    }

                    $row[$column] = $identityMaps[$foreignTable][$value];
                }

                if ($sourceTable === 'audit_logs'
                    && ($row['target_type'] ?? null) === 'App\\Models\\Group') {
                    $row['target_type'] = 'Modules\\Capstone\\Models\\Group';
                }

                foreach ($targetColumnDefinitions as $column => $definition) {
                    $hasValue = array_key_exists($column, $row);
                    [$hasScalarDefault, $scalarDefault] = $this->scalarDatabaseDefault(
                        $definition['default']
                    );

                    if ($hasValue
                        && $row[$column] === null
                        && ! $definition['nullable']
                        && $hasScalarDefault) {
                        $row[$column] = $scalarDefault;
                    }

                    $usesDatabaseValue = ! $hasValue
                        && ($definition['default'] !== null || $definition['auto_increment']);

                    if (! $definition['nullable']
                        && ! $usesDatabaseValue
                        && (! $hasValue || $row[$column] === null)) {
                        $rowId = $row['id'] ?? '?';
                        $schemaErrors["{$targetTable}.{$column}:{$rowId}"] =
                            "{$targetTable}.{$column} wajib diisi, tetapi row {$rowId} bernilai null.";
                    }
                }

                $rows[] = $row;
            }

            $targetCount = DB::table($targetTable)->count();
            $preparedTables[$sourceTable] = $rows;
            $tableStats[] = [
                'source' => $sourceTable,
                'target' => $targetTable,
                'source_rows' => count($rows),
                'target_rows' => $targetCount,
            ];
        }

        if ($mappingErrors !== []) {
            throw new RuntimeException(
                "Foreign key user/profile tidak dapat dipetakan:\n- "
                .implode("\n- ", array_slice(array_values($mappingErrors), 0, 30))
            );
        }

        if ($schemaErrors !== []) {
            throw new RuntimeException(
                "Data sumber melanggar kolom wajib tujuan:\n- "
                .implode("\n- ", array_slice(array_values($schemaErrors), 0, 30))
            );
        }

        $this->assertPreparedForeignKeys($preparedTables);

        $nonEmptyTargets = collect($tableStats)
            ->filter(fn (array $stats) => $stats['target_rows'] > 0)
            ->values();

        if ($nonEmptyTargets->isNotEmpty()) {
            throw new RuntimeException(
                'Import aman mensyaratkan tabel tujuan kosong. Tabel berisi data: '
                .$nonEmptyTargets
                    ->map(fn (array $stats) => "{$stats['target']} ({$stats['target_rows']})")
                    ->implode(', ')
            );
        }

        $ssoLinkedUsers = DB::table('users')
            ->whereIn('id', $sourceUserIds)
            ->whereNotNull('sso_data')
            ->count();

        return [
            'tables' => $preparedTables,
            'stats' => $tableStats,
            'source_users' => $sourceUserIds->count(),
            'mapped_users' => count($userMap),
            'mapped_students' => count($studentMap),
            'mapped_lecturers' => count($lecturerMap),
            'sso_linked_users' => $ssoLinkedUsers,
            'planned_lecturer_user_ids' => array_keys($plannedLecturerUserIds),
            'synthetic_peer_review_templates' => $this->syntheticPeerReviewTemplates,
            'synthetic_peer_review_indicators' => $this->syntheticPeerReviewIndicators,
            'repaired_peer_reviews' => $this->repairedPeerReviews,
            'total_rows' => collect($tableStats)->sum('source_rows'),
        ];
    }

    /**
     * @param  array<string, mixed>  $prepared
     */
    private function renderAnalysis(array $prepared): void
    {
        $this->table(
            ['Sumber', 'Tujuan', 'Row dump', 'Row tujuan'],
            array_map(fn (array $stats) => [
                $stats['source'],
                $stats['target'],
                $stats['source_rows'],
                $stats['target_rows'],
            ], $prepared['stats'])
        );
        $this->table(['Pemeriksaan identitas', 'Jumlah'], [
            ['Total row Capstone', $prepared['total_rows']],
            ['Users sumber', $prepared['source_users']],
            ['Users tujuan cocok berdasarkan ID tetap', $prepared['mapped_users']],
            ['Profile mahasiswa tersedia', $prepared['mapped_students']],
            ['Profile dosen tersedia', $prepared['mapped_lecturers']],
            ['Users yang sudah diklaim SSO', $prepared['sso_linked_users']],
            [
                'Profile aktor dosen legacy yang akan dibuat',
                count($prepared['planned_lecturer_user_ids']),
            ],
            ['Template peer review kompatibilitas', $prepared['synthetic_peer_review_templates']],
            ['Indicator peer review kompatibilitas', $prepared['synthetic_peer_review_indicators']],
            ['Peer review legacy diperbaiki', $prepared['repaired_peer_reviews']],
        ]);

        if ($prepared['planned_lecturer_user_ids'] !== []) {
            $this->line(
                'User ID aktor dosen legacy: '
                .implode(', ', $prepared['planned_lecturer_user_ids'])
            );
        }
    }

    /**
     * @param  array<string, array{columns:array<int, string>, rows:array<int, array<string, string|null>>}>  $source
     * @return array{rows:int, linked_group_members:int, sso_linked_users:int, supplemental_lecturers:int, orphaned_foreign_keys:int}
     */
    private function import(array $source): array
    {
        return DB::transaction(function () use ($source) {
            $initial = $this->prepareImport($source, allowPlannedLecturers: true);
            $supplementalLecturers = $this->createSupplementalLecturers(
                $initial['planned_lecturer_user_ids']
            );
            $prepared = $this->prepareImport($source);
            $deferredGroupTitles = [];

            foreach (self::SOURCE_TABLES as $sourceTable) {
                $targetTable = $this->targetTable($sourceTable);
                $rows = $prepared['tables'][$sourceTable];

                if ($sourceTable === 'groups') {
                    foreach ($rows as &$row) {
                        if (($row['title_id'] ?? null) !== null) {
                            $deferredGroupTitles[(int) $row['id']] = (int) $row['title_id'];
                            $row['title_id'] = null;
                        }
                    }
                    unset($row);
                }

                foreach (array_chunk($rows, 200) as $chunk) {
                    DB::table($targetTable)->insert($chunk);
                }
            }

            foreach ($deferredGroupTitles as $groupId => $titleId) {
                DB::table('capstone_groups')
                    ->where('id', $groupId)
                    ->update(['title_id' => $titleId]);
            }

            foreach (self::SOURCE_TABLES as $sourceTable) {
                $targetTable = $this->targetTable($sourceTable);
                $expected = count($prepared['tables'][$sourceTable]);
                $actual = DB::table($targetTable)->count();

                if ($actual !== $expected) {
                    throw new RuntimeException(
                        "Verifikasi jumlah row gagal untuk {$targetTable}: {$actual}/{$expected}."
                    );
                }

                if ($expected > 0 && Schema::hasColumn($targetTable, 'id')) {
                    $this->advanceSequence($targetTable);
                }
            }

            $orphans = $this->countOrphanedForeignKeys();
            if ($orphans > 0) {
                throw new RuntimeException("Ditemukan {$orphans} foreign key yatim setelah import.");
            }

            $linkedGroupMembers = DB::table('capstone_group_members as members')
                ->join('students', 'students.id', '=', 'members.student_id')
                ->join('users', 'users.id', '=', 'students.user_id')
                ->count();
            $expectedGroupMembers = count($prepared['tables']['group_members']);

            if ($linkedGroupMembers !== $expectedGroupMembers) {
                throw new RuntimeException(
                    "Relasi group member ke akun gagal: {$linkedGroupMembers}/{$expectedGroupMembers}."
                );
            }

            return [
                'rows' => $prepared['total_rows'],
                'linked_group_members' => $linkedGroupMembers,
                'sso_linked_users' => $prepared['sso_linked_users'],
                'supplemental_lecturers' => $supplementalLecturers,
                'orphaned_foreign_keys' => $orphans,
            ];
        }, 3);
    }

    /**
     * @param  array<int, int>  $userIds
     */
    private function createSupplementalLecturers(array $userIds): int
    {
        $created = 0;

        foreach ($userIds as $userId) {
            if (DB::table('lecturers')->where('user_id', $userId)->exists()) {
                continue;
            }

            DB::table('lecturers')->insert([
                'user_id' => $userId,
                'employee_number' => Lecturer::LEGACY_CAPSTONE_ACTOR_PREFIX.$userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Resolve simple PostgreSQL defaults used by compatibility columns.
     *
     * @return array{0:bool, 1:mixed}
     */
    private function scalarDatabaseDefault(?string $default): array
    {
        if ($default === null || str_starts_with($default, 'nextval(')) {
            return [false, null];
        }

        if (preg_match("/^'(.*)'::[a-zA-Z0-9_ ]+$/s", $default, $matches)) {
            return [true, str_replace("''", "'", $matches[1])];
        }

        if ($default === 'true' || $default === 'false') {
            return [true, $default];
        }

        if (is_numeric($default)) {
            return [true, $default];
        }

        return [false, null];
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $preparedTables
     */
    private function assertPreparedForeignKeys(array $preparedTables): void
    {
        $errors = [];

        foreach (self::SOURCE_TABLES as $sourceTable) {
            $targetTable = $this->targetTable($sourceTable);

            foreach (Schema::getForeignKeys($targetTable) as $foreignKey) {
                if (count($foreignKey['columns']) !== 1
                    || count($foreignKey['foreign_columns']) !== 1
                    || ! str_starts_with($foreignKey['foreign_table'], 'capstone_')) {
                    continue;
                }

                $column = $foreignKey['columns'][0];
                $foreignColumn = $foreignKey['foreign_columns'][0];
                $foreignSource = substr($foreignKey['foreign_table'], strlen('capstone_'));

                if (! isset($preparedTables[$foreignSource])) {
                    throw new RuntimeException(
                        "Tabel referensi {$foreignKey['foreign_table']} tidak termasuk importer."
                    );
                }

                $validValues = collect($preparedTables[$foreignSource])
                    ->pluck($foreignColumn)
                    ->filter(fn ($value) => $value !== null)
                    ->mapWithKeys(fn ($value) => [(string) $value => true]);

                foreach ($preparedTables[$sourceTable] as $row) {
                    $value = $row[$column] ?? null;
                    if ($value !== null && ! $validValues->has((string) $value)) {
                        $rowId = $row['id'] ?? '?';
                        $key = "{$targetTable}.{$column}:{$rowId}:{$value}";
                        $errors[$key] = "{$targetTable} row {$rowId}: {$column}={$value} "
                            ."tidak ditemukan di {$foreignKey['foreign_table']}.{$foreignColumn}.";
                    }
                }
            }
        }

        if ($errors !== []) {
            throw new RuntimeException(
                "Foreign key antar data Capstone tidak lengkap:\n- "
                .implode("\n- ", array_slice(array_values($errors), 0, 30))
            );
        }
    }

    private function countOrphanedForeignKeys(): int
    {
        $orphans = 0;

        foreach (self::SOURCE_TABLES as $sourceTable) {
            $targetTable = $this->targetTable($sourceTable);

            foreach (Schema::getForeignKeys($targetTable) as $index => $foreignKey) {
                if (count($foreignKey['columns']) !== 1
                    || count($foreignKey['foreign_columns']) !== 1) {
                    continue;
                }

                $column = $foreignKey['columns'][0];
                $foreignTable = $foreignKey['foreign_table'];
                $foreignColumn = $foreignKey['foreign_columns'][0];
                $childAlias = "child_{$index}";
                $parentAlias = "parent_{$index}";
                $orphans += DB::table("{$targetTable} as {$childAlias}")
                    ->leftJoin(
                        "{$foreignTable} as {$parentAlias}",
                        "{$parentAlias}.{$foreignColumn}",
                        '=',
                        "{$childAlias}.{$column}"
                    )
                    ->whereNotNull("{$childAlias}.{$column}")
                    ->whereNull("{$parentAlias}.{$foreignColumn}")
                    ->count();
            }
        }

        return $orphans;
    }

    private function advanceSequence(string $table): void
    {
        if (! preg_match('/^capstone_[a-z0-9_]+$/', $table)) {
            throw new RuntimeException("Nama tabel tidak aman: {$table}");
        }

        DB::statement(
            "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), "
            ."GREATEST(COALESCE((SELECT MAX(id) FROM {$table}), 1), 1), true)"
        );
    }

    private function targetTable(string $sourceTable): string
    {
        return "capstone_{$sourceTable}";
    }
}
