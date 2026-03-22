<?php

namespace Modules\BankSoal\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ambil semua role user
        $roles = $user->roles->pluck('name')->toArray();

        if (in_array('admin', $roles)) {
            return view('banksoal::dashboard.admin');
        }

        if (in_array('gpm', $roles)) {
            return view('banksoal::dashboard.gpm');
        }

        if (in_array('dosen', $roles)) {
            return view('banksoal::dashboard.dosen');
        }

        if (in_array('mahasiswa', $roles)) {
            return view('banksoal::dashboard.mahasiswa');
        }

        abort(403);
    }
}