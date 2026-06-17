<?php

namespace Modules\EOffice\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        $email = strtolower($user->email ?? '');

        // Bypass permission check for valid domains and specific users
        $isAllowedEmail = str_ends_with($email, '@students.undip.ac.id')
            || str_ends_with($email, '@undip.ac.id')
            || $roles->contains('koor_kp');

        // ── FIX: Cek permission sebelum routing berdasarkan role ──
        if (!$user->can('eoffice.view') && !$isAllowedEmail) {
            abort(403, 'Anda tidak memiliki izin akses ke modul E-Office (eoffice.view).');
        }

        if ($roles->intersect(['superadmin', 'admin_eoffice'])->isNotEmpty()) {
            return app(EOfficeController::class)->adminDashboard();
        }

        if ($roles->contains('mahasiswa')) {
            return app(EOfficeController::class)->mahasiswaDashboard();
        }

        // Koordinator KP dan Dosen berbagi tampilan portal Dashboard Siperkom yang sama, 
        // sehingga mereka bisa memilih masuk ke Manajemen Praktikum atau KP.
        if ($roles->contains('dosen') || $roles->contains('koor_kp')) {
            return app(EOfficeController::class)->dosenDashboard();
        }

        abort(403, 'Akses Ditolak.');
    }
}