<?php

namespace Modules\ManajemenMahasiswa\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;

/**
 * Sumber tunggal role jabatan himpunan: daftar role, label tampilan, dan bidang/unit
 * pemegangnya.
 *
 * Slug role di database sengaja tidak diubah (ketua_himpunan, ketua_bidang, ...);
 * yang berganti hanya labelnya (KAHIM, KABID, ...).
 *
 * Kesetaraan hak akses:
 *  - KAHIM = WAKAHIM, berlaku untuk seluruh himpunan.
 *  - KABID = WAKABID = KANIT = WAKANIT, terkunci ke bidang/unitnya sendiri.
 *  - Staff Himpunan terkunci ke bidang/unitnya sendiri.
 * Bidang/unit pemegang role disimpan di mk_pengurus_bidang (relasi User::pengurusBidang,
 * didaftarkan di ManajemenMahasiswaServiceProvider).
 */
class RoleHimpunan
{
    public const KAHIM   = 'ketua_himpunan';
    public const WAKAHIM = 'wakil_ketua_himpunan';
    public const KABID   = 'ketua_bidang';
    public const WAKABID = 'wakil_ketua_bidang';
    public const KANIT   = 'ketua_unit';
    public const WAKANIT = 'wakil_ketua_unit';
    public const STAFF   = 'staff_himpunan';

    /** Pimpinan inti himpunan — akses seluruh bidang/unit. */
    public const INTI = [self::KAHIM, self::WAKAHIM];

    /** Role yang wajib memegang Bidang (jenis = bidang). */
    public const ROLE_BIDANG = [self::KABID, self::WAKABID];

    /** Role yang wajib memegang Unit (jenis = unit). */
    public const ROLE_UNIT = [self::KANIT, self::WAKANIT];

    /** Pimpinan bidang/unit — hak akses setara, terbatas pada bidang/unitnya. */
    public const PIMPINAN_BIDANG = [self::KABID, self::WAKABID, self::KANIT, self::WAKANIT];

    /** Role yang wajib terhubung ke satu bidang/unit. */
    public const BERBIDANG = [self::KABID, self::WAKABID, self::KANIT, self::WAKANIT, self::STAFF];

    /** Semua role jabatan himpunan (tanpa role payung pengurus_himpunan). */
    public const POSISI = [
        self::KAHIM, self::WAKAHIM,
        self::KABID, self::WAKABID,
        self::KANIT, self::WAKANIT,
        self::STAFF,
    ];

    /** Role di luar himpunan yang boleh melihat proker semua bidang/unit. */
    public const PENGAWAS_PROKER = [
        'superadmin', 'admin', 'admin_kemahasiswaan', 'dpm', 'gpm', 'ketua_departemen',
    ];

    public const PESAN_BIDANG_TERKUNCI = 'Anda hanya bisa memilih bidang/unit Anda sendiri. Bila bidang/unit Anda belum diatur, minta KAHIM/WAKAHIM atau admin mengaturnya di Manajemen Pengguna.';

    public const LABEL = [
        self::KAHIM   => 'KAHIM',
        self::WAKAHIM => 'WAKAHIM',
        self::KABID   => 'KABID',
        self::WAKABID => 'WAKABID',
        self::KANIT   => 'KANIT',
        self::WAKANIT => 'WAKANIT',
        self::STAFF   => 'Staff Himpunan',
    ];

    /**
     * Label tampilan sebuah role jabatan himpunan, mis. 'ketua_bidang' -> 'KABID'.
     * Role lain dikembalikan dalam bentuk "Judul Kata".
     */
    public static function label(string $role): string
    {
        return self::LABEL[$role] ?? ucwords(str_replace('_', ' ', $role));
    }

    /**
     * Label role lengkap dengan bidang/unit pemegangnya, mis. 'KABID / RISTEK'.
     * KAHIM/WAKAHIM, role non-himpunan, atau user yang belum diset bidangnya
     * hanya mendapat label singkat.
     */
    public static function labelLengkap(User $user, string $role): string
    {
        $label = self::label($role);

        if (!in_array($role, self::BERBIDANG, true)) {
            return $label;
        }

        $namaBidang = $user->pengurusBidang?->bidang?->nama_bidang;

        return $namaBidang ? "{$label} / {$namaBidang}" : $label;
    }

