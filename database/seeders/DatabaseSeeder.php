<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\BankSoal\Database\Seeders\BankSoalDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
<<<<<<< banksoal-branch
            
            
            \Modules\BankSoal\database\seeders\BankSoalDatabaseSeeder::class,
=======
            PermissionSeeder::class,
            SystemModuleSeeder::class,
>>>>>>> main
        ]);
    }
}