<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BsCplCpmkSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bs_cpl_cpmk')->insert([
            ['cpl_id' => 1, 'cpmk_id' => 1],
            ['cpl_id' => 1, 'cpmk_id' => 2],
            ['cpl_id' => 2, 'cpmk_id' => 3],
            ['cpl_id' => 2, 'cpmk_id' => 4],
            ['cpl_id' => 3, 'cpmk_id' => 5],
            ['cpl_id' => 3, 'cpmk_id' => 6],
            ['cpl_id' => 4, 'cpmk_id' => 7],
            ['cpl_id' => 4, 'cpmk_id' => 8],
            ['cpl_id' => 5, 'cpmk_id' => 9],
            ['cpl_id' => 5, 'cpmk_id' => 10],
            ['cpl_id' => 5, 'cpmk_id' => 11],
            ['cpl_id' => 5, 'cpmk_id' => 12],
            ['cpl_id' => 6, 'cpmk_id' => 13],
            ['cpl_id' => 6, 'cpmk_id' => 14],
            ['cpl_id' => 7, 'cpmk_id' => 15],
            ['cpl_id' => 8, 'cpmk_id' => 16],
            ['cpl_id' => 9, 'cpmk_id' => 17],
            ['cpl_id' => 9, 'cpmk_id' => 18],
            ['cpl_id' => 10, 'cpmk_id' => 19],
        ]);
    }
}
