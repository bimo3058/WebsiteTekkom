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
        DB::table('bs_parameter')->insert([
            [
                'aspek' => 'Apakah Mata kuliah sudah sesuai dengan pengampunya?',
                'bobot' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek' => 'Apakah SKS, Semester, dan Tahun Ajaran yang diberikan benar dan sesuai?',
                'bobot' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek' => 'Apakah Dosen pengampu sudah benar?',
                'bobot' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek' => 'Apakah pemetaan CPL yang diberikan sudah benar?',
                'bobot' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek' => 'Apakah Pemetaan CPMK yang diberikan sudah benar?',
                'bobot' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek' => 'Apakah Tabel penilaian yang diberikan sudah sesuai?',
                'bobot' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
