<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BsMataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bs_mata_kuliah')->insert([
            [
                'id' => 1,
                'kode' => 'PTSK6208',
                'nama' => 'Algoritma dan Pemrograman',
                'sks' => 2,
                'semester' => null,
                'created_at' => '2026-03-13 13:12:48',
                'updated_at' => '2026-03-13 13:12:51',
            ],
            [
                'id' => 2,
                'kode' => 'PTSK6206',
                'nama' => 'Elektronika Dasar',
                'sks' => 2,
                'semester' => null,
                'created_at' => '2026-03-13 13:14:21',
                'updated_at' => '2026-03-13 13:14:21',
            ],
            [
                'id' => 3,
                'kode' => 'PTSK6211',
                'nama' => 'Matematika Teknik',
                'sks' => 3,
                'semester' => null,
                'created_at' => '2026-03-13 06:16:10',
                'updated_at' => '2026-03-13 06:16:10',
            ],
            [
                'id' => 4,
                'kode' => 'PTSK6305',
                'nama' => 'Sistem Digital',
                'sks' => 2,
                'semester' => null,
                'created_at' => '2026-03-13 06:16:23',
                'updated_at' => '2026-03-13 06:16:23',
            ],
        ]);
    }
}
