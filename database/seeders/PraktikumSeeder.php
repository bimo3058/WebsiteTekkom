<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Praktikum;
use App\Models\SystemRole;
use Illuminate\Database\Seeder;

class PraktikumSeeder extends Seeder
{
    public function run(): void
    {
        $dosen1 = Pengguna::where('nim_nip', 'NIP001')->first(); // Dr. Ahmad Fauzi
        $dosen2 = Pengguna::where('nim_nip', 'NIP002')->first(); // Dr. Siti Rahayu

        $roleKoor   = SystemRole::firstOrCreate(['nama' => 'koor_prak']);
        $roleAsprak = SystemRole::firstOrCreate(['nama' => 'asprak']);
        $roleMhs    = SystemRole::firstOrCreate(['nama' => 'mahasiswa']);

        // ── Praktikum Data ────────────────────────────────────────────────────
        $p1 = Praktikum::firstOrCreate(
            ['nama' => 'Praktikum Pemrograman Dasar'],
            [
                'deskripsi'    => 'Praktikum dasar pemrograman menggunakan Python',
                'dosen_id'     => $dosen1->id,
                'tahun_ajaran' => 2026,
                'semester'     => 'ganjil',
                'status'       => 'aktif',
            ]
        );

        $p2 = Praktikum::firstOrCreate(
            ['nama' => 'Praktikum Basis Data'],
            [
                'deskripsi'    => 'Praktikum SQL dan database relasional',
                'dosen_id'     => $dosen1->id,
                'tahun_ajaran' => 2026,
                'semester'     => 'ganjil',
                'status'       => 'aktif',
            ]
        );

        $p3 = Praktikum::firstOrCreate(
            ['nama' => 'Praktikum Jaringan Komputer'],
            [
                'deskripsi'    => 'Praktikum konfigurasi jaringan dan routing',
                'dosen_id'     => $dosen2->id,
                'tahun_ajaran' => 2026,
                'semester'     => 'genap',
                'status'       => 'aktif',
            ]
        );

        $p4 = Praktikum::firstOrCreate(
            ['nama' => 'Praktikum Sistem Operasi'],
            [
                'deskripsi'    => 'Praktikum Linux dan manajemen proses',
                'dosen_id'     => $dosen2->id,
                'tahun_ajaran' => 2025,
                'semester'     => 'genap',
                'status'       => 'nonaktif',
            ]
        );

        // ── Assign Koor (Andi → koor di Praktikum 1) ─────────────────────────
        $andi = Pengguna::where('nim_nip', '2021001')->first();
        $p1->update(['koor_id' => $andi->id]);

        // Assign koor_prak + asprak + mahasiswa roles ke Andi
        $andi->roles()->syncWithoutDetaching([
            $roleKoor->id   => ['status' => 'aktif', 'dibuat_pada' => now()],
            $roleAsprak->id => ['status' => 'aktif', 'dibuat_pada' => now()],
        ]);

        // ── Assign Asprak (Budi → asprak di Praktikum 1) ─────────────────────
        $budi = Pengguna::where('nim_nip', '2021002')->first();
        $budi->roles()->syncWithoutDetaching([
            $roleAsprak->id => ['status' => 'aktif', 'dibuat_pada' => now()],
        ]);

        $this->command->info('✅ Praktikum seeder: 4 praktikum dibuat.');
        $this->command->info('   Koor: Andi Ahmad (2021001) → Praktikum Pemrograman Dasar');
        $this->command->info('   Asprak: Budi Santoso (2021002) → role asprak assigned');
    }
}
