<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'superadmin', 'module' => 'global'],
            ['name' => 'admin',      'module' => 'global'],
            ['name' => 'dosen',      'module' => 'global'],
            ['name' => 'mahasiswa',  'module' => 'global'],

            // Module-specific roles
            ['name' => 'gpm', 'module' => 'bank_soal'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name'], 'module' => $role['module']],
                $role
            );
        }
    }
}