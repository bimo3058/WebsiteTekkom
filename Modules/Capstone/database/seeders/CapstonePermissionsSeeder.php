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
            'capstone.view'   => 'Lihat modul Capstone',
            'capstone.edit'   => 'Edit modul Capstone',
            'capstone.delete' => 'Hapus modul Capstone',
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
            'capstone.edit',
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
            'capstone.edit',
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
