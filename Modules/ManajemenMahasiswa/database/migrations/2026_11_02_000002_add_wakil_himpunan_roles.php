<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menambah role wakil himpunan: WAKAHIM (dibuat ulang setelah dihapus di
 * 2026_06_23_000001), WAKABID, dan WAKANIT. Hak aksesnya disamakan dengan
 * ketuanya masing-masing lewat Support/RoleHimpunan.
 */
return new class extends Migration
{
    private const ROLES = [
        'wakil_ketua_himpunan',
        'wakil_ketua_bidang',
        'wakil_ketua_unit',
    ];

    public function up(): void
    {
        foreach (self::ROLES as $name) {
            $exists = DB::table('roles')
                ->where('name', $name)
                ->where('module', 'manajemen_mahasiswa')
                ->exists();

            if (!$exists) {
                DB::table('roles')->insert([
                    'name'        => $name,
                    'module'      => 'manajemen_mahasiswa',
                    'is_academic' => false,
                    'guard_name'  => 'web',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $roleIds = DB::table('roles')
            ->whereIn('name', self::ROLES)
            ->where('module', 'manajemen_mahasiswa')
            ->pluck('id');

        DB::table('model_has_roles')->whereIn('role_id', $roleIds)->delete();
        DB::table('role_has_permissions')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('id', $roleIds)->delete();
    }
};
