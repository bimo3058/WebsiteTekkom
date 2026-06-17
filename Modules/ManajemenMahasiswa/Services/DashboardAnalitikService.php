<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\DashboardAnalitik;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Pengumuman;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\ForumReport;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;

class DashboardAnalitikService
{
    /**
     * Ambil snapshot dashboard — cache 5 menit.
     * Ini adalah metode yang paling sering dipanggil karena jadi homepage
     * bagi ribuan user, jadi wajib di-cache.
     */
    public function getSnapshot(): array
    {
        return Cache::remember('mk.dashboard.snapshot', 300, function () {
            return [
                'total_mahasiswa_aktif' => Kemahasiswaan::aktif()->count(),
                'total_alumni'          => Alumni::count(),
                'total_kegiatan'        => Kegiatan::count(),
                'total_pengumuman'      => Pengumuman::published()->count(),

                // Tren kegiatan per bulan (12 bulan terakhir)
                'kegiatan_per_bulan' => Kegiatan::selectRaw("TO_CHAR(tanggal_mulai, 'YYYY-MM') as bulan, count(*) as total")
                    ->where('tanggal_mulai', '>=', now()->subMonths(12))
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->pluck('total', 'bulan'),

                // Distribusi mahasiswa per angkatan — semua status (aktif, alumni, cuti, do)
                // Tujuan: menampilkan berapa total mahasiswa yang MASUK per angkatan
                'mahasiswa_per_angkatan' => Kemahasiswaan::selectRaw('angkatan, count(*) as total')
                    ->whereNotNull('angkatan')
                    ->groupBy('angkatan')
                    ->orderBy('angkatan')
                    ->pluck('total', 'angkatan'),

                // Distribusi alumni per status pekerjaan
                'alumni_per_status'     => Alumni::selectRaw("COALESCE(status_karir, 'belum_terdata') as status, count(*) as total")
                    ->groupByRaw("COALESCE(status_karir, 'belum_terdata')")
                    ->pluck('total', 'status'),

                // Data Serapan Kerja untuk Chart
                'serapan_per_angkatan'  => app(\Modules\ManajemenMahasiswa\Services\AlumniService::class)->getSerapanPerAngkatan(),
                'distribusi_industri'   => app(\Modules\ManajemenMahasiswa\Services\AlumniService::class)->getDistribusiIndustri(),
            ];
        });
    }

    /**
     * Simpan snapshot analitik ke database (dipanggil dari Scheduler, bukan tiap request).
     */
    public function recordSnapshot(int $generatedByUserId, ?int $lecturerId = null): DashboardAnalitik
    {
        $data = [
            'total_mahasiswa_aktif' => Kemahasiswaan::aktif()->count(),
            'total_alumni'          => Alumni::count(),
            'total_kegiatan'        => Kegiatan::count(),
            'total_pengumuman'      => Pengumuman::published()->count(),
            'tanggal_generate'      => now(),
            'generated_by_user_id'  => $generatedByUserId,
            'lecturer_id'           => $lecturerId,
        ];

        return DashboardAnalitik::create($data);
    }

