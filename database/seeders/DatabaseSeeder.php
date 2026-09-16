<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\BankSoal\Database\Seeders\BankSoalDatabaseSeeder;
use Modules\Capstone\Database\Seeders\CapstoneDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            UserSeeder::class,
            // BankSoalDatabaseSeeder::class,
            // PermissionSeeder::class,
            // DosenPembimbingSeeder::class,
            // MahasiswaSeeder::class,
            // SystemModuleSeeder::class,
            // CapstoneDatabaseSeeder::class,
        ]);
    }
}
