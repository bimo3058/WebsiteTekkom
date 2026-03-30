<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

class SuperAdminController extends Controller
{
    private const TTL_STATS  = 300;
    private const TTL_ROLES  = 3600;
    private const TTL_USERS  = 60;
    private const TTL_RECENT = 30;

    // ── Cache key helpers ──────────────────────────────────────────────────────

    private function userListCacheKey(string $search, string $role, int $page, int $perPage = 10): string
    {
        $parts = [
            'page='     . $page,
            'role='     . $role,
            'per_page=' . $perPage,
            'search='   . mb_strtolower(trim($search)),
        ];
        sort($parts);
        return 'sa:users:' . implode(':', $parts);
    }

    private function bustUserCache(): void
    {
        Cache::tags(['sa_users'])->flush();
        Cache::forget('sa:total_users');
        Cache::forget('sa:total_superadmins');
        Cache::forget('sa:total_lecturers');
        Cache::forget('sa:total_students');
        Cache::forget('sa:recent_users');
    }

    // ── Dashboard ──────────────────────────────────────────────────────────────

    public function index()
    {
        $activeImport = \App\Models\ImportStatus::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->latest()
            ->first();

        $data = [
            'total_users'       => Cache::remember('sa:total_users',       self::TTL_STATS,  fn() => User::count()),
            'total_superadmins' => Cache::remember('sa:total_superadmins', self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'superadmin'))->count()),
            'total_lecturers'   => Cache::remember('sa:total_lecturers',   self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->count()),
            'total_students'    => Cache::remember('sa:total_students',    self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))->count()),
            'recent_users'      => Cache::remember('sa:recent_users',      self::TTL_RECENT, fn() => User::with('roles')->latest('created_at')->limit(10)->get()),
            'modules'           => Cache::remember('sa:modules_stats',     self::TTL_STATS,  fn() => $this->getModulesStats()),
            'recent_logs'       => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
            
            'activeImportId'    => $activeImport?->id, 
        ];

        return view('superadmin.dashboard', $data);
    }

    private function getModulesStats(): array
    {
        // Ambil status aktif dari database
        $dbModules = \App\Models\SystemModule::pluck('is_active', 'slug')->toArray();

        return [
            'bank_soal' => [
                'name'        => 'Bank Soal',
                'slug'        => 'bank_soal',
                'is_active'   => $dbModules['bank_soal'] ?? false,
                'icon'        => 'book',
                'route'       => 'banksoal.dashboard',
                'pertanyaan'  => DB::table('bs_pertanyaan')->count(),
                'mata_kuliah' => DB::table('bs_mata_kuliah')->count(),
            ],
            'capstone' => [
                'name'      => 'Capstone',
                'slug'      => 'capstone',
                'is_active' => $dbModules['capstone'] ?? false,
                'icon'      => 'school',
                'route'     => 'capstone.dashboard',
                'groups'    => DB::table('capstone_groups')->whereNull('deleted_at')->count(),
                'periods'   => DB::table('capstone_periods')->count(),
            ],
            'eoffice' => [
                'name'      => 'E-Office',
                'slug'      => 'eoffice',
                'is_active' => $dbModules['eoffice'] ?? false,
                'icon'      => 'work',
                'route'     => 'eoffice.dashboard',
            ],
            'manajemen_mahasiswa' => [
                'name'       => 'Manajemen Mahasiswa',
                'slug'       => 'manajemen_mahasiswa',
                'is_active'  => $dbModules['manajemen_mahasiswa'] ?? false,
                'icon'       => 'user',
                'route'      => 'manajemenmahasiswa.mahasiswa.dashboard',
                'students'   => DB::table('students')->count(),
                'alumni'     => DB::table('mk_alumni')->count(),
            ],
        ];
    }

    // ── Users — List ───────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $search  = (string) $request->input('search', '');
        $role    = (string) $request->input('role', 'all');
        $page    = (int)    $request->input('page', 1);
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100])
                   ? (int) $request->input('per_page', 10)
                   : 10;

        $roles    = Cache::remember('sa:roles_list', self::TTL_ROLES, fn() => Role::orderBy('name')->get());
        $cacheKey = $this->userListCacheKey($search, $role, $page, $perPage);

        $query = User::with(['roles', 'lecturer', 'student'])
            ->whereNull('deleted_at')
            ->select('id', 'name', 'email', 'created_at', 'last_login', 'deleted_at', 'suspended_at', 'suspension_reason');

        if ($role !== 'all') {
            $query->whereExists(function ($sub) use ($role) {
                $sub->select(DB::raw(1))
                    ->from('user_roles')
                    ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                    ->whereColumn('user_roles.user_id', 'users.id')
                    ->where('roles.name', $role);
            });
        }

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name',  'ILIKE', $term)
                  ->orWhere('email', 'ILIKE', $term);
            });
        }

        $total     = Cache::tags(['sa_users'])->remember($cacheKey . ':count', self::TTL_USERS, fn() => (clone $query)->count());
        $users     = $query->latest()->paginate($perPage)->withQueryString();
        $paginator = $users;

        if ($request->ajax()) {
            return response()->json([
                'rows'       => view('superadmin.users._table_rows', compact('users'))->render(),
                'pagination' => view('superadmin.users._pagination', compact('paginator'))->render(),
                'total'      => $users->total(),
            ]);
        }

        return view('superadmin.users.index', compact('users', 'roles', 'paginator'));
    }

    // ── Users — Store ──────────────────────────────────────────────────────────

    public function storeUser(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'external_id'     => ['required', 'string', 'max:255', 'unique:users,external_id'],
            'roles'           => ['nullable', 'array'],
            'roles.*'         => ['exists:roles,id'],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'student_number'  => ['nullable', 'string', 'max:50'],
            'cohort_year'     => ['nullable', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
        ]);

        if ($validator->fails()) {
            return redirect()->route('superadmin.users.index')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'external_id' => $validated['external_id'],
            ]);

            $roleIds   = $validated['roles'] ?? [];
            $roleNames = collect();

            if (!empty($roleIds)) {
                $user->roles()->sync($roleIds);
                $roleNames = Role::whereIn('id', $roleIds)->pluck('name');
            }

            if ($roleNames->contains('dosen') && !empty($validated['employee_number'])) {
                Lecturer::create(['user_id' => $user->id, 'employee_number' => $validated['employee_number']]);
            }

            if ($roleNames->contains('mahasiswa') && !empty($validated['student_number'])) {
                Student::create([
                    'user_id'        => $user->id,
                    'student_number' => $validated['student_number'],
                    'cohort_year'    => $validated['cohort_year'] ?? date('Y'),
                ]);
            }

            // ── Ganti UserAuditLog → AuditLogger ──────────────────────────────
            AuditLogger::create(
                module:  'user_management',
                desc:    "Menambah user baru: {$user->name} ({$user->email})",
                subject: $user,
                newData: [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'roles' => $roleNames->values()->toArray(),
                ],
            );

            $this->bustUserCache();
            DB::commit();

            return redirect()->route('superadmin.users.index')
                ->with('success', "User \"{$user->name}\" berhasil ditambahkan.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    // ── Users — Update Permissions ─────────────────────────────────────────────
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'Tidak dapat mengubah permission superadmin.');
        }

        DB::beginTransaction();
        try {
            // ── 1. Update Roles ──────────────────────────────────────────────
            $roleIds = $request->input('roles', []);
            $user->roles()->sync($roleIds);

            // ── 2. Full Sync Direct Permissions ─────────────────────────────
            // Ambil semua nama permission yang valid dari DB
            $allPermByName = \App\Models\Permission::pluck('id', 'name'); // ['banksoal.view' => 1, ...]

            // Hanya ambil permission yang benar-benar ada di DB (sanitasi input)
            $checkedNames = collect($request->input('permissions', []));
            
            $grantIds = $checkedNames
                ->filter(fn($name) => $allPermByName->has($name))
                ->map(fn($name) => $allPermByName[$name])
                ->values()
                ->toArray();

            // SYNC: hapus semua yang lama, pasang yang baru
            // Ini otomatis handle grant + revoke sekaligus
            $user->directPermissions()->sync($grantIds);

            // ── 3. Bersihkan cache ────────────────────────────────────────────
            $user->clearUserCache();
            \Illuminate\Support\Facades\Cache::forget("user_permissions_{$user->id}");

            // ── 4. Audit log ──────────────────────────────────────────────────
            \App\Services\AuditLogger::update(
                module:  'user_management',
                desc:    "Update permissions user: {$user->name} ({$user->email})",
                subject: $user,
                oldData: [],
                newData: [
                    'roles'       => $roleIds,
                    'permissions' => $checkedNames->values(),
                ],
            );

            DB::commit();
            return back()->with('success', "Permission user \"{$user->name}\" berhasil diperbarui.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update permission: ' . $e->getMessage());
        }
    }

    // ── Modules — Update Config ───────────────────────────────────────────────
    public function updateConfig(Request $request, $slug)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            // 'config' => 'nullable|array' // Jika nanti pakai kolom JSON
        ]);

        $module = \App\Models\SystemModule::where('slug', $slug)->firstOrFail();
        
        // Update data
        $module->update([
            'name' => $request->name,
            // Di sini kamu bisa tambahkan logic simpan config lain
        ]);

        // Hapus cache agar perubahan langsung muncul di Dashboard
        \Illuminate\Support\Facades\Cache::forget('sa:modules_stats');

        return back()->with('success', "Konfigurasi modul {$module->name} berhasil diperbarui!");
    }

    // ── Users — Update Roles ───────────────────────────────────────────────────
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles'           => ['nullable', 'array'],
            'roles.*'         => ['exists:roles,id'],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'student_number'  => ['nullable', 'string', 'max:50'],
            'cohort_year'     => ['nullable', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
        ]);

        DB::beginTransaction();
        try {
            $oldRoleNames = $user->roles->pluck('name')->values()->toArray(); // snapshot sebelum sync

            $newRoleIds   = $validated['roles'] ?? [];
            $user->roles()->sync($newRoleIds);
            $newRoleNames = Role::whereIn('id', $newRoleIds)->pluck('name');

            if ($newRoleNames->contains('dosen') && !$user->lecturer && !empty($validated['employee_number'])) {
                Lecturer::create(['user_id' => $user->id, 'employee_number' => $validated['employee_number']]);
            }

            if ($newRoleNames->contains('mahasiswa') && !$user->student && !empty($validated['student_number'])) {
                Student::create([
                    'user_id'        => $user->id,
                    'student_number' => $validated['student_number'],
                    'cohort_year'    => $validated['cohort_year'] ?? date('Y'),
                ]);
            }

            // ── Ganti UserAuditLog → AuditLogger ──────────────────────────────
            AuditLogger::update(
                module:  'user_management',
                desc:    "Mengubah role user: {$user->name} ({$user->email})",
                subject: $user,
                oldData: ['roles' => $oldRoleNames],
                newData: ['roles' => $newRoleNames->values()->toArray()],
            );

            $this->bustUserCache();
            DB::commit();

            return back()->with('success', "Role untuk {$user->name} berhasil diperbarui.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update role: ' . $e->getMessage());
        }
    }

    // ── Users — Delete ─────────────────────────────────────────────────────────
    public function destroyUser(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        DB::beginTransaction();
        try {
            $type = $request->input('delete_type', 'soft'); // Default: hapus biasa
            $oldData = [
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
            ];

            if ($type === 'permanent') {
                // Hapus Permanen dari DB
                $user->roles()->detach(); // Putus relasi pivot
                $user->forceDelete();
                $msg = "User \"{$user->name}\" dihapus permanen.";
            } else {
                // Hapus Biasa (Soft Delete)
                $user->delete();
                $msg = "User \"{$user->name}\" dipindahkan ke sampah.";
            }

            AuditLogger::delete(
                module: 'user_management',
                desc: $msg,
                subject: $user,
                oldData: $oldData
            );

            $this->bustUserCache();
            DB::commit();

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ── Users -- Bulk Delete ────────────────────────────────────────────────────────
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('delete_type', 'soft');

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada user yang dipilih.');
        }

        // Pastikan admin tidak menghapus dirinya sendiri dari bulk action
        $ids = array_diff($ids, [auth()->id()]);

        try {
            DB::beginTransaction();
            
            $users = User::whereIn('id', $ids)->get();

            foreach ($users as $user) {
                if ($type === 'permanent') {
                    $user->roles()->detach();
                    $user->forceDelete();
                } else {
                    $user->delete();
                }
            }

            AuditLogger::delete(
                module: 'user_management',
                desc: "Bulk Delete ($type): " . count($ids) . " users",
                subject: null,
                oldData: ['ids' => $ids, 'type' => $type],
            );

            DB::commit();
            return back()->with('success', count($ids) . " user berhasil dihapus ($type).");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus massal: ' . $e->getMessage());
        }
    }

    // ── Account Control ────────────────────────────────────────────────────────
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        DB::beginTransaction();
        try {
            $oldData = ['name' => $user->name, 'email' => $user->email];
            
            $user->update($validated);
            $user->clearUserCache();

            AuditLogger::update(
                module:  'user_management',
                desc:    "Update info user: {$user->name}",
                subject: $user,
                oldData: $oldData,
                newData: $validated,
            );

            $this->bustUserCache();
            DB::commit();

            return back()->with('success', "Info user \"{$user->name}\" berhasil diperbarui.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update user: ' . $e->getMessage());
        }
    }

    public function forceLogout(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat force logout akun sendiri.');
        }

        $user->forceLogout();

        AuditLogger::update(
            module:  'user_management',
            desc:    "Force logout user: {$user->name} ({$user->email})",
            subject: $user,
            oldData: [],
            newData: ['action' => 'force_logout'],
        );

        return back()->with('success', "User \"{$user->name}\" berhasil di-logout paksa.");
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat suspend akun sendiri.');
        }

        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'Tidak dapat suspend akun superadmin.');
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->suspend($request->reason ?? '');

        AuditLogger::update(
            module:  'user_management',
            desc:    "Suspend user: {$user->name} ({$user->email})",
            subject: $user,
            oldData: ['suspended' => false],
            newData: ['suspended' => true, 'reason' => $request->reason],
        );

        $this->bustUserCache();

        return back()->with('success', "User \"{$user->name}\" berhasil disuspend.");
    }

    public function unsuspend(User $user)
    {
        $user->unsuspend();

        AuditLogger::update(
            module:  'user_management',
            desc:    "Unsuspend user: {$user->name} ({$user->email})",
            subject: $user,
            oldData: ['suspended' => true],
            newData: ['suspended' => false],
        );

        $this->bustUserCache();

        return back()->with('success', "User \"{$user->name}\" berhasil di-unsuspend.");
    }

    public function usersByCategory(Request $request, $category)
    {
        $categories = [
            'Admins'    => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
            'Dosen'     => ['dosen'],
            'Mahasiswa' => ['mahasiswa'],
            'GPM'       => ['gpm'],
        ];

        if (!isset($categories[$category])) {
            abort(404);
        }

        $slugs = $categories[$category];

        // 1. Tangkap input dari request
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20); // Default 20 jika tidak dipilih

        // 2. Query User dengan filter search
        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', $slugs))
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->with(['roles', 'directPermissions', 'roles.permissions'])
            ->paginate($perPage);

        // 3. Data pendukung untuk UI Permission
        $roles       = \App\Models\Role::with('permissions')->get();
        $permissions = \App\Models\Permission::all()->groupBy('module');

        return view('superadmin.permission.category', compact('users', 'category', 'roles', 'permissions'));
    }
    
    // ── Users — Bulk Import ─────────────────────────────────────────────────
    public function bulkImport(Request $request) 
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('file');

        // Hitung total baris (dikurangi 1 untuk header)
        $totalRows = count(file($file->getRealPath())) - 1;

        // 1. Simpan status awal
        $importStatus = \App\Models\ImportStatus::create([
            'user_id' => auth()->id(),
            'filename' => $file->getClientOriginalName(),
            'total_rows' => $totalRows,
            'status' => 'pending'
        ]);

        $storage = new \App\Services\SupabaseStorage();
        $path = $storage->upload($file, 'csv-imports', 'data_user');

        if ($path) {
            // 2. Kirim ID status ke Job
            \App\Jobs\ProcessBulkImport::dispatch($path, 'data_user', $importStatus->id);
            
            // Kembalikan ID status agar Frontend bisa mulai polling
            return back()->with([
                'success' => 'Import dimulai...',
                'import_id' => $importStatus->id 
            ]);
        }
    }

    // ─ Users — Cancel Import ─────────────────────────────────────────────────
    public function cancelImport($id)
    {
        $status = \App\Models\ImportStatus::findOrFail($id);
        
        // Update status di DB agar Job berhenti di baris berikutnya
        $status->update(['status' => 'failed']); 

        return response()->json(['message' => 'Proses dibatalkan']);
    }

    // ── Modules ────────────────────────────────────────────────────────────────
    public function modules()
    {
        // Ambil data dari database sekarang, bukan hardcode array lagi
        $modules = \App\Models\SystemModule::all();
        return view('superadmin.modules.index', compact('modules'));
    }

    public function toggleModule(Request $request, $slug)
    {
        $module = \App\Models\SystemModule::where('slug', $slug)->firstOrFail();

        \Illuminate\Support\Facades\DB::table('system_modules')
            ->where('slug', $slug)
            ->update([
                'is_active'  => \Illuminate\Support\Facades\DB::raw('NOT is_active'),
                'updated_at' => now(),
            ]);

        \Illuminate\Support\Facades\Cache::forget("module_active_{$slug}");
        \Illuminate\Support\Facades\Cache::forget('sa:modules_stats');

        $module->refresh();

        return back()->with('success', "Status modul {$module->name} berhasil diubah!");
    }
    
    // ── Permissions ────────────────────────────────────────────────────────────
    public function permissions()
    {
        // Tambahkan .permissions di sebelah roles
        $users = User::with(['roles.permissions', 'directPermissions']) 
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $permissions = \App\Models\Permission::all()->groupBy('module');

        $roles = Role::with('permissions')->get();

        return view('superadmin.permission.permissions', compact('users', 'permissions', 'roles'));
    }

    // ── Storage Test — Upload ──────────────────────────────────────────────────
 
    public function testUpload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // max 10MB
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx',
            ],
        ]);
 
        $storage = new \App\Services\SupabaseStorage();
        $path    = $storage->upload($request->file('file'), 'test-uploads');
 
        if (!$path) {
            return back()->with('upload_error', 'Upload ke Supabase gagal. Cek service role key dan nama bucket.');
        }
 
        $publicUrl = $storage->publicUrl($path);
 
        return back()->with([
            'upload_success' => true,
            'upload_path'    => $path,
            'upload_url'     => $publicUrl,
            'upload_name'    => $request->file('file')->getClientOriginalName(),
            'upload_size'    => $request->file('file')->getSize(),
        ]);
    }
 
    // ── Storage Test — Delete ──────────────────────────────────────────────────
 
    public function testDelete(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
        ]);
 
        $storage = new \App\Services\SupabaseStorage();
        $deleted = $storage->delete($request->input('path'));
 
        if (!$deleted) {
            return back()->with('upload_error', 'Gagal menghapus file dari Supabase.');
        }
 
        return back()->with('delete_success', 'File berhasil dihapus dari Supabase Storage.');
    }

    // ── Audit Logs ─────────────────────────────────────────────────────────────

    public function auditLogs(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [25, 50, 100]) ? (int) $request->per_page : 25;

        $query = AuditLog::with('user')
            ->when($request->filled('module'),    fn($q) => $q->where('module', $request->module))
            ->when($request->filled('action'),    fn($q) => $q->where('action', $request->action))
            ->when($request->filled('user_id'),   fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->filled('search'),    fn($q) => $q->where('description', 'ILIKE', "%{$request->search}%"))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $users   = User::select('id', 'name', 'email')->orderBy('name')->get();
        $modules = ['bank_soal', 'capstone', 'eoffice', 'manajemen_mahasiswa', 'user_management'];
        $actions = ['CREATE', 'UPDATE', 'DELETE', 'VIEW', 'LOGIN'];

        return view('superadmin.audit-logs', compact('query', 'users', 'modules', 'actions'));
    }
}