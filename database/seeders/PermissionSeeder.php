<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisi Data Permissions
        $permissions = [
            // Bank Soal
            ['name' => 'banksoal.view',   'display_name' => 'Lihat Bank Soal',   'module' => 'banksoal'],
            ['name' => 'banksoal.edit',   'display_name' => 'Edit Bank Soal',    'module' => 'banksoal'],
            ['name' => 'banksoal.delete', 'display_name' => 'Hapus Bank Soal',   'module' => 'banksoal'],

            // Kemahasiswaan
            ['name' => 'kemahasiswaan.view',   'display_name' => 'Lihat Kemahasiswaan',  'module' => 'kemahasiswaan'],
            ['name' => 'kemahasiswaan.edit',   'display_name' => 'Edit Kemahasiswaan',   'module' => 'kemahasiswaan'],
            ['name' => 'kemahasiswaan.delete', 'display_name' => 'Hapus Kemahasiswaan',  'module' => 'kemahasiswaan'],

            // Capstone
            ['name' => 'capstone.view',   'display_name' => 'Lihat Capstone',  'module' => 'capstone'],
            ['name' => 'capstone.edit',   'display_name' => 'Edit Capstone',   'module' => 'capstone'],
            ['name' => 'capstone.delete', 'display_name' => 'Hapus Capstone',  'module' => 'capstone'],

            // E-Office
            ['name' => 'eoffice.view',   'display_name' => 'Lihat E-Office',  'module' => 'eoffice'],
            ['name' => 'eoffice.edit',   'display_name' => 'Edit E-Office',   'module' => 'eoffice'],
            ['name' => 'eoffice.delete', 'display_name' => 'Hapus E-Office',  'module' => 'eoffice'],
        ];

        // 2. Insert Permissions
        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                ...$permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Assign SEMUA permission ke superadmin & admin
        $allPermissionIds = DB::table('permissions')->pluck('id');
        $adminRoles = DB::table('roles')
            ->whereIn('name', ['superadmin', 'admin'])
            ->pluck('id');

        foreach ($adminRoles as $roleId) {
            foreach ($allPermissionIds as $permissionId) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // 4. Assign hanya VIEW ke admin_readonly
        $readonlyRole = DB::table('roles')->where('name', 'admin_readonly')->first();
        if ($readonlyRole) {
            $viewPermissions = DB::table('permissions')
                ->where('name', 'like', '%.view')
                ->pluck('id');

            foreach ($viewPermissions as $permissionId) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $readonlyRole->id,
                    'permission_id' => $permissionId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // 5. IMPLEMENTASI BARU: Role per Modul (View + Edit)
        $moduleRoles = [
            ['name' => 'admin_banksoal',      'module' => 'banksoal'],
            ['name' => 'admin_capstone',      'module' => 'capstone'],
            ['name' => 'admin_eoffice',       'module' => 'eoffice'],
            ['name' => 'admin_kemahasiswaan', 'module' => 'kemahasiswaan'],
        ];

        foreach ($moduleRoles as $mRole) {
            // Gunakan updateOrInsert agar tidak duplikat jika seeder dijalankan ulang
            DB::table('roles')->updateOrInsert(
                ['name' => $mRole['name']],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $roleId = DB::table('roles')->where('name', $mRole['name'])->value('id');
            $module = $mRole['module'];

            // Ambil ID permission view & edit untuk modul terkait
            $permissionIds = DB::table('permissions')
                ->where('module', $module)
                ->whereIn('name', ["$module.view", "$module.edit"])
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $pId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}