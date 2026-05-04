<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Load semua role di awal — langsung error kalau RoleSeeder belum dijalankan
        $roles = [
            'superadmin' => Role::where('name', 'superadmin')->where('module', 'global')->firstOrFail(),
            'dosen'      => Role::where('name', 'dosen')->where('module', 'global')->firstOrFail(),
            'mahasiswa'  => Role::where('name', 'mahasiswa')->where('module', 'global')->firstOrFail(),
            'gpm'        => Role::where('name', 'gpm')->where('module', 'bank_soal')->firstOrFail(),
            
        ];

        // -------------------------------------------------------
        // 1. SUPERADMIN
        // -------------------------------------------------------
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@kampus.ac.id'],
            [
                'external_id' => 'EXT-SUPERADMIN-001',
                'name'        => 'Super Admin',
                'password'    => Hash::make('password'),
            ]
        );
        $superadmin->roles()->syncWithoutDetaching([$roles['superadmin']->id]);

        // -------------------------------------------------------
        // 2. DOSEN
        // -------------------------------------------------------
        $dosenUsers = [
            [
                'external_id'     => 'EXT-DSN-001',
                'name'            => 'Dr. Budi Santoso',
                'email'           => 'budi.santoso@kampus.ac.id',
                'employee_number' => 'NIP-2001-001',
            ],
            [
                'external_id'     => 'EXT-DSN-002',
                'name'            => 'Dr. Siti Rahayu',
                'email'           => 'siti.rahayu@kampus.ac.id',
                'employee_number' => 'NIP-2001-002',
            ],
            [
                'external_id'     => 'EXT-DSN-003',
                'name'            => 'Prof. Rini Handayani',
                'email'           => 'rini.handayani@kampus.ac.id',
                'employee_number' => 'NIP-2001-005',
            ],
        ];

        foreach ($dosenUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'external_id' => $data['external_id'],
                    'name'        => $data['name'],
                    'password'    => Hash::make('password'),
                ]
            );
            $user->roles()->syncWithoutDetaching([$roles['dosen']->id]);

            // Buat Lecturer record
            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['employee_number' => $data['employee_number']]
            );
        }

        // -------------------------------------------------------
        // 3. GPM + Dosen
        // -------------------------------------------------------
        $gpmUsers = [
            [
                'external_id'     => 'EXT-GPM-001',
                'name'            => 'Prof. Ahmad Fauzi',
                'email'           => 'ahmad.fauzi@kampus.ac.id',
                'employee_number' => 'NIP-2001-003',
            ],
            [
                'external_id'     => 'EXT-GPM-002',
                'name'            => 'Prof. Dewi Lestari',
                'email'           => 'dewi.lestari@kampus.ac.id',
                'employee_number' => 'NIP-2001-004',
            ],
            [
                'external_id'     => 'EXT-GPM-003',
                'name'            => 'Dr. Arif Budiman',
                'email'           => 'arif.budiman@kampus.ac.id',
                'employee_number' => 'NIP-2001-006',
            ],
            [
                'external_id'     => 'EXT-GPM-004',
                'name'            => 'Prof. Sri Mulyana',
                'email'           => 'sri.mulyana@kampus.ac.id',
                'employee_number' => 'NIP-2001-007',
            ],
        ];

        foreach ($gpmUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'external_id' => $data['external_id'],
                    'name'        => $data['name'],
                    'password'    => Hash::make('password'),
                ]
            );
            $user->roles()->syncWithoutDetaching([$roles['dosen']->id, $roles['gpm']->id]);

            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['employee_number' => $data['employee_number']]
            );
        }

        // -------------------------------------------------------
        // 4. MAHASISWA
        // -------------------------------------------------------
        $mahasiswaUsers = [
            [
                'external_id'    => 'EXT-MHS-001',
                'name'           => 'Andi Pratama',
                'email'          => 'andi.pratama@student.kampus.ac.id',
                'student_number' => '2021001001',
                'cohort_year'    => 2021,
            ],
            [
                'external_id'    => 'EXT-MHS-002',
                'name'           => 'Bela Safitri',
                'email'          => 'bela.safitri@student.kampus.ac.id',
                'student_number' => '2021001002',
                'cohort_year'    => 2021,
            ],
            [
                'external_id'    => 'EXT-MHS-003',
                'name'           => 'Cahyo Nugroho',
                'email'          => 'cahyo.nugroho@student.kampus.ac.id',
                'student_number' => '2021001003',
                'cohort_year'    => 2021,
            ],
            [
                'external_id'    => 'EXT-MHS-004',
                'name'           => 'Dina Marlina',
                'email'          => 'dina.marlina@student.kampus.ac.id',
                'student_number' => '2022001001',
                'cohort_year'    => 2022,
            ],
        ];

        foreach ($mahasiswaUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'external_id' => $data['external_id'],
                    'name'        => $data['name'],
                    'password'    => Hash::make('password'),
                ]
            );
            $user->roles()->syncWithoutDetaching([$roles['mahasiswa']->id]);

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => $data['student_number'],
                    'cohort_year'    => $data['cohort_year'],
                ]
            );
        }
    }
}