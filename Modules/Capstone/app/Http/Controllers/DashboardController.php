<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        // ── FIX: Cek permission sebelum routing berdasarkan role ──
        if (!$user->can('capstone.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Capstone (capstone.view).');
        }

        if ($roles->intersect(['superadmin', 'admin_capstone'])->isNotEmpty()) {
            return app(CapstoneController::class)->adminDashboard();
        }

        if ($roles->contains('dosen')) {
            return app(CapstoneController::class)->dosenDashboard();
        }

        if ($roles->contains('mahasiswa')) {
            return app(CapstoneController::class)->mahasiswaDashboard();
        }

        abort(403, 'Akses ditolak.');
    }
}