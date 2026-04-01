<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionAssigner
{
    private const ROLE_MODULE_MAPPING = [
        'admin_banksoal' => ['banksoal'],
        'admin_capstone' => ['capstone'],
        'admin_eoffice' => ['eoffice'],
        'admin_kemahasiswaan' => ['kemahasiswaan'],
        'dosen' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'mahasiswa' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'gpm' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'superadmin' => ['all'],
    ];

    private const ACTIONS = ['view', 'edit', 'delete'];

    public static function assignByRoles(User $user, array $roleNames): void
    {
        try {
            DB::beginTransaction();

            Log::info("Assigning permissions", [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $roleNames,
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
            ]);

            $permissionIds = [];

            foreach ($roleNames as $roleName) {
                $modules = self::ROLE_MODULE_MAPPING[$roleName] ?? [];
                
                if (empty($modules)) {
                    Log::warning("No modules found for role", ['role' => $roleName]);
                    continue;
                }

                if (in_array('all', $modules)) {
                    // Superadmin: ambil semua permission
                    $permissionIds = Permission::all()->pluck('id')->toArray();
                    Log::info("Superadmin: assigning all permissions", [
                        'count' => count($permissionIds)
                    ]);
                    break;
                }

                // Di dalam assignByRoles, setelah tentukan modules
                foreach ($modules as $module) {
                    foreach (self::ACTIONS as $action) {
                        $permissionName = "{$module}.{$action}";
                        
                        // Query langsung tanpa caching
                        $permission = DB::table('permissions')
                            ->where('name', $permissionName)
                            ->first();
                            
                        Log::info("Checking permission", [
                            'name' => $permissionName,
                            'found' => $permission ? true : false,
                            'id' => $permission->id ?? null
                        ]);
                        
                        if ($permission) {
                            $permissionIds[] = $permission->id;
                        }
                    }
                }
            }

            // Hapus duplikat
            $permissionIds = array_unique($permissionIds);
            
            Log::info("Final permission IDs to assign", [
                'count' => count($permissionIds),
                'ids' => $permissionIds
            ]);

            if (!empty($permissionIds)) {
                // Sync ke user_permissions
                $user->directPermissions()->sync($permissionIds);
                
                // Verifikasi
                $assignedCount = DB::table('user_permissions')
                    ->where('user_id', $user->id)
                    ->count();
                    
                Log::info("Permissions synced", [
                    'user_id' => $user->id,
                    'expected' => count($permissionIds),
                    'actual' => $assignedCount
                ]);
            } else {
                Log::warning("No permissions to assign", [
                    'user_id' => $user->id,
                    'roles' => $roleNames
                ]);
            }

            $user->clearUserCache();
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to assign permissions", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}