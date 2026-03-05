<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SuperAdminController extends Controller
{
    public function index()
    {
        $data = [
            'total_users'       => Cache::remember('sa:total_users', 300, fn() => User::count()),
            'total_superadmins' => Cache::remember('sa:total_superadmins', 300, fn() => User::whereHas('roles', fn($q) => $q->where('name', 'superadmin'))->count()),
            'total_lecturers'   => Cache::remember('sa:total_lecturers', 300, fn() => User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->count()),
            'total_students'    => Cache::remember('sa:total_students', 300, fn() => User::whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))->count()),
            'recent_users'      => Cache::remember('sa:recent_users', 60, fn() => User::with('roles')->latest('created_at')->limit(10)->get()),
            'modules'           => Cache::remember('sa:modules_stats', 300, fn() => $this->getModulesStats()),
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

    public function users(Request $request)
    {
        $query = User::with('roles');

        // Filter by role — $request->role berisi nama role (string), bukan kolom di users
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles'   => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $user->roles()->sync($request->roles);

            return redirect()->back()->with('success', "Role untuk {$user->name} berhasil diupdate.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update role: ' . $e->getMessage());
        }
    }

    public function modules()
    {
        $modules = [
            [
                'id'          => 'bank_soal',
                'name'        => 'Bank Soal',
                'description' => 'Manage question bank and learning materials',
                'icon'        => 'book',
                'status'      => true,
                'active_users'     => DB::table('bs_dosen_pengampu_mk')->count(),
                'total_questions'  => DB::table('bs_pertanyaan')->count(),
            ],
            [
                'id'          => 'capstone',
                'name'        => 'Capstone',
                'description' => 'Manage capstone projects and thesis',
                'icon'        => 'graduation-cap',
                'status'      => true,
                'active_groups'   => DB::table('capstone_groups')->whereNull('deleted_at')->count(),
            ],
            [
                'id'          => 'eoffice',
                'name'        => 'E-Office',
                'description' => 'Manage office documents and workflow',
                'icon'        => 'briefcase',
                'status'      => true,
            ],
            [
                'id'             => 'manajemen_mahasiswa',
                'name'           => 'Manajemen Mahasiswa',
                'description'    => 'Manage student data and activities',
                'icon'           => 'users',
                'status'         => true,
                'total_students' => DB::table('students')->count(),
                'total_alumni'   => DB::table('mk_alumni')->count(),
            ],
        ];

        return view('superadmin.modules.index', compact('modules'));
    }

    public function auditLogs(Request $request)
    {
        $query = UserAuditLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs  = $query->latest()->paginate(50);
        $users = User::select('id', 'name', 'email')->get();

        return view('superadmin.audit-logs', compact('logs', 'users'));
    }
}