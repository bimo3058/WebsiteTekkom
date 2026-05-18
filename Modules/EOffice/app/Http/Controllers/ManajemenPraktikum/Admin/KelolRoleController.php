<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;

class KelolRoleController extends Controller
{
    public function index(Request $request)
    {
        $praktikumList = Praktikum::with(['dosen', 'koordinator'])
            ->orderBy('nama')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $anggota = $praktikumId
            ? AsprakPraktikum::with('user')
                ->where('praktikum_id', $praktikumId)
                ->whereNull('deleted_at')
                ->orderBy('role')
                ->get()
            : collect();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('eoffice::manajemen-praktikum.admin.kelola-role', compact(
            'praktikumList',
            'praktikum',
            'praktikumId',
            'anggota',
            'users'
        ));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'praktikum_id' => 'required|exists:eo_praktikum,id',
            'role'         => 'required|in:asprak,koor',
        ]);

        // Restore soft-deleted record atau buat baru
        $existing = AsprakPraktikum::withTrashed()
            ->where('user_id', $request->user_id)
            ->where('praktikum_id', $request->praktikum_id)
            ->where('role', $request->role)
            ->first();

        if ($existing) {
            $existing->restore();
        } else {
            AsprakPraktikum::create([
                'user_id'      => $request->user_id,
                'praktikum_id' => $request->praktikum_id,
                'role'         => $request->role,
            ]);
        }

        // Sync role di tabel Spatie
        $roleName = $request->role === 'koor' ? 'koor_prak' : 'asprak';
        $role     = Role::where('name', $roleName)->where('module', 'eoffice')->first();
        if ($role) {
            User::find($request->user_id)?->roles()->syncWithoutDetaching([$role->id]);
        }

        $user = User::find($request->user_id);
        return back()->with('success', "{$user?->name} berhasil di-assign sebagai {$request->role}.");
    }

    public function revokeRole($id)
    {
        $record = AsprakPraktikum::with('user')->findOrFail($id);
        $name   = $record->user?->name ?? 'User';
        $role   = $record->role;

        $record->delete(); // soft delete — tidak cabut role global karena bisa asprak di praktikum lain

        return back()->with('success', "{$name} berhasil dilepas dari role {$role} di praktikum ini.");
    }
}
