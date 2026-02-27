<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManajemenMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manajemenmahasiswa::index');
    }

    /**
     * Show the dashboard for manajemen mahasiswa module.
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->roles()->whereIn('name', ['superadmin', 'admin'])->exists()) {
            return view('manajemenmahasiswa::dashboard.admin');
        }

        if ($user->roles()->where('name', 'dosen')->exists()) {
            return view('manajemenmahasiswa::dashboard.dosen');
        }

        return view('manajemenmahasiswa::dashboard.mahasiswa');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manajemenmahasiswa::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('manajemenmahasiswa::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('manajemenmahasiswa::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
