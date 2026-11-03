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
     * Pengelola direktori. Sejak S-71 (3 Nov 2026) tidak lagi mengubah kontak mahasiswa;
     * daftar ini masih dipakai AlumniPolicy untuk form edit Direktori Alumni.
     */
    public const PENGELOLA = [
        'superadmin',
        'admin_kemahasiswaan',
    ];

    /**
     * Role yang boleh melihat kontak pribadi (Email Pribadi, WhatsApp/Telepon)
     * mahasiswa lain di Direktori Mahasiswa. Role di luar daftar ini hanya melihat
     * kontak pribadinya sendiri — prinsip need-to-know (UU PDP No. 27/2022
     * Pasal 16 ayat 2, Pasal 35, Pasal 36).
     */
    public const LIHAT_KONTAK_PRIBADI = [
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
        'gpm',
        'dosen',
        'dpm',
    ];

    /**
     * Membuka form edit dan menyimpan perubahannya.
     *
     * Hanya pemilik baris, role apa pun — itulah yang menggantikan halaman "Profil Saya".
     * Field yang boleh ia ubah dibatasi terpisah di controller; Policy ini hanya menjawab
     * "baris siapa", bukan "kolom apa".
     *
     * Dipanggil langsung oleh controller, bukan lewat authorize(): Gate::before meloloskan
     * superadmin sebelum method ini sempat berjalan.
     */
    public function update(User $user, Kemahasiswaan $mhs): bool
    {
        return (int) $mhs->user_id === (int) $user->getKey();
    }
}
