<?php

namespace App\Policies;

use App\Models\CvProfile;
use App\Models\User;

/**
 * "CV milik siapa yang boleh dibuka" di Direktori Mahasiswa & Direktori Alumni.
 *
 * Lapis kedua di belakang `role:` middleware di routes/web.php, mengikuti pola
 * yang sama dengan KegiatanPolicy: route menentukan ROLE apa yang boleh memakai
 * aksi, Policy ini menentukan CV MILIK SIAPA. Keduanya harus lolos, sehingga
 * Policy tidak pernah memperluas hak sebuah role.
 *
 * Pengecekan role memakai daftar putih hasAnyRole(), karena di modul ini satu
 * akun bisa memegang beberapa role sekaligus — seluruh pemegang jabatan
 * himpunan juga memegang role `mahasiswa`, jadi blacklist pasti bocor.
 */
class CvProfilePolicy
{
    /**
     * Pengelola & pembina yang boleh membuka CV orang lain.
     *
     * Sengaja TIDAK memuat jabatan himpunan (pengurus_himpunan, ketua_himpunan,
     * ketua_bidang, ketua_unit): mereka mahasiswa aktif, dan CV memuat email
     * pribadi, nomor WhatsApp, serta domisili pemiliknya. Mereka tetap bisa
     * membuka CV sendiri lewat route /profil/cv seperti mahasiswa biasa.
     *
     * Keputusan pemilik modul (20 Sep 2026): role `admin` biasa dan dosen
     * koordinator (`dosen_koor`) tidak dipakai di bab Direktori Mahasiswa,
     * jadi keduanya tidak boleh masuk daftar ini. Jangan ditambahkan kembali.
     */
    public const PENGELOLA_CV = [
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
        'dosen',
        'dpm',
        'gpm',
    ];

    /**
     * Membuka / mengunduh sebuah CV.
     *
     * $cvProfile bisa berupa instance yang belum tersimpan (mahasiswa yang belum
     * pernah mengisi CV Builder) — yang dibaca hanya user_id, yang selalu terisi.
     */
    public function view(User $user, CvProfile $cvProfile): bool
    {
        if ((int) $cvProfile->user_id === (int) $user->getKey()) {
            return true;
        }

        return $user->hasAnyRole(self::PENGELOLA_CV);
    }
}
