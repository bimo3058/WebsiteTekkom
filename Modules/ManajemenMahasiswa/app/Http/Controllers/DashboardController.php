<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Urutan prioritas role spesifik modul kemahasiswaan
    // Makin atas = makin prioritas
    private const ROLE_PRIORITY = [
        'superadmin',
        'admin_kemahasiswaan',
        'gpm',
        'dosen',
        'pengurus_himpunan', // ← spesifik modul, prioritas di atas mahasiswa
        'alumni',
        'mahasiswa',
    ];

    public function index(Request $request)
    {
        $user  = auth()->user();
        $roles = $user->getCachedRoles()->pluck('name')->map(fn($r) => strtolower($r));

        if (!$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        // Cek apakah user minta switch ke mode mahasiswa
        $activeMode = session('mk_active_mode_' . $user->id);

        // Kalau user punya role pengurus/alumni DAN mahasiswa,
        // dan belum ada mode aktif → default ke role prioritas tertinggi
        if (!$activeMode) {
            foreach (self::ROLE_PRIORITY as $priority) {
                if ($roles->contains($priority)) {
                    $activeMode = $priority;
                    session(['mk_active_mode_' . $user->id => $activeMode]);
                    break;
                }
            }
        }

        return $this->renderDashboard($activeMode);
    }

    public function switchMode(Request $request)
    {
        $user      = auth()->user();
        $roles     = $user->getCachedRoles()->pluck('name')->map(fn($r) => strtolower($r));
        $targetMode = $request->input('mode');

        // Validasi — user hanya bisa switch ke role yang dia punya
        if (!$roles->contains($targetMode)) {
            abort(403, 'Anda tidak memiliki role tersebut.');
        }

        session(['mk_active_mode_' . $user->id => $targetMode]);

        return redirect()->route('manajemenmahasiswa.mahasiswa.dashboard')
            ->with('success', 'Mode berhasil diubah.');
    }

    private function renderDashboard(string $mode)
    {
        $kemahasiswaan = app(KemahasiswaanController::class);

        return match($mode) {
            'superadmin', 'admin_kemahasiswaan' => $kemahasiswaan->adminDashboard(),
            'dosen', 'gpm'                      => $kemahasiswaan->dosenDashboard(),
            'pengurus_himpunan'                 => $kemahasiswaan->pengurusDashboard(),
            'alumni'                            => $kemahasiswaan->alumniDashboard(),
            'mahasiswa'                         => $kemahasiswaan->mahasiswaDashboard(),
            default                             => abort(403, 'Akses Ditolak.'),
        };
    }
}