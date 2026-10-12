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
     * Role yang boleh melihat data mahasiswa secara utuh di Direktori Mahasiswa:
     * status akademik lengkap (termasuk yang sudah bukan mahasiswa) serta kontak
     * pribadi (Email Pribadi, WhatsApp/Telepon).
     *
     * Role di luar daftar ini (mahasiswa, alumni, pengurus himpunan, dst.) hanya
     * melihat mahasiswa yang masih berstatus mahasiswa dan tanpa kontak pribadi
     * orang lain — prinsip need-to-know (UU PDP No. 27/2022 Pasal 16 ayat 2,
     * Pasal 35, Pasal 36).
     */
    public const LIHAT_DATA_LENGKAP = [
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
        'gpm',
        'dosen',
        'dpm',
    ];

    /**
     * Status yang menandakan orang itu sudah bukan mahasiswa lagi. Disembunyikan
     * total dari role di luar LIHAT_DATA_LENGKAP. Cuti dan Mangkir sengaja tidak
     * termasuk: keduanya masih berstatus mahasiswa dan bisa aktif kembali.
     */
    public const STATUS_BUKAN_MAHASISWA = [
        Kemahasiswaan::STATUS_DO,
        Kemahasiswaan::STATUS_WAFAT,
        Kemahasiswaan::STATUS_PINDAH_STUDI,
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

    /**
     * Mengubah Status Kemahasiswaan — SATU-SATUNYA yang boleh dilakukan pengelola
     * pada baris orang lain. Biodata (email pribadi, kontak) milik mahasiswa itu
     * sendiri; nama, NIM, dan angkatan milik SSO.
     *
     * Pengelola tidak boleh mengubah status barisnya sendiri (kasus pengelola yang
     * juga terdaftar sebagai mahasiswa): keputusan atas dirinya diambil pengelola lain.
     */
    public function ubahStatus(User $user, Kemahasiswaan $mhs): bool
    {
        return $user->hasAnyRole(self::PENGELOLA)
            && (int) $mhs->user_id !== (int) $user->getKey();
    }
}
