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

    public function bustStatsCache()
    {
        $this->bustUserCache();
        return response()->json(['ok' => true]);
    }
    
    private function bustUserCache(): void
    {
        try {
            Cache::tags(['sa_users'])->flush();
        } catch (\BadMethodCallException $e) {
            // Driver tidak support tags (file/database) — forget manual
        }

        $keys = [
            'sa:total_users', 'sa:total_superadmins', 'sa:total_lecturers',
            'sa:total_students', 'sa:total_gpm', 'sa:total_admin_modul',
            'sa:recent_users', 'sa:recent_logins', 'sa:new_registrations',
        ];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
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
            'total_gpm'         => Cache::remember('sa:total_gpm',         self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'gpm'))->count()),
            'total_admin_modul' => Cache::remember('sa:total_admin_modul', self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'LIKE', 'admin_%'))->count()),
            'total_gpm'         => Cache::remember('sa:total_gpm',         self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'gpm'))->count()),
            'total_admin_modul' => Cache::remember('sa:total_admin_modul', self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'LIKE', 'admin_%'))->count()),
            'total_lecturers'   => Cache::remember('sa:total_lecturers',   self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->count()),
            'total_students'    => Cache::remember('sa:total_students',    self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))->count()),
            
            // 1. Variabel Baru: Khusus untuk tabel "Login Terakhir" (Diurutkan berdasarkan last_login)
            'recent_logins'     => Cache::remember('sa:recent_logins',     self::TTL_RECENT, fn() => 
                                    User::with('roles')->whereNotNull('last_login')->orderByDesc('last_login')->limit(5)->get()
                                ),

            // 2. Variabel Baru: Khusus untuk tabel "User Baru Terdaftar" (Diurutkan berdasarkan created_at)
            'new_registrations' => Cache::remember('sa:new_registrations', self::TTL_RECENT, fn() => 
                                    User::with('roles')->latest('created_at')->limit(5)->get()
                                ),

            'modules'           => Cache::remember('sa:modules_stats',     self::TTL_STATS,  fn() => $this->getModulesStats()),
            'recent_logs' => Cache::remember('sa:recent_logs', 30, fn() =>
                AuditLog::with(['user' => fn($q) => $q->select('id', 'name', 'email', 'avatar_url')])
                    ->latest('created_at')->limit(8)->get()
            ),
            'activeImportId'    => $activeImport?->id, 
        ];

        return view('superadmin.dashboard.dashboard', $data);
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

        $perPageInput = $request->input('per_page', $request->session()->get('um_per_page', 10));
        $perPage = in_array((int) $perPageInput, [10, 25, 50, 100]) ? (int) $perPageInput : 10;

        // Simpan ke session kalau ada di request
        if ($request->has('per_page')) {
            $request->session()->put('um_per_page', $perPage);
        }

        $roles    = Cache::remember('sa:roles_list', self::TTL_ROLES, fn() => Role::orderBy('name')->get());
        $cacheKey = $this->userListCacheKey($search, $role, $page, $perPage);

        // EAGER LOADING: Tambahkan 'directPermissions' untuk mendukung desain tabel baru
        $query = User::with(['roles', 'directPermissions']) 
            ->whereNull('deleted_at')
            ->select('id', 'name', 'email', 'avatar_url', 'created_at', 'last_login', 'deleted_at', 'suspended_at', 'suspension_reason', 'is_online');

        // Filter Role menggunakan whereExists agar performa lebih cepat pada PostgreSQL
        if ($role !== 'all') {
            $query->whereExists(function ($sub) use ($role) {
                $sub->select(DB::raw(1))
                    ->from('user_roles')
                    ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                    ->whereColumn('user_roles.user_id', 'users.id')
                    ->where('roles.name', $role);
            });
        }

        // Pencarian dengan ILIKE (Case-Insensitive untuk PostgreSQL)
        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name',  'ILIKE', $term)
                ->orWhere('email', 'ILIKE', $term);
            });
        }

        // Menggunakan Cache Tags agar manajemen cache user lebih rapi
        $users = Cache::tags(['sa_users'])->remember($cacheKey, self::TTL_USERS, function() use ($query, $perPage) {
            return $query->latest()->paginate($perPage);
        });

        $paginator = $users;

        // Support untuk AJAX Refresh (jika tabel di-update tanpa reload)
        if ($request->ajax()) {
            return response()->json([
                'rows'       => view('superadmin.users._table_rows', compact('users'))->render(),
                'pagination' => view('superadmin.users._pagination', compact('paginator'))->render(),
                'total'      => $users->total(),
            ]);
        }

        return view('superadmin.users.index', compact('users', 'roles', 'paginator'));
    }

    // User Status (Online / Suspended)
    public function onlineUsers(Request $request)
    {
        // 1. Ambil parameter filter
        $search  = (string) $request->input('search', '');
        $role    = (string) $request->input('role', 'all');
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100])
                ? (int) $request->input('per_page', 10)
                : 10;

        // 2. Ambil daftar role untuk dropdown
        $roles = \Illuminate\Support\Facades\Cache::remember('sa:roles_list', 3600, fn() => Role::orderBy('name')->get());

        $query = \App\Models\User::with(['roles', 'directPermissions'])
            ->whereNull('deleted_at')
            ->where('is_online', \Illuminate\Support\Facades\DB::raw('true'));

        // 3. Logic Filter Role
        if ($role !== 'all') {
            $query->whereExists(function ($sub) use ($role) {
                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('user_roles')
                    ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                    ->whereColumn('user_roles.user_id', 'users.id')
                    ->where('roles.name', $role);
            });
        }

        // 4. Logic Filter Search
        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ILIKE', $term)->orWhere('email', 'ILIKE', $term);
            });
        }

        // 5. Paginate
        $users = $query->latest('last_login')->paginate($perPage);
        
        return view('superadmin.users.online', compact('users', 'roles'));
    }

    public function suspendedUsers(Request $request)
    {
        $search  = (string) $request->input('search', '');
        $role    = (string) $request->input('role', 'all');
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50, 100])
                ? (int) $request->input('per_page', 10)
                : 10;

        $roles = \Illuminate\Support\Facades\Cache::remember('sa:roles_list', 3600, fn() => Role::orderBy('name')->get());

        $query = \App\Models\User::with(['roles', 'directPermissions'])
            ->whereNull('deleted_at')
            ->whereNotNull('suspended_at');

        if ($role !== 'all') {
            $query->whereExists(function ($sub) use ($role) {
                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('user_roles')
                    ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                    ->whereColumn('user_roles.user_id', 'users.id')
                    ->where('roles.name', $role);
            });
        }

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ILIKE', $term)->orWhere('email', 'ILIKE', $term);
            });
        }

        $users = $query->latest('suspended_at')->paginate($perPage);
        
        return view('superadmin.users.suspended', compact('users', 'roles'));
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
        // 1. Validasi Input
        $ids = $request->input('ids');
        $type = $request->input('delete_type', 'soft');

        if (empty($ids)) {
            $errorMsg = 'Tidak ada user yang dipilih.';
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // 2. Proteksi Diri Sendiri (Jangan biarkan admin menghapus akunnya sendiri)
        $ids = array_diff($ids, [auth()->id()]);

        DB::beginTransaction();
        try {
            // 3. Ambil data user untuk keperluan Audit Log
            $users = User::whereIn('id', $ids)->get();
            $count = $users->count();

            if ($count === 0) {
                throw new \Exception("User tidak ditemukan atau Anda mencoba menghapus akun sendiri.");
            }

            // 4. Eksekusi Penghapusan
            foreach ($users as $user) {
                if ($type === 'permanent') {
                    $user->roles()->detach(); // Putus semua relasi role
                    $user->forceDelete();     // Hapus permanen dari DB
                } else {
                    $user->delete();          // Soft delete (masuk ke trashed)
                }
            }

            // 5. Logging Aktivitas
            AuditLogger::delete(
                module: 'user_management',
                desc: "Bulk Delete ($type): " . $count . " users",
                subject: null,
                oldData: ['ids' => $ids, 'type' => $type],
            );

            DB::commit();

            // 6. BUST CACHE (Krusial agar data langsung hilang saat reload)
            $this->bustUserCache(); 

            $successMsg = $count . " user berhasil dihapus ($type).";

            // 7. Handle Response
            if ($request->wantsJson()) {
                // Flash ke session secara manual agar dibaca oleh Blade alert setelah reload
                session()->flash('success', $successMsg); 
                
                return response()->json([
                    'status' => 'success',
                    'message' => $successMsg
                ]);
            }

            return back()->with('success', $successMsg);

        } catch (\Throwable $e) {
            DB::rollBack();
            $errDetail = 'Gagal menghapus massal: ' . $e->getMessage();

            if ($request->wantsJson()) {
                session()->flash('error', $errDetail);
                return response()->json(['status' => 'error', 'message' => $errDetail], 500);
            }

            return back()->with('error', $errDetail);
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
            'Unassigned' => [], // Tambahkan key ini agar tidak 404
        ];

        if (!isset($categories[$category])) {
            abort(404);
        }

        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);

        // --- LOGIKA QUERY ---
        $query = User::query();

        if ($category === 'Unassigned') {
            // Cari user yang tidak punya role sama sekali
            $query->doesntHave('roles');
        } else {
            // Cari user berdasarkan role yang didefinisikan di atas
            $slugs = $categories[$category];
            $query->whereHas('roles', fn($q) => $q->whereIn('name', $slugs));
        }

        // Tambahkan filter search jika ada
        $users = $query
            ->when($search, function($q) use ($search) {
                $q->where(function($inner) use ($search) {
                    $inner->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            })
            ->with(['roles.permissions', 'directPermissions']) // roles.permissions sudah include roles
            ->select('id', 'name', 'email', 'avatar_url', 'suspended_at')
            ->paginate($perPage);

        // Data pendukung untuk UI Permission (agar card bisa di-edit langsung)
        $roles       = \App\Models\Role::with('permissions')->get();
        $permissions = \App\Models\Permission::all()->groupBy('module');

        return view('superadmin.permission.category', compact('users', 'category', 'roles', 'permissions'));
    }
    
    // ── Users — Bulk Import ─────────────────────────────────────────────────
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        $file = $request->file('file');

        // ==========================================
        // ✅ 1. CEK SIDIK JARI FILE (HASHING)
        // ==========================================
        $fileHash = md5_file($file->getRealPath());

        $existingImport = \App\Models\ImportStatus::where('file_hash', $fileHash)
                            ->where('status', 'completed')
                            ->whereNotNull('path')
                            ->latest()
                            ->first();

        // ==========================================
        // ✅ 2. VALIDASI STRUKTUR CSV
        // ==========================================
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (!$header || count($header) < 8) {
            fclose($handle);
            return response()->json([
                'status'  => 'error',
                'message' => 'Struktur CSV tidak valid (minimal 8 kolom: name, email, password, role, external_id, student_number, cohort_year, permissions)'
            ], 422);
        }

        // Normalize header
        $header = array_map('trim', array_map('strtolower', $header));

        $emailCol      = array_search('email', $header);
        $nameCol       = array_search('name', $header);
        $externalIdCol = array_search('external_id', $header);

        if ($emailCol === false) {
            fclose($handle);
            return response()->json([
                'status'  => 'error',
                'message' => 'Kolom "email" tidak ditemukan di CSV.'
            ], 422);
        }

        // ==========================================
        // ✅ 3. BACA SEMUA EMAIL DARI CSV
        // ==========================================
        $csvRows       = [];
        $csvEmails     = [];
        $csvExternalIds = [];
        $totalRows     = 0;
        $validRowFound = false;

        while (($row = fgetcsv($handle)) !== false) {
            $totalRows++;
            $email = isset($row[$emailCol]) ? trim($row[$emailCol]) : null;

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validRowFound    = true;
                $csvEmails[]      = strtolower($email);
                $csvRows[]        = [
                    'name'        => isset($row[$nameCol]) ? trim($row[$nameCol]) : $email,
                    'email'       => $email,
                    'external_id' => ($externalIdCol !== false && isset($row[$externalIdCol]))
                                        ? trim($row[$externalIdCol]) : null,
                ];
                if ($externalIdCol !== false && isset($row[$externalIdCol]) && trim($row[$externalIdCol]) !== '') {
                    $csvExternalIds[] = trim($row[$externalIdCol]);
                }
            }
        }
        fclose($handle);

        if (!$validRowFound) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada data valid dalam file.'
            ], 422);
        }

        // ==========================================
        // ✅ 4. CEK DUPLIKASI EMAIL DI DATABASE
        // ==========================================
        $duplicateUsers = \App\Models\User::whereIn(
                DB::raw('LOWER(email)'), $csvEmails
            )
            ->select('id', 'name', 'email', 'avatar_url', 'created_at')
            ->with('roles:id,name')
            ->get();

        // Cek juga external_id jika ada
        $duplicateExternalIds = collect();
        if (!empty($csvExternalIds)) {
            $duplicateExternalIds = \App\Models\User::whereIn('external_id', $csvExternalIds)
                ->select('id', 'name', 'email', 'avatar_url', 'created_at')
                ->with('roles:id,name')
                ->get();
        }

        // Merge dan deduplicate by id
        $allDuplicates = $duplicateUsers
            ->merge($duplicateExternalIds)
            ->unique('id')
            ->values();

        if ($allDuplicates->isNotEmpty()) {
            return response()->json([
                'status'     => 'duplicate',
                'message'    => $allDuplicates->count() . ' data dalam file sudah terdaftar di sistem.',
                'duplicates' => $allDuplicates->map(fn($u) => [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'avatar_url' => $u->avatar_url,
                    'roles'      => $u->roles->pluck('name')->toArray(),
                    'created_at' => $u->created_at->format('d M Y'),
                ]),
            ], 422);
        }

        // ==========================================
        // ✅ 5. LOGIKA DEDUPLIKASI BUCKET
        // ==========================================
        $storage = new \App\Services\SupabaseStorage();

        if ($existingImport) {
            $testUrl = $storage->signedUrl($existingImport->path, 60, 'data_user');
            if ($testUrl) {
                $path    = $existingImport->path;
                $message = 'File identik ditemukan. Menggunakan aset yang sudah ada.';
            } else {
                // Path lama tidak valid, upload ulang ke csv_imports
                $path    = $storage->upload($file, 'csv_imports', 'data_user');
                $message = 'File baru berhasil diunggah.';
            }
        } else {
            $path    = $storage->upload($file, 'csv_imports', 'data_user');
            $message = 'File baru berhasil diunggah.';
        }

        if (!$path) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses file ke storage'
            ], 500);
        }

        // ==========================================
        // ✅ 6. SIMPAN STATUS & DISPATCH
        // ==========================================
        $import = \App\Models\ImportStatus::create([
            'user_id'        => auth()->id(),
            'filename'       => $file->getClientOriginalName(),
            'file_hash'      => $fileHash,
            'path'           => $path,
            'total_rows'     => $totalRows,
            'processed_rows' => 0,
            'status'         => 'pending'
        ]);

        \App\Jobs\ProcessBulkImport::dispatch(
            $path,
            'data_user',
            $import->id
        );

        return response()->json([
            'status'    => 'success',
            'message'   => $message,
            'import_id' => $import->id
        ]);
    }

    // ── Users — Import Status ─────────────────────────────────────────────────
    public function getImportStatus($id)
    {
        try {
            $import = \App\Models\ImportStatus::find($id);
            
            if (!$import) {
                // Jika import status tidak ditemukan, hapus session
                session()->forget('import_id');
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Import record not found'
                ], 404);
            }
            
            // Jika status sudah completed atau failed dan sudah lama, hapus dari session
            if (in_array($import->status, ['completed', 'failed'])) {
                $createdAt = $import->created_at;
                $hoursSince = $createdAt ? now()->diffInHours($createdAt) : 0;
                
                // Hapus dari session jika sudah lebih dari 1 jam
                if ($hoursSince > 1) {
                    session()->forget('import_id');
                }
            }
            
            return response()->json([
                'id' => $import->id,
                'status' => $import->status,
                'processed' => $import->processed_rows,
                'total' => $import->total_rows,
                'filename' => $import->filename,
                'error_message' => $import->error_message,
                'percentage' => $import->percentage
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error getting import status", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get import status'
            ], 500);
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

    public function permissions(Request $request)
    {
        $search = $request->input('search');

        $query = User::with(['roles.permissions', 'directPermissions']) 
            ->whereNull('deleted_at');

        // Tambahkan Logika Search agar filter berfungsi
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%") // Gunakan ILIKE untuk PostgreSQL atau LIKE untuk MySQL
                ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        // Ambil data (Gunakan get() karena dashboard utama menampilkan kategori, 
        // tapi filter pencarian akan membatasi user yang muncul di dalam kategori tersebut)
        $users = $query->orderBy('name')->get();

        $permissions = \App\Models\Permission::all()->groupBy('module');
        $roles = Role::with('permissions')->get();

        return view('superadmin.permission.permissions', compact('users', 'permissions', 'roles'));
    }

    public function repairAllPermissions(Request $request)
    {
        $dryRun = $request->input('dry_run', false);
        
        if ($dryRun) {
            // Dry run - only check, don't repair
            $needsRepair = [];
            User::chunk(100, function ($users) use (&$needsRepair) {
                foreach ($users as $user) {
                    $verification = PermissionAssigner::verifyPermissions($user);
                    if (!$verification['has_correct_permissions']) {
                        $needsRepair[] = [
                            'user'    => $user->email,
                            'missing' => $verification['missing'],
                            'excess'  => $verification['excess'],
                        ];
                    }
                }
            });
            
            return response()->json([
                'message' => 'Dry run completed',
                'users_needing_repair' => $needsRepair,
                'total' => count($needsRepair)
            ]);
        }
        
        // Actually repair
        $repaired = 0;
        User::chunk(100, function ($users) use (&$repaired) {
            foreach ($users as $user) {
                PermissionAssigner::repairPermissions($user);
                $repaired++;
            }
        });

        return response()->json([
            'message' => "Repaired permissions for {$repaired} users"
        ]);
        
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

        $logs = AuditLog::with([
                'user'       => fn($q) => $q->select('id', 'name', 'email', 'avatar_url', 'is_online', 'suspended_at'),
                'user.roles',
            ])
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
        $actions = ['CREATE', 'UPDATE', 'DELETE', 'VIEW', 'LOGIN', 'LOGOUT'];

        return view('superadmin.audit-logs.audit-logs', compact('logs', 'users', 'modules', 'actions'));
    }

    public function bulkDeleteAuditLogs(Request $request)
    {
        $ids = $request->input('ids', []);
        $timeOption = $request->input('time_option', '');
        $deleteType = $request->input('delete_type', 'selected'); // 'selected', '6hours', '12hours', '24hours'
        
        try {
            DB::beginTransaction();
            
            $query = AuditLog::query();
            
            // Filter berdasarkan jenis hapus
            if ($deleteType === 'selected' && !empty($ids)) {
                $query->whereIn('id', $ids);
                $count = count($ids);
                $description = "Menghapus {$count} log terpilih";
            } 
            elseif ($deleteType === '6hours') {
                $query->where('created_at', '<', now()->subHours(6));
                $count = $query->count();
                $description = "Menghapus log lebih dari 6 jam yang lalu ({$count} log)";
            }
            elseif ($deleteType === '12hours') {
                $query->where('created_at', '<', now()->subHours(12));
                $count = $query->count();
                $description = "Menghapus log lebih dari 12 jam yang lalu ({$count} log)";
            }
            elseif ($deleteType === '24hours') {
                $query->where('created_at', '<', now()->subHours(24));
                $count = $query->count();
                $description = "Menghapus log lebih dari 24 jam yang lalu ({$count} log)";
            }
            else {
                return back()->with('error', 'Tidak ada log yang dipilih untuk dihapus.');
            }
            
            if ($count === 0) {
                return back()->with('error', 'Tidak ada log yang memenuhi kriteria untuk dihapus.');
            }
            
            // Hapus log
            $deleted = $query->delete();
            
            // Audit log untuk aksi bulk delete
            AuditLogger::delete(
                module: 'user_management',
                desc: $description,
                subject: null,
                oldData: [
                    'delete_type' => $deleteType,
                    'ids' => $ids,
                    'count' => $deleted
                ]
            );
            
            DB::commit();
            
            return back()->with('success', "Berhasil menghapus {$deleted} log aktivitas.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }
}