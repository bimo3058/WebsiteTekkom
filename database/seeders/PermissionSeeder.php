<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'banksoal.view',        'display_name' => 'Lihat Bank Soal',    'module' => 'banksoal'],
            ['name' => 'banksoal.edit',        'display_name' => 'Edit Bank Soal',     'module' => 'banksoal'],
            ['name' => 'banksoal.delete',      'display_name' => 'Hapus Bank Soal',    'module' => 'banksoal'],
            ['name' => 'kemahasiswaan.view',   'display_name' => 'Lihat Kemahasiswaan', 'module' => 'kemahasiswaan'],
            ['name' => 'kemahasiswaan.edit',   'display_name' => 'Edit Kemahasiswaan',  'module' => 'kemahasiswaan'],
            ['name' => 'kemahasiswaan.delete', 'display_name' => 'Hapus Kemahasiswaan', 'module' => 'kemahasiswaan'],
            ['name' => 'capstone.view',        'display_name' => 'Lihat Capstone',     'module' => 'capstone'],
            ['name' => 'capstone.edit',        'display_name' => 'Edit Capstone',      'module' => 'capstone'],
            ['name' => 'capstone.delete',      'display_name' => 'Hapus Capstone',     'module' => 'capstone'],
            ['name' => 'eoffice.view',         'display_name' => 'Lihat E-Office',     'module' => 'eoffice'],
            ['name' => 'eoffice.edit',         'display_name' => 'Edit E-Office',      'module' => 'eoffice'],
            ['name' => 'eoffice.delete',       'display_name' => 'Hapus E-Office',     'module' => 'eoffice'],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => 'web'],
                ['display_name' => $data['display_name'], 'module' => $data['module']]
            );
        }

        // Assign semua permission ke superadmin dan admin
        $allPermissions = Permission::all();
        foreach (['superadmin', 'admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($allPermissions);
            }
        }

        // Assign view+edit ke admin per modul
        $moduleRoles = [
            'admin_banksoal'      => 'banksoal',
            'admin_capstone'      => 'capstone',
            'admin_eoffice'       => 'eoffice',
            'admin_kemahasiswaan' => 'kemahasiswaan',
        ];

        foreach ($moduleRoles as $roleName => $module) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) continue;

            $perms = Permission::where('module', $module)
                ->whereIn('name', ["{$module}.view", "{$module}.edit"])
                ->get();

            $role->syncPermissions($perms);
        }
    }
}
