<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Pengaduan;

/**
 * Statistik halaman Layanan Pengaduan: bahan evaluasi mutu per periode untuk
 * GPM (pengendali mutu prodi, Peraturan Rektor Undip 18/2024) dan pengelola lain —
 * tren per bulan dan kategori. Hanya angka gabungan.
 *
 * Volume pengaduan prodi kecil (puluhan–ratusan per tahun), jadi agregasi dikerjakan
 * di PHP atas satu kueri, tanpa SQL JSON khusus per metrik.
 */
class PengaduanStatistikService
{
    public const PERIODE = [
        '1_bulan' => '1 bulan terakhir',
        '6_bulan' => '6 bulan terakhir',
        '1_tahun' => '1 tahun terakhir',
        'semua'   => 'Semua waktu',
        'khusus'  => 'Rentang khusus',
    ];

    public const PERIODE_BAWAAN = '6_bulan';

    /** Format isian Dari/Sampai pada rentang khusus (input type="date"). */
    private const POLA_TANGGAL = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';

    /** Bentuk bulan (Y-m) dari tautan lama, sebelum isian berganti tanggal. */
    private const POLA_BULAN = '/^\d{4}-(0[1-9]|1[0-2])$/';

    /**
     * Rentang yang jarak awal–akhirnya paling banyak sekian hari digambar per hari,
     * lebih panjang per bulan. 31 supaya "1 bulan terakhir" selalu per hari.
     */
    private const BATAS_HARIAN = 31;

    /** Panjang tiap periode dalam bulan, dihitung mundur dari hari ini. */
    private const PERIODE_BULAN = [
        '1_bulan' => 1,
        '6_bulan' => 6,
        '1_tahun' => 12,
    ];

    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private ?Collection $tiket = null;

    public function evaluasi(string $periode, ?string $dari = null, ?string $sampai = null): array
    {
        [$mulai, $selesai, $labelPeriode] = $this->rentang($periode, $dari, $sampai);
        $now = CarbonImmutable::now();

        $tiket = $this->tiket()->filter(
            fn (Pengaduan $p) => $p->created_at >= $mulai && $p->created_at <= $selesai
        );

        [$satuan, $perWaktu] = $this->perWaktu($tiket, $mulai, $selesai->min($now));

        return [
            'periode'       => $periode,
            'label_periode' => $labelPeriode,
            'mulai'         => $mulai,
            'selesai'       => $selesai,
            'total'         => $tiket->count(),
            'satuan_waktu'  => $satuan,
            'per_waktu'     => $perWaktu,
            'per_kategori'  => $this->perKategori($tiket),
        ];
    }

    /**
     * Rentang tanggal periode: 1 bulan / 6 bulan / 1 tahun terakhir dihitung mundur
     * dari hari ini; "semua" sejak pengaduan pertama; "khusus" dari awal hari Dari
     * sampai akhir hari Sampai (Y-m-d; Y-m dari tautan lama = sebulan penuh). Isian
     * khusus yang kosong/tak valid jatuh ke 6 bulan terakhir / hari ini, dan
     * Dari > Sampai ditukar.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: string}
     */
    public function rentang(string $periode, ?string $dari = null, ?string $sampai = null): array
    {
        $now = CarbonImmutable::now();

        if ($periode === 'khusus') {
            $bawaan = $now->subMonths(self::PERIODE_BULAN[self::PERIODE_BAWAAN]);
            $awal = self::isian($dari) ?? [$bawaan->startOfDay(), $bawaan->endOfDay()];
            $akhir = self::isian($sampai) ?? [$now->startOfDay(), $now->endOfDay()];
            if ($awal[0] > $akhir[0]) {
                [$awal, $akhir] = [$akhir, $awal];
            }
            [$mulai, $selesai] = [$awal[0], $akhir[1]];

            $label = self::labelTanggal($mulai);
            if (!$mulai->isSameDay($selesai)) {
                $label .= ' – ' . self::labelTanggal($selesai);
            }

            return [$mulai, $selesai, $label];
        }

        if (isset(self::PERIODE_BULAN[$periode])) {
            return [$now->subMonths(self::PERIODE_BULAN[$periode])->startOfDay(), $now, self::PERIODE[$periode]];
        }

        $pertama = $this->tiket()->min('created_at');
        $mulai = $pertama ? CarbonImmutable::parse($pertama)->startOfMonth() : $now->startOfMonth();

        return [$mulai, $now, 'Semua waktu'];
    }

