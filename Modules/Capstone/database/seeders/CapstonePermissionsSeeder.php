<?php

namespace Modules\Capstone\Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class CapstonePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permissions
        $perms = [
            'capstone.view' => 'Lihat modul Capstone',
            'capstone.periods.manage' => 'Kelola periode',
            'capstone.titles.create' => 'Buat judul TA',
            'capstone.titles.approve' => 'Approve judul mahasiswa',
            'capstone.groups.view' => 'Lihat group',
            'capstone.groups.manage' => 'Kelola group',
            'capstone.bids.create' => 'Submit bidding',
            'capstone.bids.recommend' => 'Rekomendasi bidding',
            'capstone.documents.upload' => 'Upload dokumen',
            'capstone.documents.review' => 'Review dokumen',
            'capstone.schedules.manage' => 'Kelola jadwal seminar/sidang',
            'capstone.evaluations.submit' => 'Submit evaluasi',
            'capstone.expo.manage' => 'Kelola expo event',
            'capstone.finalization.execute' => 'Eksekusi finalisasi alokasi',
            'capstone.reports.export' => 'Export laporan',
            'capstone.assessment.manage' => 'Kelola komponen assessment',
            'capstone.signatures.sign' => 'Tanda tangan digital',
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

        Role::firstOrCreate(
            ['name' => 'dosen', 'module' => 'global'],
            ['guard_name' => 'web', 'is_academic' => true]
        );

        Role::firstOrCreate(
            ['name' => 'mahasiswa', 'module' => 'global'],
            ['guard_name' => 'web', 'is_academic' => true]
        );

        // Assign permissions ke role
        $adminCapstone->permissions()->sync(
            Permission::where('module', 'capstone')->pluck('id')
        );

        $dosenPermissionIds = Permission::whereIn('name', [
            'capstone.view',
            'capstone.titles.create',
            'capstone.titles.approve',
            'capstone.groups.view',
            'capstone.bids.recommend',
            'capstone.documents.review',
            'capstone.evaluations.submit',
            'capstone.signatures.sign',
        ])->pluck('id');

        // Imported databases may still have both a module-specific and a
        // global academic role. Grant every same-named Spatie role so neither
        // legacy users nor newly synchronized SSO users lose Capstone access.
        Role::query()
            ->where('name', 'dosen')
            ->where('guard_name', 'web')
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching($dosenPermissionIds));

        $mahasiswaPermissionIds = Permission::whereIn('name', [
            'capstone.view',
            'capstone.bids.create',
            'capstone.documents.upload',
        ])->pluck('id');

        Role::query()
            ->where('name', 'mahasiswa')
            ->where('guard_name', 'web')
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching($mahasiswaPermissionIds));

        $superadmin = Role::where('name', 'superadmin')->where('guard_name', 'web')->first();
        $superadmin?->permissions()->syncWithoutDetaching(
            Permission::where('module', 'capstone')->pluck('id')
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
