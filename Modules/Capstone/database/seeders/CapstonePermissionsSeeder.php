<?php

namespace Modules\Capstone\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CapstonePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $perms = [
            'capstone.view'                       => 'Lihat modul Capstone',
            'capstone.periods.manage'             => 'Kelola periode',
            'capstone.titles.create'              => 'Buat judul TA',
            'capstone.titles.approve'             => 'Approve judul mahasiswa',
            'capstone.groups.view'                => 'Lihat group',
            'capstone.groups.manage'              => 'Kelola group',
            'capstone.bids.create'                => 'Submit bidding',
            'capstone.bids.recommend'             => 'Rekomendasi bidding',
            'capstone.documents.upload'           => 'Upload dokumen',
            'capstone.documents.review'           => 'Review dokumen',
            'capstone.schedules.manage'           => 'Kelola jadwal seminar/sidang',
            'capstone.evaluations.submit'         => 'Submit evaluasi',
            'capstone.expo.manage'                => 'Kelola expo event',
            'capstone.finalization.execute'       => 'Eksekusi finalisasi alokasi',
            'capstone.reports.export'             => 'Export laporan',
            'capstone.assessment.manage'          => 'Kelola komponen assessment',
            'capstone.signatures.sign'            => 'Tanda tangan digital',
        ];

        foreach ($perms as $name => $display) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['display_name' => $display, 'module' => 'capstone']
            );
        }

        // Roles
        $adminCapstone = Role::firstOrCreate(
            ['name' => 'admin_capstone', 'guard_name' => 'web'],
            ['module' => 'capstone', 'is_academic' => false]
        );

        $dosen = Role::firstOrCreate(
            ['name' => 'dosen', 'guard_name' => 'web'],
            ['module' => 'global', 'is_academic' => true]
        );

        $mahasiswa = Role::firstOrCreate(
            ['name' => 'mahasiswa', 'guard_name' => 'web'],
            ['module' => 'global', 'is_academic' => true]
        );

        // Assign permissions ke role
        $adminCapstone->permissions()->sync(
            Permission::where('module', 'capstone')->pluck('id')
        );

        $dosen->permissions()->syncWithoutDetaching(
            Permission::whereIn('name', [
                'capstone.view',
                'capstone.titles.create',
                'capstone.titles.approve',
                'capstone.groups.view',
                'capstone.bids.recommend',
                'capstone.documents.review',
                'capstone.evaluations.submit',
                'capstone.signatures.sign',
            ])->pluck('id')
        );

        $mahasiswa->permissions()->syncWithoutDetaching(
            Permission::whereIn('name', [
                'capstone.view',
                'capstone.bids.create',
                'capstone.documents.upload',
            ])->pluck('id')
        );

        $superadmin = Role::where('name', 'superadmin')->where('guard_name', 'web')->first();
        $superadmin?->permissions()->syncWithoutDetaching(
            Permission::where('module', 'capstone')->pluck('id')
        );
    }
}
