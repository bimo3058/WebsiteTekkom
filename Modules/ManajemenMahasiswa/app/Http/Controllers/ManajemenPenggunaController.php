<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\PermissionAssigner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\PengurusHimaskom;

class ManajemenPenggunaController extends Controller
{
    // Semua role posisi himpunan (tidak termasuk pengurus_himpunan yang bersifat umbrella)
    private const HIMPUNAN_POSITION_ROLES = [
        'ketua_himpunan',
        'wakil_ketua_himpunan',
        'ketua_bidang',
        'ketua_unit',
        'staff_himpunan',
    ];

    // Role yang dapat diassign oleh masing-masing level caller
    private const ASSIGNABLE_BY = [
        'admin_kemahasiswaan' => ['ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'alumni'],
        'admin'               => ['ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'alumni'],
        'superadmin'          => ['ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'alumni'],
        'ketua_himpunan'      => ['wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'],
        'wakil_ketua_himpunan'=> ['ketua_bidang', 'ketua_unit', 'staff_himpunan'],
        'ketua_bidang'        => ['staff_himpunan'],
        'ketua_unit'          => ['staff_himpunan'],
    ];

    // ── Halaman Utama ─────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $actor = auth()->user();
        $search = $request->input('search');

        $query = User::with(['roles', 'student'])
            ->whereNull('deleted_at')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['mahasiswa', 'alumni', 'pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES]));

        if ($search) {
            $query->where(fn($q) => $q
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('student', fn($sq) => $sq->where('student_number', 'LIKE', "%{$search}%"))
            );
        }

        // kabid/kanit hanya melihat anggota divisi sendiri
        if ($actor->hasAnyRole(['ketua_bidang', 'ketua_unit'])) {
            $actorDivisi = $this->getActorDivisi($actor);
            if ($actorDivisi) {
                $query->whereHas('student.pengurusHimaskom', fn($q) =>
                    $q->where('divisi', $actorDivisi)
                );
            }
        }

        $users = $query->orderBy('name')->get();

        $categories = [
            'Mahasiswa Aktif'   => $users->filter(fn($u) => $this->isMahasiswaAktif($u))->sortBy('name')->take(5),
            'Pengurus Himpunan' => $users->filter(fn($u) => $this->isPengurus($u))->sortBy('name')->take(5),
            'Alumni'            => $users->filter(fn($u) => $u->roles->pluck('name')->contains('alumni'))->sortBy('name')->take(5),
        ];

        $assignableRoles = $this->getAssignableRoles($actor);

        return view('manajemenmahasiswa::permissions.index', compact('users', 'categories', 'assignableRoles', 'search'));
    }

    // ── Halaman Kategori ──────────────────────────────────────────────────────

    public function category(Request $request, string $category)
    {
        $actor    = auth()->user();
        $search   = $request->input('search');
        $perPage  = (int) $request->input('per_page', 10);
        $roleFilter = $request->input('role', 'all');

        $validCategories = ['Mahasiswa Aktif', 'Pengurus Himpunan', 'Alumni'];
        abort_unless(in_array($category, $validCategories), 404);

        $query = User::with(['roles', 'student'])
            ->whereNull('deleted_at');

        match ($category) {
            'Mahasiswa Aktif'   => $query->whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))
                                         ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])),
            'Pengurus Himpunan' => $query->whereHas('roles', fn($q) => $q->whereIn('name', ['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])),
            'Alumni'            => $query->whereHas('roles', fn($q) => $q->where('name', 'alumni')),
        };

        if ($search) {
            $query->where(fn($q) => $q
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('student', fn($sq) => $sq->where('student_number', 'LIKE', "%{$search}%"))
            );
        }

        if ($category === 'Pengurus Himpunan' && $roleFilter !== 'all') {
            $query->whereHas('roles', fn($q) => $q->where('name', $roleFilter));
        }

        // kabid/kanit hanya melihat divisi sendiri
        if ($actor->hasAnyRole(['ketua_bidang', 'ketua_unit'])) {
            $actorDivisi = $this->getActorDivisi($actor);
            if ($actorDivisi) {
                $query->whereHas('student.pengurusHimaskom', fn($q) =>
                    $q->where('divisi', $actorDivisi)
                );
            }
        }

        $users = $query->orderBy('name')->paginate($perPage);
        $assignableRoles = $this->getAssignableRoles($actor);

        return view('manajemenmahasiswa::permissions.category', compact(
            'users', 'category', 'assignableRoles', 'search', 'perPage', 'roleFilter'
        ));
    }

    // ── Update Role ───────────────────────────────────────────────────────────

    public function updateRole(Request $request, User $user)
    {
        $actor = auth()->user();

        // Jangan boleh ubah role diri sendiri
        abort_if($actor->id === $user->id, 403, 'Tidak dapat mengubah role diri sendiri.');

        // Jangan boleh ubah superadmin/admin global
        abort_if($user->hasAnyRole(['superadmin', 'admin', 'dosen', 'gpm']), 403, 'Tidak dapat mengubah role pengguna ini.');

        $request->validate([
            'roles'   => ['required', 'array', 'min:1'],
            'roles.*' => ['string'],
        ]);

        $selectedRoles = $request->input('roles', []);

        // Validasi: semua role yang dipilih harus dalam daftar yang boleh di-assign actor
        $assignable     = $this->getAssignableRoles($actor)->pluck('name')->toArray();
        $allowedChoices = array_merge($assignable, ['mahasiswa']);
        foreach ($selectedRoles as $role) {
            abort_unless(in_array($role, $allowedChoices), 403, "Anda tidak memiliki izin untuk mengassign role '{$role}'.");
        }

        // Validasi divisi untuk ketua_bidang/ketua_unit
        if ($actor->hasAnyRole(['ketua_bidang', 'ketua_unit'])) {
            $actorDivisi = $this->getActorDivisi($actor);
            $userDivisi  = $this->getActorDivisi($user);
            abort_if(
                $actorDivisi && $userDivisi && $actorDivisi !== $userDivisi,
                403,
                'Anda hanya dapat mengubah anggota divisi Anda sendiri.'
            );
        }

        DB::beginTransaction();
        try {
            $oldRoleNames = $user->roles->pluck('name')->values()->toArray();

            $newRoleIds = $this->resolveMultipleRoleIds($selectedRoles);
            $user->roles()->sync($newRoleIds);

            $user->load('roles');
            $user->syncPermissionsFromRoles();
            $user->clearUserCache();

            $newRoleLabels = implode(', ', array_map([$this, 'getRoleLabel'], $selectedRoles));

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Mengubah role pengguna: {$user->name} ({$user->email})",
                subject: $user,
                oldData: ['roles' => $oldRoleNames],
                newData: ['roles' => $user->roles->pluck('name')->values()->toArray(), 'assigned_by' => $actor->name],
            );

            DB::commit();
            return back()->with('success', "Role \"{$user->name}\" berhasil diubah menjadi: {$newRoleLabels}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah role: ' . $e->getMessage());
        }
    }

    // ── Cek Alumni Otomatis ───────────────────────────────────────────────────

    public function checkAlumni(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin_kemahasiswaan', 'admin', 'superadmin']), 403);

        $cutoffYear = now()->year - 5; // angkatan <= 2021 jika sekarang 2026
        $dryRun     = $request->boolean('dry_run', false);

        $alumniRole   = Role::where('name', 'alumni')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        if (!$alumniRole || !$mahasiswaRole) {
            return response()->json(['error' => 'Role alumni atau mahasiswa tidak ditemukan.'], 422);
        }

        $students = \App\Models\Student::where('cohort_year', '<=', $cutoffYear)
            ->with('user.roles')
            ->get();

        $toPromote = $students->filter(fn($s) => $s->user && !$s->user->hasRole('alumni'));
        $count     = $toPromote->count();

        if ($dryRun) {
            return response()->json([
                'count'   => $count,
                'message' => "Ditemukan {$count} mahasiswa yang akan dijadikan alumni (angkatan ≤ {$cutoffYear}).",
                'preview' => $toPromote->take(10)->map(fn($s) => [
                    'name'        => $s->user->name,
                    'nim'         => $s->student_number,
                    'cohort_year' => $s->cohort_year,
                ]),
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($toPromote as $student) {
                $user = $student->user;

                $currentRoles = $user->roles->pluck('id')->toArray();
                // Hapus role mahasiswa dan posisi himpunan, tambah alumni
                $positionRoleIds = Role::whereIn('name', ['mahasiswa', 'pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])
                    ->pluck('id')->toArray();
                $keptRoles  = array_diff($currentRoles, $positionRoleIds);
                $newRoleIds = array_unique([...$keptRoles, $alumniRole->id]);

                $user->roles()->sync($newRoleIds);
                $user->load('roles');
                $user->syncPermissionsFromRoles();
                $user->clearUserCache();
            }

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Auto-assign alumni: {$count} mahasiswa diubah menjadi alumni (angkatan ≤ {$cutoffYear})",
                subject: null,
                oldData: [],
                newData: ['count' => $count, 'cutoff_year' => $cutoffYear],
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => "{$count} mahasiswa berhasil diubah menjadi alumni.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Kembalikan collection Role yang boleh diassign oleh actor ini.
     */
    private function getAssignableRoles(User $actor): \Illuminate\Support\Collection
    {
        $actorRoles  = $actor->roles->pluck('name')->toArray();
        $allowedNames = [];

        foreach (self::ASSIGNABLE_BY as $callerRole => $allowed) {
            if (in_array($callerRole, $actorRoles)) {
                $allowedNames = array_unique(array_merge($allowedNames, $allowed));
            }
        }

        return Role::whereIn('name', $allowedNames)->get();
    }

    /**
     * Resolve role IDs dari array pilihan multi-role.
     *
     * Aturan:
     * - Alumni → menggantikan semua (tidak lagi mahasiswa aktif).
     * - Mahasiswa Biasa (dipilih tanpa posisi) → strip semua posisi, hanya mahasiswa.
     * - Role posisi → mahasiswa SELALU dipertahankan + posisi + pengurus_himpunan.
     */
    private function resolveMultipleRoleIds(array $selectedRoles): array
    {
        if (in_array('alumni', $selectedRoles)) {
            $role = Role::where('name', 'alumni')->first();
            return $role ? [$role->id] : [];
        }

        if (in_array('mahasiswa', $selectedRoles)) {
            $role = Role::where('name', 'mahasiswa')->first();
            return $role ? [$role->id] : [];
        }

        // Role posisi: selalu tambahkan mahasiswa + pengurus_himpunan
        $positionNames = array_intersect($selectedRoles, self::HIMPUNAN_POSITION_ROLES);
        $finalNames    = array_unique([...$positionNames, 'mahasiswa', 'pengurus_himpunan']);

        return Role::whereIn('name', $finalNames)->pluck('id')->toArray();
    }

    /**
     * Ambil divisi aktif dari actor (untuk ketua_bidang/ketua_unit).
     */
    private function getActorDivisi(User $actor): ?string
    {
        if (!$actor->student) return null;

        return PengurusHimaskom::where('student_id', $actor->student->id)
            ->where('status_keaktifan', PengurusHimaskom::STATUS_AKTIF)
            ->latest()
            ->value('divisi');
    }

    private function isMahasiswaAktif(User $u): bool
    {
        $names = $u->roles->pluck('name');
        return $names->contains('mahasiswa')
            && $names->intersect(['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])->isEmpty()
            && !$names->contains('alumni');
    }

    private function isPengurus(User $u): bool
    {
        return $u->roles->pluck('name')
            ->intersect(['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])
            ->isNotEmpty();
    }

    private function getRoleLabel(string $roleName): string
    {
        return match ($roleName) {
            'ketua_himpunan'       => 'Ketua Himpunan',
            'wakil_ketua_himpunan' => 'Wakil Ketua Himpunan',
            'ketua_bidang'         => 'Ketua Bidang',
            'ketua_unit'           => 'Ketua Unit',
            'staff_himpunan'       => 'Staff Himpunan',
            'alumni'               => 'Alumni',
            'mahasiswa'            => 'Mahasiswa Biasa',
            default                => ucfirst(str_replace('_', ' ', $roleName)),
        };
    }
}
