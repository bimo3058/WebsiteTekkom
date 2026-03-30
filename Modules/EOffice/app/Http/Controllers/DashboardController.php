<?php

namespace Modules\EOffice\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        // ── FIX: Cek permission sebelum routing berdasarkan role ──
        if (!$user->can('eoffice.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul E-Office (eoffice.view).');
        }

        if ($roles->intersect(['superadmin', 'admin_eoffice'])->isNotEmpty()) {
            return app(EOfficeController::class)->adminDashboard();
        }

        if ($roles->contains('dosen')) {
            return app(EOfficeController::class)->dosenDashboard();
        }

        if ($roles->contains('mahasiswa')) {
            return app(EOfficeController::class)->mahasiswaDashboard();
        }

        abort(403, 'Akses Ditolak.');
    }
}