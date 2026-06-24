<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('eoffice::manajemen-ruangan.admin.user.index', compact('users', 'roles'));
    }

    public function create()
    {
        // For simplicity, we can use a modal in index, or a separate view.
        // I will use a separate view.
        $roles = Role::all();
        return view('eoffice::manajemen-ruangan.admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->role) {
            $user->assignRole($request->role);
        }

        return redirect()->route('eoffice.peminjaman.admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('eoffice::manajemen-ruangan.admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->role) {
            // Remove previous roles or just sync
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('eoffice.peminjaman.admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Instead of hard delete, suspend or delete based on logic.
        // We'll use soft deletes since User model uses SoftDeletes.
        $user->delete();
        return redirect()->route('eoffice.peminjaman.admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function suspend(User $user)
    {
        if ($user->isSuspended()) {
            $user->unsuspend();
            $msg = 'Akun user berhasil diaktifkan.';
        } else {
            $user->suspend('Dinonaktifkan oleh Admin Manajemen Ruangan');
            $msg = 'Akun user berhasil dinonaktifkan.';
        }

        return back()->with('success', $msg);
    }
}
