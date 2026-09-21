<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            ['name' => 'dpm',               'module' => 'manajemen_mahasiswa', 'is_academic' => true],
            ['name' => 'ketua_departemen',   'module' => 'manajemen_mahasiswa', 'is_academic' => false],
        ];

        foreach ($roles as $role) {
            $exists = DB::table('roles')
                ->where('name', $role['name'])
                ->where('module', $role['module'])
                ->exists();

            if (!$exists) {
                DB::table('roles')->insert(array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['dpm', 'ketua_departemen'])->delete();
    }
};
