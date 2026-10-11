<?php

namespace Modules\ManajemenMahasiswa\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;
use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;

/**
 * Statistik halaman Layanan Pengaduan, dibagi menurut kebutuhan perannya:
 *
 *  - operasional(): "Antrian Kerja" untuk admin kemahasiswaan — apa yang harus
 *    dibuka/ditindaklanjuti sekarang (tiket per tiket, waktu nyata).
 *  - evaluasi():    "Evaluasi Mutu" untuk GPM (pengendali mutu prodi, Peraturan
 *    Rektor Undip 18/2024) — tren, masalah berulang, dan kecepatan penanganan
 *    per periode, sebagai bahan perbaikan. Hanya angka gabungan.
 *
 * Model layanan sekarang satu arah (Baru → Tercatat), jadi "ditangani" diukur dari
 * kapan tiket PERTAMA KALI ditandai tercatat (log `tercatat`), bukan dari kolom
 * answered_at alur lama yang tidak pernah terisi lagi.
 *
 * Volume pengaduan prodi kecil (puluhan–ratusan per tahun), jadi agregasi dikerjakan
 * di PHP atas satu kueri, tanpa SQL JSON khusus per metrik.
 */
class PengaduanStatistikService
{
    /** Batas waktu wajar tiket ditandai tercatat. */
    public const BATAS_HARI = 7;

    /**
     * Angka di bawah ini disamarkan ("< 3") untuk pengelompokan yang bisa menunjuk
     * pelapor, yaitu angkatan dan status pihak terlapor.
     */
    public const MIN_TAMPIL = 3;

