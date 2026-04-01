<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        // ── FIX: Cek permission sebelum routing berdasarkan role ──
        if (!$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa (kemahasiswaan.view).');
        }

        if ($roles->intersect(['superadmin', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return app(KemahasiswaanController::class)->adminDashboard();
        }

        if ($roles->contains('dosen')) {
            return app(KemahasiswaanController::class)->dosenDashboard();
        }

        if ($roles->contains('mahasiswa')) {
            return app(KemahasiswaanController::class)->mahasiswaDashboard();
        }

        abort(403, 'Akses Ditolak.');
    }
}