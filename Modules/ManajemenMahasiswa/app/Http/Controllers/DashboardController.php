<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\DashboardAnalitikService;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardAnalitikService $analitikService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        if (
            \in_array('superadmin', $roles) ||
            \in_array('admin', $roles) ||
            \in_array('admin_kemahasiswaan', $roles) ||
            \in_array('gpm', $roles) ||
            \in_array('pengurus_himpunan', $roles)
        ) {
            $snapshot = $this->analitikService->getSnapshot();

            // Status breakdown untuk donut chart
            $statusMahasiswa = [
                'aktif' => Kemahasiswaan::where('status', Kemahasiswaan::STATUS_AKTIF)->count(),
                'cuti' => Kemahasiswaan::where('status', Kemahasiswaan::STATUS_CUTI)->count(),
                'do' => Kemahasiswaan::where('status', Kemahasiswaan::STATUS_DO)->count(),
                'lulus' => Kemahasiswaan::where('status', Kemahasiswaan::STATUS_ALUMNI)->count(),
            ];

            // 10 alumni terbaru untuk tabel serapan
            $serapanAlumni = Alumni::with('user')
                ->latest()
                ->limit(10)
                ->get();

            // Data analytics alumni baru
            $alumniService = app(\Modules\ManajemenMahasiswa\Services\AlumniService::class);
            $serapanPerAngkatan = $alumniService->getSerapanPerAngkatan();
            $distribusiIndustri = $alumniService->getDistribusiIndustri();

            return view('manajemenmahasiswa::dashboard.dashboard-analitik', compact(
                'snapshot',
                'statusMahasiswa',
                'serapanAlumni',
                'serapanPerAngkatan',
                'distribusiIndustri',
            ));
        }

        if (\in_array('dosen', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        if (\in_array('mahasiswa', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        if (\in_array('alumni', $roles)) {
            return redirect()->route('manajemenmahasiswa.pengumuman.index');
        }

        abort(403);
    }

    public function switchMode()
    {
        return redirect()->back();
    }
}
