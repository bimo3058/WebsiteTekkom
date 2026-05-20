<?php

namespace Modules\EOffice\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        $email = strtolower($user->email ?? '');

        // Bypass permission check for valid domains and specific users
        $isAllowedEmail = str_ends_with($email, '@students.undip.ac.id') 
                       || str_ends_with($email, '@undip.ac.id') 
                       || $email === 'ike.pertiwi@undip.ac.id';

        // ── FIX: Cek permission sebelum routing berdasarkan role ──
        if (!$user->can('eoffice.view') && !$isAllowedEmail) {
            abort(403, 'Anda tidak memiliki izin akses ke modul E-Office (eoffice.view).');
        }

        if ($roles->intersect(['superadmin', 'admin_eoffice'])->isNotEmpty()) {
            return app(EOfficeController::class)->adminDashboard();
        }

        if ($email === 'ike.pertiwi@undip.ac.id') {
            return redirect()->route('eoffice.kp.koordinator.dashboard');
        }

        if ($roles->contains('mahasiswa') || str_ends_with($email, '@students.undip.ac.id')) {
            return app(EOfficeController::class)->mahasiswaDashboard();
        }

        if ($roles->contains('dosen') || str_ends_with($email, '@undip.ac.id')) {
            return app(EOfficeController::class)->dosenDashboard();
        }

        abort(403, 'Akses Ditolak.');
    }
}