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
    ];

    public const PERIODE_BAWAAN = '6_bulan';

    /** Panjang tiap periode dalam bulan, dihitung mundur dari hari ini. */
    private const PERIODE_BULAN = [
        '1_bulan' => 1,
        '6_bulan' => 6,
        '1_tahun' => 12,
    ];

    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private ?Collection $tiket = null;

    public function evaluasi(string $periode): array
    {
        [$mulai, $selesai, $labelPeriode] = $this->rentang($periode);
        $now = CarbonImmutable::now();

        $tiket = $this->tiket()->filter(
            fn (Pengaduan $p) => $p->created_at >= $mulai && $p->created_at <= $selesai
        );

        return [
            'periode'       => $periode,
            'label_periode' => $labelPeriode,
            'total'         => $tiket->count(),
            'per_bulan'     => $this->perBulan($tiket, $mulai, $selesai->min($now)),
            'per_kategori'  => $this->perKategori($tiket),
        ];
    }

    /**
     * Rentang tanggal periode: 1 bulan / 6 bulan / 1 tahun terakhir dihitung mundur
     * dari hari ini; "semua" sejak pengaduan pertama.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: string}
     */
    public function rentang(string $periode): array
    {
        $now = CarbonImmutable::now();

        if (isset(self::PERIODE_BULAN[$periode])) {
            return [$now->subMonths(self::PERIODE_BULAN[$periode])->startOfDay(), $now, self::PERIODE[$periode]];
        }

        $pertama = $this->tiket()->min('created_at');
        $mulai = $pertama ? CarbonImmutable::parse($pertama)->startOfMonth() : $now->startOfMonth();

        return [$mulai, $now, 'Semua waktu'];
    }

    // ── Bagian ───────────────────────────────────────────────────────────

    /** @return array<int, array{label: string, nilai: int}> diurutkan terbanyak */
    private function perKategori(Collection $tiket): array
    {
        return $tiket
            ->countBy(fn (Pengaduan $p) => Pengaduan::kategoriLabel((string) $p->kategori))
            ->sortDesc()
            ->map(fn ($nilai, $label) => ['label' => (string) $label, 'nilai' => (int) $nilai])
            ->values()
            ->all();
    }

    /** Jumlah per bulan, termasuk bulan tanpa pengaduan. */
    private function perBulan(Collection $tiket, CarbonImmutable $mulai, CarbonImmutable $selesai): array
    {
        $hitung = $tiket->countBy(fn (Pengaduan $p) => $p->created_at->format('Y-m'));

        $hasil = [];
        for ($bulan = $mulai->startOfMonth(); $bulan <= $selesai; $bulan = $bulan->addMonth()) {
            $hasil[] = [
                'label'   => self::BULAN[$bulan->month - 1] . ' ' . $bulan->format('y'),
                'lengkap' => self::BULAN[$bulan->month - 1] . ' ' . $bulan->year,
                'nilai'   => (int) ($hitung[$bulan->format('Y-m')] ?? 0),
            ];
        }

        return $hasil;
    }

    // ── Pembantu ─────────────────────────────────────────────────────────

    private function tiket(): Collection
    {
        return $this->tiket ??= Pengaduan::query()
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'kategori', 'created_at']);
    }

}
