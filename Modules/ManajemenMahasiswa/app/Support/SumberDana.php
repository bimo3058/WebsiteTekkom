<?php

namespace Modules\ManajemenMahasiswa\Support;

use Modules\ManajemenMahasiswa\Models\Kegiatan;

/**
 * Anggaran kegiatan dirinci per sumber dana (S-80).
 *
 * Usulan Pak Maman (L-41): himpunan harus mengontrol anggaran. Surya memilih
 * pembagian per sumber dana, bukan per item RAB — sama dengan bagian "Sumber Dana"
 * di proposal & LPJ HIMASKOM (Recreation 2025: Dana Departemen Rp 2.500.000, Kas
 * Himpunan Rp 197.000, Kas Bidang Ristek Rp 297.000 → LPJ Rp 91.500). Hanya tiga
 * sumber itu; opsi "Lainnya" sempat ada lalu dicabut atas keputusan Surya (10 Nov 2026).
 *
 * Kolom `anggaran` tetap ada dan selalu = jumlah ketiga sumber, supaya tab Rencana
 * vs Pelaksanaan (S-70), Dashboard Analitik, dan ekspor (S-78) tetap jalan. Kegiatan
 * lama yang belum dirinci mempertahankan angka `anggaran`-nya.
 *
 * Satu sumber aturan untuk Rencana Proker, Pelaksanaan, dan Laporan & Arsip.
 */
class SumberDana
{
    /** kolom => label. Urutan = urutan di form, detail, dan ekspor. */
    public const KOLOM = [
        'dana_departemen' => 'Dana Departemen',
        'kas_himpunan'    => 'Kas Himpunan',
        'kas_bidang'      => 'Kas Bidang',
    ];

    private const MAKS = '9999999999999';

    /**
     * Aturan validasi.
     *
     * @param bool $wajib minimal satu sumber terisi (Pelaksanaan: realisasi wajib;
     *                    Rencana: ditagih saat Ajukan lewat terisi(), bukan di sini)
     */
    public static function aturan(bool $wajib): array
    {
        $kolom = array_keys(self::KOLOM);
        $aturan = [];
        foreach ($kolom as $i => $k) {
            // "Minimal satu terisi" cukup dipasang di kolom pertama — kalau di keempatnya,
            // pesan yang sama muncul empat kali.
            $lain = implode(',', array_diff($kolom, [$k]));
            $aturan[$k] = ($wajib && $i === 0 ? "nullable|required_without_all:{$lain}" : 'nullable') . '|numeric|min:0|max:' . self::MAKS;
        }

        return $aturan;
    }

    public static function pesan(): array
    {
        $pesan = [];
        foreach (self::KOLOM as $k => $label) {
            $pesan["{$k}.required_without_all"] = 'Anggaran wajib dirinci per sumber dana — isi minimal satu (Dana Departemen, Kas Himpunan, atau Kas Bidang). Isi 0 bila kegiatan tanpa biaya.';
            $pesan["{$k}.numeric"] = "{$label} harus berupa angka rupiah tanpa titik.";
            $pesan["{$k}.min"]     = "{$label} tidak boleh negatif.";
            $pesan["{$k}.max"]     = "{$label} terlalu besar.";
        }

        return $pesan;
    }

    /**
     * Kolom untuk disimpan, termasuk `anggaran` = jumlah sumber.
     *
     * @param Kegiatan|null $lama baris yang diedit — anggaran lama tanpa rincian dipertahankan
     */
    public static function kolom(array $validated, ?Kegiatan $lama = null): array
    {
        $nilai = [];
        foreach (array_keys(self::KOLOM) as $k) {
            $v = $validated[$k] ?? null;
            $nilai[$k] = ($v === null || $v === '') ? null : (float) $v;
        }

        $adaRincian = collect($nilai)->contains(fn ($v) => $v !== null);

        // Kegiatan lama (anggaran ada, rincian belum pernah ada) yang disimpan tanpa
        // rincian tetap memakai angka lamanya. Rincian yang sengaja dikosongkan → null.
        $nilai['anggaran'] = $adaRincian
            ? array_sum(array_filter($nilai, fn ($v, $k) => $v !== null && isset(self::KOLOM[$k]), ARRAY_FILTER_USE_BOTH))
            : ($lama && !self::terisi($lama) ? $lama->anggaran : null);

        return $nilai;
    }

    /** Apakah kegiatan sudah punya rincian sumber dana (minimal satu kolom terisi). */
    public static function terisi(Kegiatan $k): bool
    {
        foreach (array_keys(self::KOLOM) as $kolom) {
            if ($k->{$kolom} !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rincian untuk ditampilkan: [label => nominal] untuk kolom yang terisi.
     *
     * @return array<string, float>
     */
    public static function rincian(Kegiatan $k): array
    {
        $hasil = [];
        foreach (self::KOLOM as $kolom => $label) {
            if ($k->{$kolom} === null) {
                continue;
            }
            $hasil[$label] = (float) $k->{$kolom};
        }

        return $hasil;
    }

    /** Nilai untuk kolom input angka: tanpa ekor ".00" dari cast decimal. */
    public static function nilaiInput(?Kegiatan $k, string $kolom): ?string
    {
        $v = old($kolom, $k?->{$kolom});
        if (is_string($v) && str_contains($v, '.')) {
            $v = rtrim(rtrim($v, '0'), '.');
        }

        return $v === null ? null : (string) $v;
    }
}
