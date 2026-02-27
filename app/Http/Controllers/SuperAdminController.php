<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('superadmin.dashboard', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:SUPERADMIN,ADMIN,LECTURER,STUDENT'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'Role updated successfully.');
    }
}
