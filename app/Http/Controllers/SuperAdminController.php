<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Display superadmin dashboard
     */
    public function index()
    {
        $data = [
            'total_users' => User::count(),
            'total_superadmins' => User::whereHas('roles', function ($query) {
                $query->where('name', 'superadmin');
            })->count(),
            'total_lecturers' => User::whereHas('roles', function ($query) {
                $query->where('name', 'dosen');
            })->count(),
            'total_students' => User::whereHas('roles', function ($query) {
                $query->where('name', 'mahasiswa');
            })->count(),
            'recent_users' => User::latest('created_at')->limit(10)->get(),
            'modules' => $this->getModulesStats(),
        ];

        return view('superadmin.dashboard', $data);
    }

    /**
     * Get statistics for each module
     */
    private function getModulesStats()
    {
        return [
            'bank_soal' => [
                'name' => 'Bank Soal',
                'icon' => 'book',
                'route' => 'banksoal.dashboard',
                'pertanyaan' => DB::table('bs_pertanyaan')->count(),
                'mata_kuliah' => DB::table('bs_mata_kuliah')->count(),
            ],
            'capstone' => [
                'name' => 'Capstone',
                'icon' => 'graduation-cap',
                'route' => 'capstone.dashboard',
                'groups' => DB::table('capstone_groups')->count(),
                'periods' => DB::table('capstone_periods')->count(),
            ],
            'eoffice' => [
                'name' => 'E-Office',
                'icon' => 'briefcase',
                'route' => 'eoffice.dashboard',
            ],
            'manajemen_mahasiswa' => [
                'name' => 'Manajemen Mahasiswa',
                'icon' => 'users',
                'route' => 'mahasiswa.dashboard',
                'students' => DB::table('students')->count(),
                'alumni' => DB::table('mk_alumni')->count(),
            ],
        ];
    }

    /**
     * Show user management page
     */
    public function users(Request $request)
    {
        $query = User::with('roles');

        // Filter by role
        if ($request->has('role') && $request->role !== 'all') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search by name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $roles = Role::all();

        return view('superadmin.users.index', compact('users', 'roles'));
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            // Sync roles (bisa multiple roles)
            $user->roles()->sync($request->roles);

            return redirect()->back()->with('success', "Role for {$user->name} has been updated.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user role: ' . $e->getMessage());
        }
    }

    /**
     * Show modules management page
     */
    public function modules()
    {
        $modules = [
            [
                'id' => 'bank_soal',
                'name' => 'Bank Soal',
                'description' => 'Manage question bank and learning materials',
                'icon' => 'book',
                'status' => true,
                'active_users' => DB::table('bs_dosen_pengampu_mk')->count(),
                'total_questions' => DB::table('bs_pertanyaan')->count(),
            ],
            [
                'id' => 'capstone',
                'name' => 'Capstone',
                'description' => 'Manage capstone projects and thesis',
                'icon' => 'graduation-cap',
                'status' => true,
                'active_groups' => DB::table('capstone_groups')->where('deleted_at', null)->count(),
                'active_students' => DB::table('capstone_individual_ta')->where('deleted_at', null)->count(),
            ],
            [
                'id' => 'eoffice',
                'name' => 'E-Office',
                'description' => 'Manage office documents and workflow',
                'icon' => 'briefcase',
                'status' => true,
            ],
            [
                'id' => 'manajemen_mahasiswa',
                'name' => 'Manajemen Mahasiswa',
                'description' => 'Manage student data and activities',
                'icon' => 'users',
                'status' => true,
                'total_students' => DB::table('students')->count(),
                'total_alumni' => DB::table('mk_alumni')->count(),
            ],
        ];

        return view('superadmin.modules.index', compact('modules'));
    }

    /**
     * Show audit logs
     */
    public function auditLogs(Request $request)
    {
        $query = \App\Models\UserAuditLog::with('user');

        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action !== '') {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()->paginate(50);
        $users = User::all();

        return view('superadmin.audit-logs', compact('logs', 'users'));
    }
}