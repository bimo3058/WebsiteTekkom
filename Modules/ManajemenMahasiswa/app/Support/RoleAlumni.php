<?php

namespace Modules\ManajemenMahasiswa\Support;

use App\Models\Role;
use App\Models\User;

/**
 * Menyelaraskan role `alumni` dengan Status Kemahasiswaan.
 *
 * Direktori Alumni menyinkronkan tabel mk_alumni dari role `alumni` setiap kali
 * dibuka (DirektoriAlumniController::syncFromKemahasiswaan). Status "Alumni" yang
 * diubah admin tanpa ikut menyentuh role akan dibatalkan sinkron itu: baris
 * mk_alumni dihapus dan status kembali "Aktif". Karena itu setiap perubahan status
 * ke/dari Alumni WAJIB lewat sini.
 *
 * Hanya role `alumni` yang ditambah/dicabut. Role `mahasiswa` sengaja tidak
 * disentuh: login SSO (AcademicRoleSynchronizer) memasangnya kembali untuk setiap
 * akun yang punya data student, jadi mencabutnya di sini sia-sia.
 */
class RoleAlumni
{
    /** @return bool true bila role benar-benar berubah */
    public static function selaraskan(User $user, bool $jadiAlumni): bool
    {
        $role = Role::where('name', 'alumni')->where('module', 'global')->first();
        if (!$role) {
            throw new \RuntimeException('Role alumni tidak ditemukan.');
        }

        $punya = $user->roles()->whereKey($role->id)->exists();
        if ($punya === $jadiAlumni) {
            return false;
        }

        $jadiAlumni
            ? $user->roles()->syncWithoutDetaching([$role->id])
            : $user->roles()->detach($role->id);

        $user->unsetRelation('roles');
        $user->syncPermissionsFromRoles();
        $user->clearUserCache();

        return true;
    }
}
