<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed dua role inti Manajemen Praktikum:
 *   - koor_prak  : Koordinator Praktikum
 *   - asprak     : Asisten Praktikum
 *
 * Mengapa pakai migration, bukan seeder?
 * Supaya bisa di-rollback dan tidak bergantung pada urutan `db:seed`.
 * Keduanya idempotent (INSERT … ON CONFLICT DO NOTHING).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // ── Roles ─────────────────────────────────────────────────────────────
        // Guard name sesuai default Spatie Laravel-Permission yang dipakai proyek.
        // module = 'eoffice' agar tidak bentrok dengan role modul lain
        // (capstone, kemahasiswaan, dll).
        $roles = [
            [
                'name'        => 'koor_prak',
                'module'      => 'eoffice',
                'guard_name'  => 'web',
                'is_academic' => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'asprak',
                'module'      => 'eoffice',
                'guard_name'  => 'web',
                'is_academic' => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        foreach ($roles as $role) {
            // Idempotent: skip jika sudah ada (name + module unik secara logika)
            $exists = DB::table('roles')
                ->where('name', $role['name'])
                ->where('module', $role['module'])
                ->exists();

            if (! $exists) {
                DB::table('roles')->insert($role);
            }
        }

        // ── Permissions ────────────────────────────────────────────────────────
        // Permissions granular untuk masing-masing role.
        // Dikelompokkan per resource. Format: <action>-<resource> (kebab-case).
        $permissions = [

            // ── koor_prak ──────────────────────────────────────────────────
            // Scope kontrol akses pada resource: selalu dibatasi oleh
            // eo_praktikum.koor_id = auth()->id() di level controller/middleware.

            'koor.dashboard',

            'koor.modul.view',      // lihat modul milik praktikumnya
            'koor.modul.manage',    // CRUD modul

            'koor.praktikan.view',  // lihat daftar praktikan
            'koor.praktikan.import',// import praktikan

            'koor.asprak.view',     // lihat asprak yang terdaftar
            'koor.asprak.seleksi',  // approve/reject pendaftaran asprak

            'koor.bagi-modul.manage',   // assign asprak ke modul

            'koor.pengumuman.view',
            'koor.pengumuman.manage',

            'koor.nilai.view',
            'koor.nilai.approve',   // forward nilai ke dosen untuk publikasi

            // ── asprak ────────────────────────────────────────────────────
            // Scope: hanya modul yang di-assign ke asprak bersangkutan
            // (via modul_asprak.asprak_id).

            'asprak.dashboard',

            'asprak.absensi.view',
            'asprak.absensi.manage',// CRUD absensi modul yang diampunya

            'asprak.tugas.view',
            'asprak.tugas.manage',  // CRUD tugas + beri nilai + revisi

            'asprak.materi.view',
            'asprak.materi.manage', // upload/hapus materi

            'asprak.pengumuman.view',
            'asprak.pengumuman.manage',
        ];

        foreach ($permissions as $permName) {
            $exists = DB::table('permissions')
                ->where('name', $permName)
                ->where('module', 'eoffice')
                ->exists();

            if (! $exists) {
                DB::table('permissions')->insert([
                    'name'         => $permName,
                    'display_name' => $permName,
                    'module'       => 'eoffice',
                    'guard_name'   => 'web',
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }

        // ── role_has_permissions ───────────────────────────────────────────────
        $koorRole  = DB::table('roles')->where('name', 'koor_prak')->where('module', 'eoffice')->first();
        $asprakRole = DB::table('roles')->where('name', 'asprak')->where('module', 'eoffice')->first();

        $koorPerms = array_filter($permissions, fn($p) => str_starts_with($p, 'koor.'));
        $asprakPerms = array_filter($permissions, fn($p) => str_starts_with($p, 'asprak.'));

        $this->attachPermissionsToRole($koorRole->id, $koorPerms, 'eoffice');
        $this->attachPermissionsToRole($asprakRole->id, $asprakPerms, 'eoffice');
    }

    public function down(): void
    {
        // Lepaskan role_has_permissions dulu
        $roles = DB::table('roles')
            ->whereIn('name', ['koor_prak', 'asprak'])
            ->where('module', 'eoffice')
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('role_id', $roles)->delete();

        // Hapus permissions
        $permNames = array_merge(
            ['koor.dashboard','koor.modul.view','koor.modul.manage','koor.praktikan.view',
             'koor.praktikan.import','koor.asprak.view','koor.asprak.seleksi',
             'koor.bagi-modul.manage','koor.pengumuman.view','koor.pengumuman.manage',
             'koor.nilai.view','koor.nilai.approve'],
            ['asprak.dashboard','asprak.absensi.view','asprak.absensi.manage',
             'asprak.tugas.view','asprak.tugas.manage','asprak.materi.view',
             'asprak.materi.manage','asprak.pengumuman.view','asprak.pengumuman.manage']
        );

        DB::table('permissions')
            ->whereIn('name', $permNames)
            ->where('module', 'eoffice')
            ->delete();

        // Hapus roles
        DB::table('roles')
            ->whereIn('name', ['koor_prak', 'asprak'])
            ->where('module', 'eoffice')
            ->delete();
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function attachPermissionsToRole(int $roleId, array $permNames, string $module): void
    {
        foreach ($permNames as $permName) {
            $perm = DB::table('permissions')
                ->where('name', $permName)
                ->where('module', $module)
                ->first();

            if (! $perm) continue;

            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $perm->id)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $perm->id,
                ]);
            }
        }
    }
};