    // ── Bagian ───────────────────────────────────────────────────────────

    /**
     * Kunci kategori lama digabung ke kategori utamanya (sama dengan filter Kategori
     * di Daftar Pengaduan), supaya tautan per kategori menampilkan tiket yang sama.
     *
     * @return array<int, array{kunci: string, label: string, nilai: int}> diurutkan terbanyak
     */
    private function perKategori(Collection $tiket): array
    {
        return $tiket
            ->countBy(fn (Pengaduan $p) => Pengaduan::normalizeKategori((string) $p->kategori))
            ->sortDesc()
            ->map(fn ($nilai, $kunci) => [
                'kunci' => (string) $kunci,
                'label' => Pengaduan::kategoriLabel((string) $kunci),
                'nilai' => (int) $nilai,
            ])
            ->values()
            ->all();
    }

    /**
     * Jumlah per hari (jarak ≤ BATAS_HARIAN hari) atau per bulan, termasuk yang
     * tanpa pengaduan. Batas mulai/selesai tiap titik dipotong ke rentang periode,
     * supaya daftar yang ditaut berisi tiket yang sama dengan angka batangnya.
     *
     * @return array{0: string, 1: array} ['hari'|'bulan', titik-titik grafik]
     */
    private function perWaktu(Collection $tiket, CarbonImmutable $mulai, CarbonImmutable $selesai): array
    {
        $harian = (int) $mulai->startOfDay()->diffInDays($selesai->startOfDay()) <= self::BATAS_HARIAN;
        $hitung = $tiket->countBy(fn (Pengaduan $p) => $p->created_at->format($harian ? 'Y-m-d' : 'Y-m'));

        $hasil = [];
        $titik = $harian ? $mulai->startOfDay() : $mulai->startOfMonth();
        for (; $titik <= $selesai; $titik = $harian ? $titik->addDay() : $titik->addMonth()) {
            $bulan = self::BULAN[$titik->month - 1];
            $hasil[] = [
                'label'   => $harian ? (string) $titik->day : $bulan . ' ' . $titik->format('y'),
                'lengkap' => $harian ? self::labelTanggal($titik) : $bulan . ' ' . $titik->year,
                'nilai'   => (int) ($hitung[$titik->format($harian ? 'Y-m-d' : 'Y-m')] ?? 0),
                'mulai'   => $titik->max($mulai),
                'selesai' => ($harian ? $titik->endOfDay() : $titik->endOfMonth())->min($selesai),
            ];
        }

        return [$harian ? 'hari' : 'bulan', $hasil];
    }

    // ── Pembantu ─────────────────────────────────────────────────────────

    /**
     * [awal, akhir] dari isian Dari/Sampai: sehari untuk "Y-m-d", sebulan penuh untuk
     * "Y-m" (tautan lama), atau null bila kosong/tak valid (mis. 2026-02-31).
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}|null
     */
    private static function isian(?string $nilai): ?array
    {
        if ($nilai !== null && preg_match(self::POLA_TANGGAL, $nilai)) {
            $hari = CarbonImmutable::createFromFormat('!Y-m-d', $nilai);

            return $hari->format('Y-m-d') === $nilai ? [$hari->startOfDay(), $hari->endOfDay()] : null;
        }

        if ($nilai !== null && preg_match(self::POLA_BULAN, $nilai)) {
            $bulan = CarbonImmutable::createFromFormat('!Y-m', $nilai);

            return [$bulan->startOfMonth(), $bulan->endOfMonth()];
        }

        return null;
    }

    /** "5 Okt 2026" */
    private static function labelTanggal(CarbonImmutable $tanggal): string
    {
        return $tanggal->day . ' ' . self::BULAN[$tanggal->month - 1] . ' ' . $tanggal->year;
    }

    private function tiket(): Collection
    {
        return $this->tiket ??= Pengaduan::query()
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'kategori', 'created_at']);
    }

}
