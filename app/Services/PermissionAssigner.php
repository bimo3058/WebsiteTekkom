<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionAssigner
{
    private const ROLE_MODULE_MAPPING = [
        'admin_banksoal' => ['banksoal'],
        'admin_capstone' => ['capstone'],
        'admin_eoffice' => ['eoffice'],
        'admin_praktikum' => ['eoffice'],
        'koordinator_cerc' => ['eoffice'],
        'admin_kemahasiswaan' => ['kemahasiswaan'],
        'dosen' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'mahasiswa' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'gpm' => ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'],
        'pengurus_himpunan' => ['kemahasiswaan'],
        'alumni' => ['kemahasiswaan'],
        'superadmin' => ['all'],
        'admin' => ['all'],
    ];

    private const ACTIONS = ['view', 'edit', 'delete'];

    public static function roleModuleMapping(): array
    {
        return self::ROLE_MODULE_MAPPING;
    }

    /** A module administrator's scope takes priority over academic roles; null means unrestricted. */
    public static function restrictedModules(array $roleNames): ?array
    {
        if (array_intersect($roleNames, ['superadmin', 'admin'])) {
            return null;
        }

        $modules = [];
        foreach ($roleNames as $roleName) {
            if (str_starts_with($roleName, 'admin_')) {
                $modules = array_merge($modules, self::ROLE_MODULE_MAPPING[$roleName] ?? []);
            }
        }

        return array_filter($roleNames, fn ($name) => str_starts_with($name, 'admin_'))
            ? array_values(array_unique($modules))
            : null;
    }

    public static function filterPermissionNames(array $roleNames, array $permissionNames): array
    {
        $modules = self::restrictedModules($roleNames);

        return array_values(array_unique(array_filter(
            $permissionNames,
            fn ($name) => is_string($name) && ($modules === null || in_array(explode('.', $name)[0], $modules, true))
        )));
    }

    public static function defaultPermissionNames(array $roleNames): array
    {
        $modules = [];
        foreach ($roleNames as $roleName) {
            $modules = array_merge($modules, self::ROLE_MODULE_MAPPING[$roleName] ?? []);
        }

        $names = in_array('all', $modules, true)
            ? Permission::query()->pluck('name')->all()
            : array_merge([], ...array_map(
                fn ($module) => array_map(fn ($action) => "{$module}.{$action}", self::ACTIONS),
                array_unique($modules)
            ));

        return self::filterPermissionNames($roleNames, $names);
    }

    /** Synchronize the full selection so stale permissions are also revoked. */
    public static function syncSelection(User $user, array $roleNames, array $permissionNames): array
    {
        $permissions = Permission::query()
            ->whereIn('name', self::filterPermissionNames($roleNames, $permissionNames))
            ->get();

        $user->syncPermissions($permissions);
        $user->unsetRelation('roles');
        $user->clearUserCache();

        return $permissions->pluck('name')->all();
    }

    public static function assignByRoles(User $user, array $roleNames): void
    {
        try {
            DB::beginTransaction();

            Log::info('Assigning permissions via Spatie', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $roleNames,
            ]);

            self::syncSelection($user, $roleNames, self::defaultPermissionNames($roleNames));
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign permissions', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public static function verifyPermissions(User $user): array
    {
        $roleNames = $user->roles()->pluck('name')->toArray();
        $expected = self::defaultPermissionNames($roleNames);

        $current = $user->permissions()->pluck('name')->toArray();
        $missing = array_diff($expected, $current);
        $excess = array_diff($current, $expected);

        return [
            'has_correct_permissions' => empty($missing) && empty($excess),
            'missing' => array_values($missing),
            'excess' => array_values($excess),
        ];
    }

    public static function repairPermissions(User $user): void
    {
        $roleNames = $user->roles()->pluck('name')->toArray();
        self::assignByRoles($user, $roleNames);
    }
}
