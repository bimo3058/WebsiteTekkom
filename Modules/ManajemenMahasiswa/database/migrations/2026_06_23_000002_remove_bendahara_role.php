<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hapus role `bendahara` (modul Manajemen Mahasiswa) yang sudah tidak dipakai.
 * Selain menghapus baris role-nya, migration ini juga melepas assignment role
 * tersebut dari user (pivot Spatie default: model_has_roles) dan
 * role_has_permissions agar tidak ada baris yatim.
 */
return new class extends Migration
{
    public function up(): void
    {
        $roleIds = DB::table('roles')
            ->where('name', 'bendahara')
            ->where('module', 'manajemen_mahasiswa')
            ->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        // Lepas assignment ke user
        if (Schema::hasTable('model_has_roles')) {
            DB::table('model_has_roles')->whereIn('role_id', $roleIds)->delete();
        }

        // Lepas permission yang menempel pada role ini
        if (Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')->whereIn('role_id', $roleIds)->delete();
        }

        // Hapus role-nya
        DB::table('roles')->whereIn('id', $roleIds)->delete();
    }

    public function down(): void
    {
        // Sengaja no-op: role `bendahara` sudah tidak digunakan
        // dan tidak perlu dihidupkan kembali.
    }
};
