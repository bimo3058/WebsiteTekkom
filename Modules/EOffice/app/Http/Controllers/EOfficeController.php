<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('eoffice::index');
    }

    /**
     * Show the dashboard for eoffice module.
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->roles()->whereIn('name', ['superadmin', 'admin'])->exists()) {
            return view('eoffice::dashboard.admin');
        }

        if ($user->roles()->where('name', 'dosen')->exists()) {
            return view('eoffice::dashboard.dosen');
        }

        return view('eoffice::dashboard.mahasiswa');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('eoffice::create');
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
        return view('eoffice::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('eoffice::edit');
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
