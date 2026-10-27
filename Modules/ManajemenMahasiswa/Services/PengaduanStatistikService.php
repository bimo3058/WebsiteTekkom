<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;

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

    /** Baris masalah berulang yang ditampilkan per kategori; sisanya hanya dihitung. */
    private const BERULANG_PER_KATEGORI = 5;

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
     * Masalah yang sama dari ≥ 2 pelapor, per kategori. "Sama" = SEMUA pertanyaan teks
     * kategori itu dijawab sama (huruf kecil & spasi dirapikan), mis. Fasilitas: hal
     * "AC" DAN lokasi "Ruang A.3.12" — AC di ruang lain adalah masalah lain. Tiket yang
     * salah satu jawabannya kosong (atau berisi label pertanyaan, peninggalan form lama)
     * tidak ikut, begitu pula kunci lama di luar set kategorinya.
     *
     * Satuannya pelapor: satu akun yang mengirim dua kali dihitung sekali. Tiket anonim
     * dihitung per tiket, tanpa melihat akun pengirimnya.
     *
     * @return array<int, array{kode: string, kategori: string, pertanyaan: array<string, string>, baris: array, lainnya: int}>
     */
    private function berulang(Collection $tiket): array
    {
        $abaikan = PengaduanPertanyaan::semuaLabel();
        $grup = [];

        foreach ($tiket as $p) {
            $kategori = Pengaduan::normalizeKategori((string) $p->kategori);
            $kunciTeks = PengaduanPertanyaan::kunciTeks($kategori);
            if (empty($kunciTeks)) {
                continue;
            }

            $jawaban = [];
            foreach ($kunciTeks as $kunci) {
                $teks = self::rapikan((string) data_get($p->data_template, $kunci));
                if (mb_strlen($teks) < 2 || isset($abaikan[mb_strtolower($teks)])) {
                    continue 2;
                }
                $jawaban[$kunci] = $teks;
            }

            $pelapor = (!$p->is_anonim && $p->user_id) ? 'u' . $p->user_id : 't' . $p->id;
            $frekuensi = mb_strtolower(trim((string) data_get($p->data_template, 'frekuensi')));

            $id = mb_strtolower(implode("\n", $jawaban));
            $grup[$kategori][$id]['tulisan'][] = json_encode($jawaban, JSON_UNESCAPED_UNICODE);
            $grup[$kategori][$id]['pelapor'][$pelapor] = true;
            if (in_array($frekuensi, PengaduanPertanyaan::FREKUENSI_SERING, true)) {
                $grup[$kategori][$id]['sering'][$pelapor] = true;
            }
        }

        return collect($grup)
            ->map(function (array $masalah, string $kategori) {
                $baris = collect($masalah)
                    ->filter(fn ($g) => count($g['pelapor']) >= 2)
                    ->map(fn ($g) => [
                        // Penulisan yang paling sering dipakai.
                        'jawaban' => json_decode(collect($g['tulisan'])->countBy()->sortDesc()->keys()->first(), true),
                        'pelapor' => count($g['pelapor']),
                        'sering'  => count($g['sering'] ?? []),
                    ])
                    ->sortByDesc('pelapor')
                    ->values();

                return [
                    'kode'       => $kategori,
                    'kategori'   => Pengaduan::kategoriLabel($kategori),
                    'pertanyaan' => array_map(
                        fn ($field) => $field['label'],
                        array_intersect_key(PengaduanPertanyaan::untuk($kategori)['fields'], array_flip(PengaduanPertanyaan::kunciTeks($kategori)))
                    ),
                    'baris'      => $baris->take(self::BERULANG_PER_KATEGORI)->all(),
                    'lainnya'    => max(0, $baris->count() - self::BERULANG_PER_KATEGORI),
                ];
            })
            ->filter(fn ($k) => !empty($k['baris']))
            ->sortByDesc(fn ($k) => $k['baris'][0]['pelapor'])
            ->values()
            ->all();
    }

    // ── Pembantu ─────────────────────────────────────────────────────────

    /** Spasi ganda dirapikan & ujung dipangkas; cara yang sama dipakai filter persis di daftar pengaduan. */
    public static function rapikan(string $teks): string
    {
        return trim(preg_replace('/\s+/u', ' ', $teks));
    }

    private function tiket(): Collection
    {
        return $this->tiket ??= Pengaduan::query()
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'user_id', 'is_anonim', 'kategori', 'data_template', 'created_at']);
    }

}
