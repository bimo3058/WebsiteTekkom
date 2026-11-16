<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
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
use Modules\ManajemenMahasiswa\Models\PengaduanLog;
use Modules\ManajemenMahasiswa\Models\ForumReport;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;
use Modules\ManajemenMahasiswa\Models\PengurusHimaskom;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;

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
                'total_mahasiswa_aktif' => $this->queryMahasiswaAktif()->count(),
                'total_alumni'          => Alumni::count(),
                'total_kegiatan'        => Kegiatan::unik()->count(),
                'total_pengumuman'      => Pengumuman::published()->count(),

                // Tren kegiatan per bulan (12 bulan terakhir)
                'kegiatan_per_bulan' => Kegiatan::unik()->selectRaw("TO_CHAR(tanggal_mulai, 'YYYY-MM') as bulan, count(*) as total")
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
            'total_mahasiswa_aktif' => $this->queryMahasiswaAktif()->count(),
            'total_alumni'          => Alumni::count(),
            'total_kegiatan'        => Kegiatan::unik()->count(),
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
     * - dpm  : DPM / Dosen Pembina Mahasiswa (fokus pembinaan himpunan & kegiatan kemahasiswaan)
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
            // GPM & Ketua Departemen — fokus data evaluasi (tanpa action items operasional)
            'gpm' => ['activity', 'evaluasi_mutu', 'mahasiswa', 'calon_do', 'lulusan', 'alumni'],
            // DPM — fokus pembinaan himpunan, proker, pelaksanaan kegiatan, partisipasi, dan prestasi/reward.
            'dpm' => ['dpm_himpunan', 'dpm_proker', 'dpm_pelaksanaan', 'dpm_partisipasi', 'dpm_prestasi_reward', 'mahasiswa', 'calon_do', 'lulusan'],
            // Admin — dashboard operasional modul kemahasiswaan
            default => ['action_items', 'admin_operasional', 'activity', 'mahasiswa', 'calon_do', 'lulusan', 'alumni'],
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
        if (in_array('admin_operasional', $sections, true)) $data['admin_operasional'] = $this->getAdminOperationalStats();
        if (in_array('activity', $sections, true))       $data['activity']     = $this->getActivityStats();
        if (in_array('evaluasi_mutu', $sections, true))  $data['evaluasi']     = $this->getEvaluasiMutu();
        if (in_array('dpm_himpunan', $sections, true))   $data['dpm_himpunan'] = $this->getDpmHimpunanStats();
        if (in_array('dpm_proker', $sections, true))     $data['dpm_proker']   = $this->getDpmProkerStats();
        if (in_array('dpm_pelaksanaan', $sections, true)) $data['dpm_pelaksanaan'] = $this->getDpmPelaksanaanStats();
        if (in_array('dpm_partisipasi', $sections, true)) $data['dpm_partisipasi'] = $this->getDpmPartisipasiStats();
        if (in_array('dpm_prestasi_reward', $sections, true)) $data['dpm_prestasi_reward'] = $this->getDpmPrestasiRewardStats();
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
                // "Baru" = belum ditandai tercatat — sama dengan filter "Baru" di halaman
                // Pengaduan (bukan hanya status `baru`, yang tidak mencakup `dibaca` dll.).
                'pengaduan_baru'      => Pengaduan::where('status', '!=', Pengaduan::STATUS_DRAFT)
                                            ->whereNotIn('status', Pengaduan::TERCATAT_STATUSES)
                                            ->count(),
                'verif_kegiatan'      => RiwayatKegiatan::pending()->count(),
                'verif_prestasi'      => Prestasi::pending()->count(),
                'reward_pending'      => Prestasi::rewardDiajukan()->count(),
                'laporan_forum'       => ForumReport::where('status', 'pending')->count(),
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
            $kegiatanTrend = [];            // berdasarkan tanggal input (created_at) — dipakai admin
            $kegiatanTrendPelaksanaan = []; // berdasarkan tanggal pelaksanaan (tanggal_mulai) — dipakai GPM (2D)
            for ($i = 5; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $label = $m->locale('id')->translatedFormat('M Y');
                // "Ditambahkan" = proker baru dibuat; salinan hasil Ajukan (rencana_id
                // terisi) bukan kegiatan baru, jadi tidak dihitung lagi.
                $kegiatanTrend[$label] = Kegiatan::whereNull('rencana_id')
                    ->whereYear('created_at', $m->year)
                    ->whereMonth('created_at', $m->month)
                    ->count();
                $kegiatanTrendPelaksanaan[$label] = Kegiatan::unik()
                    ->whereNotNull('tanggal_mulai')
                    ->whereYear('tanggal_mulai', $m->year)
                    ->whereMonth('tanggal_mulai', $m->month)
                    ->count();
            }

            return [
                // Sinkron dengan popup "kegiatan" di DashboardController::modalData().
                'kegiatan_bulan_ini'          => Kegiatan::whereNull('rencana_id')
                                                    ->whereYear('created_at', now()->year)
                                                    ->whereMonth('created_at', now()->month)->count(),
                'pengumuman_bulan_ini'         => Pengumuman::published()
                                                    ->whereYear('published_at', now()->year)
                                                    ->whereMonth('published_at', now()->month)->count(),
                // Kegiatan tahap Pelaksanaan yang tanggalnya mencakup hari ini
                // (Kegiatan::scopeBerlangsungPada). Dulu kegiatan tanpa tanggal selesai,
                // draft, dan arsip ikut terhitung "berlangsung" selamanya.
                'kegiatan_berlangsung'         => Kegiatan::berlangsungPada()->count(),
                'kegiatan_trend'               => $kegiatanTrend,
                'kegiatan_trend_pelaksanaan'   => $kegiatanTrendPelaksanaan,
            ];
        });
    }

    /**
     * ADMIN — ringkasan operasional modul yang perlu dipantau admin kemahasiswaan.
     */
    public function getAdminOperationalStats(): array
    {
        return Cache::remember('mk.dashboard.admin.operasional', 120, function () {
            $pengaduan = $this->statistikPengaduan();

            $pengaduanPerKategori = Pengaduan::where('status', '!=', Pengaduan::STATUS_DRAFT)
                ->selectRaw('kategori, count(*) as total')
                ->groupBy('kategori')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'kategori')
                ->toArray();

            // Satu baris per kegiatan (Kegiatan::scopeUnik) — rencana yang sudah
            // diajukan tidak dihitung lagi di samping salinannya.
            $kegiatanStatus = Kegiatan::unik()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $realisasi = $this->realisasiRencanaVsPelaksanaan();

            return [
                // Status pengaduan mengikuti halaman Pengaduan: hanya "Baru" (dipecah
                // belum/sudah dibaca dari read_at) dan "Tercatat". Ketiganya menjumlah ke total.
                'sla_hari' => $pengaduan['sla_hari'],
                'pengaduan_total' => $pengaduan['total'],
                'pengaduan_baru' => $pengaduan['baru'],
                'pengaduan_belum_dibaca' => $pengaduan['baru_belum_dibaca'],
                'pengaduan_dibaca' => $pengaduan['baru_dibaca'],
                'pengaduan_tercatat' => $pengaduan['tercatat'],
                'pengaduan_sla_lewat' => $pengaduan['sla_lewat'],
                'pengaduan_sla_terpenuhi' => $pengaduan['sla_terpenuhi'],
                'pengaduan_per_kategori' => $pengaduanPerKategori,
                // Definisi sama dengan Evaluasi Mutu GPM (statistikPengaduan()).
                'responsivitas_pengaduan' => $pengaduan['responsivitas'],

                'verif_riwayat_pending' => RiwayatKegiatan::pending()->count(),
                'verif_prestasi_pending' => Prestasi::pending()->count(),
                'reward_pending' => Prestasi::rewardDiajukan()->count(),
                'reward_sks_pending' => (int) Prestasi::where('claim_status', Prestasi::CLAIM_DIAJUKAN)->sum('reward_sks_max'),
                'reward_mk_pending' => (int) Prestasi::where('claim_status', Prestasi::CLAIM_DIAJUKAN)->sum('reward_jml_mk_max'),
                'pengumuman_pending' => PengumumanApprovalRequest::where('status', PengumumanApprovalRequest::STATUS_PENDING)->count(),
                'forum_report_pending' => ForumReport::where('status', 'pending')->count(),

                'kegiatan_draft' => $kegiatanStatus[Kegiatan::STATUS_DRAFT] ?? 0,
                // Rencana diajukan yang salinannya sudah dihapus — tetap dihitung supaya
                // rinciannya menjumlah ke total.
                'kegiatan_diajukan' => $kegiatanStatus[Kegiatan::STATUS_DIAJUKAN] ?? 0,
                'kegiatan_pelaksanaan' => $kegiatanStatus[Kegiatan::STATUS_DISETUJUI] ?? 0,
                'kegiatan_selesai' => $kegiatanStatus[Kegiatan::STATUS_SELESAI] ?? 0,
                'kegiatan_tanpa_dosen' => Kegiatan::unik()->doesntHave('dosenPendampings')->count(),
                // Kegiatan di Pelaksanaan yang datanya belum disesuaikan dengan realisasi
                // (flag is_pelaksanaan_updated, syarat Unggah ke Arsip).
                'kegiatan_belum_diperbarui' => $realisasi['belum_diperbarui'],
                'realisasi_jumlah_kegiatan' => $realisasi['jumlah_kegiatan'],
                'target_peserta' => $realisasi['peserta_rencana'],
                'realisasi_peserta' => $realisasi['peserta_realisasi'],
                'persen_realisasi_peserta' => $realisasi['persen_peserta'],
                'anggaran_rencana' => $realisasi['anggaran_rencana'],
                'anggaran_realisasi' => $realisasi['anggaran_realisasi'],
                'persen_realisasi_anggaran' => $realisasi['persen_anggaran'],

                'mahasiswa_tanpa_nim' => Kemahasiswaan::whereNull('nim')->orWhere('nim', '')->count(),
                'mahasiswa_tanpa_angkatan' => Kemahasiswaan::whereNull('angkatan')->count(),
                // Akun ber-role alumni yang belum punya baris direktori alumni. Dulu dari
                // status kemahasiswaan 'alumni' yang dibekukan sejak S-53 (15 Nov 2026).
                'lulusan_belum_sinkron' => $this->alumniBelumSinkron(),
                'alumni_belum_terdata' => Alumni::whereNull('status_karir')->count(),
            ];
        });
    }

    /**
     * DPM - Struktur dan kesehatan kepengurusan himpunan.
     */
    public function getDpmHimpunanStats(): array
    {
        return Cache::remember('mk.dashboard.dpm.himpunan', 300, function () {
            $periodeAktif = DB::table('mk_kepengurusan')
                ->orderByDesc('tahun_periode')
                ->orderByDesc('id')
                ->first();

            $pengurusBase = PengurusHimaskom::query()
                ->when($periodeAktif, fn ($q) => $q->where('kepengurusan_id', $periodeAktif->id));

            $statusCounts = (clone $pengurusBase)
                ->selectRaw('status_keaktifan, count(*) as total')
                ->groupBy('status_keaktifan')
                ->pluck('total', 'status_keaktifan')
                ->toArray();

            $perDivisi = (clone $pengurusBase)
                ->selectRaw("COALESCE(divisi, 'Tanpa Divisi') as divisi, count(*) as total")
                ->groupByRaw("COALESCE(divisi, 'Tanpa Divisi')")
                ->orderByDesc('total')
                ->pluck('total', 'divisi')
                ->toArray();

            $pengurusInti = (clone $pengurusBase)
                ->with(['student.user'])
                ->orderByRaw("CASE WHEN LOWER(jabatan) LIKE '%ketua%' THEN 0 WHEN LOWER(jabatan) LIKE '%sekretaris%' THEN 1 WHEN LOWER(jabatan) LIKE '%bendahara%' THEN 2 ELSE 3 END")
                ->orderBy('jabatan')
                ->limit(8)
                ->get()
                ->map(fn ($pengurus) => [
                    'nama'   => $pengurus->student?->user?->name ?? $pengurus->student?->name ?? 'Mahasiswa',
                    'nim'    => $pengurus->student?->student_number ?? '-',
                    'jabatan'=> $pengurus->jabatan ?? '-',
                    'status' => $pengurus->status_keaktifan ?? '-',
                ]);

            if (array_sum($statusCounts) === 0) {
                return $this->getDpmHimpunanRoleFallback($periodeAktif);
            }

            return [
                'source'         => 'kepengurusan',
                'periode_aktif'  => $periodeAktif?->tahun_periode,
                'total_periode'  => DB::table('mk_kepengurusan')->count(),
                'total_pengurus' => array_sum($statusCounts),
                'total_aktif'    => $statusCounts[PengurusHimaskom::STATUS_AKTIF] ?? 0,
                'total_nonaktif' => $statusCounts[PengurusHimaskom::STATUS_NONAKTIF] ?? 0,
                'total_cuti'     => $statusCounts[PengurusHimaskom::STATUS_CUTI] ?? 0,
                'kpi_3_label'    => 'Pengurus Aktif',
                'kpi_3_value'    => $statusCounts[PengurusHimaskom::STATUS_AKTIF] ?? 0,
                'kpi_4_label'    => 'Nonaktif / Cuti',
                'kpi_4_value'    => ($statusCounts[PengurusHimaskom::STATUS_NONAKTIF] ?? 0) + ($statusCounts[PengurusHimaskom::STATUS_CUTI] ?? 0),
                'composition_label' => 'Komposisi Pengurus per Divisi',
                'per_divisi'     => $perDivisi,
                'pengurus_inti'  => $pengurusInti,
            ];
        });
    }

    private function getDpmHimpunanRoleFallback(?object $periodeAktif): array
    {
        $roleLabels = RoleHimpunan::LABEL;

        $roleNames = array_keys($roleLabels);
        $pengurus = User::role($roleNames)
            ->with(['student', 'roles'])
            ->orderBy('name')
            ->get();

        $perJabatan = [];
        foreach ($roleLabels as $role => $label) {
            $perJabatan[$label] = $pengurus->filter(fn ($user) => $user->hasRole($role))->count();
        }
        $perJabatan = array_filter($perJabatan, fn ($total) => $total > 0);

        $pengurusIntiRoles = ['ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'wakil_ketua_bidang', 'ketua_unit', 'wakil_ketua_unit'];
        $pengurusInti = $pengurus
            ->filter(fn ($user) => $user->hasAnyRole($pengurusIntiRoles))
            ->take(8)
            ->map(function ($user) use ($roleLabels, $roleNames) {
                $jabatan = collect($roleNames)
                    ->filter(fn ($role) => $user->hasRole($role))
                    ->map(fn ($role) => $roleLabels[$role])
                    ->implode(', ');

                return [
                    'nama'    => $user->name ?? 'Mahasiswa',
                    'nim'     => $user->student?->student_number ?? '-',
                    'jabatan' => $jabatan ?: '-',
                    'status'  => 'Aktif',
                ];
            })
            ->values();

        return [
            'source'            => 'role',
            'periode_aktif'     => $periodeAktif?->tahun_periode,
            'total_periode'     => DB::table('mk_kepengurusan')->count(),
            'total_pengurus'    => $pengurus->count(),
            'total_aktif'       => $pengurus->count(),
            'total_nonaktif'    => 0,
            'total_cuti'        => 0,
            'kpi_3_label'       => 'Pengurus Inti',
            'kpi_3_value'       => $pengurus->filter(fn ($user) => $user->hasAnyRole($pengurusIntiRoles))->count(),
            'kpi_4_label'       => 'Ketua Unit/Bidang',
            'kpi_4_value'       => $pengurus->filter(fn ($user) => $user->hasAnyRole(['ketua_unit', 'wakil_ketua_unit', 'ketua_bidang', 'wakil_ketua_bidang']))->count(),
            'composition_label' => 'Komposisi Pengurus per Jabatan',
            'per_divisi'        => $perJabatan,
            'pengurus_inti'     => $pengurusInti,
        ];
    }

    /**
     * DPM - Monitoring program kerja himpunan.
     */
    public function getDpmProkerStats(): array
    {
        return Cache::remember('mk.dashboard.dpm.proker', 120, function () {
            // Satu baris per proker (Kegiatan::scopeUnik).
            $statusCounts = Kegiatan::unik()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Pivot bidang/kategori ikut tersalin saat Ajukan, jadi baris rencana yang
            // sudah punya salinan dibuang di sini juga.
            $perBidang = DB::table('mk_kegiatan_bidang as kb')
                ->join('mk_bidang as b', 'kb.bidang_id', '=', 'b.id')
                ->tap(fn ($q) => $this->hanyaKegiatanUnik($q, 'kb.kegiatan_id'))
                ->selectRaw('b.nama_bidang, count(*) as total')
                ->groupBy('b.nama_bidang')
                ->orderByDesc('total')
                ->pluck('total', 'b.nama_bidang')
                ->toArray();

            $perKategori = DB::table('mk_kegiatan_kategori as kk')
                ->join('mk_kategori_kegiatan as kat', 'kk.kategori_kegiatan_id', '=', 'kat.id')
                ->tap(fn ($q) => $this->hanyaKegiatanUnik($q, 'kk.kegiatan_id'))
                ->selectRaw('kat.nama_kategori, count(*) as total')
                ->groupBy('kat.nama_kategori')
                ->orderByDesc('total')
                ->pluck('total', 'kat.nama_kategori')
                ->toArray();

            return [
                'total'             => array_sum($statusCounts),
                'total_draft'       => $statusCounts[Kegiatan::STATUS_DRAFT] ?? 0,
                'total_diajukan'    => $statusCounts[Kegiatan::STATUS_DIAJUKAN] ?? 0,
                'total_pelaksanaan' => $statusCounts[Kegiatan::STATUS_DISETUJUI] ?? 0,
                'total_selesai'     => $statusCounts[Kegiatan::STATUS_SELESAI] ?? 0,
                // Proker baru yang dibuat tahun ini — salinan hasil Ajukan tidak dihitung.
                'tahun_ini'         => Kegiatan::whereNull('rencana_id')->whereYear('created_at', now()->year)->count(),
                'per_bidang'        => $perBidang,
                'per_kategori'      => $perKategori,
                'proker_terbaru'    => Kegiatan::perencanaan()
                                        ->with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
                                        ->orderByDesc('created_at')
                                        ->limit(6)
                                        ->get(),
            ];
        });
    }

    /**
     * DPM - Monitoring pelaksanaan kegiatan dan realisasi.
     */
    public function getDpmPelaksanaanStats(): array
    {
        return Cache::remember('mk.dashboard.dpm.pelaksanaan', 120, function () {
            $realisasi = $this->realisasiRencanaVsPelaksanaan();

            return [
                'total_pelaksanaan'      => Kegiatan::pelaksanaan()->count(),
                'total_selesai'          => Kegiatan::selesai()->count(),
                // Kegiatan yang dimulai bulan ini — tiap kegiatan sekali (Kegiatan::scopeUnik).
                'bulan_ini'              => Kegiatan::unik()
                                                ->whereYear('tanggal_mulai', now()->year)
                                                ->whereMonth('tanggal_mulai', now()->month)
                                                ->count(),
                'berlangsung_hari_ini'   => Kegiatan::berlangsungPada()->count(),
                // Di Pelaksanaan tapi datanya belum disesuaikan dengan realisasi
                // (is_pelaksanaan_updated). Kolom realisasi_* lama tidak pernah diisi form.
                'belum_realisasi'        => $realisasi['belum_diperbarui'],
                'tanpa_dosen_pendamping' => Kegiatan::unik()->doesntHave('dosenPendampings')->count(),
                'realisasi_jumlah_kegiatan' => $realisasi['jumlah_kegiatan'],
                'target_peserta'         => $realisasi['peserta_rencana'],
                'realisasi_peserta'      => $realisasi['peserta_realisasi'],
                'rate_peserta'           => $realisasi['persen_peserta'],
                'anggaran_rencana'       => $realisasi['anggaran_rencana'],
                'anggaran_realisasi'     => $realisasi['anggaran_realisasi'],
                'rate_anggaran'          => $realisasi['persen_anggaran'],
                'pelaksanaan_mendatang'  => Kegiatan::pelaksanaan()
                                                ->with(['bidangs', 'kategoris', 'ketuaPelaksana.user', 'dosenPendampings.user'])
                                                ->whereNotNull('tanggal_mulai')
                                                ->whereDate('tanggal_mulai', '>=', now()->toDateString())
                                                ->orderBy('tanggal_mulai')
                                                ->limit(6)
                                                ->get(),
            ];
        });
    }

    /**
     * DPM - Partisipasi mahasiswa dalam kegiatan dan riwayat terverifikasi.
     */
    public function getDpmPartisipasiStats(): array
    {
        return Cache::remember('mk.dashboard.dpm.partisipasi', 180, function () {
            $riwayatStatus = RiwayatKegiatan::selectRaw('verification_status, count(*) as total')
                ->groupBy('verification_status')
                ->pluck('total', 'verification_status')
                ->toArray();

            $studentRiwayatIds = RiwayatKegiatan::whereNotNull('student_id')->pluck('student_id');
            $studentPanitiaIds = DB::table('mk_kegiatan_panitia')->whereNotNull('student_id')->pluck('student_id');
            $mahasiswaTerlibat = $studentRiwayatIds->merge($studentPanitiaIds)->unique()->count();

            $perPeranRiwayat = RiwayatKegiatan::selectRaw("COALESCE(peran, peran_manual, 'lainnya') as peran, count(*) as total")
                ->groupByRaw("COALESCE(peran, peran_manual, 'lainnya')")
                ->orderByDesc('total')
                ->pluck('total', 'peran')
                ->toArray();

            $perPeranPanitia = DB::table('mk_kegiatan_panitia')
                ->selectRaw("COALESCE(peran, 'panitia') as peran, count(*) as total")
                ->groupByRaw("COALESCE(peran, 'panitia')")
                ->orderByDesc('total')
                ->pluck('total', 'peran')
                ->toArray();

            $perAngkatan = DB::table('mk_riwayat_kegiatan as rk')
                ->join('students as s', 'rk.student_id', '=', 's.id')
                ->selectRaw('s.cohort_year as angkatan, count(*) as total')
                ->whereNotNull('s.cohort_year')
                ->groupBy('s.cohort_year')
                ->orderBy('s.cohort_year')
                ->pluck('total', 'angkatan')
                ->toArray();

            return [
                'total_riwayat'      => array_sum($riwayatStatus),
                'riwayat_pending'    => $riwayatStatus[RiwayatKegiatan::VERIF_PENDING] ?? 0,
                'riwayat_approved'   => $riwayatStatus[RiwayatKegiatan::VERIF_APPROVED] ?? 0,
                'riwayat_rejected'   => $riwayatStatus[RiwayatKegiatan::VERIF_REJECTED] ?? 0,
                'total_panitia'      => DB::table('mk_kegiatan_panitia')->count(),
                'mahasiswa_terlibat' => $mahasiswaTerlibat,
                'per_peran_riwayat'  => $perPeranRiwayat,
                'per_peran_panitia'  => $perPeranPanitia,
                'per_angkatan'       => $perAngkatan,
                'top_mahasiswa'      => RiwayatKegiatan::selectRaw('student_id, count(*) as total')
                                        ->with('student.user')
                                        ->whereNotNull('student_id')
                                        ->groupBy('student_id')
                                        ->orderByDesc('total')
                                        ->limit(6)
                                        ->get(),
            ];
        });
    }

    /**
     * DPM - Prestasi dan workflow reward.
     */
    public function getDpmPrestasiRewardStats(): array
    {
        return Cache::remember('mk.dashboard.dpm.prestasi_reward', 180, function () {
            $verifCounts = Prestasi::selectRaw('verification_status, count(*) as total')
                ->groupBy('verification_status')
                ->pluck('total', 'verification_status')
                ->toArray();

            $rewardCounts = Prestasi::selectRaw('claim_status, count(*) as total')
                ->groupBy('claim_status')
                ->pluck('total', 'claim_status')
                ->toArray();

            return [
                'total_prestasi'      => array_sum($verifCounts),
                'prestasi_pending'    => $verifCounts[Prestasi::VERIF_PENDING] ?? 0,
                'prestasi_approved'   => $verifCounts[Prestasi::VERIF_APPROVED] ?? 0,
                'prestasi_rejected'   => $verifCounts[Prestasi::VERIF_REJECTED] ?? 0,
                'reward_belum_ajukan' => $rewardCounts[Prestasi::CLAIM_BELUM_AJUKAN] ?? 0,
                'reward_diajukan'     => $rewardCounts[Prestasi::CLAIM_DIAJUKAN] ?? 0,
                'reward_disetujui'    => $rewardCounts[Prestasi::CLAIM_DISETUJUI] ?? 0,
                'reward_ditolak'      => $rewardCounts[Prestasi::CLAIM_DITOLAK] ?? 0,
                'reward_sks_diajukan' => (int) Prestasi::where('claim_status', Prestasi::CLAIM_DIAJUKAN)->sum('reward_sks_max'),
                'reward_mk_diajukan'  => (int) Prestasi::where('claim_status', Prestasi::CLAIM_DIAJUKAN)->sum('reward_jml_mk_max'),
                'per_tingkat'         => Prestasi::approved()
                                        ->selectRaw('tingkat, count(*) as total')
                                        ->groupBy('tingkat')
                                        ->pluck('total', 'tingkat')
                                        ->toArray(),
                'reward_terbaru'      => Prestasi::where('claim_status', Prestasi::CLAIM_DIAJUKAN)
                                        ->with(['kemahasiswaan.user'])
                                        ->orderByDesc('claimed_at')
                                        ->limit(6)
                                        ->get(),
            ];
        });
    }

    /**
     * TIER 3 - Data Mahasiswa & Prestasi (cache 120 detik).
     *
     * Hitungan per status kemahasiswaan (aktif/cuti/DO/...) dan grafik per angkatan
     * dihapus (15 Nov 2026): statusnya dibekukan sejak S-53 dan kartunya sudah
     * dicabut dari dashboard. "Mahasiswa aktif" kini dari role (queryMahasiswaAktif()).
     */
    public function getMahasiswaStats(): array
    {
        return Cache::remember('mk.dashboard.tier3', 120, function () {
            $prestasiPerTingkat = Prestasi::approved()
                ->selectRaw('tingkat, count(*) as total')
                ->groupBy('tingkat')
                ->pluck('total', 'tingkat')
                ->toArray();

            return [
                'total_aktif'          => $this->queryMahasiswaAktif()->count(),
                'prestasi_per_tingkat' => $prestasiPerTingkat,
                'total_prestasi'       => array_sum($prestasiPerTingkat),
                // Counter prestasi menunggu review (FASE 2B) — tersedia lintas scope
                // tanpa bergantung pada section action_items (yang tak dimuat GPM).
                'prestasi_pending'     => Prestasi::pending()->count(),
                'prestasi_terbaru'     => Prestasi::approved()
                                            ->with(['kemahasiswaan.user'])
                                            ->orderByDesc('verified_at')
                                            ->limit(5)
                                            ->get(),
                // Bentuknya dipertahankan (nama, nim, angkatan, user) untuk tabel
                // "Mahasiswa Aktif Terbaru" di view.
                'mahasiswa_terbaru'    => $this->queryMahasiswaAktif()->with('user')
                                            ->orderByDesc('created_at')->limit(8)->get()
                                            ->map(fn (Student $s) => (object) [
                                                'nama'     => $s->user?->name,
                                                'nim'      => $s->student_number,
                                                'angkatan' => $s->cohort_year,
                                                'user'     => $s->user,
                                            ]),
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
     * Mengukur kualitas penyelenggaraan prodi: kelulusan, masa studi,
     * waktu tunggu kerja, dan responsivitas layanan pengaduan.
     */
    public function getEvaluasiMutu(): array
    {
        return Cache::remember('mk.dashboard.evaluasi', 300, function () {
            // Kelulusan per angkatan dari sumber yang masih diperbarui (15 Nov 2026):
            // seluruh mahasiswa angkatan itu = akun ber-role mahasiswa/alumni yang punya
            // data angkatan (students.cohort_year); lulus = sudah alumni (role alumni /
            // mk_alumni). Dulu dari status kemahasiswaan yang dibekukan sejak S-53. Drop
            // Out tidak lagi dihitung: tidak ada sumber status DO selain data beku itu
            // (status resmi di SIAP Undip).
            $idAlumni = $this->idUserAlumni();
            $mahasiswaPerAngkatan = Student::query()
                ->whereNotNull('cohort_year')
                ->whereHas('user', fn ($u) => $u->whereHas('roles', fn ($r) => $r->whereIn('name', ['mahasiswa', 'alumni'])))
                ->get(['user_id', 'cohort_year'])
                ->groupBy('cohort_year');

            // Masa studi normal prodi = 4 tahun. Angkatan yang belum mencapai
            // usia 4 tahun belum jatuh tempo kelulusan, jadi % kelulusan 0%
            // di angkatan tersebut bukan indikasi kegagalan.
            $currentYear   = now()->year;
            $masaStudiNormal = 4;

            $kelulusan = [];
            foreach ($mahasiswaPerAngkatan as $angkatan => $rows) {
                $total = $rows->count();
                $lulus = $rows->filter(fn ($s) => $idAlumni->contains((int) $s->user_id))->count();
                // Kohort dianggap jatuh tempo bila usianya >= masa studi normal
                $jatuhTempo = ($currentYear - (int) $angkatan) >= $masaStudiNormal;
                $kelulusan[$angkatan] = [
                    'total'        => $total,
                    'lulus'        => $lulus,
                    'jatuh_tempo'  => $jatuhTempo,
                    // Rate kelulusan hanya bermakna untuk kohort yang sudah jatuh tempo
                    'rate_lulus'   => ($jatuhTempo && $total > 0) ? round($lulus / $total * 100, 1) : 0,
                ];
            }
            ksort($kelulusan);

            // Rata-rata masa studi (tahun) dari alumni — ideal 4 tahun
            $masaStudiQuery = Alumni::whereNotNull('tahun_lulus')
                ->whereNotNull('angkatan')
                ->whereRaw('tahun_lulus >= angkatan');
            $sampleMasaStudi = (clone $masaStudiQuery)->count();
            $rataMasaStudi   = (float) (clone $masaStudiQuery)->avg(DB::raw('tahun_lulus - angkatan'));

            // Rata-rata waktu tunggu kerja (tahun) dari lulus sampai mulai bekerja
            $waktuTungguQuery = Alumni::whereNotNull('tahun_mulai_bekerja')
                ->whereNotNull('tahun_lulus')
                ->whereRaw('tahun_mulai_bekerja >= tahun_lulus');
            $sampleWaktuTunggu = (clone $waktuTungguQuery)->count();
            $rataWaktuTunggu   = (float) (clone $waktuTungguQuery)->avg(DB::raw('tahun_mulai_bekerja - tahun_lulus'));

            // Responsivitas layanan pengaduan — berbasis SLA (FASE 3C): % pengaduan
            // (non-draft) yang ditangani pertama kali dalam ≤ 7 hari sejak dibuat.
            // Lihat statistikPengaduan() — dulu memakai answered_at, yang tidak diisi lagi
            // sejak fitur jawab dihapus (pengaduan kini satu arah: Baru → Tercatat).
            $statPengaduan = $this->statistikPengaduan();
            $slaHari = $statPengaduan['sla_hari'];
            $totalPengaduan = $statPengaduan['total'];
            $pengaduanSlaTerpenuhi = $statPengaduan['sla_terpenuhi'];

            // Serapan kerja keseluruhan (bekerja + wirausaha dari yang terdata)
            $totalAlumni  = Alumni::count();
            $totalTerdata = Alumni::whereNotNull('status_karir')->count();
            $totalBekerja = Alumni::whereIn('status_karir', [Alumni::STATUS_BEKERJA, Alumni::STATUS_WIRAUSAHA])->count();
            // Ambang minimum sampel agar metrik persentase/rata-rata layak ditampilkan
            $minSampel = 3;

            // Kelulusan tepat waktu (FASE 3A) — % alumni yang lulus dalam ≤ 4 tahun
            $lulusTepatWaktu = (clone $masaStudiQuery)->whereRaw('tahun_lulus - angkatan <= 4')->count();
            $kelulusanTepatWaktu = $sampleMasaStudi > 0 ? round($lulusTepatWaktu / $sampleMasaStudi * 100) : 0;

            // Nilai metrik yang dibandingkan dengan target mutu
            $serapanKerja = $totalTerdata > 0 ? round($totalBekerja / $totalTerdata * 100) : 0;
            $responsivitas = $statPengaduan['responsivitas'];

            // ── Evaluasi Kegiatan Kemahasiswaan (bahan evaluasi GPM/Kadep) ──
            // Distribusi kegiatan per kategori — untuk menilai relevansi kurikulum
            $kegiatanPerKategori = DB::table('mk_kegiatan_kategori as kk')
                ->join('mk_kategori_kegiatan as kat', 'kk.kategori_kegiatan_id', '=', 'kat.id')
                ->tap(fn ($q) => $this->hanyaKegiatanUnik($q, 'kk.kegiatan_id'))
                ->selectRaw('kat.nama_kategori, count(*) as total')
                ->groupBy('kat.nama_kategori')
                ->orderByDesc('total')
                ->pluck('total', 'kat.nama_kategori')
                ->toArray();

            // Tingkat realisasi kegiatan — % kegiatan yang sudah terlaksana (selesai).
            // Dihitung per kegiatan (Kegiatan::scopeUnik): dulu baris rencana + salinannya
            // terhitung dua, sehingga penyebutnya membengkak dan persentasenya turun.
            $totalKegiatan   = Kegiatan::unik()->count();
            $kegiatanSelesai = Kegiatan::unik()->where('status', Kegiatan::STATUS_SELESAI)->count();
            $kegiatanBerjalan = Kegiatan::unik()->where('status', Kegiatan::STATUS_DISETUJUI)->count();
            $kegiatanTahunIni = Kegiatan::whereNull('rencana_id')->whereYear('created_at', now()->year)->count();

            // Breakdown SEMUA status kegiatan (FASE 3D) — agar jumlah konsisten dgn total.
            // Termasuk status legacy bila masih ada di data lama.
            $kegiatanPerStatus = Kegiatan::unik()->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Evaluasi terhadap target mutu (FASE 3A)
            $targets = [
                'masa_studi'            => $this->evaluateQualityTarget('masa_studi', round($rataMasaStudi, 1), $sampleMasaStudi >= $minSampel),
                'waktu_tunggu_kerja'    => $this->evaluateQualityTarget('waktu_tunggu_kerja', round($rataWaktuTunggu, 1), $sampleWaktuTunggu >= $minSampel),
                'serapan_kerja'         => $this->evaluateQualityTarget('serapan_kerja', $serapanKerja, $totalTerdata >= $minSampel),
                'kelulusan_tepat_waktu' => $this->evaluateQualityTarget('kelulusan_tepat_waktu', $kelulusanTepatWaktu, $sampleMasaStudi >= $minSampel),
            ];

            return [
                'kelulusan_per_angkatan' => $kelulusan,
                'rata_masa_studi'        => round($rataMasaStudi, 1),
                'rata_waktu_tunggu'      => round($rataWaktuTunggu, 1),
                // Responsivitas berbasis SLA (FASE 3C)
                'responsivitas'          => $responsivitas,
                'sla_hari'               => $slaHari,
                'pengaduan_sla_terpenuhi' => $pengaduanSlaTerpenuhi,
                'pengaduan_total'        => $totalPengaduan,
                'serapan_kerja'          => $serapanKerja,
                // Kelulusan tepat waktu (≤ 4 thn) untuk pembanding target
                'kelulusan_tepat_waktu'  => $kelulusanTepatWaktu,
                // ── Denominator & kecukupan data alumni (FASE 1A/1C) ──
                'total_alumni'           => $totalAlumni,
                'total_terdata'          => $totalTerdata,
                'min_sampel'             => $minSampel,
                'sample_masa_studi'      => $sampleMasaStudi,
                'sample_waktu_tunggu'    => $sampleWaktuTunggu,
                // Kelengkapan data karir = alumni terdata / total alumni
                'kelengkapan_data_alumni' => $totalAlumni > 0 ? round($totalTerdata / $totalAlumni * 100) : 0,
                // Target mutu pembanding (FASE 3A)
                'targets'                => $targets,
                // Evaluasi kegiatan kemahasiswaan
                'kegiatan_per_kategori'  => $kegiatanPerKategori,
                'kegiatan_per_status'    => $kegiatanPerStatus,
                'kegiatan_total'         => $totalKegiatan,
                'kegiatan_selesai'       => $kegiatanSelesai,
                'kegiatan_berjalan'      => $kegiatanBerjalan,
                'kegiatan_tahun_ini'     => $kegiatanTahunIni,
                'rate_realisasi_kegiatan' => $totalKegiatan > 0 ? round($kegiatanSelesai / $totalKegiatan * 100) : 0,
            ];
        });
    }

    /**
     * Mahasiswa aktif menurut sumber yang masih diperbarui: akun ber-role `mahasiswa`,
     * bukan alumni (role `alumni` / baris mk_alumni), dan punya data angkatan
     * (students.cohort_year). Pengganti Kemahasiswaan::aktif() yang statusnya
     * dibekukan sejak S-53 (status resmi kini di SIAP Undip). Dipakai juga oleh
     * popup DashboardController::modalData() supaya angka kartu & popup sama.
     *
     * @return \Illuminate\Database\Eloquent\Builder<Student>
     */
    public function queryMahasiswaAktif()
    {
        return Student::query()
            ->whereNotNull('cohort_year')
            ->whereHas('user', fn ($u) => $u
                ->whereHas('roles', fn ($r) => $r->where('name', 'mahasiswa'))
                ->whereDoesntHave('roles', fn ($r) => $r->where('name', 'alumni')))
            ->whereNotIn('user_id', Alumni::whereNotNull('user_id')->select('user_id'));
    }

    /**
     * ID user yang sudah lulus: ber-role `alumni` atau tercatat di mk_alumni.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function idUserAlumni()
    {
        return User::role('alumni')->pluck('id')
            ->merge(Alumni::whereNotNull('user_id')->pluck('user_id'))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    /** Akun ber-role alumni yang belum punya baris di direktori alumni (mk_alumni). */
    private function alumniBelumSinkron(): int
    {
        return User::role('alumni')
            ->whereNotIn('id', Alumni::whereNotNull('user_id')->select('user_id'))
            ->count();
    }

    /** Aksi pengelola di mk_pengaduan_log yang dihitung sebagai penanganan pengaduan. */
    private const AKSI_PENANGANAN_PENGADUAN = [
        PengaduanLog::ACTION_TERCATAT,
        PengaduanLog::ACTION_DIDELEGASIKAN,
        PengaduanLog::ACTION_DITANGGAPI_DOSEN,
        PengaduanLog::ACTION_DIJAWAB,
        PengaduanLog::ACTION_DITUTUP_ADMIN,
    ];

    /**
     * Statistik layanan pengaduan dengan definisi yang sama dengan halaman Pengaduan.
     *
     * Status tampilan pengaduan kini hanya dua: "Tercatat" (Pengaduan::TERCATAT_STATUSES)
     * dan "Baru" (selain itu) — "sudah dibaca" diambil dari read_at. Waktu penanganan
     * pertama diambil dari log aksi pengelola (AKSI_PENANGANAN_PENGADUAN), dengan
     * answered_at sebagai cadangan untuk data alur lama. answered_at sendiri tidak
     * diisi lagi sejak fitur jawab dihapus.
     *
     *  - sla_lewat     : masih "Baru" dan sudah lebih dari SLA hari sejak dibuat.
     *  - sla_terpenuhi : ditangani pertama kali ≤ SLA hari sejak dibuat.
     *  - responsivitas : sla_terpenuhi / total (%), dipakai admin & GPM.
     *
     * @return array{sla_hari:int, total:int, baru:int, baru_belum_dibaca:int, baru_dibaca:int,
     *               tercatat:int, sla_lewat:int, sla_terpenuhi:int, responsivitas:int|float}
     */
    private function statistikPengaduan(): array
    {
        $slaHari = 7;

        $daftar = Pengaduan::where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->get(['id', 'status', 'read_at', 'answered_at', 'created_at']);

        $penangananPertama = PengaduanLog::whereIn('pengaduan_id', $daftar->pluck('id'))
            ->whereIn('action', self::AKSI_PENANGANAN_PENGADUAN)
            ->selectRaw('pengaduan_id, MIN(created_at) as pertama')
            ->groupBy('pengaduan_id')
            ->pluck('pertama', 'pengaduan_id');

        $tercatat = $daftar->filter(fn ($p) => $p->isTercatat());
        $baru     = $daftar->reject(fn ($p) => $p->isTercatat());

        $slaTerpenuhi = $daftar->filter(function ($p) use ($penangananPertama, $slaHari) {
            $pertama = $penangananPertama->get($p->id) ?? $p->answered_at;

            return $pertama && Carbon::parse($pertama)->lte($p->created_at->copy()->addDays($slaHari));
        })->count();

        return [
            'sla_hari'          => $slaHari,
            'total'             => $daftar->count(),
            'baru'              => $baru->count(),
            'baru_belum_dibaca' => $baru->whereNull('read_at')->count(),
            'baru_dibaca'       => $baru->whereNotNull('read_at')->count(),
            'tercatat'          => $tercatat->count(),
            'sla_lewat'         => $baru->filter(fn ($p) => $p->created_at->lt(now()->subDays($slaHari)))->count(),
            'sla_terpenuhi'     => $slaTerpenuhi,
            'responsivitas'     => $daftar->isNotEmpty() ? round($slaTerpenuhi / $daftar->count() * 100) : 0,
        ];
    }

    /**
     * Batasi query pivot kegiatan (bidang/kategori) ke satu baris per kegiatan —
     * padanan Kegiatan::scopeUnik untuk query builder: buang baris rencana yang
     * sudah punya salinan (pivotnya ikut tersalin saat Ajukan Proker).
     */
    private function hanyaKegiatanUnik($query, string $kolomKegiatanId): void
    {
        $query->whereNotExists(fn ($sub) => $sub
            ->from('mk_kegiatan as salinan')
            ->whereColumn('salinan.rencana_id', $kolomKegiatanId));
    }

    /**
     * Rencana vs realisasi kegiatan.
     *
     * Sejak S-49 rencana disalin ke Pelaksanaan saat diajukan, lalu isian salinan
     * diubah sesuai realisasi (peserta, anggaran) — jadi baris RENCANA = rencana,
     * baris SALINAN = realisasi. Dihitung hanya untuk kegiatan yang realisasinya
     * sudah dicatat: sudah diperbarui di Pelaksanaan (is_pelaksanaan_updated) atau
     * sudah diunggah ke Arsip. Kolom lama realisasi_* tidak pernah diisi form mana
     * pun, jadi tidak dipakai.
     *
     * @return array{jumlah_kegiatan:int, peserta_rencana:int, peserta_realisasi:int, persen_peserta:int|float,
     *               anggaran_rencana:float, anggaran_realisasi:float, persen_anggaran:int|float, belum_diperbarui:int}
     */
    private function realisasiRencanaVsPelaksanaan(): array
    {
        $pasangan = DB::table('mk_kegiatan as realisasi')
            ->join('mk_kegiatan as rencana', 'rencana.id', '=', 'realisasi.rencana_id')
            ->where(fn ($q) => $q
                ->where('realisasi.status', Kegiatan::STATUS_SELESAI)
                ->orWhere(fn ($p) => $p
                    ->where('realisasi.status', Kegiatan::STATUS_DISETUJUI)
                    ->where('realisasi.is_pelaksanaan_updated', true)));

        $peserta = (clone $pasangan)
            ->whereNotNull('rencana.target_peserta')
            ->whereNotNull('realisasi.target_peserta')
            ->selectRaw('COALESCE(SUM(rencana.target_peserta), 0) as rencana, COALESCE(SUM(realisasi.target_peserta), 0) as realisasi')
            ->first();

        $anggaran = (clone $pasangan)
            ->whereNotNull('rencana.anggaran')
            ->whereNotNull('realisasi.anggaran')
            ->selectRaw('COALESCE(SUM(rencana.anggaran), 0) as rencana, COALESCE(SUM(realisasi.anggaran), 0) as realisasi')
            ->first();

        $pesertaRencana    = (int) $peserta->rencana;
        $pesertaRealisasi  = (int) $peserta->realisasi;
        $anggaranRencana   = (float) $anggaran->rencana;
        $anggaranRealisasi = (float) $anggaran->realisasi;

        return [
            'jumlah_kegiatan'    => (clone $pasangan)->count(),
            'peserta_rencana'    => $pesertaRencana,
            'peserta_realisasi'  => $pesertaRealisasi,
            'persen_peserta'     => $pesertaRencana > 0 ? round($pesertaRealisasi / $pesertaRencana * 100) : 0,
            'anggaran_rencana'   => $anggaranRencana,
            'anggaran_realisasi' => $anggaranRealisasi,
            'persen_anggaran'    => $anggaranRencana > 0 ? round($anggaranRealisasi / $anggaranRencana * 100) : 0,
            'belum_diperbarui'   => Kegiatan::pelaksanaan()
                ->where(fn ($q) => $q->where('is_pelaksanaan_updated', false)->orWhereNull('is_pelaksanaan_updated'))
                ->count(),
        ];
    }

    /**
     * Evaluasi sebuah nilai metrik terhadap target mutu di config/quality_targets.
     *
     * @param  string      $metricKey  Kunci metrik (mis. 'masa_studi').
     * @param  float|null  $value      Nilai metrik aktual.
     * @param  bool        $cukup      Apakah sampel cukup untuk dinilai.
     * @return array  ['target','comparison','label','unit','status'] — status: tercapai|tidak|kurang.
     */
    private function evaluateQualityTarget(string $metricKey, ?float $value, bool $cukup): array
    {
        $cfg = config("quality_targets.$metricKey");
        if (!$cfg) {
            return [];
        }

        $status = 'kurang'; // data belum cukup
        if ($cukup && $value !== null) {
            $tercapai = $cfg['comparison'] === '<='
                ? $value <= $cfg['target']
                : $value >= $cfg['target'];
            $status = $tercapai ? 'tercapai' : 'tidak';
        }

        return [
            'target'     => $cfg['target'],
            'comparison' => $cfg['comparison'],
            'label'      => $cfg['label'],
            'unit'       => $cfg['unit'] ?? '',
            'status'     => $status,
        ];
    }

    /**
     * Ambang angkatan deteksi dini DO. Mahasiswa masuk Agustus, 1 tahun = 2 semester:
     * angkatan ≤ `pantau` sudah semester ≥ 9, angkatan ≤ `kritis` sudah semester ≥ 12.
     *
     * @return array{pantau:int, kritis:int}
     */
    public function ambangCalonDO(): array
    {
        $tahun = now()->year;

        return [
            'pantau' => $tahun - (now()->month >= 8 ? 4 : 5),
            'kritis' => $tahun - 6,
        ];
    }

    /** Estimasi semester berjalan sebuah angkatan (semester ganjil mulai Agustus). */
    public function semesterAngkatan(int $angkatan): int
    {
        $tahunBerlalu = now()->year - $angkatan;

        return now()->month >= 8 ? ($tahunBerlalu * 2 + 1) : ($tahunBerlalu * 2);
    }

    /**
     * EVALUASI CALON DO — deteksi dini bertingkat (FASE 3B).
     *  - "Perlu Pemantauan": mahasiswa aktif semester ≥ 9
     *  - "Kritis": mahasiswa aktif semester ≥ 12
     *
     * "Mahasiswa aktif" dari queryMahasiswaAktif() (role mahasiswa, bukan alumni) —
     * dulu dari status kemahasiswaan yang dibekukan sejak S-53, sehingga mahasiswa yang
     * sudah lulus/keluar ikut terhitung. Semester tetap perkiraan dari angkatan.
     */
    public function getCalonDO(): array
    {
        return Cache::remember('mk.dashboard.calon_do', 300, function () {
            $ambang = $this->ambangCalonDO();

            $kandidat = $this->queryMahasiswaAktif()
                ->where('cohort_year', '<=', $ambang['pantau'])
                ->with('user:id,name')
                ->get(['id', 'user_id', 'student_number', 'cohort_year'])
                // angkatan terlama (paling kritis) dahulu, lalu nama
                ->sortBy(fn (Student $s) => sprintf('%04d|%s', $s->cohort_year, mb_strtolower((string) $s->user?->name)))
                ->values();

            $kritisCount = $kandidat->where('cohort_year', '<=', $ambang['kritis'])->count();

            $list = $kandidat->take(12)->map(function (Student $s) {
                $semester = $this->semesterAngkatan((int) $s->cohort_year);

                return [
                    'nama'     => $s->user?->name ?? '-',
                    'nim'      => $s->student_number ?? '-',
                    'angkatan' => (int) $s->cohort_year,
                    'semester' => $semester,
                    'tier'     => $semester >= 12 ? 'kritis' : 'pantau',
                ];
            });

            return [
                'total_count'      => $kandidat->count(),
                'kritis_count'     => $kritisCount,
                'pantau_count'     => $kandidat->count() - $kritisCount,
                'threshold_pantau' => $ambang['pantau'],
                'threshold_kritis' => $ambang['kritis'],
                'list'             => $list,
            ];
        });
    }

    /**
     * LULUSAN PER PERIODE — jumlah lulusan per tahun lulus dari direktori alumni
     * (mk_alumni), plus akun ber-role alumni yang belum punya baris direktori.
     * Dulu dari status kemahasiswaan 'alumni' yang dibekukan sejak S-53.
     */
    public function getLulusanPerPeriode(): array
    {
        return Cache::remember('mk.dashboard.lulusan', 300, function () {
            $perTahun = Alumni::whereNotNull('tahun_lulus')
                ->selectRaw('tahun_lulus, count(*) as total')
                ->groupBy('tahun_lulus')
                ->orderBy('tahun_lulus')
                ->pluck('total', 'tahun_lulus')
                ->toArray();

            return [
                'per_tahun'        => $perTahun,
                'total_lulus'      => array_sum($perTahun),
                'belum_sinkron'    => $this->alumniBelumSinkron(),
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
        Cache::forget('mk.dashboard.admin.operasional');
        Cache::forget('mk.dashboard.tier2');
        Cache::forget('mk.dashboard.tier3');
        Cache::forget('mk.dashboard.tier4');
        Cache::forget('mk.dashboard.evaluasi');
        Cache::forget('mk.dashboard.dpm.himpunan');
        Cache::forget('mk.dashboard.dpm.proker');
        Cache::forget('mk.dashboard.dpm.pelaksanaan');
        Cache::forget('mk.dashboard.dpm.partisipasi');
        Cache::forget('mk.dashboard.dpm.prestasi_reward');
        Cache::forget('mk.dashboard.calon_do');
        Cache::forget('mk.dashboard.lulusan');
    }
}
