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
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $praktikumId = $request->input('praktikum_id');
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId) ?? $praktikumList->first();
        $praktikumId = $praktikum?->id;

        $anggota = $praktikumId
            ? AsprakPraktikum::withTrashed()
                ->with('user')
                ->where('praktikum_id', $praktikumId)
                ->orderByRaw('deleted_at IS NOT NULL') // Active first
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

        // 3. Cabut role Spatie HANYA jika tidak ada di praktikum lain
        $roleName = $role === 'koor' ? 'koor_prak' : 'asprak';
        $stillHasRole = AsprakPraktikum::where('user_id', $userId)
            ->where('role', $role)
            ->where('id', '!=', $record->id)
            ->exists();

        if ($user && !$stillHasRole) {
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

        $message = "{$name} berhasil dilepas dari role {$role} di praktikum ini.";
        if (!$stillHasRole) {
            $message .= " Akses {$roleName} dari sistem sudah dicabut sepenuhnya.";
        }

        return back()->with('success', $message);
    }

    public function revokeAll(Request $request, $praktikumId)
    {
        $praktikum = Praktikum::findOrFail($praktikumId);
        $records = AsprakPraktikum::with('user')->where('praktikum_id', $praktikumId)->get();

        foreach($records as $record) {
            $user = $record->user;
            $role = $record->role;
            $userId = $record->user_id;

            $record->delete();

            if ($role === 'koor') {
                $praktikum->update(['koor_id' => null]);
            }

            $roleName = $role === 'koor' ? 'koor_prak' : 'asprak';
            $stillHasRole = AsprakPraktikum::where('user_id', $userId)
                ->where('role', $role)
                ->where('id', '!=', $record->id)
                ->exists();

            if ($user && !$stillHasRole) {
                try {
                    if ($user->hasRole($roleName)) {
                        $user->removeRole($roleName);
                    }
                } catch (\Exception $e) {
                    $spatieRole = Role::where('name', $roleName)
                        ->where('module', 'eoffice')
                        ->first();
                    if ($spatieRole) {
                        $user->roles()->detach($spatieRole->id);
                    }
                }

                app(PermissionRegistrar::class)->forgetCachedPermissions();

                \DB::table('users')
                    ->where('id', $userId)
                    ->increment('session_version');
            }
        }

        // 3. Update status praktikum menjadi non-aktif (read-only)
        $praktikum->update([
            'is_active' => false,
            'status' => 'nonaktif'
        ]);

        return redirect()->route('eoffice.manprak.admin.kelola-role.index')->with('success', "Seluruh role koordinator & asprak pada praktikum {$praktikum->nama} berhasil dicabut, dan praktikum dinonaktifkan.");
    }

    public function restoreRole($id)
    {
        $record = AsprakPraktikum::withTrashed()->with('user')->findOrFail($id);
        $user   = $record->user;
        $name   = $user?->name ?? 'User';
        $role   = $record->role;

        // 1. Restore the soft-deleted record
        $record->restore();

        // 2. Jika role = koor, update koor_id di eo_praktikum
        if ($role === 'koor') {
            Praktikum::where('id', $record->praktikum_id)
                ->update(['koor_id' => $record->user_id]);
        }

        // 3. Kembalikan Role Spatie
        $roleName = $role === 'koor' ? 'koor_prak' : 'asprak';
        $spatieRole = Role::where('name', $roleName)->where('module', 'eoffice')->first();
        if ($spatieRole && $user) {
            $user->roles()->syncWithoutDetaching([$spatieRole->id]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        return back()->with('success', "Role {$role} berhasil dikembalikan untuk {$name}.");
    }
}