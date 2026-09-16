<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class ImportNeonUsers extends Command
{
    protected $signature = 'users:import-neon
                            {dump : Path to the Neon custom-format PostgreSQL dump}
                            {--apply : Commit the import; without this option only a dry-run is performed}
                            {--pg-restore= : Explicit path to pg_restore}';

    protected $description = 'Import legacy Neon users into the current users, profiles, roles, and permissions schema';

    /**
     * @var array<int, string>
     */
    private const SOURCE_COLUMNS = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'role',
        'nip',
        'nim',
        'is_active',
    ];

    /**
     * @var array<int, string>
     */
    private const ACADEMIC_PERMISSION_NAMES = [
        'banksoal.view',
        'banksoal.edit',
        'banksoal.delete',
        'capstone.view',
        'capstone.edit',
        'capstone.delete',
        'eoffice.view',
        'eoffice.edit',
        'eoffice.delete',
        'kemahasiswaan.view',
        'kemahasiswaan.edit',
        'kemahasiswaan.delete',
    ];

    public function handle(): int
    {
        try {
            $this->assertDestinationSchema();

            $sourceRows = $this->readDumpUsers((string) $this->argument('dump'));
            $users = $this->normalizeUsers($sourceRows);
            $analysis = $this->analyze($users);

            $this->renderAnalysis($analysis);

            if (! $this->option('apply')) {
                $this->info('Dry-run selesai. Tidak ada data database yang diubah.');

                return self::SUCCESS;
            }

            if ($analysis['conflicts']['ids'] > 0
                || $analysis['conflicts']['emails'] > 0
                || $analysis['conflicts']['external_ids'] > 0) {
                $this->error('Import dibatalkan karena ada konflik unik di database tujuan.');

                return self::FAILURE;
            }

            if ($this->input->isInteractive()
                && ! $this->confirm('Impor seluruh akun dan lengkapi role/permission yang belum ada?', false)) {
                $this->warn('Import dibatalkan.');

                return self::SUCCESS;
            }

            $result = $this->import($users);

            $this->newLine();
            $this->info('Import Neon selesai dan transaksi berhasil di-commit.');
            $this->table(['Data', 'Jumlah'], [
                ['Users', $result['users']],
                ['Students', $result['students']],
                ['Lecturers', $result['lecturers']],
                ['Role assignments ditambahkan', $result['roles']],
                ['Direct permissions ditambahkan', $result['permissions']],
                ['Role assignments lama dipertahankan', $result['preserved_role_pivots']],
                ['Direct permissions lama dipertahankan', $result['preserved_permission_pivots']],
            ]);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function assertDestinationSchema(): void
    {
        $required = [
            'users' => ['id', 'external_id', 'name', 'email', 'password', 'suspended_at'],
            'students' => ['user_id', 'student_number', 'cohort_year'],
            'lecturers' => ['user_id', 'employee_number'],
            'roles' => ['id', 'name', 'module', 'guard_name', 'is_academic'],
            'permissions' => ['id', 'name'],
            'model_has_roles' => ['role_id', 'model_type', 'model_id'],
            'model_has_permissions' => ['permission_id', 'model_type', 'model_id'],
        ];

        foreach ($required as $table => $columns) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Tabel tujuan {$table} tidak ditemukan.");
            }

            $missing = array_diff($columns, Schema::getColumnListing($table));
            if ($missing !== []) {
                throw new RuntimeException(
                    "Kolom tujuan {$table}.".implode(", {$table}.", $missing).' tidak ditemukan.'
                );
            }
        }
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function readDumpUsers(string $dump): array
    {
        $dumpPath = realpath($dump);
        if ($dumpPath === false || ! is_file($dumpPath)) {
            throw new RuntimeException("Dump tidak ditemukan: {$dump}");
        }

        $pgRestore = $this->resolvePgRestore();
        $process = new Process([
            $pgRestore,
            '--data-only',
            '--table=users',
            '--file=-',
            $dumpPath,
        ]);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('pg_restore gagal membaca dump: '.trim($process->getErrorOutput()));
        }

        return $this->parseUsersCopy($process->getOutput());
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

        $windowsCandidates = [
            'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_restore.exe',
            'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_restore.exe',
            'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_restore.exe',
        ];

        foreach ($windowsCandidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('pg_restore tidak tersedia. Gunakan opsi --pg-restore.');
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function parseUsersCopy(string $sql): array
    {
        $rows = [];
        $insideCopy = false;
        $headerFound = false;

        foreach (preg_split('/\R/', $sql) ?: [] as $line) {
            if (! $insideCopy) {
                if (! str_starts_with($line, 'COPY public.users ')) {
                    continue;
                }

                if (! preg_match('/^COPY public\.users \(([^)]+)\) FROM stdin;$/', $line, $matches)) {
                    throw new RuntimeException('Header COPY users pada dump tidak dikenali.');
                }

                $columns = array_map('trim', explode(',', $matches[1]));
                if ($columns !== self::SOURCE_COLUMNS) {
                    throw new RuntimeException(
                        'Skema users dump berbeda. Ditemukan: '.implode(', ', $columns)
                    );
                }

                $headerFound = true;
                $insideCopy = true;

                continue;
            }

            if ($line === '\\.') {
                $insideCopy = false;
                break;
            }

            $values = explode("\t", $line);
            if (count($values) !== count(self::SOURCE_COLUMNS)) {
                throw new RuntimeException('Jumlah kolom data users pada dump tidak sesuai header.');
            }

            $decoded = array_map(fn (string $value) => $this->decodeCopyValue($value), $values);
            $rows[] = array_combine(self::SOURCE_COLUMNS, $decoded);
        }

        if (! $headerFound) {
            throw new RuntimeException('TABLE DATA public.users tidak ditemukan dalam dump.');
        }

        if ($insideCopy) {
            throw new RuntimeException('Data COPY users tidak ditutup dengan benar.');
        }

        if ($rows === []) {
            throw new RuntimeException('Dump tidak berisi row users.');
        }

        return $rows;
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
     * @param  array<int, array<string, string|null>>  $sourceRows
     * @return array<int, array<string, mixed>>
     */
    private function normalizeUsers(array $sourceRows): array
    {
        $users = [];
        $ids = [];
        $emails = [];
        $studentNumbers = [];
        $employeeNumbers = [];

        foreach ($sourceRows as $row) {
            $id = filter_var($row['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $name = trim((string) $row['name']);
            $email = strtolower(trim((string) $row['email']));
            $role = strtolower(trim((string) $row['role']));

            if ($id === false || $name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Dump mengandung ID, nama, atau email users yang tidak valid.');
            }

            if (! in_array($role, ['admin', 'dosen', 'mahasiswa'], true)) {
                throw new RuntimeException("Role sumber tidak didukung: {$role}");
            }

            if (isset($ids[$id]) || isset($emails[$email])) {
                throw new RuntimeException('Dump mengandung ID atau email users duplikat.');
            }

            $ids[$id] = true;
            $emails[$email] = true;
            $nim = $this->nullableIdentifier($row['nim']);
            $nip = $this->nullableIdentifier($row['nip']);
            $studentNumber = null;
            $employeeNumber = null;
            $cohortYear = null;
            $generatedStudentNumber = false;

            if ($role === 'mahasiswa') {
                $studentNumber = $nim ?: "NEON-{$id}";
                $generatedStudentNumber = $nim === null;
                $cohortYear = $this->extractCohortYear($studentNumber);

                if (isset($studentNumbers[$studentNumber])) {
                    throw new RuntimeException('Dump menghasilkan student_number duplikat setelah normalisasi.');
                }
                $studentNumbers[$studentNumber] = true;
            }

            if ($role === 'dosen') {
                $employeeNumber = $nip ?: "NEON-{$id}";
                if (isset($employeeNumbers[$employeeNumber])) {
                    throw new RuntimeException('Dump menghasilkan employee_number duplikat setelah normalisasi.');
                }
                $employeeNumbers[$employeeNumber] = true;
            }

            $isActive = in_array(strtolower((string) $row['is_active']), ['t', 'true', '1'], true);

            $users[] = [
                'source_id' => $id,
                'source_role' => $role,
                'target_role' => $role === 'admin' ? 'superadmin' : $role,
                'external_id' => "neon:user:{$id}",
                'name' => $name,
                'email' => $email,
                'email_verified_at' => $row['email_verified_at'],
                'password' => $row['password'],
                // Remember tokens from a different deployment must never remain valid.
                'remember_token' => null,
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'is_active' => $isActive,
                'student_number' => $studentNumber,
                'generated_student_number' => $generatedStudentNumber,
                'cohort_year' => $cohortYear,
                'employee_number' => $employeeNumber,
            ];
        }

        return $users;
    }

    private function nullableIdentifier(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function extractCohortYear(string $studentNumber): int
    {
        $currentYear = (int) date('Y');

        if (strlen($studentNumber) >= 8) {
            $fromMiddle = (int) ('20'.substr($studentNumber, 6, 2));
            if ($fromMiddle >= 2000 && $fromMiddle <= $currentYear) {
                return $fromMiddle;
            }
        }

        if (strlen($studentNumber) >= 2) {
            $fromPrefix = (int) ('20'.substr($studentNumber, 0, 2));
            if ($fromPrefix >= 2000 && $fromPrefix <= $currentYear) {
                return $fromPrefix;
            }
        }

        return $currentYear;
    }

    /**
     * @param  array<int, array<string, mixed>>  $users
     * @return array<string, mixed>
     */
    private function analyze(array $users): array
    {
        $ids = array_column($users, 'source_id');
        $emails = array_column($users, 'email');
        $externalIds = array_column($users, 'external_id');

        return [
            'source' => [
                'users' => count($users),
                'students' => count(array_filter($users, fn (array $user) => $user['source_role'] === 'mahasiswa')),
                'lecturers' => count(array_filter($users, fn (array $user) => $user['source_role'] === 'dosen')),
                'admins' => count(array_filter($users, fn (array $user) => $user['source_role'] === 'admin')),
                'generated_student_numbers' => count(array_filter(
                    $users,
                    fn (array $user) => $user['generated_student_number']
                )),
            ],
            'destination' => [
                'users' => DB::table('users')->count(),
                'students' => DB::table('students')->count(),
                'lecturers' => DB::table('lecturers')->count(),
            ],
            'conflicts' => [
                'ids' => DB::table('users')->whereIn('id', $ids)->count(),
                'emails' => DB::table('users')->whereIn(DB::raw('LOWER(email)'), $emails)->count(),
                'external_ids' => DB::table('users')->whereIn('external_id', $externalIds)->count(),
            ],
            'existing_pivots' => [
                'roles' => DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->whereIn('model_id', $ids)
                    ->count(),
                'permissions' => DB::table('model_has_permissions')
                    ->where('model_type', User::class)
                    ->whereIn('model_id', $ids)
                    ->count(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    private function renderAnalysis(array $analysis): void
    {
        $this->info($this->option('apply') ? 'Rencana import Neon' : 'Dry-run import Neon');
        $this->table(['Pemeriksaan', 'Jumlah'], [
            ['Users sumber', $analysis['source']['users']],
            ['Mahasiswa sumber', $analysis['source']['students']],
            ['Dosen sumber', $analysis['source']['lecturers']],
            ['Admin → superadmin', $analysis['source']['admins']],
            ['NIM legacy yang dibuat', $analysis['source']['generated_student_numbers']],
            ['Users tujuan saat ini', $analysis['destination']['users']],
            ['Students tujuan saat ini', $analysis['destination']['students']],
            ['Lecturers tujuan saat ini', $analysis['destination']['lecturers']],
            ['Konflik ID', $analysis['conflicts']['ids']],
            ['Konflik email', $analysis['conflicts']['emails']],
            ['Konflik external_id', $analysis['conflicts']['external_ids']],
            ['Pivot role lama yang dipertahankan', $analysis['existing_pivots']['roles']],
            ['Pivot permission lama yang dipertahankan', $analysis['existing_pivots']['permissions']],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $users
     * @return array<string, int>
     */
    private function import(array $users): array
    {
        return DB::transaction(function () use ($users) {
            $analysis = $this->analyze($users);
            if (array_sum($analysis['conflicts']) > 0) {
                throw new RuntimeException('Konflik database berubah sejak dry-run. Transaksi dibatalkan.');
            }

            $ids = array_column($users, 'source_id');
            $preservedRolePivots = DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $ids)
                ->count();
            $preservedPermissionPivots = DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->whereIn('model_id', $ids)
                ->count();

            $roleIds = [
                'superadmin' => $this->ensureTargetRole('superadmin', 'global', false),
                'dosen' => $this->ensureTargetRole('dosen', 'global', true),
                'mahasiswa' => $this->ensureTargetRole('mahasiswa', 'global', true),
            ];

            $now = now();
            $userRows = [];
            $studentRows = [];
            $lecturerRows = [];
            $roleRows = [];

            foreach ($users as $user) {
                $userRows[] = [
                    'id' => $user['source_id'],
                    'external_id' => $user['external_id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'last_login' => null,
                    'last_synced_from_sso' => null,
                    'sso_data' => null,
                    'created_at' => $user['created_at'] ?: $now,
                    'updated_at' => $user['updated_at'] ?: $now,
                    'deleted_at' => null,
                    'email_verified_at' => $user['email_verified_at'],
                    'remember_token' => null,
                    'password' => $user['password'],
                    'suspended_at' => $user['is_active'] ? null : $now,
                    'suspension_reason' => $user['is_active']
                        ? null
                        : 'Imported from Neon with inactive status.',
                    'personal_email' => null,
                    'session_version' => 0,
                    'avatar_url_format' => null,
                    'is_online' => false,
                    'whatsapp' => null,
                    'avatar_url' => null,
                ];

                if ($user['source_role'] === 'mahasiswa') {
                    $studentRows[] = [
                        'user_id' => $user['source_id'],
                        'student_number' => $user['student_number'],
                        'cohort_year' => $user['cohort_year'],
                        'created_at' => $user['created_at'] ?: $now,
                        'updated_at' => $user['updated_at'] ?: $now,
                    ];
                }

                if ($user['source_role'] === 'dosen') {
                    $lecturerRows[] = [
                        'user_id' => $user['source_id'],
                        'employee_number' => $user['employee_number'],
                        'created_at' => $user['created_at'] ?: $now,
                        'updated_at' => $user['updated_at'] ?: $now,
                    ];
                }

                $roleRows[] = [
                    'role_id' => $roleIds[$user['target_role']],
                    'model_type' => User::class,
                    'model_id' => $user['source_id'],
                ];
            }

            foreach (array_chunk($userRows, 100) as $chunk) {
                DB::table('users')->insert($chunk);
            }
            foreach (array_chunk($studentRows, 100) as $chunk) {
                DB::table('students')->insert($chunk);
            }
            foreach (array_chunk($lecturerRows, 100) as $chunk) {
                DB::table('lecturers')->insert($chunk);
            }
            $roleRows = $this->missingRoleRows($roleRows, $ids);
            foreach (array_chunk($roleRows, 500) as $chunk) {
                DB::table('model_has_roles')->insert($chunk);
            }

            $permissionRows = $this->missingPermissionRows($this->permissionRows($users), $ids);
            foreach (array_chunk($permissionRows, 1000) as $chunk) {
                DB::table('model_has_permissions')->insert($chunk);
            }

            $this->advanceSequence('users');
            $this->advanceSequence('students');
            $this->advanceSequence('lecturers');
            $this->advanceSequence('roles');

            $importedUsers = DB::table('users')->whereIn('id', $ids)->count();
            if ($importedUsers !== count($users)) {
                throw new RuntimeException('Jumlah users hasil import tidak sesuai sumber.');
            }

            return [
                'users' => $importedUsers,
                'students' => count($studentRows),
                'lecturers' => count($lecturerRows),
                'roles' => count($roleRows),
                'permissions' => count($permissionRows),
                'preserved_role_pivots' => $preservedRolePivots,
                'preserved_permission_pivots' => $preservedPermissionPivots,
            ];
        }, 3);
    }

    /**
     * @param  array<int, array{role_id:int, model_type:string, model_id:int}>  $roleRows
     * @param  array<int, int>  $userIds
     * @return array<int, array{role_id:int, model_type:string, model_id:int}>
     */
    private function missingRoleRows(array $roleRows, array $userIds): array
    {
        $existing = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->whereIn('model_id', $userIds)
            ->get(['role_id', 'model_id'])
            ->mapWithKeys(fn ($row) => ["{$row->role_id}:{$row->model_id}" => true]);

        return array_values(array_filter(
            $roleRows,
            fn (array $row) => ! $existing->has("{$row['role_id']}:{$row['model_id']}")
        ));
    }

    /**
     * @param  array<int, array{permission_id:int, model_type:string, model_id:int}>  $permissionRows
     * @param  array<int, int>  $userIds
     * @return array<int, array{permission_id:int, model_type:string, model_id:int}>
     */
    private function missingPermissionRows(array $permissionRows, array $userIds): array
    {
        $existing = DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->whereIn('model_id', $userIds)
            ->get(['permission_id', 'model_id'])
            ->mapWithKeys(fn ($row) => ["{$row->permission_id}:{$row->model_id}" => true]);

        return array_values(array_filter(
            $permissionRows,
            fn (array $row) => ! $existing->has("{$row['permission_id']}:{$row['model_id']}")
        ));
    }

    private function ensureTargetRole(string $name, string $module, bool $isAcademic): int
    {
        $role = DB::table('roles')
            ->where('name', $name)
            ->where('module', $module)
            ->first();

        if (! $role) {
            $id = ((int) DB::table('roles')->max('id')) + 1;
            DB::table('roles')->insert([
                'id' => $id,
                'name' => $name,
                'module' => $module,
                'guard_name' => 'web',
                'is_academic' => $isAcademic,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $id;
        }

        $sameIdCount = DB::table('roles')->where('id', $role->id)->count();
        if ($sameIdCount > 1) {
            $rolePermissionCount = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->count();
            if ($rolePermissionCount > 0) {
                throw new RuntimeException(
                    "Role {$module}:{$name} memakai ID duplikat dengan permission ambigu. Import dibatalkan."
                );
            }

            $newId = ((int) DB::table('roles')->max('id')) + 1;
            DB::table('roles')
                ->where('id', $role->id)
                ->where('name', $name)
                ->where('module', $module)
                ->update(['id' => $newId, 'updated_at' => now()]);

            return $newId;
        }

        return (int) $role->id;
    }

    /**
     * @param  array<int, array<string, mixed>>  $users
     * @return array<int, array<string, int|string>>
     */
    private function permissionRows(array $users): array
    {
        $academicPermissionIds = DB::table('permissions')
            ->whereIn('name', self::ACADEMIC_PERMISSION_NAMES)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($academicPermissionIds) !== count(self::ACADEMIC_PERMISSION_NAMES)) {
            throw new RuntimeException('Permission akademik tujuan belum lengkap. Import dibatalkan.');
        }

        $allPermissionIds = DB::table('permissions')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $rows = [];

        foreach ($users as $user) {
            $permissionIds = $user['target_role'] === 'superadmin'
                ? $allPermissionIds
                : $academicPermissionIds;

            foreach ($permissionIds as $permissionId) {
                $rows[] = [
                    'permission_id' => $permissionId,
                    'model_type' => User::class,
                    'model_id' => $user['source_id'],
                ];
            }
        }

        return $rows;
    }

    private function advanceSequence(string $table): void
    {
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('{$table}', 'id'), "
            ."GREATEST(COALESCE((SELECT MAX(id) FROM {$table}), 1), 1), true)"
        );
    }
}
