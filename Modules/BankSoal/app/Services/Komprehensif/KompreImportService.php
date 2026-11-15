<?php

namespace Modules\BankSoal\Services\Komprehensif;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\BankSoal\Enums\JadwalStatus;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Komprehensif\ImportBatch;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\Komprehensif\Imports\LegacyRekapParser;
use Modules\BankSoal\Services\Komprehensif\Imports\TemplateNilaiParser;

class KompreImportService
{
    private const CACHE_PREFIX = 'kompre-import:';

    /** @var Collection<int, Cpl> */
    private $cpls;

    public function __construct(private LegacyUserProvisioner $provisioner)
    {
        $this->cpls = collect();
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<string, mixed>
     */
    public function preview(array $paths, int $adminId): array
    {
        $parsedFiles = [];

        foreach ($paths as $path) {
            $parsedFiles[] = $this->parseFile($path);
        }

        $summary = $this->summarize($parsedFiles);
        $token = Str::random(40);

        Cache::put(self::CACHE_PREFIX.$token, [
            'admin_id' => $adminId,
            'files' => $parsedFiles,
        ], now()->addMinutes(30));

        return [
            'token' => $token,
            'files' => $summary['files'],
            'totals' => $summary['totals'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $token, int $adminId): array
    {
        $payload = Cache::pull(self::CACHE_PREFIX.$token);

        if (! $payload || (int) ($payload['admin_id'] ?? 0) !== $adminId) {
            throw new \RuntimeException('Sesi import kedaluwarsa. Silakan upload ulang.');
        }

        $results = [];

        foreach ($payload['files'] as $parsed) {
            if (isset($parsed['error'])) {
                $results[] = [
                    'file' => $parsed['file_name'] ?? '-',
                    'success' => false,
                    'message' => $parsed['error'],
                ];

                continue;
            }

            $hash = $parsed['file_hash'] ?? null;

            if ($hash && ImportBatch::where('file_hash', $hash)->where('status', 'committed')->exists()) {
                $results[] = [
                    'file' => $parsed['file_name'],
                    'success' => true,
                    'skipped_file' => true,
                    'message' => 'File ini sudah pernah diimport.',
                ];

                continue;
            }

            $batch = ImportBatch::create([
                'user_id' => $adminId,
                'tipe' => $parsed['type'],
                'nama_file' => $parsed['file_name'],
                'file_hash' => $hash,
                'status' => 'preview',
            ]);

            try {
                $result = DB::transaction(fn () => $this->importParsed($parsed, $batch, $adminId));

                $batch->update([
                    'total_baris' => $result['total'],
                    'berhasil' => $result['imported'],
                    'dilewati' => $result['skipped'],
                    'gagal' => $result['failed'],
                    'errors' => $result['errors'],
                    'status' => 'committed',
                ]);

                $results[] = [
                    'file' => $parsed['file_name'],
                    'success' => true,
                    'batch_id' => $batch->id,
                    ...$result,
                ];
            } catch (\Throwable $e) {
                $batch->update([
                    'status' => 'failed',
                    'errors' => [['message' => $e->getMessage()]],
                ]);

                $results[] = [
                    'file' => $parsed['file_name'],
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['results' => $results];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseFile(string $path): array
    {
        try {
            return (new TemplateNilaiParser)->parse($path);
        } catch (\Throwable $templateError) {
            try {
                return (new LegacyRekapParser)->parse($path);
            } catch (\Throwable $legacyError) {
                return [
                    'type' => 'unknown',
                    'file_name' => basename($path),
                    'file_hash' => hash_file('sha256', $path) ?: null,
                    'error' => 'Format file tidak dikenali. Gunakan template import atau file rekap hasil tes.',
                ];
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $parsedFiles
     * @return array{files: array<int, array<string, mixed>>, totals: array<string, int>}
     */
    private function summarize(array $parsedFiles): array
    {
        $files = [];
        $totals = [
            'rows' => 0,
            'existing_students' => 0,
            'new_students' => 0,
            'pass' => 0,
            'fail' => 0,
            'mismatch' => 0,
            'blank' => 0,
        ];

        foreach ($parsedFiles as $parsed) {
            if (isset($parsed['error'])) {
                $files[] = [
                    'name' => $parsed['file_name'] ?? '-',
                    'type' => 'unknown',
                    'error' => $parsed['error'],
                    'periods' => [],
                ];

                continue;
            }

            $groups = $parsed['type'] === 'template'
                ? $this->templateGroups($parsed)
                : [$this->legacyGroup($parsed)];

            $periodSummaries = [];

            foreach ($groups as $group) {
                $rows = $group['rows'];
                $nims = array_values(array_unique(array_map(
                    fn (array $row) => (string) $row['nim'],
                    $rows
                )));

                $found = $nims === []
                    ? []
                    : Student::whereIn('student_number', $nims)->pluck('student_number')->all();
                $missing = array_values(array_diff($nims, $found));

                $blank = 0;
                $pass = 0;
                $fail = 0;
                $mismatch = 0;

                foreach ($rows as $row) {
                    if ($this->isBlankRow($row)) {
                        $blank++;

                        continue;
                    }

                    $total = (float) ($row['total'] ?? 0);
                    $total >= 60 ? $pass++ : $fail++;

                    $status = strtoupper(trim((string) ($row['status'] ?? '')));
                    if ($status !== '' && in_array($status, ['LULUS', 'TIDAK LULUS'], true)) {
                        $expected = $total >= 60 ? 'LULUS' : 'TIDAK LULUS';
                        if ($status !== $expected) {
                            $mismatch++;
                        }
                    }
                }

                $periodSummaries[] = [
                    'name' => $group['period']['nama'] ?? '-',
                    'session' => $group['session']['nama_sesi'] ?? '-',
                    'rows' => count($rows),
                    'blank' => $blank,
                    'pass' => $pass,
                    'fail' => $fail,
                    'mismatch' => $mismatch,
                    'existing_students' => count($found),
                    'new_students' => count($missing),
                ];

                $totals['rows'] += count($rows);
                $totals['blank'] += $blank;
                $totals['pass'] += $pass;
                $totals['fail'] += $fail;
                $totals['mismatch'] += $mismatch;
                $totals['existing_students'] += count($found);
                $totals['new_students'] += count($missing);
            }

            $files[] = [
                'name' => $parsed['file_name'],
                'type' => $parsed['type'],
                'already_imported' => $parsed['file_hash']
                    ? ImportBatch::where('file_hash', $parsed['file_hash'])->where('status', 'committed')->exists()
                    : false,
                'periods' => $periodSummaries,
            ];
        }

        return ['files' => $files, 'totals' => $totals];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function templateGroups(array $parsed): array
    {
        $groups = [];

        foreach ($parsed['periods'] ?? [] as $periodGroup) {
            $rows = $periodGroup['rows'] ?? [];
            $sessions = [];

            foreach ($rows as $row) {
                $key = ($row['session'] ?? '').'|'.($row['tanggal_ujian'] ?? '');
                $sessions[$key] = [
                    'nama_sesi' => $row['session'] ?? 'Sesi 1',
                    'tanggal_ujian' => $row['tanggal_ujian'] ?? null,
                ];
            }

            $groups[] = [
                'period' => $periodGroup['period'],
                'session' => $sessions === [] ? ['nama_sesi' => 'Sesi 1', 'tanggal_ujian' => null] : reset($sessions),
                'sessions' => array_values($sessions),
                'rows' => $rows,
            ];
        }

        return $groups;
    }

    /**
     * @return array<string, mixed>
     */
    private function legacyGroup(array $parsed): array
    {
        return [
            'period' => $parsed['period'],
            'session' => $parsed['session'],
            'sessions' => [$parsed['session']],
            'rows' => $parsed['rows'] ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function importParsed(array $parsed, ImportBatch $batch, int $adminId): array
    {
        $this->cpls = Cpl::orderBy('kode')->get();

        return $parsed['type'] === 'template'
            ? $this->importTemplate($parsed, $batch, $adminId)
            : $this->importLegacy($parsed, $batch, $adminId);
    }

    /**
     * @return array<string, mixed>
     */
    private function importLegacy(array $parsed, ImportBatch $batch, int $adminId): array
    {
        [$period, $periodCreated] = $this->ensurePeriod($parsed['period'], true);
        [$jadwal, $jadwalCreated] = $this->ensureJadwal($period, $parsed['session'], count($parsed['rows']));

        $result = $this->emptyResult(count($parsed['rows']));
        $result['periods_created'] = $periodCreated ? 1 : 0;
        $result['jadwals_created'] = $jadwalCreated ? 1 : 0;

        foreach ($parsed['rows'] as $row) {
            $this->importRow($row, $period, $jadwal, $batch, $adminId, 'import_legacy', false, $result);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function importTemplate(array $parsed, ImportBatch $batch, int $adminId): array
    {
        $result = $this->emptyResult();
        $result['periods_created'] = 0;
        $result['jadwals_created'] = 0;

        foreach ($parsed['periods'] as $periodGroup) {
            [$period, $periodCreated] = $this->ensurePeriod($periodGroup['period']);
            $result['periods_created'] += $periodCreated ? 1 : 0;

            $sessions = [];
            foreach ($periodGroup['rows'] as $row) {
                $key = ($row['session'] ?? '').'|'.($row['tanggal_ujian'] ?? '');
                if (! isset($sessions[$key])) {
                    [$jadwal, $created] = $this->ensureJadwal($period, [
                        'nama_sesi' => $row['session'] ?? 'Sesi 1',
                        'tanggal_ujian' => $row['tanggal_ujian'] ?? null,
                    ], count($periodGroup['rows']));
                    $sessions[$key] = $jadwal;
                    $result['jadwals_created'] += $created ? 1 : 0;
                }
            }

            foreach ($periodGroup['rows'] as $row) {
                $key = ($row['session'] ?? '').'|'.($row['tanggal_ujian'] ?? '');
                $jadwal = $sessions[$key];

                $result['total']++;

                if ($this->isBlankRow($row)) {
                    $result['skipped']++;

                    continue;
                }

                $this->importRow($row, $period, $jadwal, $batch, $adminId, 'import_template', true, $result);
            }
        }

        return $result;
    }

    private function importRow(
        array $row,
        PeriodeUjian $period,
        JadwalUjian $jadwal,
        ImportBatch $batch,
        int $adminId,
        string $source,
        bool $template,
        array &$result
    ): void {
        try {
            $provisioned = $this->provisioner->resolve((string) $row['nim'], (string) ($row['nama'] ?? ''));
            $user = $provisioned['user'];

            $result['users_created'] += $provisioned['created_user'] ? 1 : 0;
            $result['students_created'] += $provisioned['created_student'] ? 1 : 0;

            $this->ensurePendaftar($period, $jadwal, $user, $row, $adminId);

            $exists = KompreSession::where('user_id', $user->id)
                ->where('jadwal_id', $jadwal->id)
                ->where('status', KompreSessionStatus::Finished)
                ->exists();

            if ($exists) {
                $result['skipped']++;

                return;
            }

            $total = round((float) ($row['total'] ?? 0), 2);
            $lulus = $total >= 60;

            $scores = $this->buildCplScores($row);

            $date = $jadwal->tanggal_ujian?->format('Y-m-d') ?? now()->toDateString();
            $startedAt = Carbon::parse($date.' '.($jadwal->waktu_mulai ?: '08:00'));
            $finishedAt = Carbon::parse($date.' '.($jadwal->waktu_selesai ?: '10:00'));

            $sessionName = $jadwal->nama_sesi === 'Susulan' ? 'Susulan' : 'Sesi '.$jadwal->nama_sesi;

            KompreSession::create([
                'user_id' => $user->id,
                'jadwal_id' => $jadwal->id,
                'title' => 'Ujian Komprehensif '.$sessionName,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
                'score' => $total,
                'status' => KompreSessionStatus::Finished,
                'lulus' => $lulus,
                'cpl_scores' => $scores,
                'source' => $source,
                'import_batch_id' => $batch->id,
            ]);

            $result['imported']++;

            $status = strtoupper(trim((string) ($row['status'] ?? '')));
            if ($status !== '' && in_array($status, ['LULUS', 'TIDAK LULUS'], true)) {
                $expected = $lulus ? 'LULUS' : 'TIDAK LULUS';
                if ($status !== $expected) {
                    $result['mismatch']++;
                    $this->addError($result, $row, "Status file {$status} berbeda dengan total {$total}; memakai aturan total.");
                }
            }
        } catch (\Throwable $e) {
            $result['failed']++;
            $this->addError($result, $row, $e->getMessage());
        }
    }

    private function ensurePeriod(array $data, bool $expandDateRange = false): array
    {
        $period = PeriodeUjian::where('nama_periode', $data['nama'])->first();

        if ($period) {
            if ($expandDateRange) {
                $start = $data['tanggal_mulai'] ?? now()->toDateString();
                $end = $data['tanggal_selesai'] ?? $start;
                $examStart = $data['tanggal_mulai_ujian'] ?? $start;
                $examEnd = $data['tanggal_selesai_ujian'] ?? $end;

                $period->update([
                    'tanggal_mulai' => collect([$period->tanggal_mulai?->toDateString(), $start])->filter()->min(),
                    'tanggal_selesai' => collect([$period->tanggal_selesai?->toDateString(), $end])->filter()->max(),
                    'tanggal_mulai_ujian' => collect([$period->tanggal_mulai_ujian?->toDateString(), $examStart])->filter()->min(),
                    'tanggal_selesai_ujian' => collect([$period->tanggal_selesai_ujian?->toDateString(), $examEnd])->filter()->max(),
                    'status' => 'selesai',
                ]);
            }

            return [$period, false];
        }

        $start = $data['tanggal_mulai'] ?? now()->toDateString();
        $end = $data['tanggal_selesai'] ?? $start;
        $examStart = $data['tanggal_mulai_ujian'] ?? $start;
        $examEnd = $data['tanggal_selesai_ujian'] ?? $end;

        $status = $data['status'] ?? null;
        if (! in_array($status, ['draft', 'aktif', 'selesai'], true)) {
            $today = now()->toDateString();
            $status = $examEnd < $today ? 'selesai' : (($examStart <= $today && $today <= $examEnd) ? 'aktif' : 'draft');
        }

        $slugBase = Str::slug($data['nama']);
        $slug = $slugBase;
        while (PeriodeUjian::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.Str::lower(Str::random(5));
        }

        $period = PeriodeUjian::create([
            'nama_periode' => $data['nama'],
            'slug' => $slug,
            'tanggal_mulai' => $start,
            'tanggal_selesai' => $end,
            'tanggal_mulai_ujian' => $examStart,
            'tanggal_selesai_ujian' => $examEnd,
            'status' => $status,
            'deskripsi' => 'Migrasi data hasil ujian komprehensif dari sistem lama.',
            'kuota_peserta' => null,
            'target_wisuda_options' => null,
        ]);

        return [$period, true];
    }

    private function ensureJadwal(PeriodeUjian $period, array $session, int $rowCount): array
    {
        $namaSesi = $this->normalizeSessionName((string) ($session['nama_sesi'] ?? 'Sesi 1'));
        $tanggal = $session['tanggal_ujian']
            ?? $period->tanggal_mulai_ujian?->format('Y-m-d')
            ?? now()->toDateString();

        $jadwal = JadwalUjian::withTrashed()
            ->where('periode_ujian_id', $period->id)
            ->where('nama_sesi', $namaSesi)
            ->where('tanggal_ujian', $tanggal)
            ->first();

        if ($jadwal) {
            if ($jadwal->trashed()) {
                $jadwal->restore();
            }

            return [$jadwal, false];
        }

        $jadwal = JadwalUjian::create([
            'periode_ujian_id' => $period->id,
            'nama_sesi' => $namaSesi,
            'tanggal_ujian' => $tanggal,
            'kuota' => max(1, $rowCount),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => null,
            'status' => JadwalStatus::Selesai->value,
            'token' => null,
        ]);

        return [$jadwal, true];
    }

    private function ensurePendaftar(PeriodeUjian $period, JadwalUjian $jadwal, $user, array $row, int $adminId): void
    {
        $pendaftar = PendaftarUjian::withTrashed()
            ->where('periode_ujian_id', $period->id)
            ->where('nim', (string) $row['nim'])
            ->first();

        $payload = [
            'mahasiswa_id' => $user->id,
            'nama_lengkap' => $row['nama'] ?? $user->name,
            'semester_aktif' => 7,
            'status_pendaftaran' => PendaftaranStatus::Approved->value,
            'jadwal_ujian_id' => $jadwal->id,
            'catatan_admin' => 'Import data lama',
            'ditambahkan_oleh' => $adminId,
        ];

        if ($pendaftar) {
            if ($pendaftar->trashed()) {
                $pendaftar->restore();
            }
            $pendaftar->update($payload);

            return;
        }

        PendaftarUjian::create([
            'periode_ujian_id' => $period->id,
            'nim' => (string) $row['nim'],
            ...$payload,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildCplScores(array $row): array
    {
        $filled = array_filter(
            $row['cpl_values'] ?? [],
            fn ($value) => $value !== null
        );

        if ($filled === []) {
            return [];
        }

        $scores = [];

        foreach ($this->cpls as $cpl) {
            $number = $this->cplNumber($cpl->kode);
            $nilai = $number !== null
                ? (float) ($row['cpl_values'][$number] ?? 0)
                : 0.0;

            $scores[] = [
                'cpl_id' => $cpl->id,
                'kode' => $cpl->kode,
                'deskripsi' => $cpl->deskripsi,
                'total' => 10,
                'benar' => (int) round($nilai),
                'nilai' => round($nilai, 2),
                'lulus' => $nilai >= 6.0,
            ];
        }

        return $scores;
    }

    private function cplNumber(string $kode): ?int
    {
        return preg_match('/(\d+)\s*$/', $kode, $m) ? (int) $m[1] : null;
    }

    private function normalizeSessionName(string $name): string
    {
        if (preg_match('/Sesi\s*(\d+)/i', $name, $m)) {
            return (string) (int) $m[1];
        }

        if (stripos($name, 'susulan') !== false) {
            return 'Susulan';
        }

        return trim($name) ?: 'Sesi 1';
    }

    private function isBlankRow(array $row): bool
    {
        if (($row['total'] ?? null) !== null) {
            return false;
        }

        foreach ($row['cpl_values'] ?? [] as $value) {
            if ($value !== null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyResult(int $total = 0): array
    {
        return [
            'total' => $total,
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'mismatch' => 0,
            'users_created' => 0,
            'students_created' => 0,
            'errors' => [],
        ];
    }

    private function addError(array &$result, array $row, string $message): void
    {
        if (count($result['errors']) >= 100) {
            return;
        }

        $result['errors'][] = [
            'row' => $row['row'] ?? null,
            'nim' => $row['nim'] ?? null,
            'nama' => $row['nama'] ?? null,
            'message' => $message,
        ];
    }
}
