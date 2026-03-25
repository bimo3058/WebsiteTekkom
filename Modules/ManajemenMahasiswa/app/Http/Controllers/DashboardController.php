<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->roles->pluck('name');

        if ($roles->intersect(['superadmin', 'admin'])->isNotEmpty()) {
            return view('manajemenmahasiswa::dashboard.admin');
        }

        if ($roles->contains('dosen')) {
            return view('manajemenmahasiswa::dashboard.dosen');
        }

        return view('manajemenmahasiswa::dashboard.mahasiswa');
    }
}