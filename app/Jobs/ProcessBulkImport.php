<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\ImportStatus;
use App\Services\SupabaseStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProcessBulkImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $bucket;
    protected $importStatusId;

    /**
     * @param string $filePath Path file di Supabase
     * @param string $bucket Nama bucket
     * @param int $importStatusId ID dari tabel import_statuses
     */
    public function __construct($filePath, $bucket = 'data_user', $importStatusId)
    {
        $this->filePath = $filePath;
        $this->bucket = $bucket;
        $this->importStatusId = $importStatusId;
    }

    public function handle()
    {
        // 1. Inisialisasi Status
        $importStatus = ImportStatus::find($this->importStatusId);
        if (!$importStatus) return;

        $importStatus->update(['status' => 'processing']);

        $storage = new SupabaseStorage();
        $fileContent = $this->getFileFromSupabase($storage);
        
        if (!$fileContent) {
            $importStatus->update(['status' => 'failed']);
            return;
        }

        // 2. Persiapan File CSV
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        fseek($tempFile, 0);

        fgetcsv($tempFile); // Lewati Header

        $processedCount = 0;

        try {
            while (($row = fgetcsv($tempFile)) !== FALSE) {
                // --- FITUR PEMBATALAN (CANCEL) ---
                // Cek status dari database setiap 5 baris untuk melihat apakah user menekan 'Cancel'
                if ($processedCount % 5 == 0) {
                    $importStatus->refresh();
                    if ($importStatus->status === 'failed') {
                        Log::warning("Bulk Import dibatalkan oleh user. ID Status: {$this->importStatusId}");
                        return; // Berhenti memproses sisa baris
                    }
                }

                if (empty($row[1])) continue; // Lewati jika kolom email kosong

                // 3. Eksekusi Database per Baris
                DB::transaction(function () use ($row) {
                    // Create/Update User utama
                    $user = User::updateOrCreate(
                        ['email' => $row[1]],
                        [
                            'name'        => $row[0],
                            'password'    => Hash::make($row[2]),
                            'external_id' => $row[4],
                        ]
                    );

                    // Sinkronisasi Role (Multiple Roles didukung via koma)
                    $inputRoles = explode(',', strtolower($row[3]));
                    $roleIds = Role::whereIn('name', $inputRoles)->pluck('id');
                    $user->roles()->syncWithoutDetaching($roleIds);

                    // Detail spesifik: Mahasiswa
                    if (in_array('mahasiswa', $inputRoles)) {
                        Student::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'student_number' => $row[5],
                                'cohort_year'    => $row[6] ?? date('Y'),
                            ]
                        );
                    }

                    // Detail spesifik: Dosen / GPM
                    if (in_array('dosen', $inputRoles) || in_array('gpm', $inputRoles)) {
                        Lecturer::updateOrCreate(
                            ['user_id' => $user->id],
                            ['employee_number' => $row[4]] 
                        );
                    }

                    $user->clearUserCache();
                });

                $processedCount++;

                // 4. Update Progress Real-time
                // Update setiap 5 baris agar tidak membebani performa database PostgreSQL
                if ($processedCount % 5 == 0 || $processedCount == $importStatus->total_rows) {
                    $importStatus->update(['processed_rows' => $processedCount]);
                }
            }

            // 5. Tandai Selesai
            $importStatus->update(['status' => 'completed']);
            Log::info("Bulk Import Berhasil: " . $this->filePath);

        } catch (\Exception $e) {
            // Jangan timpa status jika sudah dibatalkan ('failed' oleh user)
            $importStatus->refresh();
            if ($importStatus->status !== 'failed') {
                $importStatus->update(['status' => 'failed']);
            }
            Log::error("Bulk Import Error: " . $e->getMessage());
        } finally {
            if (is_resource($tempFile)) {
                fclose($tempFile);
            }
        }
    }

    private function getFileFromSupabase($storage)
    {
        try {
            // Gunakan Signed URL untuk keamanan akses file private
            $url = $storage->signedUrl($this->filePath, 300, $this->bucket);
            return file_get_contents($url);
        } catch (\Exception $e) {
            Log::error("Gagal mendownload file dari Supabase: " . $e->getMessage());
            return null;
        }
    }
}