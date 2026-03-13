<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Modules\ManajemenMahasiswa\Models\DashboardAnalitik;
use Modules\ManajemenMahasiswa\Models\Pengumuman;
use Modules\ManajemenMahasiswa\Services\DashboardAnalitikService;
use Modules\ManajemenMahasiswa\Services\ForumService;
use Modules\ManajemenMahasiswa\Services\KemahasiswaanService;
use Modules\ManajemenMahasiswa\Services\KegiatanService;
use Modules\ManajemenMahasiswa\Services\PengumumanService;
use Illuminate\Http\Request;

class ManajemenMahasiswaController extends Controller
{
    public function __construct(
        private KemahasiswaanService     $kemahasiswaanService,
        private KegiatanService          $kegiatanService,
        private PengumumanService        $pengumumanService,
        private ForumService             $forumService,
        private DashboardAnalitikService $dashboardService,
    ) {}

    public function index()
    {
        return view('manajemenmahasiswa::index');
    }

    public function dashboard()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name');

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
        $stats      = $this->dashboardService->getSnapshot();
        $pengumuman = $this->pengumumanService->listPublished(Pengumuman::AUDIENCE_ALL);
        $kegiatan   = $this->kegiatanService->listKegiatan();
        return view('manajemenmahasiswa::dashboard.admin', compact('stats', 'pengumuman', 'kegiatan'));
    }

    private function dosenDashboard()
    {
        $stats      = $this->dashboardService->getSnapshot();
        $pengumuman = $this->pengumumanService->listPublished(Pengumuman::AUDIENCE_DOSEN);
        $forums     = $this->forumService->listForums();
        return view('manajemenmahasiswa::dashboard.dosen', compact('stats', 'pengumuman', 'forums'));
    }

    private function mahasiswaDashboard()
    {
        $user       = auth()->user();
        $pengumuman = $this->pengumumanService->listPublished(Pengumuman::AUDIENCE_MAHASISWA);
        $forums     = $this->forumService->listForums();
        $profil     = $this->kemahasiswaanService->getByUser($user->id);
        return view('manajemenmahasiswa::dashboard.mahasiswa', compact('pengumuman', 'forums', 'profil'));
    }

    public function create()
    {
        return view('manajemenmahasiswa::create');
    }

    public function store(Request $request)
    {
        // TODO: implementasi
        // AuditLogger::create('manajemen_mahasiswa', "Menambah data: ...", $model, $model->toArray());
    }

    public function show($id)
    {
        // TODO: implementasi
        // AuditLogger::view('manajemen_mahasiswa', "Melihat data ID {$id}");
        return view('manajemenmahasiswa::show');
    }

    public function edit($id)
    {
        return view('manajemenmahasiswa::edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: implementasi
        // AuditLogger::update('manajemen_mahasiswa', "Mengubah data ID {$id}", $model, $oldData, $newData);
    }

    public function destroy($id)
    {
        // TODO: implementasi
        // AuditLogger::delete('manajemen_mahasiswa', "Menghapus data ID {$id}", $model, $oldData);
    }
}