<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        }

        // Admin module roles are redirected by RedirectBasedOnRole middleware,
        // but guard here in case middleware is bypassed.
        if ($user->hasRole('admin_banksoal'))      return redirect()->route('banksoal.dashboard');
        if ($user->hasRole('admin_capstone'))      return redirect()->route('capstone.dashboard');
        if ($user->hasRole('admin_eoffice'))       return redirect()->route('eoffice.dashboard');
        if ($user->hasRole('admin_kemahasiswaan')) return redirect()->route('manajemenmahasiswa.dashboard');

        $isMahasiswa       = $user->hasRole('mahasiswa');
        $isDosen           = $user->hasRole('dosen') || $user->hasRole('dosen_koor');
        $isAlumni          = $user->hasRole('alumni');
        $isPengurusHimpunan = $user->hasRole('pengurus_himpunan');

        // Bank Soal card — mahasiswa goes to komprehensif, alumni sees riwayat only
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

        // Manajemen Mahasiswa card
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

        return view('dashboard', compact('cards'));
    }
}