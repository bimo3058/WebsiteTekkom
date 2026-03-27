<?php

namespace Modules\BankSoal\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name'); // pakai collection biar fleksibel

        // Superadmin & Admin
        if ($roles->intersect(['superadmin', 'admin_banksoal'])->isNotEmpty()) {
            return view('banksoal::dashboard.admin');
        }

        // GPM
        if ($roles->contains('gpm')) {
            return view('banksoal::dashboard.gpm');
        }

        // Dosen
        if ($roles->contains('dosen')) {
            return view('banksoal::dashboard.dosen');
        }

        // Mahasiswa
        if ($roles->contains('mahasiswa')) {
            return view('banksoal::dashboard.mahasiswa');
        }

        // fallback kalau role aneh / tidak terdaftar
        abort(403, 'Role tidak memiliki akses ke dashboard Bank Soal.');
    }
}