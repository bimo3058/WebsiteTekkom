<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;

class BankSoalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BsMataKuliahSeeder::class,
            BsCplSeeder::class,
            BsCpmkSeeder::class,
            BsMataKuliahCplSeeder::class,
            BsCplCpmkSeeder::class,
            BsRpsParamSeeder::class,
        ]);
    }
}