    /**
     * Riwayat snapshot yang pernah disimpan.
     */
    public function getHistory(int $limit = 30)
    {
        return DashboardAnalitik::with(['generatedBy', 'lecturer'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Tentukan scope dashboard berdasarkan role user.
     * - gpm  : GPM & Ketua Departemen (fokus evaluasi mutu)
     * - dpm  : DPM (fokus data mahasiswa akademik)
     * - admin: Superadmin, Admin, Admin Kemahasiswaan (dashboard penuh)
     */
    public function resolveScope(array $roles): string
    {
        if (array_intersect($roles, ['gpm', 'ketua_departemen'])) {
            return 'gpm';
        }
        if (in_array('dpm', $roles, true)) {
            return 'dpm';
        }
        return 'admin';
    }

    /**
     * Daftar section yang ditampilkan untuk tiap scope.
     */
    public function sectionsForScope(string $scope): array
    {
        return match ($scope) {
            // GPM & Ketua Departemen — semua data + metrik evaluasi mutu
            'gpm' => ['action_items', 'activity', 'evaluasi_mutu', 'mahasiswa', 'calon_do', 'lulusan', 'alumni'],
            // DPM — fokus mahasiswa akademik saja (tanpa alumni karir / operasional / mutu)
            'dpm' => ['mahasiswa', 'calon_do', 'lulusan'],
            // Admin — dashboard penuh tanpa section metrik mutu
            default => ['action_items', 'activity', 'mahasiswa', 'calon_do', 'lulusan', 'alumni'],
        };
    }

    /**
     * Entry point utama dashboard — merakit data sesuai scope role.
     * Setiap section punya cache berbeda agar efisien.
     */
    public function getRealTimeDashboard(string $scope = 'admin'): array
    {
        $sections = $this->sectionsForScope($scope);

        $data = [
            'scope'        => $scope,
            'sections'     => $sections,
            'generated_at' => now(),
        ];

        if (in_array('action_items', $sections, true))  $data['action_items'] = $this->getActionItems();
        if (in_array('activity', $sections, true))       $data['activity']     = $this->getActivityStats();
        if (in_array('evaluasi_mutu', $sections, true))  $data['evaluasi']     = $this->getEvaluasiMutu();
        if (in_array('mahasiswa', $sections, true))      $data['mahasiswa']    = $this->getMahasiswaStats();
        if (in_array('calon_do', $sections, true))       $data['calon_do']     = $this->getCalonDO();
        if (in_array('lulusan', $sections, true))        $data['lulusan']      = $this->getLulusanPerPeriode();
        if (in_array('alumni', $sections, true))         $data['alumni']       = $this->getAlumniStats();

        return $data;
    }

    /**
     * TIER 1 — Butuh Tindakan Segera (cache 30 detik).
     * Data yang paling sering berubah: pengaduan masuk, verifikasi pending,
     * laporan forum — semua ini adalah hal yang admin/GPM harus segera tangani.
     */
    public function getActionItems(): array
    {
        return Cache::remember('mk.dashboard.tier1', 30, function () {
            return [
                'pengaduan_baru'      => Pengaduan::where('status', Pengaduan::STATUS_BARU)->count(),
                'verif_kegiatan'      => RiwayatKegiatan::pending()->count(),
                'verif_prestasi'      => Prestasi::pending()->count(),
                'laporan_forum'       => ForumReport::count(),
                'pengumuman_pending'  => PengumumanApprovalRequest::where('status', PengumumanApprovalRequest::STATUS_PENDING)->count(),
            ];
        });
    }

    /**
     * TIER 2 — Aktivitas Terkini (cache 60 detik).
     * Berubah beberapa kali sehari: kegiatan & pengumuman bulan ini,
     * verifikasi pengumuman masuk minggu ini, kegiatan berlangsung hari ini,
     * dan trend kegiatan 6 bulan untuk chart.
     */
    public function getActivityStats(): array
    {
        return Cache::remember('mk.dashboard.tier2', 60, function () {
            $kegiatanTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $kegiatanTrend[$m->translatedFormat('M Y')] = Kegiatan::whereYear('created_at', $m->year)
                    ->whereMonth('created_at', $m->month)
                    ->count();
            }

            return [
                'kegiatan_bulan_ini'          => Kegiatan::whereYear('created_at', now()->year)
                                                    ->whereMonth('created_at', now()->month)->count(),
                'pengumuman_bulan_ini'         => Pengumuman::published()
                                                    ->whereYear('published_at', now()->year)
                                                    ->whereMonth('published_at', now()->month)->count(),
                // Kegiatan yang sedang berlangsung hari ini (tanggal_mulai <= hari ini <= tanggal_selesai)
                'kegiatan_berlangsung'         => Kegiatan::whereNotNull('tanggal_mulai')
                                                    ->where('tanggal_mulai', '<=', now())
                                                    ->where(fn ($q) => $q
                                                        ->whereNull('tanggal_selesai')
                                                        ->orWhere('tanggal_selesai', '>=', now())
                                                    )
                                                    ->count(),
                'kegiatan_trend'               => $kegiatanTrend,
            ];
        });
    }

    /**
     * TIER 3 — Data Mahasiswa (cache 120 detik).
     * Berubah saat admin update status mahasiswa atau data baru masuk — biasanya mingguan.
     */
    public function getMahasiswaStats(): array
    {
        return Cache::remember('mk.dashboard.tier3', 120, function () {
            $statusCounts = Kemahasiswaan::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Semua angkatan yang ada (sebagai sumbu X)
            $angkatanList = Kemahasiswaan::whereNotNull('angkatan')
                ->selectRaw('DISTINCT angkatan')
                ->orderBy('angkatan')
                ->pluck('angkatan')
                ->toArray();

            // Count per status per angkatan — untuk multi-line chart
            $rawPerStatusAngkatan = Kemahasiswaan::selectRaw('status, angkatan, count(*) as total')
                ->whereNotNull('angkatan')
                ->groupBy('status', 'angkatan')
                ->get()
                ->groupBy('status');

            // Bangun struktur: ['aktif' => [2020=>5, 2021=>8, ...], 'alumni' => [...], ...]
            // Angkatan yang tidak ada datanya diisi 0
            $perAngkatanByStatus = [];
            $statusKeys = [
                Kemahasiswaan::STATUS_AKTIF,
                Kemahasiswaan::STATUS_ALUMNI,
                Kemahasiswaan::STATUS_CUTI,
                Kemahasiswaan::STATUS_DO,
                Kemahasiswaan::STATUS_PINDAH_STUDI,
                Kemahasiswaan::STATUS_WAFAT,
                Kemahasiswaan::STATUS_MANGKIR,
            ];
            foreach ($statusKeys as $status) {
                $rows = $rawPerStatusAngkatan->get($status, collect());
                $byAngkatan = $rows->pluck('total', 'angkatan')->toArray();
                $perAngkatanByStatus[$status] = array_map(
                    fn ($a) => (int) ($byAngkatan[$a] ?? 0),
                    $angkatanList
                );
            }

            $prestasiPerTingkat = Prestasi::approved()
                ->selectRaw('tingkat, count(*) as total')
                ->groupBy('tingkat')
                ->pluck('total', 'tingkat')
                ->toArray();

            return [
                'total_aktif'          => $statusCounts[Kemahasiswaan::STATUS_AKTIF] ?? 0,
                'total_cuti'           => $statusCounts[Kemahasiswaan::STATUS_CUTI] ?? 0,
                'total_do'             => $statusCounts[Kemahasiswaan::STATUS_DO] ?? 0,
                'total_pindah'         => $statusCounts[Kemahasiswaan::STATUS_PINDAH_STUDI] ?? 0,
                'total_alumni_status'  => $statusCounts[Kemahasiswaan::STATUS_ALUMNI] ?? 0,
                'total_wafat'          => $statusCounts[Kemahasiswaan::STATUS_WAFAT] ?? 0,
                'total_mangkir'        => $statusCounts[Kemahasiswaan::STATUS_MANGKIR] ?? 0,
                'angkatan_list'              => $angkatanList,
                'per_angkatan_by_status'     => $perAngkatanByStatus,
                'prestasi_per_tingkat' => $prestasiPerTingkat,
                'total_prestasi'       => array_sum($prestasiPerTingkat),
                'prestasi_terbaru'     => Prestasi::approved()
                                            ->with(['kemahasiswaan.user'])
                                            ->orderByDesc('verified_at')
                                            ->limit(5)
                                            ->get(),
                'mahasiswa_terbaru'    => Kemahasiswaan::aktif()->with('user')
                                            ->whereNotNull('angkatan')
                                            ->orderByDesc('created_at')->limit(8)->get(),
            ];
        });
    }

    /**
     * TIER 4 — Data Alumni (cache 300 detik / 5 menit).
     * Berubah ketika alumni update profil karir — jarang, paling lambat berubah.
     */
    public function getAlumniStats(): array
    {
        return Cache::remember('mk.dashboard.tier4', 300, function () {
            $statusKarir = Alumni::selectRaw(
                    "COALESCE(status_karir, 'belum_terdata') as status, count(*) as total"
                )
                ->groupByRaw("COALESCE(status_karir, 'belum_terdata')")
                ->pluck('total', 'status')
                ->toArray();

            return [
                'total'                => Alumni::count(),
                'total_terdata'        => Alumni::whereNotNull('status_karir')->count(),
                'total_belum_terdata'  => Alumni::whereNull('status_karir')->count(),
                'per_status_karir'     => $statusKarir,
                'distribusi_industri'  => Alumni::whereNotNull('bidang_industri')
                                            ->selectRaw('bidang_industri, count(*) as total')
                                            ->groupBy('bidang_industri')
                                            ->orderByDesc('total')->limit(8)
                                            ->pluck('total', 'bidang_industri')->toArray(),
                'serapan_per_angkatan' => app(AlumniService::class)->getSerapanPerAngkatan(),
                'alumni_terbaru'       => Alumni::with('user')->orderByDesc('created_at')->limit(8)->get(),
            ];
        });
    }

    /**
     * METRIK EVALUASI MUTU — untuk GPM & Ketua Departemen (cache 300 detik).
     * Mengukur kualitas penyelenggaraan prodi: kelulusan, masa studi, DO rate,
     * waktu tunggu kerja, dan responsivitas layanan pengaduan.
     */
    public function getEvaluasiMutu(): array
    {
        return Cache::remember('mk.dashboard.evaluasi', 300, function () {
            // Distribusi status per angkatan untuk hitung kelulusan & DO rate
            $perAngkatan = Kemahasiswaan::selectRaw('angkatan, status, count(*) as total')
                ->whereNotNull('angkatan')
                ->groupBy('angkatan', 'status')
                ->get()
                ->groupBy('angkatan');

            $kelulusan = [];
            foreach ($perAngkatan as $angkatan => $rows) {
                $byStatus = $rows->pluck('total', 'status');
                $total    = $rows->sum('total');
                $lulus    = (int) ($byStatus[Kemahasiswaan::STATUS_ALUMNI] ?? 0);
                $do       = (int) ($byStatus[Kemahasiswaan::STATUS_DO] ?? 0);
                $kelulusan[$angkatan] = [
                    'total'        => $total,
                    'lulus'        => $lulus,
                    'do'           => $do,
                    'rate_lulus'   => $total > 0 ? round($lulus / $total * 100, 1) : 0,
                    'rate_do'      => $total > 0 ? round($do / $total * 100, 1) : 0,
                ];
            }
            ksort($kelulusan);

            // Rata-rata masa studi (tahun) dari alumni — ideal 4 tahun
            $rataMasaStudi = (float) Alumni::whereNotNull('tahun_lulus')
                ->whereNotNull('angkatan')
                ->whereRaw('tahun_lulus >= angkatan')
                ->avg(DB::raw('tahun_lulus - angkatan'));

            // Rata-rata waktu tunggu kerja (tahun) dari lulus sampai mulai bekerja
            $rataWaktuTunggu = (float) Alumni::whereNotNull('tahun_mulai_bekerja')
                ->whereNotNull('tahun_lulus')
                ->whereRaw('tahun_mulai_bekerja >= tahun_lulus')
                ->avg(DB::raw('tahun_mulai_bekerja - tahun_lulus'));

            // Responsivitas layanan pengaduan — % yang sudah ditangani
            $totalPengaduan = Pengaduan::where('status', '!=', Pengaduan::STATUS_DRAFT)->count();
            $pengaduanDitangani = Pengaduan::whereIn('status', [
                Pengaduan::STATUS_DIDELEGASIKAN,
                Pengaduan::STATUS_SELESAI,
            ])->count();

            // Serapan kerja keseluruhan (bekerja + wirausaha dari yang terdata)
            $totalTerdata = Alumni::whereNotNull('status_karir')->count();
            $totalBekerja = Alumni::whereIn('status_karir', [Alumni::STATUS_BEKERJA, Alumni::STATUS_WIRAUSAHA])->count();

            return [
                'kelulusan_per_angkatan' => $kelulusan,
                'rata_masa_studi'        => round($rataMasaStudi, 1),
                'rata_waktu_tunggu'      => round($rataWaktuTunggu, 1),
                'responsivitas'          => $totalPengaduan > 0 ? round($pengaduanDitangani / $totalPengaduan * 100) : 0,
                'pengaduan_ditangani'    => $pengaduanDitangani,
                'pengaduan_total'        => $totalPengaduan,
                'serapan_kerja'          => $totalTerdata > 0 ? round($totalBekerja / $totalTerdata * 100) : 0,
            ];
        });
    }

    /**
     * EVALUASI CALON DO — mahasiswa aktif yang sudah memasuki semester ≥ 13.
     * Mahasiswa masuk Agustus, 1 tahun = 2 semester. Semester 13 dimulai
     * 6 tahun setelah angkatan, jadi: angkatan ≤ (tahun sekarang − 6).
     */
    public function getCalonDO(): array
    {
        return Cache::remember('mk.dashboard.calon_do', 300, function () {
            $thresholdAngkatan = now()->year - 6;
            $currentYear  = now()->year;
            $currentMonth = now()->month;

            $base = Kemahasiswaan::aktif()
                ->whereNotNull('angkatan')
                ->where('angkatan', '<=', $thresholdAngkatan);

            $list = (clone $base)
                ->with('user:id,email')
                ->orderBy('angkatan')
                ->orderBy('nama')
                ->limit(8)
                ->get()
                ->map(function ($m) use ($currentYear, $currentMonth) {
                    $yearsElapsed = $currentYear - $m->angkatan;
                    // Estimasi semester berjalan (ganjil mulai Agustus)
                    $semester = $currentMonth >= 8 ? ($yearsElapsed * 2 + 1) : ($yearsElapsed * 2);
                    return [
                        'nama'     => $m->nama ?? $m->user?->name ?? '-',
                        'nim'      => $m->nim ?? '-',
                        'angkatan' => (int) $m->angkatan,
                        'semester' => max($semester, 13),
                    ];
                });

            return [
                'count'              => (clone $base)->count(),
                'threshold_angkatan' => $thresholdAngkatan,
                'list'               => $list,
            ];
        });
    }

    /**
     * LULUSAN PER PERIODE — pantau kelulusan & sinkronisasi ke direktori alumni.
     * Menampilkan jumlah lulusan per tahun + deteksi mahasiswa berstatus alumni
     * yang belum punya record di tabel mk_alumni (belum tersinkron).
     */
    public function getLulusanPerPeriode(): array
    {
        return Cache::remember('mk.dashboard.lulusan', 300, function () {
            $perTahun = Kemahasiswaan::alumni()
                ->whereNotNull('tahun_lulus')
                ->selectRaw('tahun_lulus, count(*) as total')
                ->groupBy('tahun_lulus')
                ->orderBy('tahun_lulus')
                ->pluck('total', 'tahun_lulus')
                ->toArray();

            // Mahasiswa berstatus alumni yang belum tersinkron ke mk_alumni
            $alumniUserIds = Alumni::whereNotNull('user_id')->pluck('user_id')->all();
            $belumSinkron  = Kemahasiswaan::alumni()
                ->whereNotNull('user_id')
                ->when(!empty($alumniUserIds), fn ($q) => $q->whereNotIn('user_id', $alumniUserIds))
                ->count();

            return [
                'per_tahun'        => $perTahun,
                'total_lulus'      => array_sum($perTahun),
                'belum_sinkron'    => $belumSinkron,
                'total_alumni'     => Alumni::count(),
            ];
        });
    }

    /**
     * Invalidate semua tier cache.
     */
    public function invalidateCache(): void
    {
        Cache::forget('mk.dashboard.snapshot');
        Cache::forget('mk.dashboard.tier1');
        Cache::forget('mk.dashboard.tier2');
        Cache::forget('mk.dashboard.tier3');
        Cache::forget('mk.dashboard.tier4');
        Cache::forget('mk.dashboard.evaluasi');
        Cache::forget('mk.dashboard.calon_do');
        Cache::forget('mk.dashboard.lulusan');
    }
}