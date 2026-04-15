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

        $isMahasiswa = $user->hasRole('mahasiswa');
        $isDosen = $user->hasRole('dosen');
        // $isDosen     = $user->hasRole('dosen') && !$user->hasRole('gpm');

        $cards = [
            [
                'icon' => $isMahasiswa ? 'quiz' : ($isDosen ? 'description' : 'menu_book'),
                'title' => $isMahasiswa ? 'Ujian Komprehensif' : ($isDosen ? 'Manajemen RPS / Bank Soal' : 'Bank Soal'),
                'description' => $isMahasiswa
                    ? 'Ikuti ujian komprehensif online.'
                    : ($isDosen
                        ? 'Buat dan kelola RPS mata kuliah.'
                        : 'Kelola soal, RPS, dan kompre.'),
                'route' => $isMahasiswa ? 'komprehensif.mahasiswa.dashboard' : 'banksoal.dashboard',
                'color' => 'blue',
            ],
            [
                'icon' => 'school',
                'title' => 'Capstone & TA',
                'description' => $isMahasiswa
                    ? 'Lihat progress capstone dan tugas akhir.'
                    : 'Manajemen capstone dan tugas akhir.',
                'route' => 'capstone.dashboard',
                'color' => 'purple',
            ],
            [
                'icon' => 'groups',
                'title' => $isMahasiswa ? 'Forum Mahasiswa' : 'Manajemen Mahasiswa',
                'description' => $isMahasiswa
                    ? 'Kegiatan, prestasi, dan forum mahasiswa.'
                    : 'Kegiatan, alumni, dan forum mahasiswa.',
                'route' => $isMahasiswa ? 'manajemenmahasiswa.mahasiswa.dashboard' : 'manajemenmahasiswa.dashboard',
                'color' => 'green',
            ],
            [
                'icon' => 'folder_open',
                'title' => 'E-Office',
                'description' => $isMahasiswa
                    ? 'Lihat pengumuman dan dokumen.'
                    : 'Manajemen dokumen dan workflow.',
                'route' => 'eoffice.dashboard',
                'color' => 'orange',
            ],
        ];

        return view('dashboard', compact('cards'));
    }
}