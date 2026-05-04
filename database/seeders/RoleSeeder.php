<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
<<<<<<< HEAD
            ['nama' => 'admin',     'deskripsi' => 'Super admin — akses penuh ke semua fitur sistem.'],
            ['nama' => 'dosen',     'deskripsi' => 'Dosen pengampu — kelola praktikum, modul, dan nilai.'],
            ['nama' => 'koor_prak', 'deskripsi' => 'Koordinator praktikum — manage asprak dan modul.'],
            ['nama' => 'asprak',    'deskripsi' => 'Asisten praktikum — upload materi, nilai praktikan.'],
            ['nama' => 'mahasiswa', 'deskripsi' => 'Mahasiswa — lihat modul, kumpul tugas, lihat nilai.'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama' => $role['nama']], $role);
        }

        $this->command->info('✅ Role seeder: 5 role berhasil dibuat.');
    }
}
=======
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
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
