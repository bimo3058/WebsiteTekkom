<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\ImportStatus;
use App\Services\SupabaseStorage;
use App\Services\BulkUserImportExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;

class ProcessBulkImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout      = 600;
    public $tries        = 3;
    public $maxExceptions = 1;

    protected $filePath;
    protected $bucket;
    protected $importStatusId;
    protected array $ssoAccessByRow = [];

    public function __construct($filePath, $bucket = 'data_user', $importStatusId = null, array $ssoAccessByRow = [])
    {
        $this->filePath       = $filePath;
        $this->bucket         = $bucket;
        $this->importStatusId = $importStatusId;
        $this->ssoAccessByRow = $ssoAccessByRow;
    }

    public function handle()
    {
        $importStatus = ImportStatus::find($this->importStatusId);
        if (!$importStatus) {
            Log::error("Import status not found", ['id' => $this->importStatusId]);
            return;
        }

        if ($this->ssoAccessByRow === []) {
            $this->ssoAccessByRow = (array) data_get($importStatus->result_summary, 'sso_access_by_row', []);
        }

        $importStatus->update(['status' => 'processing']);
        Log::info("Starting bulk import", ['import_id' => $this->importStatusId]);

        $storage     = new SupabaseStorage();
        $fileContent = $this->getFileFromSupabase($storage);

        if (!$fileContent) {
            $importStatus->update([
                'status'        => 'failed',
                'error_message' => 'Gagal mendownload file dari Supabase',
            ]);
            return;
        }

        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        fseek($tempFile, 0);

        $excelReader = app(BulkUserImportExcel::class);
        try {
            $parsed = $excelReader->readFile(stream_get_meta_data($tempFile)['uri']);
        } catch (\Throwable $e) {
            fclose($tempFile);
            $importStatus->update(['status' => 'failed', 'error_message' => 'File Excel tidak dapat dibaca.']);
            return;
        }
        $header = $parsed['header'];

        if (!$header) {
            fclose($tempFile);
            $importStatus->update([
                'status'        => 'failed',
                'error_message' => 'Header Excel tidak ditemukan.',
            ]);
            return;
        }

        $normalizedHeader = $excelReader->normalizeHeader($header);
        $requiredColumns = [
            $excelReader->index($normalizedHeader, ['email']),
            $excelReader->index($normalizedHeader, ['name', 'nama']),
            $excelReader->index($normalizedHeader, ['nim/nip', 'nimnip', 'nomorinduk', 'nim', 'nip']),
        ];
        if (in_array(false, $requiredColumns, true)) {
            fclose($tempFile);
            $importStatus->update([
                'status' => 'failed',
                'error_message' => 'Excel wajib memiliki kolom NIM/NIP, Nama, dan Email.',
            ]);
            return;
        }

        Log::info("Excel Header", ['header' => $header]);

        $processedCount = 0;
        $failedCount    = 0;

        try {
            $dataRowIndex = 0;
            foreach ($parsed['rows'] as $row) {
                $rowIndex = $dataRowIndex++;

                // ── Cek cancel SETIAP ROW ─────────────────────────────
                $importStatus->refresh();
                if ($importStatus->status === 'failed') {
                    fclose($tempFile);
                    Log::info("Import cancelled by user", [
                        'import_id' => $this->importStatusId,
                        'processed' => $processedCount,
                    ]);
                    // Bust cache dengan data yang sudah masuk saat cancel
                    $this->bustCache();
                    return;
                }
                // ─────────────────────────────────────────────────────

                $row = $excelReader->mapRow($normalizedHeader, $row);
                if (trim($row[0]) === '' || trim($row[1]) === '' || trim($row[4]) === '') {
                    $failedCount++;
                    Log::warning('Invalid bulk import row: identity, name, or email missing', ['row_index' => $rowIndex]);
                    continue;
                }

                if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                    $failedCount++;
                    Log::warning("Invalid email", ['email' => $row[1]]);
                    continue;
                }

                try {
                    DB::transaction(function () use ($row, $rowIndex, &$processedCount, $excelReader, $importStatus) {
                        $email      = strtolower(trim($row[1]));
                        $emailRole = $excelReader->emailRole($email);
                        $inputRoles = $emailRole ? [$emailRole] : [];

                        if ($inputRoles === []) {
                            throw new \Exception('Role tidak dapat ditentukan dari domain email.');
                        }

                        if (in_array('mahasiswa', $inputRoles, true) && trim((string) ($row[5] ?? '')) === '') {
                            throw new \Exception('NIM wajib diisi untuk user dengan role mahasiswa.');
                        }
                        if (in_array('dosen', $inputRoles, true) && trim((string) ($row[4] ?? '')) === '') {
                            throw new \Exception('NIP/external_id wajib diisi untuk user dengan role dosen.');
                        }

                        Log::info("=== PROCESSING ROW ===", [
                            'email'                  => $email,
                            'roles'                  => $inputRoles,
                            'name'                   => trim($row[0]),
                        ]);

                        $roleIds = Role::whereIn('name', $inputRoles)->pluck('id');

                        if ($roleIds->isEmpty()) {
                            throw new \Exception("Role hasil deteksi email tidak ditemukan: ".implode(', ', $inputRoles));
                        }

                        $existingUser = User::query()
                            ->where('email', $email)
                            ->orWhere('external_id', trim($row[4]))
                            ->lockForUpdate()
                            ->first();
                        if ($existingUser) {
                            throw new \RuntimeException('Email atau NIM/NIP sudah terdaftar.');
                        }

                        $ssoEnabled = $this->ssoAccessByRow[$rowIndex] ?? true;
                        $user = User::create([
                            'name' => trim($row[0]),
                            'email' => $email,
                            'password' => null,
                            'external_id' => trim($row[4]),
                            'sso_enabled' => $ssoEnabled,
                            'sso_enabled_at' => $ssoEnabled ? now() : null,
                            'sso_enabled_by' => $importStatus->user_id,
                        ]);

                        Log::info("User created/found", [
                            'user_id' => $user->id,
                            'email'   => $email,
                            'is_new'  => $user->wasRecentlyCreated,
                        ]);

                        $user->roles()->sync($roleIds);
                        $user->syncPermissionsFromRoles();

                        AuditLog::create([
                            'user_id' => $importStatus->user_id,
                            'module' => 'user_management',
                            'action' => $ssoEnabled ? 'SSO_ACCESS_ENABLED' : 'SSO_ACCESS_DISABLED',
                            'subject_type' => User::class,
                            'subject_id' => $user->id,
                            'description' => sprintf(
                                'Impor bulk mengatur akses SSO untuk %s (%s): nonaktif → %s.',
                                $user->name,
                                $user->email,
                                $ssoEnabled ? 'aktif' : 'nonaktif',
                            ),
                            'created_at' => now(),
                        ]);

                        if (in_array('mahasiswa', $inputRoles)) {
                            Student::updateOrCreate(
                                ['user_id' => $user->id],
                                [
                                    'student_number' => trim($row[5]),
                                    'cohort_year'    => !empty($row[6]) ? trim($row[6]) : date('Y'),
                                ]
                            );
                        }

                        if (in_array('dosen', $inputRoles) || in_array('gpm', $inputRoles)) {
                            Lecturer::updateOrCreate(
                                ['user_id' => $user->id],
                                ['employee_number' => trim($row[4])]
                            );
                        }

                        $user->clearUserCache();
                        Log::info("=== ROW PROCESSING COMPLETE ===", ['user_id' => $user->id]);
                    });

                    $processedCount++;
                    Log::info("Row processed successfully", [
                        'processed_count' => $processedCount,
                        'email'           => strtolower(trim($row[1])),
                    ]);

                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to import row", [
                        'error'    => $e->getMessage(),
                        'row_index' => $rowIndex,
                        'email' => $row[1] ?? null,
                    ]);
                    continue;
                }

                // Update progress setiap 5 row atau row terakhir
                if ($processedCount % 5 === 0 || $processedCount === $importStatus->total_rows) {
                    $importStatus->update([
                        'processed_rows' => $processedCount,
                        'error_message'  => $failedCount > 0 ? "{$failedCount} baris gagal diimport" : null,
                    ]);
                }
            }

            fclose($tempFile);

            if ($processedCount === 0) {
                $importStatus->update([
                    'status'        => 'failed',
                    'error_message' => 'Tidak ada data valid yang ditemukan dalam file',
                ]);
            } else {
                $this->bustCache();

                $importStatus->update([
                    'status'         => 'completed',
                    'processed_rows' => $processedCount,
                    'error_message'  => $failedCount > 0 ? "Selesai dengan {$failedCount} baris gagal" : null,
                ]);

                Log::info("Bulk Import completed", [
                    'import_id' => $this->importStatusId,
                    'processed' => $processedCount,
                    'failed'    => $failedCount,
                ]);
            }

        } catch (\Exception $e) {
            fclose($tempFile);

            $importStatus->refresh();
            if ($importStatus->status !== 'failed') {
                $importStatus->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            Log::error("Bulk Import Error", [
                'import_id' => $this->importStatusId,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    // ── Bust stats cache ─────────────────────────────────────────
    private function bustCache(): void
    {
        Cache::put('sa:users:version', (int) Cache::get('sa:users:version', 0) + 1, now()->addDays(30));
        foreach ([
            'sa:total_users', 'sa:total_superadmins', 'sa:total_lecturers',
            'sa:total_students', 'sa:total_gpm', 'sa:total_admin_modul',
            'sa:new_registrations', 'sa:recent_users', 'sa:recent_logins',
        ] as $key) {
            Cache::forget($key);
        }
        try {
            Cache::tags(['sa_users'])->flush();
        } catch (\BadMethodCallException $e) {
            // Driver tidak support tags
        }
    }

    // ── Download file dari Supabase ───────────────────────────────
    private function getFileFromSupabase($storage)
    {
        try {
            $url = $storage->signedUrl($this->filePath, 300, $this->bucket);
            if (!$url) {
                Log::error("Failed to get signed URL", [
                    'file_path' => $this->filePath,
                    'bucket'    => $this->bucket,
                ]);
                return null;
            }

            $content = file_get_contents($url);
            if ($content === false) {
                Log::error("Failed to download file", ['url' => $url]);
                return null;
            }

            Log::info("File downloaded successfully", [
                'file_path' => $this->filePath,
                'size'      => strlen($content),
            ]);

            return $content;

        } catch (\Exception $e) {
            Log::error("Exception downloading file", ['error' => $e->getMessage()]);
            return null;
        }
    }
}
