<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\ImportStatus;
use App\Services\SupabaseStorage;
use App\Services\PermissionAssigner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Permission;

class ProcessBulkImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $bucket;
    protected $importStatusId;

    public function __construct($filePath, $bucket = 'data_user', $importStatusId)
    {
        $this->filePath = $filePath;
        $this->bucket = $bucket;
        $this->importStatusId = $importStatusId;
    }

    public function handle()
    {
        $importStatus = ImportStatus::find($this->importStatusId);
        if (!$importStatus) {
            Log::error("Import status not found", ['id' => $this->importStatusId]);
            return;
        }

        $importStatus->update(['status' => 'processing']);
        Log::info("Starting bulk import", ['import_id' => $this->importStatusId]);

        $storage = new SupabaseStorage();
        $fileContent = $this->getFileFromSupabase($storage);
        
        if (!$fileContent) {
            $importStatus->update([
                'status' => 'failed',
                'error_message' => 'Gagal mendownload file dari Supabase'
            ]);
            return;
        }

        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        fseek($tempFile, 0);

        $header = fgetcsv($tempFile); 
        
        if (!$header || count($header) < 8) {
            fclose($tempFile);
            $importStatus->update([
                'status' => 'failed',
                'error_message' => 'Struktur CSV tidak valid (minimal 8 kolom: name, email, password, role, external_id, student_number, cohort_year, permissions)'
            ]);
            $storage->delete($this->filePath, $this->bucket);
            return;
        }

        Log::info("CSV Header", ['header' => $header]);

        $processedCount = 0;
        $failedCount = 0;

        try {
            while (($row = fgetcsv($tempFile)) !== FALSE) {
                if ($processedCount % 5 == 0) {
                    $importStatus->refresh();
                    if ($importStatus->status === 'failed') {
                        fclose($tempFile);
                        Log::info("Import cancelled by user", ['import_id' => $this->importStatusId]);
                        return;
                    }
                }

                if (empty($row) || count($row) < 8 || empty($row[1])) {
                    $failedCount++;
                    Log::warning("Invalid row", ['row' => $row, 'row_count' => count($row)]);
                    continue;
                }

                if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                    $failedCount++;
                    Log::warning("Invalid email", ['email' => $row[1]]);
                    continue;
                }

                try {
                    DB::transaction(function () use ($row, &$processedCount) {
                        $email = strtolower(trim($row[1]));
                        $inputRoles = array_map('trim', explode(',', strtolower(trim($row[3]))));
                        
                        // Ambil permissions dari CSV (kolom ke-8, index 7)
                        $csvPermissionsRaw = !empty($row[7]) ? trim($row[7]) : '';
                        $csvPermissions = !empty($csvPermissionsRaw) ? array_map('trim', explode(',', $csvPermissionsRaw)) : [];
                        
                        Log::info("=== PROCESSING ROW ===", [
                            'email' => $email,
                            'roles' => $inputRoles,
                            'csv_permissions_raw' => $csvPermissionsRaw,
                            'csv_permissions_count' => count($csvPermissions),
                            'name' => trim($row[0])
                        ]);
                        
                        // Get role IDs
                        $roleIds = Role::whereIn('name', $inputRoles)->pluck('id');
                        
                        if ($roleIds->isEmpty()) {
                            throw new \Exception("Role '{$row[3]}' tidak ditemukan. Available roles: " . implode(', ', Role::pluck('name')->toArray()));
                        }

                        // Create or update user
                        $user = User::updateOrCreate(
                            ['email' => $email],
                            [
                                'name' => trim($row[0]),
                                'password' => Hash::make($row[2]),
                                'external_id' => trim($row[4]),
                            ]
                        );

                        Log::info("User created/found", [
                            'user_id' => $user->id,
                            'email' => $email,
                            'is_new' => $user->wasRecentlyCreated
                        ]);

                        // Sync roles
                        $user->roles()->sync($roleIds);
                        
                        Log::info("Roles synced", [
                            'user_id' => $user->id,
                            'role_ids' => $roleIds->toArray(),
                            'role_names' => $inputRoles
                        ]);

                        // Handle student role
                        if (in_array('mahasiswa', $inputRoles)) {
                            Student::updateOrCreate(
                                ['user_id' => $user->id],
                                [
                                    'student_number' => trim($row[5]), 
                                    'cohort_year' => !empty($row[6]) ? trim($row[6]) : date('Y')
                                ]
                            );
                            Log::info("Student record created/updated", ['user_id' => $user->id]);
                        }

                        // Handle lecturer/GPM role
                        if (in_array('dosen', $inputRoles) || in_array('gpm', $inputRoles)) {
                            Lecturer::updateOrCreate(
                                ['user_id' => $user->id], 
                                ['employee_number' => trim($row[4])]
                            );
                            Log::info("Lecturer/GPM record created/updated", ['user_id' => $user->id]);
                        }

                        // ✅ ASSIGN PERMISSIONS DENGAN DEBUG DETAIL
                        Log::info("=== PERMISSION ASSIGNMENT START ===", [
                            'user_id' => $user->id,
                            'has_csv_permissions' => !empty($csvPermissions),
                            'csv_permissions' => $csvPermissions
                        ]);
                        
                        if (!empty($csvPermissions)) {
                            Log::info("Processing CSV permissions", [
                                'user_id' => $user->id,
                                'permissions_raw' => $csvPermissions,
                                'count' => count($csvPermissions)
                            ]);
                            
                            $permissionIds = [];
                            foreach ($csvPermissions as $permName) {
                                $permName = trim($permName);
                                Log::debug("Looking for permission", ['name' => $permName]);
                                
                                $permission = Permission::where('name', $permName)->first();
                                if ($permission) {
                                    $permissionIds[] = $permission->id;
                                    Log::debug("Found permission", [
                                        'name' => $permName,
                                        'id' => $permission->id
                                    ]);
                                } else {
                                    Log::warning("Permission NOT found in database", [
                                        'name' => $permName,
                                        'user_id' => $user->id
                                    ]);
                                }
                            }
                            
                            Log::info("Collected permission IDs", [
                                'user_id' => $user->id,
                                'permission_ids' => $permissionIds,
                                'count' => count($permissionIds)
                            ]);
                            
                            if (!empty($permissionIds)) {
                                $syncResult = $user->directPermissions()->sync($permissionIds);
                                Log::info("Permissions assigned from CSV", [
                                    'user_id' => $user->id,
                                    'permissions' => $csvPermissions,
                                    'permission_ids' => $permissionIds,
                                    'sync_result' => $syncResult,
                                    'count' => count($permissionIds)
                                ]);
                                
                                // Verify after sync
                                $assigned = $user->directPermissions()->get();
                                Log::info("Verification after CSV assignment", [
                                    'user_id' => $user->id,
                                    'assigned_count' => $assigned->count(),
                                    'assigned_permissions' => $assigned->pluck('name')->toArray()
                                ]);
                            } else {
                                Log::warning("No valid permission IDs found from CSV", [
                                    'user_id' => $user->id,
                                    'csv_permissions' => $csvPermissions
                                ]);
                            }
                        } else {
                            Log::info("No CSV permissions, skipping assignment", [
                                'user_id' => $user->id,
                                'roles' => $inputRoles
                            ]);
                        }
                        
                        $user->clearUserCache();
                        Log::info("=== ROW PROCESSING COMPLETE ===", ['user_id' => $user->id]);
                    });
                    
                    $processedCount++;
                    Log::info("Row processed successfully", [
                        'processed_count' => $processedCount,
                        'email' => strtolower(trim($row[1]))
                    ]);
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to import row", [
                        'error' => $e->getMessage(),
                        'row_data' => $row,
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }

                if ($processedCount % 5 == 0 || $processedCount == $importStatus->total_rows) {
                    $importStatus->update([
                        'processed_rows' => $processedCount,
                        'error_message' => $failedCount > 0 ? "{$failedCount} baris gagal diimport" : null
                    ]);
                }
            }

            fclose($tempFile);

            if ($processedCount === 0) {
                $importStatus->update([
                    'status' => 'failed',
                    'error_message' => 'Tidak ada data valid yang ditemukan dalam file'
                ]);
            } else {
                $importStatus->update([
                    'status' => 'completed',
                    'processed_rows' => $processedCount,
                    'error_message' => $failedCount > 0 ? "Selesai dengan {$failedCount} baris gagal" : null
                ]);
                Log::info("Bulk Import completed", [
                    'import_id' => $this->importStatusId,
                    'processed' => $processedCount,
                    'failed' => $failedCount
                ]);
            }

        } catch (\Exception $e) {
            fclose($tempFile);
            
            $importStatus->refresh();

            if ($importStatus->status !== 'failed') {
                $importStatus->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }

            try {
                $storage->delete($this->filePath, $this->bucket);
            } catch (\Exception $deleteError) {
                Log::error("Failed to delete file from storage", [
                    'error' => $deleteError->getMessage()
                ]);
            }

            Log::error("Bulk Import Error", [
                'import_id' => $this->importStatusId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    private function getFileFromSupabase($storage)
    {
        try {
            $url = $storage->signedUrl($this->filePath, 300, $this->bucket);
            if (!$url) {
                Log::error("Failed to get signed URL", [
                    'file_path' => $this->filePath,
                    'bucket' => $this->bucket
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
                'size' => strlen($content)
            ]);
            
            return $content;
        } catch (\Exception $e) {
            Log::error("Exception downloading file", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}