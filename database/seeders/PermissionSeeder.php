<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                ...$permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign semua permission ke superadmin dan admin
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

        // Assign hanya view ke admin_readonly kalau ada
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
    }
}