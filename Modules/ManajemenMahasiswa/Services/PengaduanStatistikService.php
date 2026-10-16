<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Pengaduan;

/**
 * Statistik halaman Layanan Pengaduan: bahan evaluasi mutu per periode untuk
 * GPM (pengendali mutu prodi, Peraturan Rektor Undip 18/2024) dan pengelola lain —
 * tren per bulan, kategori, dan masalah berulang.
 * Hanya angka gabungan.
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

    /** Isian bebas yang dihitung kemunculannya untuk mencari masalah berulang. */
    private const ISIAN_BERULANG = [
        'lokasi'      => 'Lokasi',
        'objek'       => 'Objek / Layanan',
        'mata_kuliah' => 'Mata Kuliah',
        'nama_tendik' => 'Petugas / Unit',
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
            'berulang'      => $this->berulang($tiket),
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

    /**
     * Isian bebas yang muncul di ≥ 2 pengaduan (setelah huruf kecil & spasi dirapikan).
     * Teks yang ditampilkan = penulisan yang paling sering dipakai.
     */
    private function berulang(Collection $tiket): array
    {
        $grup = [];
        foreach ($tiket as $p) {
            foreach (self::ISIAN_BERULANG as $kunci => $labelIsian) {
                $teks = trim(preg_replace('/\s+/', ' ', (string) data_get($p->data_template, $kunci)));
                if (mb_strlen($teks) < 2) {
                    continue;
                }

                $id = $kunci . '|' . mb_strtolower($teks);
                $grup[$id]['isian'] = $labelIsian;
                $grup[$id]['tulisan'][] = $teks;
                $grup[$id]['kategori'][] = Pengaduan::kategoriLabel((string) $p->kategori);
            }
        }

        return collect($grup)
            ->filter(fn ($g) => count($g['tulisan']) >= 2)
            ->map(fn ($g) => [
                'isian'    => $g['isian'],
                'teks'     => collect($g['tulisan'])->countBy()->sortDesc()->keys()->first(),
                'kategori' => collect($g['kategori'])->countBy()->sortDesc()->keys()->first(),
                'nilai'    => count($g['tulisan']),
            ])
            ->sortByDesc('nilai')
            ->take(10)
            ->values()
            ->all();
    }

    // ── Pembantu ─────────────────────────────────────────────────────────

    private function tiket(): Collection
    {
        return $this->tiket ??= Pengaduan::query()
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'kategori', 'data_template', 'created_at']);
    }

}
