<?php

namespace Modules\ManajemenMahasiswa\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Periode kepengurusan (kabinet) HIMASKOM untuk filter Periode Kepengurusan di
 * Rencana Proker, Pelaksanaan Kegiatan, dan Laporan & Arsip (S-62).
 *
 * Periode ditulis "2025/2026" (S-63) dan dikunci dengan tahun awalnya: kabinet
 * yang dilantik pada 2025 = periode 2025/2026. Sebuah kegiatan masuk kabinet yang
 * sedang menjabat pada tanggal mulainya; periode berakhir sehari sebelum kabinet
 * berikutnya mulai.
 *
 * Kabinet tidak berganti di tanggal yang sama tiap tahun (Agu, Sep, Jul, Jun, Mei),
 * jadi kabinet yang sudah lewat dicatat tanggal mulainya di TERCATAT. Tanggal itu
 * PERKIRAAN dari tanda tangan caption IG HIMASKOM (postingan terakhir kabinet lama
 * vs pertama kabinet baru, riset 27 Okt 2026) — ganti dengan tanggal serah terima
 * jabatan bila ada SK/berita acaranya.
 *
 * Periode sesudah kabinet terakhir yang tercatat dibuat otomatis mengikuti tahun
 * berjalan, dengan anggapan kabinet berganti pada tanggal & bulan yang sama dengan
 * kabinet terakhir. Bila tanggal pelantikan sebenarnya diketahui, cukup tambahkan
 * barisnya di TERCATAT.
 */
class PeriodeKepengurusan
{
    /** tahun awal periode => tanggal kabinet mulai menjabat. Urut dari terlama. */
    public const TERCATAT = [
        2021 => '2021-10-01', // Kolaborasi Karsa Wujudkan Karya
        2022 => '2022-08-01', // Satu Asa Sejuta Cerita
        2023 => '2023-09-11', // Berdikari Unggul dan Berarti Bersama
        2024 => '2024-07-19', // Titik Bangkit
        2025 => '2025-06-01', // Sinergi Pradipa
        2026 => '2026-05-07', // Eskalasi Karya
    ];

    public static function ada(mixed $tahun): bool
    {
        return is_numeric($tahun) && (int) $tahun <= 9998 && self::mulai((int) $tahun) !== null;
    }

    /** Contoh: "2026/2027". */
    public static function label(int $tahun): string
    {
        return $tahun . '/' . ($tahun + 1);
    }

    /** Tanggal kabinet periode ini mulai menjabat (Y-m-d), atau null bila sebelum periode tertua. */
    public static function mulai(int $tahun): ?string
    {
        if (isset(self::TERCATAT[$tahun])) {
            return self::TERCATAT[$tahun];
        }

        $terakhir = array_key_last(self::TERCATAT);

        return $tahun > $terakhir
            ? $tahun . substr(self::TERCATAT[$terakhir], 4)
            : null;
    }

    /** @return array{0: string, 1: string} tanggal mulai & selesai (Y-m-d) */
    public static function rentang(int $tahun): array
    {
        return [
            self::mulai($tahun),
            Carbon::parse(self::mulai($tahun + 1))->subDay()->toDateString(),
        ];
    }

    /** Periode yang berjalan pada tanggal itu, atau null bila sebelum periode tertua. */
    public static function untukTanggal(?CarbonInterface $tanggal): ?int
    {
        if (!$tanggal) {
            return null;
        }

        $tahun = $tanggal->year;
        $mulai = self::mulai($tahun);

        if ($mulai !== null && $tanggal->toDateString() >= $mulai) {
            return $tahun;
        }

        return self::mulai($tahun - 1) !== null ? $tahun - 1 : null;
    }

    /** Periode yang berjalan hari ini. */
    public static function sekarang(): int
    {
        return self::untukTanggal(now());
    }
}
