<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CapstoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('capstone::index');
    }

    /**
     * Show the dashboard for capstone module.
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Tentukan view berdasarkan role
        if ($user->roles()->whereIn('name', ['superadmin', 'admin'])->exists()) {
            return view('capstone::dashboard.admin');
        }

        if ($user->roles()->where('name', 'dosen')->exists()) {
            return view('capstone::dashboard.dosen');
        }

        if ($user->roles()->where('name', 'mahasiswa')->exists()) {
            return view('capstone::dashboard.mahasiswa');
        }

        // Default
        return view('capstone::dashboard.mahasiswa');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('capstone::create');
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
        return view('capstone::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('capstone::edit');
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
