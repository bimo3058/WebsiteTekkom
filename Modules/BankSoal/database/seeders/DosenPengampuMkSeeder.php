<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenPengampuMkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();
        
        $data = [
            // User 2
            ['user_id' => 2, 'mk_id' => 1, 'is_rps' => 'false'],
            ['user_id' => 2, 'mk_id' => 4, 'is_rps' => 'false'],
            ['user_id' => 2, 'mk_id' => 7, 'is_rps' => 'false'],
            ['user_id' => 2, 'mk_id' => 10, 'is_rps' => 'false'],
            ['user_id' => 2, 'mk_id' => 15, 'is_rps' => 'false'],
            
            // User 3
            ['user_id' => 3, 'mk_id' => 2, 'is_rps' => 'false'],
            ['user_id' => 3, 'mk_id' => 4, 'is_rps' => 'false'],
            ['user_id' => 3, 'mk_id' => 8, 'is_rps' => 'false'],
            ['user_id' => 3, 'mk_id' => 12, 'is_rps' => 'false'],
            
            // User 4
            ['user_id' => 4, 'mk_id' => 3, 'is_rps' => 'false'],
            ['user_id' => 4, 'mk_id' => 6, 'is_rps' => 'false'],
            ['user_id' => 4, 'mk_id' => 9, 'is_rps' => 'false'],
            ['user_id' => 4, 'mk_id' => 14, 'is_rps' => 'false'],
            ['user_id' => 4, 'mk_id' => 20, 'is_rps' => 'false'],
            
            // User 5
            ['user_id' => 5, 'mk_id' => 5, 'is_rps' => 'false'],
            ['user_id' => 5, 'mk_id' => 11, 'is_rps' => 'false'],
            ['user_id' => 5, 'mk_id' => 16, 'is_rps' => 'false'],
            
            // User 6
            ['user_id' => 6, 'mk_id' => 2, 'is_rps' => 'false'],
            ['user_id' => 6, 'mk_id' => 13, 'is_rps' => 'false'],
            ['user_id' => 6, 'mk_id' => 18, 'is_rps' => 'false'],
            
            // User 7
            ['user_id' => 7, 'mk_id' => 4, 'is_rps' => 'false'],
            ['user_id' => 7, 'mk_id' => 10, 'is_rps' => 'false'],
            ['user_id' => 7, 'mk_id' => 19, 'is_rps' => 'false'],
            
            // User 8
            ['user_id' => 8, 'mk_id' => 7, 'is_rps' => 'false'],
            ['user_id' => 8, 'mk_id' => 15, 'is_rps' => 'false'],
            ['user_id' => 8, 'mk_id' => 25, 'is_rps' => 'false'],
        ];
        
        // Add timestamps
        foreach ($data as &$item) {
            $item['created_at'] = $timestamp;
            $item['updated_at'] = $timestamp;
        }

        // Insert data ke database
        DB::table('bs_dosen_pengampu_mk')->insert($data);
    }
}
