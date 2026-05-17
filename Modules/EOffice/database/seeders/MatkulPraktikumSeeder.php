<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder mata kuliah praktikum Kurikulum 2024
 * S1 Teknik Komputer UNDIP
 */
class MatkulPraktikumSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = [
            // Semester 1
            ['kode' => 'TSK1624107', 'nama' => 'Praktikum Pemrograman Dasar',                          'sks' => 1, 'semester' => 1],
            // Semester 2
            ['kode' => 'TSK1624202', 'nama' => 'Pengenalan Jaringan Komputer + Praktikum',             'sks' => 2, 'semester' => 2],
            ['kode' => 'TSK1624207', 'nama' => 'Praktikum Fisika Dasar I',                             'sks' => 1, 'semester' => 2],
            // Semester 3
            ['kode' => 'TSK1624303', 'nama' => 'Praktikum Multimedia',                                 'sks' => 1, 'semester' => 3],
            ['kode' => 'TSK1624308', 'nama' => 'Praktikum Switching, Routing dan Jaringan Nirkabel',   'sks' => 1, 'semester' => 3],
            ['kode' => 'TSK1624309', 'nama' => 'Praktikum Fisika Dasar II',                            'sks' => 1, 'semester' => 3],
            ['kode' => 'TSK1624310', 'nama' => 'Praktikum Sistem Digital',                             'sks' => 1, 'semester' => 3],
            ['kode' => 'TSK1624312', 'nama' => 'Praktikum Elektronika Dasar',                          'sks' => 1, 'semester' => 3],
            // Semester 4
            ['kode' => 'TSK1624402', 'nama' => 'Praktikum Rekayasa Perangkat Lunak',                   'sks' => 1, 'semester' => 4],
            ['kode' => 'TSK1624405', 'nama' => 'Praktikum Sistem Basis Data',                          'sks' => 1, 'semester' => 4],
            ['kode' => 'TSK1624410', 'nama' => 'Praktikum Sistem Digital Lanjut',                      'sks' => 1, 'semester' => 4],
            // Semester 5
            ['kode' => 'TSK1624504', 'nama' => 'Praktikum Pemrograman Perangkat Bergerak',             'sks' => 1, 'semester' => 5],
            // Semester 6
            ['kode' => 'TSK1624605', 'nama' => 'Praktikum Teknik Mikroprosesor dan Antarmuka',         'sks' => 1, 'semester' => 6],
            ['kode' => 'TSK1624607', 'nama' => 'Praktikum Teknik Kendali dan Otomasi',                 'sks' => 1, 'semester' => 6],
            ['kode' => 'TSK1624616', 'nama' => 'Jaringan Skala Besar, Keamanan dan Otomasi + Praktikum','sks' => 3, 'semester' => 6],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('eo_matkul_praktikum')->upsert(
            $data,
            ['kode'],                        // unique key
            ['nama', 'sks', 'semester', 'updated_at']  // update jika sudah ada
        );

        $this->command->info('Seeded ' . count($data) . ' mata kuliah praktikum.');
    }
}