    public const PERIODE = [
        'semester'      => 'Semester ini',
        'semester_lalu' => 'Semester lalu',
        '12_bulan'      => '12 bulan terakhir',
        'semua'         => 'Semua waktu',
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

    /** @var array<int, CarbonImmutable>|null */
    private ?array $waktuTercatat = null;

    // ── Antrian kerja (admin) ────────────────────────────────────────────

    public function operasional(): array
    {
        $now = CarbonImmutable::now();
        $tiket = $this->tiket();
        $belum = $tiket->reject(fn (Pengaduan $p) => $p->isTercatat());

        $masuk7 = $tiket->filter(fn (Pengaduan $p) => $p->created_at >= $now->subDays(7))->count();
        $masuk7Sebelumnya = $tiket->filter(fn (Pengaduan $p) => $p->created_at >= $now->subDays(14) && $p->created_at < $now->subDays(7))->count();

        $antrian = $belum
            ->sortBy('created_at')
            ->take(8)
            ->map(fn (Pengaduan $p) => [
                'id'        => $p->id,
                'judul'     => data_get($p->data_template, 'judul') ?: '-',
                'kategori'  => Pengaduan::kategoriLabel((string) $p->kategori),
                'umur_hari' => (int) $p->created_at->diffInDays($now),
                'dibuka'    => $p->read_at !== null,
                'anonim'    => (bool) $p->is_anonim,
                'kontak'    => $this->bisaDihubungi($p),
                'ppks'      => $this->isPpks($p),
            ])
            ->values()
            ->all();

        return [
            'belum_dibuka'      => $belum->whereNull('read_at')->count(),
            'belum_tercatat'    => $belum->count(),
            'lewat_batas'       => $belum->filter(fn (Pengaduan $p) => $p->created_at < $now->subDays(self::BATAS_HARI))->count(),
            'ppks_belum'        => $belum->filter(fn (Pengaduan $p) => $this->isPpks($p))->count(),
            'masuk_7_hari'      => $masuk7,
            'masuk_7_sebelumnya' => $masuk7Sebelumnya,
            'antrian'           => $antrian,
            'belum_per_kategori' => $this->perKategori($belum),
            'belum_kontak'      => [
                'bisa'  => $belum->filter(fn (Pengaduan $p) => $this->bisaDihubungi($p))->count(),
                'tidak' => $belum->reject(fn (Pengaduan $p) => $this->bisaDihubungi($p))->count(),
            ],
        ];
    }

    // ── Evaluasi mutu (GPM) ──────────────────────────────────────────────

    public function evaluasi(string $periode): array
    {
        [$mulai, $selesai, $labelPeriode] = $this->rentang($periode);
        $now = CarbonImmutable::now();

        $tiket = $this->tiket()->filter(
            fn (Pengaduan $p) => $p->created_at >= $mulai && $p->created_at <= $selesai
        );
        $total = $tiket->count();

        // Kecepatan penanganan: tiket yang sudah tercatat, dan tiket yang jendela
        // 7 harinya sudah lewat (yang masih muda & belum tercatat belum bisa dinilai).
        $hariTercatat = [];
        $dinilai = 0;
        $tepatWaktu = 0;
        foreach ($tiket as $p) {
            $tercatatAt = $p->isTercatat() ? ($this->waktuTercatat()[$p->id] ?? null) : null;

            if ($tercatatAt !== null) {
                $hari = max(0, $p->created_at->diffInSeconds($tercatatAt) / 86400);
                $hariTercatat[] = $hari;
                $dinilai++;
                if ($hari <= self::BATAS_HARI) {
                    $tepatWaktu++;
                }
            } elseif ($p->created_at < $now->subDays(self::BATAS_HARI)) {
                $dinilai++;
            }
        }

        $anonim = $tiket->where('is_anonim', true)->count();

        return [
            'periode'          => $periode,
            'label_periode'    => $labelPeriode,
            'total'            => $total,
            'rata_hari'        => $hariTercatat ? round(array_sum($hariTercatat) / count($hariTercatat), 1) : null,
            'tepat_waktu'      => $tepatWaktu,
            'dinilai'          => $dinilai,
            'persen_tepat'     => $dinilai > 0 ? (int) round($tepatWaktu / $dinilai * 100) : null,
            'anonim'           => $anonim,
            'persen_anonim'    => $total > 0 ? (int) round($anonim / $total * 100) : null,
            'per_bulan'        => $this->perBulan($tiket, $mulai, $selesai->min($now)),
            'per_kategori'     => $this->perKategori($tiket),
            'berulang'         => $this->berulang($tiket),
            'bukti'            => $this->perBukti($tiket),
            'per_angkatan'     => $this->samarkan($this->hitung($tiket, fn (Pengaduan $p) => $this->labelAngkatan(data_get($p->data_template, 'angkatan'))), urut: 'kunci'),
            'pihak_terlapor'   => $this->samarkan($this->hitung(
                $tiket->filter(fn (Pengaduan $p) => filled(data_get($p->data_template, 'pihak_terkait'))),
                fn (Pengaduan $p) => data_get($p->data_template, 'pihak_terkait')
            )),
        ];
    }

    /**
     * Rentang tanggal periode. Semester Undip: Ganjil Agustus–Januari, Genap Februari–Juli.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: string}
     */
    public function rentang(string $periode): array
    {
        $now = CarbonImmutable::now();

        if ($periode === 'semester' || $periode === 'semester_lalu') {
            [$mulai, $selesai] = $this->semesterBerisi($now);
            if ($periode === 'semester_lalu') {
                [$mulai, $selesai] = $this->semesterBerisi($mulai->subDay());
            }

            return [$mulai, $selesai, $this->labelSemester($mulai)];
        }

        if ($periode === '12_bulan') {
            return [$now->subMonths(11)->startOfMonth(), $now, '12 bulan terakhir'];
        }

        $pertama = $this->tiket()->min('created_at');
        $mulai = $pertama ? CarbonImmutable::parse($pertama)->startOfMonth() : $now->startOfMonth();

        return [$mulai, $now, 'Semua waktu'];
    }

    // ── Bagian ───────────────────────────────────────────────────────────

    /** @return array<int, array{label: string, nilai: int}> diurutkan terbanyak */
    private function perKategori(Collection $tiket): array
    {
        return $this->hitung($tiket, fn (Pengaduan $p) => Pengaduan::kategoriLabel((string) $p->kategori));
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

    /** Sebaran ketersediaan bukti; tiket lama yang belum ditanya dipisah. */
    private function perBukti(Collection $tiket): array
    {
        $hitung = $tiket->countBy(function (Pengaduan $p) {
            $jawaban = data_get($p->data_template, 'ketersediaan_bukti');
            if ($jawaban) {
                return $jawaban;
            }

            return empty(data_get($p->data_template, 'bukti')) ? 'lama' : PengaduanPertanyaan::BUKTI_LAMPIRKAN;
        });

        $hasil = [];
        foreach (PengaduanPertanyaan::BUKTI_OPSI + ['lama' => 'Belum ditanyakan (tiket lama)'] as $kunci => $label) {
            if ($kunci === 'lama' && empty($hitung['lama'])) {
                continue;
            }
            $hasil[] = ['label' => $label, 'nilai' => (int) ($hitung[$kunci] ?? 0)];
        }

        return $hasil;
    }

    // ── Pembantu ─────────────────────────────────────────────────────────

    /** @return array<int, array{label: string, nilai: int}> */
    private function hitung(Collection $tiket, callable $kunci): array
    {
        return $tiket
            ->map($kunci)
            ->filter(fn ($v) => filled($v))
            ->map(fn ($v) => (string) $v)
            ->countBy()
            ->sortDesc()
            ->map(fn ($nilai, $label) => ['label' => (string) $label, 'nilai' => (int) $nilai])
            ->values()
            ->all();
    }

    /**
     * Tandai baris < MIN_TAMPIL sebagai disamarkan (nilai tidak dikirim ke tampilan).
     *
     * @param  'nilai'|'kunci'  $urut
     */
    private function samarkan(array $baris, string $urut = 'nilai'): array
    {
        if ($urut === 'kunci') {
            usort($baris, fn ($a, $b) => strcmp($a['label'], $b['label']));
        }

        return array_map(fn ($b) => $b['nilai'] < self::MIN_TAMPIL
            ? ['label' => $b['label'], 'nilai' => null]
            : $b, $baris);
    }

    /**
     * Tiket sebelum validasi Oktober 2026 bisa menyimpan angkatan sembarang
     * ("7x7", "2222"); dikelompokkan terpisah agar tidak mengotori grafik.
     */
    private function labelAngkatan(mixed $angkatan): ?string
    {
        if (blank($angkatan)) {
            return null;
        }

        $teks = trim((string) $angkatan);

        return preg_match('/^\d{4}$/', $teks) && (int) $teks >= 1990 && (int) $teks <= now()->year
            ? $teks
            : 'Tidak valid (tiket lama)';
    }

    private function isPpks(Pengaduan $p): bool
    {
        return Pengaduan::normalizeKategori((string) $p->kategori) === Pengaduan::KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN;
    }

    /** Pengaduan biasa selalu bisa dihubungi lewat akun; anonim hanya bila mengisi email. */
    private function bisaDihubungi(Pengaduan $p): bool
    {
        return filled(data_get($p->data_template, 'email_kontak')) || (! $p->is_anonim && $p->user_id !== null);
    }

    private function tiket(): Collection
    {
        return $this->tiket ??= Pengaduan::query()
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'user_id', 'kategori', 'is_anonim', 'status', 'read_at', 'answered_at', 'closed_at', 'data_template', 'created_at']);
    }

