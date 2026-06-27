<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BsRpsParamSeeder extends Seeder
{
    /**
     * Parameter untuk penilaian GPM terhadap RPS
     * Total bobot: 100 poin
     */
    public function run(): void
    {
        $timestamp = now();

        $data = [
            ['aspek' => 'Apakah Mata kuliah sudah sesuai dengan pengampunya?', 'bobot' => 10],
            ['aspek' => 'Apakah SKS, Semester, dan Tahun Ajaran yang diberikan benar dan sesuai?', 'bobot' => 5],
            ['aspek' => 'Apakah Dosen pengampu sudah benar?', 'bobot' => 10],
            ['aspek' => 'Apakah pemetaan CPL yang diberikan sudah benar?', 'bobot' => 25],
            ['aspek' => 'Apakah Pemetaan CPMK yang diberikan sudah benar?', 'bobot' => 25],
            ['aspek' => 'Apakah Tabel penilaian yang diberikan sudah sesuai?', 'bobot' => 25],
        ];

        foreach ($data as $item) {
            DB::table('bs_parameter')->updateOrInsert(
                ['aspek' => $item['aspek']],
                [
                    'bobot' => $item['bobot'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]
            );
        }
    }
}
