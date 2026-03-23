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
        $data = [
            'total_users'       => Cache::remember('sa:total_users',       self::TTL_STATS,  fn() => User::count()),
            'total_superadmins' => Cache::remember('sa:total_superadmins', self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'superadmin'))->count()),
            'total_lecturers'   => Cache::remember('sa:total_lecturers',   self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->count()),
            'total_students'    => Cache::remember('sa:total_students',     self::TTL_STATS,  fn() => User::whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))->count()),
            'recent_users'      => Cache::remember('sa:recent_users',      self::TTL_RECENT, fn() => User::with('roles')->latest('created_at')->limit(10)->get()),
            'modules'           => Cache::remember('sa:modules_stats',     self::TTL_STATS,  fn() => $this->getModulesStats()),
            'recent_logs'       => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
        ];

        return view('superadmin.dashboard', $data);
    }

    private function getModulesStats(): array
    {
        return [
            'bank_soal' => [
                'name'        => 'Bank Soal',
                'icon'        => 'book',
                'route'       => 'banksoal.dashboard',
                'pertanyaan'  => DB::table('bs_pertanyaan')->count(),
                'mata_kuliah' => DB::table('bs_mata_kuliah')->count(),
            ],
            'capstone' => [
                'name'    => 'Capstone',
                'icon'    => 'graduation-cap',
                'route'   => 'capstone.dashboard',
                'groups'  => DB::table('capstone_groups')->whereNull('deleted_at')->count(),
                'periods' => DB::table('capstone_periods')->count(),
            ],
            'eoffice' => [
                'name'  => 'E-Office',
                'icon'  => 'briefcase',
                'route' => 'eoffice.dashboard',
            ],
            'manajemen_mahasiswa' => [
                'name'     => 'Manajemen Mahasiswa',
                'icon'     => 'users',
                'route'    => 'mahasiswa.dashboard',
                'students' => DB::table('students')->count(),
                'alumni'   => DB::table('mk_alumni')->count(),
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
            ->select('id', 'name', 'email', 'created_at', 'last_login', 'deleted_at');

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

    // ── Users — Destroy ────────────────────────────────────────────────────────

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        DB::beginTransaction();
        try {
            $oldData = [
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->values()->toArray(),
            ];

            $user->update(['deleted_at' => now()]);

            // ── Ganti UserAuditLog → AuditLogger ──────────────────────────────
            AuditLogger::delete(
                module:  'user_management',
                desc:    "Menghapus user: {$user->name} ({$user->email})",
                subject: $user,
                oldData: $oldData,
            );

            $this->bustUserCache();
            DB::commit();

            return back()->with('success', "User \"{$user->name}\" berhasil dihapus.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // ── Modules ────────────────────────────────────────────────────────────────

    public function modules()
    {
        $modules = [
            ['id' => 'bank_soal',           'name' => 'Bank Soal',           'description' => 'Manage question bank and learning materials', 'icon' => 'book',           'status' => true, 'active_users'    => DB::table('bs_dosen_pengampu_mk')->count(), 'total_questions' => DB::table('bs_pertanyaan')->count()],
            ['id' => 'capstone',            'name' => 'Capstone',            'description' => 'Manage capstone projects and thesis',          'icon' => 'graduation-cap', 'status' => true, 'active_groups'   => DB::table('capstone_groups')->whereNull('deleted_at')->count()],
            ['id' => 'eoffice',             'name' => 'E-Office',            'description' => 'Manage office documents and workflow',         'icon' => 'briefcase',      'status' => true],
            ['id' => 'manajemen_mahasiswa', 'name' => 'Manajemen Mahasiswa', 'description' => 'Manage student data and activities',           'icon' => 'users',          'status' => true, 'total_students'  => DB::table('students')->count(), 'total_alumni' => DB::table('mk_alumni')->count()],
        ];

        return view('superadmin.modules.index', compact('modules'));
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