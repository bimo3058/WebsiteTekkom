<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Mahasiswa 1', 'nim' => '24060120120001'],
            ['nama' => 'Mahasiswa 2', 'nim' => '24060125130045'],
            ['nama' => 'Mahasiswa 3', 'nim' => '24060121120012'],
            ['nama' => 'Mahasiswa 4', 'nim' => '24060121140120'],
            ['nama' => 'Mahasiswa 5', 'nim' => '24060122130008'],
            ['nama' => 'Mahasiswa 6', 'nim' => '24060122110099'],
            ['nama' => 'Mahasiswa 7', 'nim' => '24060123100005'],
            ['nama' => 'Mahasiswa 8', 'nim' => '24060123140150'],
            ['nama' => 'Mahasiswa 9', 'nim' => '24060123190002'],
            ['nama' => 'Mahasiswa 10', 'nim' => '24060124130101'],
        ];

        $roleMahasiswa = Role::where('name', 'mahasiswa')->first();

        foreach ($data as $mhs) {
            $nim = $mhs['nim'];
            
            // Ekstrak tahun masuk (Digit 7-8)
            $kodeTahun = substr($nim, 6, 2); 
            $tahunMasuk = '20' . $kodeTahun; 

            // 1. Buat data user
            $user = User::firstOrCreate(
                ['email' => $nim . '@students.undip.ac.id'],
                [
                    'name' => $mhs['nama'],
                    'password' => Hash::make('password123'),
                    'external_id' => Str::uuid(), 
                ]
            );

            // 2. Buat data student
            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => $nim,
                    'cohort_year' => $tahunMasuk,
                ]
            );

            // 3. Assign role mahasiswa
            if ($roleMahasiswa && !$user->roles->contains($roleMahasiswa->id)) {
                $user->roles()->attach($roleMahasiswa->id);
            }
        }
    }
}
