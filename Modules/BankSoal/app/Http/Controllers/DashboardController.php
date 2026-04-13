<?php

namespace Modules\BankSoal\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');

        // =====================================================================
        // FIX: Cek permission SEBELUM routing berdasarkan role.
        //
        // SEBELUM (BUG):
        //   Controller hanya cek role. User dengan role 'dosen' SELALU
        //   bisa akses dashboard Bank Soal, meskipun permission
        //   banksoal.view sudah dicabut oleh superadmin.
        //
        // SESUDAH (FIX):
        //   Cek banksoal.view dulu. Jika tidak punya → 403.
        //   Superadmin di-bypass otomatis oleh hasPermissionTo().
        // =====================================================================
        if (!$user->can('banksoal.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Bank Soal (banksoal.view).');
        }

        // Superadmin & Admin
        if ($roles->intersect(['superadmin', 'admin', 'admin_banksoal'])->isNotEmpty()) {
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

        abort(403, 'Akses ditolak.');
    }
}