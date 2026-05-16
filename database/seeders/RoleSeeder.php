<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'superadmin',          'module' => 'global', 'is_academic' => false],
            ['name' => 'admin',               'module' => 'global', 'is_academic' => false],
            ['name' => 'dosen',               'module' => 'global', 'is_academic' => true],
            ['name' => 'mahasiswa',           'module' => 'global', 'is_academic' => true],
            ['name' => 'gpm',                 'module' => 'global', 'is_academic' => true],
            ['name' => 'admin_banksoal',      'module' => 'banksoal', 'is_academic' => false],
            ['name' => 'admin_capstone',      'module' => 'capstone', 'is_academic' => false],
            ['name' => 'admin_eoffice',       'module' => 'eoffice', 'is_academic' => false],
            ['name' => 'admin_kemahasiswaan', 'module' => 'kemahasiswaan', 'is_academic' => false],
            ['name' => 'pengurus_himpunan',   'module' => 'kemahasiswaan', 'is_academic' => false],
            ['name' => 'alumni',              'module' => 'global', 'is_academic' => false],
            ['name' => 'dosen_koor',          'module' => 'global', 'is_academic' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'guard_name' => 'web'],
                [
                    'module'      => $role['module'],
                    'is_academic' => $role['is_academic'],
                ]
            );
        }
    }
}
