<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;
use Spatie\Permission\PermissionRegistrar;

class KelolRoleController extends Controller
{
    public function index(Request $request)
    {
        $praktikumList = Praktikum::with(['dosens', 'koordinator'])
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

        // Jika assign sebagai koor, update eo_praktikum.koor_id juga
        if ($request->role === 'koor') {
            Praktikum::where('id', $request->praktikum_id)
                ->update(['koor_id' => $request->user_id]);
        }

        $user = User::find($request->user_id);
        return back()->with('success', "{$user?->name} berhasil di-assign sebagai {$request->role}.");
    }

    public function revokeRole($id)
    {
        $record = AsprakPraktikum::with('user')->findOrFail($id);
        $user   = $record->user;
        $name   = $user?->name ?? 'User';
        $role   = $record->role;
        $userId = $record->user_id;

        // 1. Soft delete record asprak_praktikum ini
        $record->delete();

        // 2. Jika role = koor, kosongkan koor_id di eo_praktikum
        if ($role === 'koor') {
            Praktikum::where('id', $record->praktikum_id)
                ->where('koor_id', $userId)
                ->update(['koor_id' => null]);
        }

        // 3. Cabut role Spatie — selalu
        //    Pakai removeRole() Spatie bukan detach() langsung agar cache permission ikut di-flush
        $roleName = $role === 'koor' ? 'koor_prak' : 'asprak';

        if ($user) {
            // Coba via Spatie removeRole() dulu (handles cache flush otomatis)
            try {
                if ($user->hasRole($roleName)) {
                    $user->removeRole($roleName);
                }
            } catch (\Exception $e) {
                // Fallback: detach manual + flush cache
                $spatieRole = Role::where('name', $roleName)
                    ->where('module', 'eoffice')
                    ->first();
                if ($spatieRole) {
                    $user->roles()->detach($spatieRole->id);
                }
            }

            // Flush Spatie permission cache untuk user ini
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Increment session version agar session aktif user ikut invalid
            \DB::table('users')
                ->where('id', $userId)
                ->increment('session_version');
        }

        return back()->with('success', "{$name} berhasil dilepas dari role {$role}. Akses {$roleName} sudah dicabut — user perlu login ulang.");
    }
}