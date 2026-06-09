<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Pengumuman;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

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

        // ── Announcements ────────────────────────────────────────────────────
        // Tab eoffice: gabungan periode pendaftaran aktif + pengumuman praktikum published
        $eofficeItems = collect();

        // 1. Periode pendaftaran yang sedang aktif/baru dibuka (maks 5 terbaru)
        $periodeAktif = PeriodePendaftaran::with(['praktikum', 'dibukaOleh'])
            ->where('is_aktif', true)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($periodeAktif as $periode) {
            $jenisLabel = $periode->jenis === 'koor' ? 'Koordinator' : 'Asisten Praktikum';
            $deadline   = $periode->ditutup_pada
                ? ' · Batas: ' . $periode->ditutup_pada->format('d M Y H:i')
                : '';

            $eofficeItems->push([
                'module'  => 'eoffice',
                'date'    => $periode->created_at?->diffForHumans() ?? '',
                'title'   => "📢 Pendaftaran {$jenisLabel} Dibuka — {$periode->praktikum?->nama}",
                'body'    => $periode->nama . $deadline,
                'pinned'  => true,
            ]);
        }

        // 2. Periode pendaftaran yang baru saja ditutup (is_aktif=false, dibuat < 7 hari)
        $periodeBaru = PeriodePendaftaran::with(['praktikum'])
            ->where('is_aktif', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($periodeBaru as $periode) {
            $jenisLabel = $periode->jenis === 'koor' ? 'Koordinator' : 'Asisten Praktikum';
            $eofficeItems->push([
                'module' => 'eoffice',
                'date'   => $periode->created_at?->diffForHumans() ?? '',
                'title'  => "Pendaftaran {$jenisLabel} Ditutup — {$periode->praktikum?->nama}",
                'body'   => 'Periode pendaftaran telah ditutup.',
                'pinned' => false,
            ]);
        }

        // 3. Pengumuman praktikum yang published (maks 5 terbaru)
        $pengumumanPraktikum = Pengumuman::with(['praktikum', 'user'])
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        foreach ($pengumumanPraktikum as $peng) {
            $eofficeItems->push([
                'module' => 'eoffice',
                'date'   => $peng->created_at?->diffForHumans() ?? '',
                'title'  => $peng->judul,
                'body'   => $peng->konten,
                'pinned' => false,
            ]);
        }

        // Sort semua eoffice items: pinned dulu, lalu by waktu (latest first)
        $eofficeItems = $eofficeItems->sortByDesc(fn($i) => [$i['pinned'] ? 1 : 0])->values()->all();

        // Placeholder untuk modul lain (bisa diisi nanti oleh masing-masing modul)
        $announcements = [
            'bank_soal'     => [],
            'capstone'      => [],
            'kemahasiswaan' => [],
            'eoffice'       => $eofficeItems,
        ];

        // Tab "all" = gabungan semua, diurutkan: pinned eoffice dulu, sisanya by date
        $allItems = collect();
        foreach ($announcements as $key => $items) {
            foreach ($items as $item) {
                $allItems->push(array_merge($item, ['module' => $key]));
            }
        }
        $announcements['all'] = $allItems->sortByDesc(fn($i) => [$i['pinned'] ?? false ? 1 : 0])->values()->all();

        $announcementCounts = [
            'bank_soal'     => count($announcements['bank_soal']),
            'capstone'      => count($announcements['capstone']),
            'kemahasiswaan' => count($announcements['kemahasiswaan']),
            'eoffice'       => count($announcements['eoffice']),
        ];

        return view('dashboard', compact('cards', 'announcements', 'announcementCounts'));
    }
}
