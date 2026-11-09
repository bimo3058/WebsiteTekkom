<?php

namespace Modules\ManajemenMahasiswa\Policies;

use App\Models\User;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;

/**
 * "Biodata direktori milik SIAPA yang boleh diubah".
 *
 * Lapis kedua di belakang `role:` middleware di routes/web.php, pola yang sama
 * dengan KegiatanPolicy dan CvProfilePolicy: route menentukan ROLE apa yang boleh
 * memakai aksi edit/update, Policy ini menentukan BARIS MILIK SIAPA yang boleh
 * disentuh. Keduanya harus lolos.
 *
 * Tanpa lapis ini, membuka gerbang route untuk role `mahasiswa` berarti setiap
 * mahasiswa bisa mengetik /direktori/mahasiswa/7/edit dan mengubah biodata orang
 * lain — persis pola IDOR yang sudah pernah terbukti di bab Kegiatan.
 *
 * Pengecekan role memakai daftar putih hasAnyRole(), karena di modul ini satu akun
 * bisa memegang beberapa role sekaligus (pengurus himpunan juga memegang
 * `mahasiswa`), sehingga blacklist pasti bocor.
 */
class KemahasiswaanPolicy
{
    /**
     * Role yang boleh mengubah biodata direktori milik mahasiswa mana pun,
     * termasuk kolom Status. Sinkron dengan gerbang route `direktori.mahasiswa.edit`.
     */
    public const PENGELOLA = [
        'superadmin',
        'admin_kemahasiswaan',
    ];

    /**
     * Membuka form edit dan menyimpan perubahannya.
     *
     * Pemilik baris selalu boleh — itulah yang menggantikan halaman "Profil Saya".
     * Field yang boleh ia ubah dibatasi terpisah di controller (Status hanya milik
     * pengelola); Policy ini hanya menjawab "baris siapa", bukan "kolom apa".
     */
    public function update(User $user, Kemahasiswaan $mhs): bool
    {
        if ((int) $mhs->user_id === (int) $user->getKey()) {
            return true;
        }

        return $user->hasAnyRole(self::PENGELOLA);
    }
}
