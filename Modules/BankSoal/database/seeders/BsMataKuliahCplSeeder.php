<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BsMataKuliahCplSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bs_mata_kuliah_cpl')->insert([
            ['mk_id' => 1, 'cpl_id' => 1],
            ['mk_id' => 1, 'cpl_id' => 4],
            ['mk_id' => 1, 'cpl_id' => 5],
            ['mk_id' => 1, 'cpl_id' => 8],
            ['mk_id' => 2, 'cpl_id' => 4],
            ['mk_id' => 2, 'cpl_id' => 5],
            ['mk_id' => 2, 'cpl_id' => 6],
            ['mk_id' => 2, 'cpl_id' => 7],
            ['mk_id' => 3, 'cpl_id' => 1],
            ['mk_id' => 4, 'cpl_id' => 2],
            ['mk_id' => 4, 'cpl_id' => 4],
            ['mk_id' => 4, 'cpl_id' => 5],
        ]);
    }
}
