<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Global roles
            [
                'name' => 'SUPERADMIN',
                'module' => 'global',
                // 'description' => 'Super Administrator - Full access to all modules',
            ],
            [
                'name' => 'DOSEN',
                'module' => 'global',
                // 'description' => 'Lecturer - Can teach and manage courses',
            ],
            [
                'name' => 'MAHASISWA',
                'module' => 'global',
                // 'description' => 'Student - Can view and submit assignments',
            ],

            // Module-specific admin roles
            [
                'name' => 'ADMIN_BANKSOAL',
                'module' => 'bank_soal',
                // 'description' => 'Admin for Bank Soal module',
            ],
            [
                'name' => 'ADMIN_CAPSTONE',
                'module' => 'capstone',
                // 'description' => 'Admin for Capstone module',
            ],
            [
                'name' => 'ADMIN_EOFFICE',
                'module' => 'eoffice',
                // 'description' => 'Admin for E-Office module',
            ],
            [
                'name' => 'ADMIN_MAHASISWA',
                'module' => 'manajemen_mahasiswa',
                // 'description' => 'Admin for Manajemen Mahasiswa module',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}