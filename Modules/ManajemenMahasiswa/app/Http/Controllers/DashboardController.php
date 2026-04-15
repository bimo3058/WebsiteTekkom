<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ambil semua role user
        $roles = $user->roles->pluck('name')->toArray();

        if (in_array('superadmin', $roles) || in_array('admin_kemahasiswaan', $roles)) {
            return view('manajemenmahasiswa::dashboard.admin');
        }

        if (in_array('dosen', $roles)) {
            return view('manajemenmahasiswa::dashboard.dosen');
        }

        if (in_array('pengurus_himpunan', $roles)) {
            return view('manajemenmahasiswa::dashboard.admin');
        }

        if (in_array('mahasiswa', $roles)) {
            return view('manajemenmahasiswa::dashboard.mahasiswa');
        }

        if (in_array('alumni', $roles)) {
            return view('manajemenmahasiswa::dashboard.mahasiswa');
        }

        abort(403);
    }

    public function switchMode()
    {
        // placeholder untuk switch mode functionality
        return redirect()->back();
    }
}
