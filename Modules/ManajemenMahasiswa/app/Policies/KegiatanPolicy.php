<?php

namespace Modules\ManajemenMahasiswa\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;

/**
 * "Kegiatan mana yang boleh diubah" di ketiga subbab Manajemen Kegiatan
 * (Rencana Proker, Pelaksanaan, Laporan & Arsip — satu baris mk_kegiatan yang
 * berpindah status).
 *
 * Lapis kedua di belakang `role:` middleware di routes/web.php: route menentukan
 * AKSI apa yang boleh dilakukan sebuah role, Policy ini menentukan KEGIATAN MANA.
 * Keduanya harus lolos, sehingga Policy tidak pernah memperluas hak sebuah role —
 * mis. staff_himpunan tetap tidak bisa menghapus walau ia terdaftar pengelola,
 * dan ketua bidang/unit tidak bisa masuk ke kegiatan bidang/unit lain.
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
        ...RoleHimpunan::INTI, ...RoleHimpunan::PIMPINAN_BIDANG,
        'superadmin', 'admin_kemahasiswaan',
    ];

    /**
     * Admin — boleh mengedit & menghapus SEMUA kegiatan tanpa perlu tercantum di
     * daftar pengelola, sekaligus mengatur Akses Kelola. Penyelamat bila
     * pemiliknya sudah lulus/berganti jabatan.
     *
     * ketua_himpunan tidak di sini karena tidak mengatur Akses Kelola — lihat
     * KETUA_SEMUA.
     */
    public const PENGELOLA_SEMUA = [
        'superadmin', 'admin_kemahasiswaan',
    ];

    /**
     * Boleh MENINJAU dan MENGHAPUS semua kegiatan di ketiga subbab: Ketua Himpunan
     * sebagai penanggung jawab utama himpunan — mis. proker bidang batal, atau
     * KABID-nya berhalangan meninjau.
     *
     * Hak hapus dibuka 30 Okt 2026 (keputusan Surya, S-67), membatalkan larangan
     * 28 Sep 2026 (S-21). Disadari: hapus itu permanen (banner, surat, foto &
     * dokumen di storage ikut hilang, tanpa tempat sampah) dan tanpa audit log.
     * Hak MENGEDIT dicabut 11 Nov 2026 (S-82): KAHIM/WAKAHIM pengawas seperti KABID.
     *
     * WAKAHIM disetarakan dengan KAHIM (2 Nov 2026).
     */
    public const KETUA_SEMUA = RoleHimpunan::INTI;

    /**
     * Satu-satunya role yang bisa ditambahkan di "Akses Kelola" — hak mengedit,
     * bukan membuat maupun menghapus.
     */
    public const ROLE_PENGELOLA = ['staff_himpunan'];

    /**
     * Ketua Pelaksana wajib staff himpunan (keputusan Surya, 29 Okt 2026), karena
     * ia otomatis ikut mengelola kegiatannya (lihat update()).
     *
     * Dipertahankan 30 Okt 2026 setelah telusur IG HIMASKOM: tidak ditemukan ketua
     * pelaksana yang bukan pengurus. Ketua yang kemudian keluar himpunan atau lulus
     * tetap tercatat (idCalonKetuaPelaksana), tetapi hak editnya putus sendiri
     * begitu role staff_himpunan-nya dicabut, karena update() memeriksa role saat itu.
     */
    public const ROLE_KETUA_PELAKSANA = ['staff_himpunan'];

    public const PESAN_KETUA_PELAKSANA = 'Ketua pelaksana harus staff himpunan. Pilih ulang dari daftar.';

    public const PESAN_KETUA_SEBIDANG = 'Ketua pelaksana harus staff himpunan dari bidang/unit pemegang proker ini. Pilih ulang dari daftar.';

    /**
     * Pemegang jabatan ini TIDAK PERNAH bisa ditambahkan di "Akses Kelola".
     *
     * Satu proker dipegang satu bidang/unit (hasil telusur 2.100 postingan IG
     * HIMASKOM 2016–2026: antarbidang praktis tidak berkolaborasi), dan pemiliknya
     * adalah ketua bidang/unit itu sendiri. Ketua bidang/unit lain tidak boleh
     * ikut mengedit apalagi menghapus. Ketua Himpunan juga tidak perlu dicantumkan
     * karena hak editnya sudah datang dari KETUA_SEMUA. Dicek ke role, bukan ke
     * data divisi, karena role bisa menumpuk: akun yang merangkap
     * staff_himpunan + ketua_unit tetap ditolak.
     */
    public const JABATAN_KETUA = [...RoleHimpunan::INTI, ...RoleHimpunan::PIMPINAN_BIDANG];

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
        ...RoleHimpunan::POSISI,
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
     *
     * $bidangId (S-72, Rencana Proker & Pelaksanaan): hanya staff bidang/unit itu.
     */
    public static function idCalonKetuaPelaksana(?int $ketuaTersimpan = null, ?int $bidangId = null): array
    {
        $ids = Student::whereHas('user', fn ($q) => $q->role(self::ROLE_KETUA_PELAKSANA)
                ->when($bidangId, fn ($u) => $u->whereHas('pengurusBidang', fn ($pb) => $pb->where('bidang_id', $bidangId))))
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
     * Ketua Pelaksana wajib staff bidang/unit pemegang proker (keputusan Surya,
     * 3 Nov 2026, S-72). Dasarnya telusur Transparansi Penilaian kabinet 2024: 84 dari
     * 86 Penanggung Jawab tercantum di bidang pemegang proker dan tidak ada yang
     * terbukti dari bidang lain. Staff bidang lain juga tidak bisa membuka prokernya
     * (view()). Ketua tersimpan tetap lolos selama bidang kegiatannya tidak diganti.
     */
    public static function aturanKetuaSebidang(?Kegiatan $kegiatan, ?int $bidangId): In
    {
        // Draft Kegiatan Prodi tanpa bidang (S-73): belum ada staff sebidang untuk dipilih.
        if (!$bidangId) {
            return Rule::in($kegiatan && $kegiatan->bidangs()->doesntExist()
                ? array_filter([$kegiatan->ketua_pelaksana_id])
                : []);
        }

        $bidangTetap = $kegiatan && $bidangId
            && $kegiatan->bidangs()->where('mk_bidang.id', $bidangId)->exists();

        return Rule::in(self::idCalonKetuaPelaksana(
            $bidangTetap ? $kegiatan->ketua_pelaksana_id : null,
            $bidangId,
        ));
    }

    /**
     * Melihat detail kegiatan di Rencana Proker & Pelaksanaan.
     *
     * KAHIM, WAKAHIM, admin, dan pengawas (DPM/GPM/Ketua Departemen) melihat semua
     * bidang/unit. KABID/WAKABID/KANIT/WAKANIT dan Staff hanya kegiatan yang diset ke
     * bidang/unitnya (pivot mk_kegiatan_bidang). Kegiatan tanpa bidang, termasuk
     * proker Prodi yang belum diberi bidang, hanya terlihat oleh kelompok pertama.
     * Sinkron dengan Kegiatan::scopeTerlihatOleh().
     */
    public function view(User $user, Kegiatan $kegiatan): bool
    {
        return RoleHimpunan::bisaLihatSemuaProker($user)
            || $this->bidangCocok($user, $kegiatan);
    }

    /**
     * MENGISI DATA kegiatan (form edit) di ketiga subbab: ketua pelaksana dan staff
     * yang ia tambahkan di Akses Kelola, ditambah admin sebagai penyelamat.
     *
     * Sejak S-82 (11 Nov 2026, keputusan Surya atas masukan Arlo): KABID, WAKABID,
     * KANIT, WAKANIT, KAHIM, dan WAKAHIM hanya mendelegasikan. Mereka membuat proker
     * dan menunjuk ketua pelaksana, lalu memantau dan meninjau (tinjau()); data yang
     * salah diperbaiki ketua pelaksana. Ini menggantikan hak edit KETUA_SEMUA dan
     * pimpinan bidang (S-67) serta larangan staff mengedit Arsip (S-75): ketua
     * pelaksana kini juga yang memperbaiki data di Laporan & Arsip.
     *
     * Ketua Pelaksana tidak perlu dicantumkan di Akses Kelola (29 Okt 2026); haknya
     * dihitung dari ketua_pelaksana_id, jadi begitu diganti, akses orang sebelumnya
     * ikut lepas. Keduanya harus lolos bisaJadiPengelola() (staff, bukan pimpinan).
     *
     * Kunci selama ditinjau (Menunggu Tinjauan) ditegakkan di controller, bukan di sini.
     */
    public function update(User $user, Kegiatan $kegiatan): bool
    {
        if ($user->hasAnyRole(self::PENGELOLA_SEMUA)) {
            return true;
        }

        return $this->view($user, $kegiatan)
            && self::bisaJadiPengelola($user)
            && ($this->ketuaPelaksana($user, $kegiatan)
                || $kegiatan->pengelola()->whereKey($user->getKey())->exists());
    }

    /**
     * MENINJAU (S-82): menyetujui kiriman ketua pelaksana — Rencana diteruskan ke
     * Pelaksanaan, Pelaksanaan diunggah ke Arsip — atau mengembalikannya dengan
     * catatan. Admin, KAHIM/WAKAHIM (semua bidang), pembuat proker, dan pimpinan
     * bidang/unit pemegangnya. Sama dengan pemegang hak edit sebelum S-82, minus staff.
     */
    public function tinjau(User $user, Kegiatan $kegiatan): bool
    {
        if ($user->hasAnyRole([...self::PENGELOLA_SEMUA, ...self::KETUA_SEMUA])) {
            return true;
        }

        return $this->view($user, $kegiatan)
            && ($this->pemilik($user, $kegiatan) || $this->pimpinanBidangnya($user, $kegiatan));
    }

    /** Menekan "Kirim untuk Ditinjau" — hanya ketua pelaksana kegiatan itu (S-82). */
    public function kirimTinjauan(User $user, Kegiatan $kegiatan): bool
    {
        return $this->view($user, $kegiatan)
            && self::bisaJadiPengelola($user)
            && $this->ketuaPelaksana($user, $kegiatan);
    }

    /**
     * Mengganti ketua pelaksana di Rencana & Pelaksanaan (S-82): satu-satunya isian
     * yang masih bisa diubah KABID setelah proker dibuat (salah pilih, ketua mundur).
     * Pemegangnya sama dengan peninjau. Ketua pelaksana tidak bisa mengganti dirinya.
     */
    public function gantiKetua(User $user, Kegiatan $kegiatan): bool
    {
        return $this->tinjau($user, $kegiatan);
    }

    private function ketuaPelaksana(User $user, Kegiatan $kegiatan): bool
    {
        return $kegiatan->ketua_pelaksana_id !== null
            && (int) $kegiatan->ketuaPelaksana?->user_id === (int) $user->getKey();
    }

    /**
     * Menghapus hanya admin, KAHIM/WAKAHIM (KETUA_SEMUA), pembuat, dan pimpinan
     * bidang/unit pemilik kegiatan. Pengelola tambahan (staff_himpunan) tidak
     * pernah boleh menghapus.
     */
    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        if ($user->hasAnyRole([...self::PENGELOLA_SEMUA, ...self::KETUA_SEMUA])) {
            return true;
        }

        return $this->view($user, $kegiatan)
            && ($this->pemilik($user, $kegiatan) || $this->pimpinanBidangnya($user, $kegiatan));
    }

    /**
     * Mengubah daftar pengelola (bagian "Akses Kelola" di form): admin dan ketua
     * pelaksana (S-82) — ketua pelaksana yang mengatur siapa staff sebidang yang ikut
     * membantu mengisi. Pengelola yang ditunjuk tidak bisa menunjuk orang lain lagi.
     * KABID/KAHIM tidak lagi membuka form edit, jadi tidak mengatur akses.
     */
    public function aturAkses(User $user, Kegiatan $kegiatan): bool
    {
        if ($user->hasAnyRole(self::PENGELOLA_SEMUA)) {
            return true;
        }

        return $this->view($user, $kegiatan)
            && self::bisaJadiPengelola($user)
            && $this->ketuaPelaksana($user, $kegiatan);
    }

    private function pemilik(User $user, Kegiatan $kegiatan): bool
    {
        return (int) $kegiatan->user_id === (int) $user->getKey()
            && $user->hasAnyRole(self::PEMBUAT_PROKER);
    }

    /** KABID/WAKABID/KANIT/WAKANIT dari bidang/unit yang sama dengan kegiatan. */
    private function pimpinanBidangnya(User $user, Kegiatan $kegiatan): bool
    {
        return RoleHimpunan::isPimpinanBidang($user)
            && $this->bidangCocok($user, $kegiatan);
    }

    /** Apakah bidang/unit user tercantum di pivot bidang kegiatan. */
    private function bidangCocok(User $user, Kegiatan $kegiatan): bool
    {
        $bidangId = RoleHimpunan::bidangId($user);
        if (!$bidangId || !$user->hasAnyRole(RoleHimpunan::BERBIDANG)) {
            return false;
        }

        return $kegiatan->relationLoaded('bidangs')
            ? $kegiatan->bidangs->contains('id', $bidangId)
            : $kegiatan->bidangs()->where('mk_bidang.id', $bidangId)->exists();
    }
}
