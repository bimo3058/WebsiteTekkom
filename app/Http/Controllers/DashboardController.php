<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\BankSoal\Services\DashboardAnnouncementService;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Services\PeriodePendaftaranService;
use Modules\ManajemenMahasiswa\Models\Pengumuman as MkPengumuman;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        app(PeriodePendaftaranService::class)->tutupKadaluarsa();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->hasRole('admin_banksoal'))      return redirect()->route('banksoal.dashboard');
        if ($user->hasRole('admin_capstone'))      return redirect()->route('capstone.dashboard');
        if ($user->hasRole('admin_eoffice'))       return redirect()->route('eoffice.dashboard');
        if ($user->hasRole('admin_kemahasiswaan')) return redirect()->route('manajemenmahasiswa.dashboard');

        $isMahasiswa        = $user->hasRole('mahasiswa');
        $isDosen            = $user->hasRole('dosen') || $user->hasRole('dosen_koor');
        $isAlumni           = $user->hasRole('alumni');
        $isPengurusHimpunan = $user->hasRole('pengurus_himpunan');

        // ── Module cards ────────────────────────────────────────────────────
        if ($isMahasiswa) {
            $bankSoalCard = [
                'icon'        => 'quiz',
                'title'       => 'Ujian Komprehensif',
                'description' => 'Ikuti ujian komprehensif online.',
                'route'       => 'komprehensif.mahasiswa.dashboard',
                'color'       => 'blue',
            ];
        } elseif ($isDosen) {
            $bankSoalCard = [
                'icon'        => 'description',
                'title'       => 'Manajemen RPS / Bank Soal',
                'description' => 'Buat dan kelola RPS mata kuliah.',
                'route'       => 'banksoal.dashboard',
                'color'       => 'blue',
            ];
        } else {
            $bankSoalCard = [
                'icon'        => 'menu_book',
                'title'       => 'Bank Soal',
                'description' => 'Akses soal dan riwayat ujian.',
                'route'       => 'banksoal.dashboard',
                'color'       => 'blue',
            ];
        }

        if ($isMahasiswa || $isPengurusHimpunan) {
            $manajemenTitle = $isPengurusHimpunan ? 'Forum & Kegiatan' : 'Forum Mahasiswa';
            $manajemenDesc  = 'Kegiatan, prestasi, dan forum mahasiswa.';
        } elseif ($isAlumni) {
            $manajemenTitle = 'Portal Alumni';
            $manajemenDesc  = 'Informasi alumni dan jaringan.';
        } else {
            $manajemenTitle = 'Manajemen Mahasiswa';
            $manajemenDesc  = 'Kegiatan, alumni, dan forum mahasiswa.';
        }

        $cards = [
            $bankSoalCard,
            [
                'icon'        => 'school',
                'title'       => 'Capstone & TA',
                'description' => $isMahasiswa
                    ? 'Lihat progress capstone dan tugas akhir.'
                    : 'Manajemen capstone dan tugas akhir.',
                'route'       => 'capstone.dashboard',
                'color'       => 'purple',
            ],
            [
                'icon'        => 'groups',
                'title'       => $manajemenTitle,
                'description' => $manajemenDesc,
                'route'       => 'manajemenmahasiswa.dashboard',
                'color'       => 'green',
            ],
            [
                'icon'        => 'folder_open',
                'title'       => 'E-Office',
                'description' => $isMahasiswa
                    ? 'Lihat pengumuman dan dokumen.'
                    : 'Manajemen dokumen dan workflow.',
                'route'       => 'eoffice.dashboard',
                'color'       => 'orange',
            ],
        ];

        // ── Pengumuman ────────────────────────────────────────────────────────
        $bankSoalAnnouncements = [];

        try {
            // Only generate personalised announcements for mahasiswa
            if ($isMahasiswa) {
                $service = app(DashboardAnnouncementService::class);
                $bankSoalAnnouncements = $service->getForDashboard($user->id);
            }
        } catch (\Throwable $e) {
            // Graceful degradation: if BankSoal module is unavailable, show empty list
            logger()->warning('DashboardAnnouncementService failed: ' . $e->getMessage());
        }

        $announcements = [
            'all'           => $bankSoalAnnouncements,
            'bank_soal'     => $bankSoalAnnouncements,
            'capstone'      => [],
            'kemahasiswaan' => [],
            'eoffice'       => [],
        ];

        $announcementCounts = [
            'all'           => count($bankSoalAnnouncements),
            'bank_soal'     => count($bankSoalAnnouncements),
            'capstone'      => 0,
            'kemahasiswaan' => 0,
            'eoffice'       => 0,
        ];
        // ── Announcements ────────────────────────────────────────────────────
        // Tab eoffice: gabungan periode pendaftaran aktif + pengumuman praktikum published
        $eofficeItems = collect();

        // 1. Periode pendaftaran yang sedang aktif/baru dibuka (maks 5 terbaru)
        $periodeAktif = PeriodePendaftaran::with(['praktikum', 'dibukaOleh'])
            ->where('is_aktif', true)
            ->where(fn ($q) => $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>', now()))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($periodeAktif as $periode) {
            $jenisLabel = match ($periode->jenis) {
                'koor' => 'Koordinator',
                'asprak' => 'Asisten Praktikum',
                'praktikan' => 'Praktikan',
                default => ucfirst($periode->jenis),
            };
            $deadline = $periode->ditutup_pada
                ? ' · Batas: ' . $periode->ditutup_pada->format('d M Y H:i')
                : '';

            $url = null;
            if ($isMahasiswa) {
                $url = route('eoffice.manprak.mahasiswa.daftar-asprak.index', ['praktikum_id' => $periode->praktikum_id]);
            }

            $eofficeItems->push([
                'module' => 'eoffice',
                'date'   => $periode->created_at?->diffForHumans() ?? '',
                'title'  => "📢 Pendaftaran {$jenisLabel} Dibuka — {$periode->praktikum?->nama}",
                'body'   => $periode->nama . $deadline,
                'pinned' => true,
                '_ts'    => $periode->created_at?->timestamp ?? 0,
                'url'    => $url,
            ]);
        }

        // 2. Periode pendaftaran yang baru saja ditutup (is_aktif=false, dibuat < 7 hari)
        $periodeBaru = collect();

        foreach ($periodeBaru as $periode) {
            $jenisLabel = $periode->jenis === 'koor' ? 'Koordinator' : 'Asisten Praktikum';
            $eofficeItems->push([
                'module' => 'eoffice',
                'date'   => $periode->created_at?->diffForHumans() ?? '',
                'title'  => "Pendaftaran {$jenisLabel} Ditutup — {$periode->praktikum?->nama}",
                'body'   => 'Periode pendaftaran telah ditutup.',
                'pinned' => false,
                '_ts'    => $periode->created_at?->timestamp ?? 0,
            ]);
        }

        // 3. Pengumuman praktikum yang published (maks 5 terbaru)
        $pengumumanPraktikum = Pengumuman::with(['praktikum', 'user'])
            ->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('tipe_sistem')->orWhere('tipe_sistem', 'buka'))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($pengumumanPraktikum as $peng) {
            $url = null;
            if ($isMahasiswa) {
                if ($peng->tipe_sistem === 'buka') {
                    $url = route('eoffice.manprak.mahasiswa.daftar-asprak.index', ['praktikum_id' => $peng->praktikum_id]);
                } else {
                    $url = route('eoffice.manprak.mahasiswa.pengumuman.index', ['praktikum_id' => $peng->praktikum_id]);
                }
            }

            $eofficeItems->push([
                'module' => 'eoffice',
                'date'   => $peng->created_at?->diffForHumans() ?? '',
                'title'  => $peng->judul,
                'body'   => $peng->konten,
                'pinned' => false,
                '_ts'    => $peng->created_at?->timestamp ?? 0,
                'url'    => $url,
            ]);
        }

        // Sort eoffice: pinned dulu, lalu by timestamp terbaru
        $eofficeItems = $eofficeItems
            ->sortByDesc(fn ($i) => [$i['pinned'] ? 1 : 0, $i['_ts'] ?? 0])
            ->values()
            ->all();

        // Tab kemahasiswaan: pengumuman published dari ManajemenMahasiswa (5 terbaru, pinned global dulu)
        $kemahasiswaanItems = MkPengumuman::published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'module' => 'kemahasiswaan',
                'date'   => ($p->published_at ?? $p->created_at)?->diffForHumans() ?? '',
                'title'  => $p->judul,
                'body'   => Str::limit(strip_tags($p->konten), 120),
                'pinned' => (bool) $p->is_pinned,
                'url'    => route('manajemenmahasiswa.pengumuman.show', $p->id),
                '_ts'    => ($p->published_at ?? $p->created_at)?->timestamp ?? 0,
            ])
            ->all();

        $announcements = [
            'bank_soal'     => [],
            'capstone'      => [],
            'kemahasiswaan' => $kemahasiswaanItems,
            'eoffice'       => $eofficeItems,
        ];

        // Tab "all" = gabungan semua modul, diurutkan: pinned dulu, lalu timestamp terbaru
        // Fix bug: sebelumnya hanya sort by pinned tanpa secondary sort by tanggal,
        // sehingga urutan bergantung pada urutan insert (kemahasiswaan dulu, baru eoffice).
        $allItems = collect();
        foreach ($announcements as $key => $items) {
            foreach ($items as $item) {
                $allItems->push(array_merge($item, ['module' => $key]));
            }
        }
        $announcements['all'] = $allItems
            ->sortByDesc(fn ($i) => [$i['pinned'] ? 1 : 0, $i['_ts'] ?? 0])
            ->values()
            ->all();

        $announcementCounts = [
            'bank_soal'     => count($announcements['bank_soal']),
            'capstone'      => count($announcements['capstone']),
            'kemahasiswaan' => count($announcements['kemahasiswaan']),
            'eoffice'       => count($announcements['eoffice']),
        ];

        return view('dashboard', compact('cards', 'announcements', 'announcementCounts'));
    }
}
