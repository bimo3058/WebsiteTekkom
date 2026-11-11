<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;
use Spatie\Permission\PermissionRegistrar;

class KelolRoleController extends Controller
{
    public function __construct(private KoorPraktikumService $koorService) {}

    public function index(Request $request)
    {
        $praktikumList = Praktikum::with(['dosens', 'koordinator'])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $praktikumId = $request->input('praktikum_id');
        $praktikum = $praktikumList->firstWhere('id', $praktikumId) ?? $praktikumList->first();
        $praktikumId = $praktikum?->id;

        $anggota = $praktikumId
            ? AsprakPraktikum::withTrashed()
                ->with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
            : collect();

        // Satu mahasiswa cukup tampil sekali. Jika mahasiswa mempunyai role
        // koordinator sekaligus asisten, tampilkan record koordinatornya.
        $anggota = $anggota
            ->groupBy('user_id')
            ->map(function ($records) {
                return $records->sortBy(fn ($record) => sprintf(
                    '%d-%d',
                    $record->trashed() ? 1 : 0,
                    $record->role === 'koor' ? 0 : 1
                ))->first();
            })
            ->sortBy(fn ($record) => sprintf(
                '%d-%d-%s',
                $record->trashed() ? 1 : 0,
                $record->role === 'koor' ? 0 : 1,
                mb_strtolower($record->user?->name ?? '')
            ))
            ->values();

        $dosenOptions = Lecturer::with('user')
            ->get()
            ->filter(fn ($lecturer) => $lecturer->user)
            ->map(fn ($lecturer) => [
                'value' => (string) $lecturer->user_id,
                'label' => $lecturer->user->name.' · '.$lecturer->user->email,
            ])
            ->sortBy('label')
            ->values();

        $mahasiswaOptions = User::whereHas('student')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn ($user) => [
                'value' => (string) $user->id,
                'label' => $user->name.' · '.$user->email,
            ]);

        $asistenAktifIds = $praktikumId
            ? AsprakPraktikum::where('praktikum_id', $praktikumId)->pluck('user_id')
            : collect();
        $asistenOptions = $mahasiswaOptions
            ->reject(fn ($option) => $asistenAktifIds->contains($option['value']))
            ->values();

        return view('eoffice::manajemen-praktikum.admin.kelola-role', compact(
            'praktikumList',
            'praktikum',
            'praktikumId',
            'anggota',
            'dosenOptions',
            'mahasiswaOptions',
            'asistenOptions'
        ));
    }

    public function updateDosen(Request $request, string $praktikumId)
    {
        $data = $request->validate([
            'dosen_ids' => 'required|array|min:1|max:3',
            'dosen_ids.*' => 'required|distinct|exists:lecturers,user_id',
        ], [
            'dosen_ids.required' => 'Minimal satu dosen pengampu harus dipilih.',
            'dosen_ids.min' => 'Minimal satu dosen pengampu harus dipilih.',
            'dosen_ids.max' => 'Dosen pengampu maksimal 3 orang.',
            'dosen_ids.*.distinct' => 'Dosen pengampu tidak boleh dipilih lebih dari sekali.',
        ]);

        $praktikum = Praktikum::findOrFail($praktikumId);
        $praktikum->dosens()->sync($data['dosen_ids']);

        return back()->with('success', 'Dosen pengampu berhasil diperbarui.');
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|exists:students,user_id',
            'praktikum_id' => 'required|exists:eo_prak_praktikum,id',
            'role' => 'required|in:asprak,koor',
        ], [
            'user_id.required' => 'Pilih mahasiswa yang akan diberi role.',
            'user_id.exists' => 'Mahasiswa yang dipilih tidak valid.',
        ]);

        $praktikum = Praktikum::findOrFail($request->praktikum_id);
        $user = User::findOrFail($request->user_id);

        if ($request->role === 'koor') {
            $hasOtherCoordinator = AsprakPraktikum::where('praktikum_id', $praktikum->id)
                ->where('role', 'koor')
                ->where('user_id', '!=', $user->id)
                ->exists();

            if ($hasOtherCoordinator || ($praktikum->koor_id && (string) $praktikum->koor_id !== (string) $user->id)) {
                return back()->with('error', 'Praktikum ini sudah memiliki Koordinator.');
            }

            DB::transaction(fn () => $this->koorService->assign($praktikum, $user));

            return back()->with('success', "{$user->name} berhasil ditambahkan sebagai Koordinator.");
        }

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
                'user_id' => $request->user_id,
                'praktikum_id' => $request->praktikum_id,
                'role' => $request->role,
            ]);
        }

        // Sync role di tabel Spatie
        $roleName = $request->role === 'koor' ? 'koor_prak' : 'asprak';
        $role = Role::where('name', $roleName)->where('module', 'eoffice')->first();
        if ($role) {
            User::find($request->user_id)?->roles()->syncWithoutDetaching([$role->id]);
        }

        return back()->with('success', "{$user->name} berhasil ditambahkan sebagai Asisten.");
    }

    public function revokeRole($id)
    {
        $record = AsprakPraktikum::with('user')->findOrFail($id);
        $user = $record->user;
        $name = $user?->name ?? 'User';
        $role = $record->role;
        $userId = $record->user_id;

        // 1. Soft delete record eo_prak_asprak_praktikum ini
        $record->delete();

        // 2. Jika role = koor, kosongkan koor_id di eo_prak_praktikum
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

        foreach ($records as $record) {
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
        $user = $record->user;
        $name = $user?->name ?? 'User';
        $role = $record->role;

        // 1. Restore the soft-deleted record
        $record->restore();

        // 2. Jika role = koor, update koor_id di eo_prak_praktikum
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