    /**
     * Waktu tiket pertama kali ditandai tercatat. Tiket alur lama (selesai /
     * didelegasikan) tidak punya log tersebut, jadi memakai closed_at / answered_at.
     *
     * @return array<int, CarbonImmutable>
     */
    private function waktuTercatat(): array
    {
        if ($this->waktuTercatat !== null) {
            return $this->waktuTercatat;
        }

        $dariLog = PengaduanLog::query()
            ->where('action', PengaduanLog::ACTION_TERCATAT)
            ->selectRaw('pengaduan_id, min(created_at) as waktu')
            ->groupBy('pengaduan_id')
            ->pluck('waktu', 'pengaduan_id');

        $hasil = [];
        foreach ($this->tiket() as $p) {
            $waktu = $dariLog[$p->id] ?? $p->closed_at ?? $p->answered_at;
            if ($waktu !== null) {
                $hasil[$p->id] = CarbonImmutable::parse($waktu);
            }
        }

        return $this->waktuTercatat = $hasil;
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    private function semesterBerisi(CarbonImmutable $tanggal): array
    {
        if ($tanggal->month >= 8) {
            $mulai = $tanggal->setDate($tanggal->year, 8, 1)->startOfDay();
        } elseif ($tanggal->month === 1) {
            $mulai = $tanggal->setDate($tanggal->year - 1, 8, 1)->startOfDay();
        } else {
            $mulai = $tanggal->setDate($tanggal->year, 2, 1)->startOfDay();
        }

        return [$mulai, $mulai->addMonths(6)->subDay()->endOfDay()];
    }

    private function labelSemester(CarbonImmutable $mulai): string
    {
        return $mulai->month === 8
            ? 'Ganjil ' . $mulai->year . '/' . ($mulai->year + 1)
            : 'Genap ' . ($mulai->year - 1) . '/' . $mulai->year;
    }
}
