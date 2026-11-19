<?php

namespace Modules\ManajemenMahasiswa\Policies;

use App\Models\User;
use Modules\ManajemenMahasiswa\Models\Alumni;

/**
 * "Data alumni milik SIAPA yang boleh diubah" — pasangan KemahasiswaanPolicy untuk
 * Direktori Alumni. Route menyaring ROLE, Policy ini menyaring BARIS.
 *
 * Tanpa lapis ini, membuka route edit untuk role `alumni` berarti setiap alumni bisa
 * mengetik /direktori/alumni/7/edit dan mengubah data karir serta kontak orang lain.
 */
class AlumniPolicy
{
    /**
     * Membuka form edit dan menyimpannya: pemilik baris (alumni itu sendiri) atau
     * pengelola. Kolom yang boleh diubah dibedakan di controller lewat ubahDataAlumni.
     */
    public function update(User $user, Alumni $alumni): bool
    {
        if ((int) $alumni->user_id === (int) $user->getKey()) {
            return true;
        }

        return $user->hasAnyRole(KemahasiswaanPolicy::PENGELOLA);
    }

    /**
     * Mengubah data alumni lain sebagai pengelola: tahun lulus, kontak, karir — tercatat
     * di audit log. Di barisnya sendiri pengelola memakai jalur pemilik, sama dengan
     * KemahasiswaanPolicy::ubahDataMahasiswa.
     */
    public function ubahDataAlumni(User $user, Alumni $alumni): bool
    {
        return $user->hasAnyRole(KemahasiswaanPolicy::PENGELOLA)
            && (int) $alumni->user_id !== (int) $user->getKey();
    }
}
