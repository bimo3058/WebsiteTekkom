<?php

namespace Database\Seeders;

use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name'        => 'Bank Soal',
                'slug'        => 'bank_soal',
                'icon'        => 'book',
                'description' => 'Manage question bank and learning materials',
            ],
            [
                'name'        => 'Capstone',
                'slug'        => 'capstone',
                'icon'        => 'graduation-cap',
                'description' => 'Manage capstone projects and thesis',
            ],
            [
                'name'        => 'E-Office',
                'slug'        => 'eoffice',
                'icon'        => 'briefcase',
                'description' => 'Manage office documents and workflow',
            ],
            [
                'name'        => 'Manajemen Mahasiswa',
                'slug'        => 'manajemen_mahasiswa',
                'icon'        => 'users',
                'description' => 'Manage student data and activities',
            ],
        ];

        foreach ($modules as $module) {
            // updateOrCreate mencegah data ganda kalau seeder dijalankan 2x
            SystemModule::updateOrCreate(
                ['slug' => $module['slug']], 
                $module
            );
        }
        
    }
}