<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenPembimbingSeeder extends Seeder
{
    public function run(): void
    {
        $dosenRole = Role::where('name', 'dosen')->where('module', 'global')->first();

        $dosenList = [
            [
                'external_id'     => 'EXT-DSN-TEKKOM-001',
                'name'            => 'Prof. Dr. Adian Fatchur Rochim, ST, MT, SMIEEE',
                'email'           => 'adian.fatchur@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-001',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-002',
                'name'            => 'Rinta Kridalukmana, S.Kom, MT., PhD',
                'email'           => 'rinta.kridalukmana@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-002',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-003',
                'name'            => 'Prof. Dr. Ir. R. Rizal Isnanto, ST, MM, MT',
                'email'           => 'rizal.isnanto@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-003',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-004',
                'name'            => 'Agung Budi Prasetijo, ST, MIT, Ph.D',
                'email'           => 'agung.budi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-004',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-005',
                'name'            => 'Dr. Oky Dwi Nurhayati, ST, MT',
                'email'           => 'oky.dwi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-005',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-006',
                'name'            => 'Eko Didik Widianto, ST, MT',
                'email'           => 'eko.didik@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-006',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-007',
                'name'            => 'Kurniawan Teguh Martono, ST, MT',
                'email'           => 'kurniawan.teguh@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-007',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-008',
                'name'            => 'Ike Pertiwi Windasari, ST, MT',
                'email'           => 'ike.pertiwi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-008',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-009',
                'name'            => 'Dania Eridani, ST, M.Eng',
                'email'           => 'dania.eridani@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-009',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-010',
                'name'            => 'Adnan Fauzi, ST, M.Kom',
                'email'           => 'adnan.fauzi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-010',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-011',
                'name'            => 'Yudi Eko Windarto, ST, M.Kom',
                'email'           => 'yudi.eko@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-011',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-012',
                'name'            => 'Kuntoro Adi Nugroho, ST, M.Eng',
                'email'           => 'kuntoro.adi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-012',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-013',
                'name'            => 'Patricia Evericho Mountaines, S.T., M.Cs.',
                'email'           => 'patricia.evericho@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-013',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-014',
                'name'            => 'Bellia Dwi Cahya Putri S.T., M.T',
                'email'           => 'bellia.dwi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-014',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-015',
                'name'            => 'Erwin Adriono S.T., M.T',
                'email'           => 'erwin.adriono@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-015',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-016',
                'name'            => 'Ilmam Fauzi Hashbil Alim, S.T., M.Kom.',
                'email'           => 'ilmam.fauzi@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-016',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-017',
                'name'            => 'Arseto Satriyo Nugroho, S.T., M.Eng.',
                'email'           => 'arseto.satriyo@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-017',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-018',
                'name'            => 'Dr. Maman Somantri, S.T., M.T.',
                'email'           => 'maman.somantri@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-018',
            ],
            [
                'external_id'     => 'EXT-DSN-TEKKOM-019',
                'name'            => 'Delphi Hanggoro, S.T., M.T',
                'email'           => 'delphi.hanggoro@undip.ac.id',
                'employee_number' => 'NIP-TEKKOM-019',
            ],
        ];

        foreach ($dosenList as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'external_id' => $data['external_id'],
                    'name'        => $data['name'],
                    'password'    => Hash::make('password'),
                ]
            );

            if ($dosenRole) {
                $user->roles()->syncWithoutDetaching([$dosenRole->id]);
            }

            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['employee_number' => $data['employee_number']]
            );
        }

        $this->command->info('✅ ' . count($dosenList) . ' dosen pembimbing berhasil ditambahkan.');
    }
}
