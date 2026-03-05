<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Kemahasiswaan\DashboardService;
use App\Services\Kemahasiswaan\ForumService;
use App\Services\Kemahasiswaan\KegiatanService;
use App\Services\Kemahasiswaan\KemahasiswaanService;
use App\Services\Kemahasiswaan\PengumumanService;
use Illuminate\Http\Request;

class ManajemenMahasiswaController extends Controller
{
    public function __construct(
        private KemahasiswaanService $kemahasiswaanService,
        private KegiatanService      $kegiatanService,
        private PengumumanService    $pengumumanService,
        private ForumService         $forumService,
        private DashboardService     $dashboardService,
    ) {}

    public function index()
    {
        return view('manajemenmahasiswa::index');
    }

    public function dashboard()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name'); // load sekali

        if ($roles->intersect(['superadmin', 'admin'])->isNotEmpty()) {
            return $this->adminDashboard();
        }

        if ($roles->contains('dosen')) {
            return $this->dosenDashboard();
        }

        return $this->mahasiswaDashboard();
    }

    private function adminDashboard()
    {
        $stats        = $this->dashboardService->getDashboardStats();
        $pengumuman   = $this->pengumumanService->getPublished();
        $kegiatan     = $this->kegiatanService->getAll();

        return view('manajemenmahasiswa::dashboard.admin', compact('stats', 'pengumuman', 'kegiatan'));
    }

    private function dosenDashboard()
    {
        $stats      = $this->dashboardService->getDashboardStats();
        $pengumuman = $this->pengumumanService->getPublished('dosen');
        $forums     = $this->forumService->getAllForums();

        return view('manajemenmahasiswa::dashboard.dosen', compact('stats', 'pengumuman', 'forums'));
    }

    private function mahasiswaDashboard()
    {
        $user       = auth()->user();
        $pengumuman = $this->pengumumanService->getPublished('mahasiswa');
        $forums     = $this->forumService->getAllForums();
        $profil     = $this->kemahasiswaanService->getByUser($user->id);

        return view('manajemenmahasiswa::dashboard.mahasiswa', compact('pengumuman', 'forums', 'profil'));
    }

    public function create()
    {
        return view('manajemenmahasiswa::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('manajemenmahasiswa::show');
    }

    public function edit($id)
    {
        return view('manajemenmahasiswa::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}