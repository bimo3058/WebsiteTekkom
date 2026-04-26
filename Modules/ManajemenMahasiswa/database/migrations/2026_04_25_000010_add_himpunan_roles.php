<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = [
            ['name' => 'ketua_himpunan',       'module' => 'manajemen_mahasiswa', 'is_academic' => false],
            ['name' => 'wakil_ketua_himpunan',  'module' => 'manajemen_mahasiswa', 'is_academic' => false],
            ['name' => 'ketua_bidang',          'module' => 'manajemen_mahasiswa', 'is_academic' => false],
            ['name' => 'ketua_unit',            'module' => 'manajemen_mahasiswa', 'is_academic' => false],
            ['name' => 'staff_himpunan',        'module' => 'manajemen_mahasiswa', 'is_academic' => false],
            ['name' => 'alumni',                'module' => 'global',              'is_academic' => false],
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
        DB::table('roles')->whereIn('name', [
            'ketua_himpunan',
            'wakil_ketua_himpunan',
            'ketua_bidang',
            'ketua_unit',
            'staff_himpunan',
            'alumni',
        ])->delete();
    }
};
