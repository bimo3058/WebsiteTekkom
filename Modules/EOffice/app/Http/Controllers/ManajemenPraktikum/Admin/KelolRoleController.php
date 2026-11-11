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
use Illuminate\Validation\ValidationException;

class KelolRoleController extends Controller
{
    public function __construct(private KoorPraktikumService $koorService) {}

    public function index(Request $request)
    {
        $isCerc=$request->routeIs('eoffice.manprak.cerc.*');
        if($isCerc && !$request->filled('praktikum_id')){
            $request->validate(['q'=>'nullable|string|max:100','per_page'=>'nullable|integer|in:10,20,50']);
            $query=Praktikum::with(['dosens','koordinator'])->where('is_active',true);
            if($request->filled('q'))$query->whereRaw('LOWER(nama) LIKE ?',['%'.mb_strtolower($request->string('q')->toString()).'%']);
            $praktikums=$query->orderBy('nama')->orderBy('id')->paginate((int)$request->input('per_page',10))->withQueryString();
            return view('eoffice::manajemen-praktikum.cerc.staffing-index',compact('praktikums'));
        }
        $praktikumList = Praktikum::with(['dosens', 'koordinator'])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $praktikumId = $request->input('praktikum_id');
        $praktikum = $isCerc?$praktikumList->firstWhere('id',$praktikumId):($praktikumList->firstWhere('id', $praktikumId) ?? $praktikumList->first());
        if($isCerc)abort_unless($praktikum,404);
        $praktikumId = $praktikum?->id;

        $anggota = $praktikumId
            ? AsprakPraktikum::withTrashed()
                ->with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
            : collect();

        $staffingAssignments=$anggota->reject(fn($record)=>$record->trashed())->map(fn($record)=>['user_id'=>(string)$record->user_id,'role'=>$record->role])->values();
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

        $mahasiswaOptions = \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::apply(User::query())
            ->when($isCerc,fn($query)=>$query->whereNull('suspended_at'))
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

        return view($isCerc?'eoffice::manajemen-praktikum.cerc.staffing-detail':'eoffice::manajemen-praktikum.admin.kelola-role', compact(
            'praktikumList',
            'praktikum',
            'praktikumId',
            'anggota',
            'dosenOptions',
            'mahasiswaOptions',
            'asistenOptions',
            'staffingAssignments'
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
        if ($request->has('user_ids')) {
            $data = $request->validate([
                'praktikum_id' => 'required|exists:eo_prak_praktikum,id',
                'role' => 'required|in:asprak',
                'user_ids' => 'required|array|min:1|max:5',
                'user_ids.*' => 'required|integer|distinct|exists:users,id|exists:students,user_id',
            ], ['user_ids.max'=>'Maksimal 5 asisten dalam satu penunjukan.', 'user_ids.*.distinct'=>'Mahasiswa tidak boleh dipilih lebih dari sekali.']);
            return DB::transaction(function () use ($data) {
                $practicum = Praktikum::whereKey($data['praktikum_id'])->lockForUpdate()->firstOrFail();
                abort_unless($practicum->is_active && $practicum->status === 'aktif', 403, 'Praktikum tidak aktif.');
                foreach ($data['user_ids'] as $id) {
                    $this->assignRole(Request::create('/assign', 'POST', ['praktikum_id'=>$practicum->id,'role'=>'asprak','user_id'=>$id]));
                }
                return back()->with('success', count($data['user_ids']).' asisten berhasil ditunjuk.');
            });
        }
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
        \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::ensure($user);
        if($user->suspended_at)throw ValidationException::withMessages(['user_id'=>'Akun mahasiswa sedang dinonaktifkan. Pilih mahasiswa lain.']);
        if(AsprakPraktikum::where('praktikum_id',$praktikum->id)->where('user_id',$user->id)->where('role',$request->role)->exists())throw ValidationException::withMessages(['user_id'=>'Mahasiswa ini sudah memiliki role tersebut pada praktikum ini.']);

        if ($request->role === 'koor') {
            $hasOtherCoordinator = AsprakPraktikum::where('praktikum_id', $praktikum->id)
                ->where('role', 'koor')
                ->where('user_id', '!=', $user->id)
                ->exists();

            if ($hasOtherCoordinator || ($praktikum->koor_id && (string) $praktikum->koor_id !== (string) $user->id)) {
                throw ValidationException::withMessages(['role'=>'Praktikum ini sudah memiliki koordinator. Cabut penugasan koordinator saat ini sebelum menunjuk penggantinya.']);
            }

            DB::transaction(fn () => $this->koorService->assign($praktikum, $user));

            \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Penunjukan koordinator', $praktikum->nama, null, [$user->name.' — Koordinator']);

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

        \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Penunjukan asisten', $praktikum->nama, null, [$user->name.' — Asisten']);
        return back()->with('success', "{$user->name} berhasil ditambahkan sebagai Asisten.");
    }

    public function revokeRole($id)
    {
        $record = AsprakPraktikum::with('user')->findOrFail($id);
        $user = $record->user;
        $name = $user?->name ?? 'User';
        $role = $record->role;
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

        \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Pencabutan penugasan', Praktikum::find($record->praktikum_id)?->nama ?? 'Praktikum', [$name.' — '.($role==='koor'?'Koordinator':'Asisten')], ['Penugasan dicabut']);
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

        // Opening/closing practicums remains an Admin Praktikum responsibility.

        if ($records->isNotEmpty()) \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Pencabutan seluruh penugasan', $praktikum->nama, $records->map(fn($record)=>($record->user?->name ?? 'Akun tidak tersedia').' — '.($record->role==='koor'?'Koordinator':'Asisten'))->all(), ['Seluruh penugasan dicabut']);

        return redirect()->route(request()->routeIs('eoffice.manprak.cerc.*')?'eoffice.manprak.cerc.kelola-role.index':'eoffice.manprak.admin.kelola-role.index',['praktikum_id'=>$praktikum->id])->with('success', "Seluruh role koordinator & asprak pada praktikum {$praktikum->nama} berhasil dicabut.");
    }

    public function restoreRole($id)
    {
        $record = AsprakPraktikum::withTrashed()->with('user')->findOrFail($id);
        $user = $record->user;
        $name = $user?->name ?? 'User';
        $role = $record->role;

        if(!$record->trashed())throw ValidationException::withMessages(['role'=>'Role ini sudah aktif. Muat ulang halaman untuk melihat penugasan terbaru.']);
        if ($user) \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::ensure($user);
        if(!$user || $user->suspended_at || !$user->student()->exists())throw ValidationException::withMessages(['user_id'=>'Role tidak dapat dikembalikan karena akun mahasiswa tidak tersedia atau sedang dinonaktifkan.']);
        if ($role==='koor') {
            $practicum=Praktikum::findOrFail($record->praktikum_id);
            if(($practicum->koor_id && (string)$practicum->koor_id!==(string)$record->user_id) || AsprakPraktikum::where('praktikum_id',$record->praktikum_id)->where('role','koor')->where('user_id','!=',$record->user_id)->exists())throw ValidationException::withMessages(['role'=>'Praktikum sudah memiliki koordinator lain. Cabut penugasan saat ini sebelum mengembalikan koordinator sebelumnya.']);
            abort_unless($user,422,'Akun mahasiswa tidak tersedia.');
            $this->koorService->assign($practicum,$user);
            \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Pemulihan koordinator', $practicum->nama, ['Penugasan dicabut'], [$name.' — Koordinator']);
            return back()->with('success',"Role koordinator berhasil dikembalikan untuk {$name}.");
        }
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

        \Modules\EOffice\Services\ManajemenPraktikum\CercActivity::record('Pemulihan asisten', Praktikum::find($record->praktikum_id)?->nama ?? 'Praktikum', ['Penugasan dicabut'], [$name.' — Asisten']);
        return back()->with('success', "Role {$role} berhasil dikembalikan untuk {$name}.");
    }
}
