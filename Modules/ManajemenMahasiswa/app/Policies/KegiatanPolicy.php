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
 * mis. staff_himpunan tetap tidak bisa menghapus walau ia terdaftar pengelola,
 * dan ketua bidang/unit tidak bisa masuk ke kegiatan buatan orang lain.
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
        'superadmin', 'admin_kemahasiswaan',
    ];

    /**
     * Admin — boleh mengedit & menghapus SEMUA kegiatan tanpa perlu tercantum di
     * daftar pengelola. Penyelamat bila pemiliknya sudah lulus/berganti jabatan.
     *
     * ketua_himpunan sengaja TIDAK termasuk — lihat EDIT_SEMUA.
     */
    public const PENGELOLA_SEMUA = [
        'superadmin', 'admin_kemahasiswaan',
    ];

    /**
     * Boleh MENGEDIT semua kegiatan, tetapi TIDAK menghapus: jalur darurat Ketua
     * Himpunan sebagai penanggung jawab utama himpunan — mis. data proker salah
     * menjelang acara sementara ketua bidangnya berhalangan, atau pembuatnya
     * sudah lulus/berganti periode.
     *
     * Tanpa hak hapus karena menghapus itu permanen (baris kegiatan beserta
     * banner, surat, foto & dokumennya di storage ikut hilang, tanpa tempat
     * sampah) dan keadaan darurat tidak pernah butuh penghapusan: data yang salah
     * cukup dibetulkan. Membatalkan proker adalah keputusan bidangnya, jadi
     * penghapusan tetap milik pembuat & admin.
     */
    public const EDIT_SEMUA = ['ketua_himpunan'];

    /**
     * Satu-satunya role yang bisa ditambahkan di "Akses Kelola" — hak mengedit,
     * bukan membuat maupun menghapus.
     */
    public const ROLE_PENGELOLA = ['staff_himpunan'];

    /**
     * Pemegang jabatan ini TIDAK PERNAH bisa ditambahkan di "Akses Kelola".
     *
     * Satu proker dipegang satu bidang/unit (hasil telusur 2.100 postingan IG
     * HIMASKOM 2016–2026: antarbidang praktis tidak berkolaborasi), dan pemiliknya
     * adalah ketua bidang/unit itu sendiri. Ketua bidang/unit lain tidak boleh
     * ikut mengedit apalagi menghapus. Ketua Himpunan juga tidak perlu dicantumkan
     * karena hak editnya sudah datang dari EDIT_SEMUA. Dicek ke role, bukan ke
     * data divisi, karena role bisa menumpuk: akun yang merangkap
     * staff_himpunan + ketua_unit tetap ditolak.
     */
    public const JABATAN_KETUA = ['ketua_himpunan', 'ketua_bidang', 'ketua_unit'];

    /**
     * Apakah akun ini memenuhi syarat sebagai pengelola tambahan.
     *
     * Dipakai dua kali: saat menawarkan calon di form (PengelolaKegiatanService)
     * dan saat menegakkan hak di update() — yang kedua supaya baris pengelola lama
     * (sebelum aturan ini) atau orang yang belakangan diangkat jadi ketua bidang
     * langsung kehilangan aksesnya tanpa perlu menyimpan ulang form.
     */
    public static function bisaJadiPengelola(User $user): bool
    {
        return $user->hasAnyRole(self::ROLE_PENGELOLA)
            && !$user->hasAnyRole([...self::JABATAN_KETUA, ...self::PENGELOLA_SEMUA]);
    }

    /**
     * Edit & simpan, termasuk Ajukan Proker dan Unggah ke Arsip (keduanya
     * mengubah status kegiatan). Hanya pembuat, admin, Ketua Himpunan (jalur
     * darurat), dan staff himpunan yang ditambahkan di Akses Kelola.
     */
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole([...self::PENGELOLA_SEMUA, ...self::EDIT_SEMUA])
            || $this->pemilik($user, $kegiatan)
            || (self::bisaJadiPengelola($user)
                && $kegiatan->pengelola()->whereKey($user->getKey())->exists());
    }

    /**
     * Menghapus hanya pembuat & admin. Ketua Himpunan (EDIT_SEMUA) dan pengelola
     * tambahan (staff_himpunan) tidak pernah boleh menghapus.
     */
    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole(self::PENGELOLA_SEMUA)
            || $this->pemilik($user, $kegiatan);
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
