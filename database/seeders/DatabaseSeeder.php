<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
=======
use Illuminate\Database\Seeder;
use Modules\BankSoal\Database\Seeders\BankSoalDatabaseSeeder;
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
<<<<<<< HEAD
            PenggunaSeeder::class,
            PraktikumSeeder::class,
        ]);
    }
}
=======
            UserSeeder::class,
            BankSoalDatabaseSeeder::class,
            PermissionSeeder::class,
            SystemModuleSeeder::class,
        ]);
    }
}
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
