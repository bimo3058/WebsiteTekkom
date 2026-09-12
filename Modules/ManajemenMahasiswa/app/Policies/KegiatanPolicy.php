<?php

namespace Modules\ManajemenMahasiswa\Policies;

use App\Models\User;
use Modules\ManajemenMahasiswa\Models\Kegiatan;

/**
 * "Kegiatan mana yang boleh diubah" di ketiga subbab Manajemen Kegiatan
 * (Rencana Proker, Pelaksanaan, Laporan & Arsip — satu baris mk_kegiatan yang
 * berpindah status).
 *
 * Lapis kedua di belakang `role:` middleware di routes/web.php: route menentukan
 * AKSI apa yang boleh dilakukan sebuah role, Policy ini menentukan KEGIATAN MANA.
 * Keduanya harus lolos, sehingga Policy tidak pernah memperluas hak sebuah role —
 * mis. staff_himpunan tetap tidak bisa menghapus walau tercatat `boleh_hapus`.
 *
 * Semua pengecekan role memakai daftar putih hasAnyRole(), karena di modul ini
 * satu akun bisa memegang beberapa role sekaligus.
 */
class KegiatanPolicy
{
    /**
     * Role yang boleh membuat proker — sinkron dengan route `proker.create`/`proker.store`.
     * Pembuat hanya dianggap pemilik selama ia masih memegang salah satunya.
     */
    public const PEMBUAT_PROKER = [
        'ketua_bidang', 'ketua_unit', 'ketua_himpunan',
        'superadmin', 'admin', 'admin_kemahasiswaan',
    ];

    /**
     * Boleh mengelola SEMUA kegiatan tanpa perlu tercantum di daftar pengelola —
     * penyelamat bila pemiliknya sudah lulus/berganti jabatan atau lupa
     * menambahkan orang.
     */
    public const PENGELOLA_SEMUA = [
        'superadmin', 'admin', 'admin_kemahasiswaan', 'ketua_himpunan',
    ];

    /**
     * Edit & simpan, termasuk Ajukan Proker dan Unggah ke Arsip (keduanya
     * mengubah status kegiatan).
     */
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole(self::PENGELOLA_SEMUA)
            || $this->pemilik($user, $kegiatan)
            || $kegiatan->pengelola()->whereKey($user->getKey())->exists();
    }

    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole(self::PENGELOLA_SEMUA)
            || $this->pemilik($user, $kegiatan)
            || $kegiatan->pengelola()
                ->whereKey($user->getKey())
                ->wherePivot('boleh_hapus', true)
                ->exists();
    }

    /**
     * Mengubah daftar pengelola (bagian "Akses Kelola" di form). Pengelola yang
     * ditunjuk tidak bisa menunjuk orang lain lagi.
     */
    public function aturAkses(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole(self::PENGELOLA_SEMUA)
            || $this->pemilik($user, $kegiatan);
    }

    private function pemilik(User $user, Kegiatan $kegiatan): bool
    {
        return (int) $kegiatan->user_id === (int) $user->getKey()
            && $user->hasAnyRole(self::PEMBUAT_PROKER);
    }
}
