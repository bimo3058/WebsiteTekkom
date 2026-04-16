<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class KemahasiswaanController extends Controller
{
    /* |--------------------------------------------------------------------------
    | Dashboard Methods
    |-------------------------------------------------------------------------- */

    use AuthorizesRequests;
    
    public function adminDashboard()
    {
        $this->authorize('kemahasiswaan.view'); // FIX
        return view('manajemenmahasiswa::dashboard.admin');
    }

    public function dosenDashboard()
    {
        $this->authorize('kemahasiswaan.view'); // FIX
        return view('manajemenmahasiswa::dashboard.dosen');
    }

    public function mahasiswaDashboard()
    {
        $this->authorize('kemahasiswaan.view'); // FIX
        return view('manajemenmahasiswa::dashboard.mahasiswa');
    }

    public function pengurusDashboard()
    {
        $this->authorize('kemahasiswaan.view');
        return view('manajemenmahasiswa::dashboard.pengurus');
    }

    public function alumniDashboard()
    {
        $this->authorize('kemahasiswaan.view');
        return view('manajemenmahasiswa::dashboard.alumni');
    }
}