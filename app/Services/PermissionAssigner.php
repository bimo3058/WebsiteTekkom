<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionAssigner
{
    private const ROLE_MODULE_MAPPING = [
        'admin_banksoal'        => ['banksoal'],
        'admin_capstone'        => ['capstone'],
        'admin_eoffice'         => ['eoffice'],
        'admin_kemahasiswaan'   => ['kemahasiswaan'],
        'dosen'                 => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'mahasiswa'             => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'gpm'                   => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'pengurus_himpunan'     => ['kemahasiswaan'],
        'alumni'                => ['kemahasiswaan'],
        'superadmin'            => ['all'],
    ];

    private const ACTIONS = ['view', 'edit', 'delete'];

    public static function assignByRoles(User $user, array $roleNames): void
    {
        try {
            DB::beginTransaction();

            Log::info('Assigning permissions via Spatie', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'roles'   => $roleNames,
            ]);

            $permissionIds = [];

            foreach ($roleNames as $roleName) {
                $modules = self::ROLE_MODULE_MAPPING[$roleName] ?? [];

                if (empty($modules)) {
                    Log::warning('No module mapping for role', ['role' => $roleName]);
                    continue;
                }

                if (in_array('all', $modules)) {
                    $permissionIds = Permission::all()->pluck('id')->toArray();
                    break;
                }

                foreach ($modules as $module) {
                    foreach (self::ACTIONS as $action) {
                        $id = DB::table('permissions')
                            ->where('name', "{$module}.{$action}")
                            ->value('id');

                        if ($id) {
                            $permissionIds[] = $id;
                        }
                    }
                }
            }

            $permissionIds = array_unique($permissionIds);

            // Spatie direct permissions via model_has_permissions table
            $user->permissions()->sync($permissionIds);

            $user->clearUserCache();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign permissions', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public static function verifyPermissions(User $user): array
    {
        $roleNames = $user->roles()->pluck('name')->toArray();
        $modules   = [];

        foreach ($roleNames as $roleName) {
            $mapped = self::ROLE_MODULE_MAPPING[$roleName] ?? [];
            if (in_array('all', $mapped)) {
                $modules = ['all'];
                break;
            }
            $modules = array_merge($modules, $mapped);
        }

        if (in_array('all', $modules)) {
            $expected = Permission::all()->pluck('name')->toArray();
        } else {
            $expected = [];
            foreach (array_unique($modules) as $module) {
                foreach (self::ACTIONS as $action) {
                    $expected[] = "{$module}.{$action}";
                }
            }
        }

        $current = $user->permissions()->pluck('name')->toArray();
        $missing = array_diff($expected, $current);
        $excess  = array_diff($current, $expected);

        return [
            'has_correct_permissions' => empty($missing) && empty($excess),
            'missing'                 => array_values($missing),
            'excess'                  => array_values($excess),
        ];
    }

    public static function repairPermissions(User $user): void
    {
        $roleNames = $user->roles()->pluck('name')->toArray();
        self::assignByRoles($user, $roleNames);
    }
}
