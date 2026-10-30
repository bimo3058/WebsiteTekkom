<?php

namespace Modules\ManajemenMahasiswa\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
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
     * Ketua Pelaksana wajib staff himpunan (keputusan Surya, 29 Okt 2026), karena
     * ia otomatis ikut mengelola kegiatannya (lihat update()).
     */
    public const ROLE_KETUA_PELAKSANA = ['staff_himpunan'];

    public const PESAN_KETUA_PELAKSANA = 'Ketua pelaksana harus staff himpunan. Pilih ulang dari daftar.';

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
     * Boleh MELIHAT anggaran dan mengunduh dokumen kegiatan (proposal, RAB, surat,
     * LPJ) di ketiga subbab — sama dengan role yang boleh membuka Rencana Proker &
     * Pelaksanaan (middleware di routes/web.php).
     *
     * Daftar putih, bukan "semua kecuali mahasiswa/alumni": role menumpuk, jadi
     * mahasiswa yang merangkap asprak/praktikan dan seluruh akun dosen dulu ikut
     * bisa mengunduh LPJ yang memuat NIM, tanda tangan, dan nomor HP.
     */
    public const LIHAT_INTERNAL = [
        'superadmin', 'admin_kemahasiswaan', 'dpm', 'gpm', 'ketua_departemen',
        'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan',
    ];

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
     * Id Student yang boleh dipilih sebagai Ketua Pelaksana: pemegang
     * ROLE_KETUA_PELAKSANA, ditambah ketua yang sudah tersimpan di kegiatan itu.
     *
     * Pengecualian ketua tersimpan disengaja: 41 dari 46 kegiatan saat aturan ini
     * dibuat berketua mahasiswa non-staff (16 sudah di Arsip). Tanpa pengecualian,
     * kegiatan lama tidak bisa disimpan ulang sebelum ketuanya diganti, padahal
     * arsip harus mencatat ketua yang sebenarnya. Begitu diganti, aturan berlaku.
     */
    public static function idCalonKetuaPelaksana(?int $ketuaTersimpan = null): array
    {
        $ids = Student::whereHas('user', fn ($q) => $q->role(self::ROLE_KETUA_PELAKSANA))
            ->pluck('id')->all();

        if ($ketuaTersimpan) {
            $ids[] = $ketuaTersimpan;
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public static function aturanKetuaPelaksana(?Kegiatan $kegiatan = null): In
    {
        return Rule::in(self::idCalonKetuaPelaksana($kegiatan?->ketua_pelaksana_id));
    }

    /**
     * Edit & simpan, termasuk Ajukan Proker dan Unggah ke Arsip (keduanya
     * mengubah status kegiatan). Hanya pembuat, admin, Ketua Himpunan (jalur
     * darurat), dan staff himpunan yang ditambahkan di Akses Kelola.
     *
     * Ketua Pelaksana otomatis dianggap pengelola tanpa perlu dicantumkan di Akses
     * Kelola (keputusan Surya, 29 Okt 2026), dengan syarat yang sama persis:
     * hanya edit, dan hanya bila akunnya lolos bisaJadiPengelola() + route `role:`.
     * Tidak ada baris pengelola yang disimpan, jadi begitu ketua pelaksana diganti,
     * akses orang sebelumnya ikut lepas.
     */
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        return $user->hasAnyRole([...self::PENGELOLA_SEMUA, ...self::EDIT_SEMUA])
            || $this->pemilik($user, $kegiatan)
            || (self::bisaJadiPengelola($user)
                && ($this->ketuaPelaksana($user, $kegiatan)
                    || $kegiatan->pengelola()->whereKey($user->getKey())->exists()));
    }

    private function ketuaPelaksana(User $user, Kegiatan $kegiatan): bool
    {
        return $kegiatan->ketua_pelaksana_id !== null
            && (int) $kegiatan->ketuaPelaksana?->user_id === (int) $user->getKey();
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
