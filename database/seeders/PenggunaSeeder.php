<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\SystemRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin    = SystemRole::firstOrCreate(['nama' => 'admin']);
        $roleDosen    = SystemRole::firstOrCreate(['nama' => 'dosen']);
        $roleMhs      = SystemRole::firstOrCreate(['nama' => 'mahasiswa']);

        // ── 1 Admin ───────────────────────────────────────────────────────────
        $admin = Pengguna::firstOrCreate(
            ['email' => 'admin@praktikum.ac.id'],
            [
                'nama'          => 'Administrator',
                'password_hash' => Hash::make('password123'),
                'nim_nip'       => 'ADM001',
                'status'        => 'aktif',
            ]
        );
        $admin->roles()->syncWithoutDetaching([
            $roleAdmin->id => ['status' => 'aktif', 'dibuat_pada' => now()],
        ]);

        // ── 2 Dosen ───────────────────────────────────────────────────────────
        $dosenData = [
            ['nama' => 'Dr. Ahmad Fauzi, M.T.',   'email' => 'ahmad.fauzi@praktikum.ac.id',   'nim_nip' => 'NIP001'],
            ['nama' => 'Dr. Siti Rahayu, M.Kom.', 'email' => 'siti.rahayu@praktikum.ac.id',   'nim_nip' => 'NIP002'],
        ];

        foreach ($dosenData as $data) {
            $dosen = Pengguna::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nama'          => $data['nama'],
                    'password_hash' => Hash::make('password123'),
                    'nim_nip'       => $data['nim_nip'],
                    'status'        => 'aktif',
                ]
            );
            $dosen->roles()->syncWithoutDetaching([
                $roleDosen->id => ['status' => 'aktif', 'dibuat_pada' => now()],
            ]);
        }

        // ── 10 Mahasiswa ──────────────────────────────────────────────────────
        $mahasiswaData = [
            ['nama' => 'Andi Ahmad',       'email' => 'andi@mhs.ac.id',    'nim_nip' => '2021001'],
            ['nama' => 'Budi Santoso',     'email' => 'budi@mhs.ac.id',    'nim_nip' => '2021002'],
            ['nama' => 'Citra Dewi',       'email' => 'citra@mhs.ac.id',   'nim_nip' => '2021003'],
            ['nama' => 'Dian Permata',     'email' => 'dian@mhs.ac.id',    'nim_nip' => '2021004'],
            ['nama' => 'Eka Nugraha',      'email' => 'eka@mhs.ac.id',     'nim_nip' => '2021005'],
            ['nama' => 'Fajar Ramadhan',   'email' => 'fajar@mhs.ac.id',   'nim_nip' => '2021006'],
            ['nama' => 'Gita Sari',        'email' => 'gita@mhs.ac.id',    'nim_nip' => '2021007'],
            ['nama' => 'Hendra Wijaya',    'email' => 'hendra@mhs.ac.id',  'nim_nip' => '2021008'],
            ['nama' => 'Indah Lestari',    'email' => 'indah@mhs.ac.id',   'nim_nip' => '2021009'],
            ['nama' => 'Joko Prabowo',     'email' => 'joko@mhs.ac.id',    'nim_nip' => '2021010'],
        ];

        foreach ($mahasiswaData as $data) {
            $mhs = Pengguna::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nama'          => $data['nama'],
                    'password_hash' => Hash::make('password123'),
                    'nim_nip'       => $data['nim_nip'],
                    'status'        => 'aktif',
                ]
            );
            $mhs->roles()->syncWithoutDetaching([
                $roleMhs->id => ['status' => 'aktif', 'dibuat_pada' => now()],
            ]);
        }

        $this->command->info('✅ Pengguna seeder: 1 admin, 2 dosen, 10 mahasiswa berhasil dibuat.');
        $this->command->info('   Password default: password123');
    }
}