    /** Apakah user KAHIM atau WAKAHIM. */
    public static function isInti(User $user): bool
    {
        return $user->hasAnyRole(self::INTI);
    }

    /** Apakah user KABID, WAKABID, KANIT, atau WAKANIT. */
    public static function isPimpinanBidang(User $user): bool
    {
        return $user->hasAnyRole(self::PIMPINAN_BIDANG);
    }

    /**
     * ID bidang/unit tempat user bertugas, atau null bila belum diset.
     * Relasi pengurusBidang ter-cache pada instance user, jadi aman dipanggil
     * berulang dalam satu request.
     */
    public static function bidangId(User $user): ?int
    {
        return $user->pengurusBidang?->bidang_id;
    }

    /**
     * Apakah user boleh melihat proker dari semua bidang/unit (KAHIM, WAKAHIM,
     * admin, dan pengawas seperti DPM/GPM/Ketua Departemen).
     */
    public static function bisaLihatSemuaProker(User $user): bool
    {
        return $user->hasAnyRole(array_merge(self::INTI, self::PENGAWAS_PROKER));
    }

    /**
     * Apakah akses proker user dibatasi ke bidang/unitnya sendiri
     * (pemegang role berbidang yang bukan inti/pengawas).
     */
    public static function terbatasBidang(User $user): bool
    {
        return !self::bisaLihatSemuaProker($user) && $user->hasAnyRole(self::BERBIDANG);
    }

    /**
     * Bidang/unit yang boleh dipilih user di filter & form kegiatan: semua untuk
     * yang tidak terbatas, hanya bidang/unitnya sendiri untuk yang terbatas
     * (kosong bila belum diset).
     *
     * @return Collection<int, Bidang>
     */
    public static function daftarBidangUntuk(User $user): Collection
    {
        if (!self::terbatasBidang($user)) {
            return Bidang::orderBy('nama_bidang')->get();
        }

        return Bidang::whereKey(self::bidangId($user))->get();
    }

    /**
     * Data kolom Bidang/Unit di form Rencana Proker & Pelaksanaan (S-72): semua
     * bidang/unit selalu tampil supaya form semua role sama. Bagi pemegang role
     * berbidang, hanya bidang/unitnya yang terpilih dan sisanya nonaktif.
     *
     * @return array{bidangList: Collection<int, Bidang>, bidangTerkunci: bool, bidangMilikId: ?int}
     */
    public static function dataFormKegiatan(User $user): array
    {
        $terkunci = self::terbatasBidang($user);

        return [
            'bidangList'     => Bidang::orderBy('nama_bidang')->get(),
            'bidangTerkunci' => $terkunci,
            'bidangMilikId'  => $terkunci ? self::bidangId($user) : null,
        ];
    }

    /**
     * Tegakkan isian terkunci di server (S-72): bagi pemegang role berbidang, bidang
     * selalu bidang/unitnya dan Kegiatan Himpunan selalu ikut. Kegiatan Prodi tetap
     * pilihan mereka (proker kerja sama dengan prodi). Input form diabaikan, jadi
     * form yang dimanipulasi pun tidak bisa mengubahnya.
     */
    public static function kunciIsianKegiatan(Request $request, User $user): void
    {
        if (!self::terbatasBidang($user)) {
            return;
        }

        $kategoriHimpunan = KategoriKegiatan::where('nama_kategori', KategoriKegiatan::NAMA_HIMPUNAN)->value('id');

        $request->merge([
            'bidang_id'            => array_values(array_filter([self::bidangId($user)])),
            'kategori_kegiatan_id' => array_values(array_unique(array_filter([
                ...(array) $request->input('kategori_kegiatan_id', []),
                $kategoriHimpunan,
            ]))),
        ]);
    }

    /**
     * Aturan validasi tiap elemen `bidang_id[]` di form kegiatan: pemegang role
     * berbidang hanya boleh memilih bidang/unitnya sendiri (juga bila form
     * dimanipulasi); KAHIM/WAKAHIM & admin bebas memilih bidang mana pun.
     * Pesan error untuk aturan `in`: PESAN_BIDANG_TERKUNCI.
     *
     * @return array<int, mixed>
     */
    public static function aturanBidangKegiatan(User $user): array
    {
        return self::terbatasBidang($user)
            ? ['integer', Rule::in(array_filter([self::bidangId($user)]))]
            : ['integer', 'exists:mk_bidang,id'];
    }
}
