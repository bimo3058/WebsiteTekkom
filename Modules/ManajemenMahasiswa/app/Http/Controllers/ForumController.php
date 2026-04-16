<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Implement role-based view return if necessary. For now, 
        // we'll return the mahasiswa view as requested in the design.
        return view('manajemenmahasiswa::forum.mahasiswa');
    }

    public function create()
    {
        $user = Auth::user();
        return view('manajemenmahasiswa::forum.create');
    }

    public function show($id)
    {
        $user = Auth::user();
        
        return view('manajemenmahasiswa::forum.show', compact('id'));
    }
}
