<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
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
