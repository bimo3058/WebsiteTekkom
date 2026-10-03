<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Capstone\Support\CapstoneActor;

/** Account management uses users.id; academic selectors keep their existing IDs. */
class BladeUserController extends Controller
{
    private const ROLE_NAMES = ['admin_capstone', 'dosen', 'mahasiswa'];

    public function index(Request $request)
    {
        $data = $request->validate([
            'role' => 'nullable|in:admin,dosen,mahasiswa', 'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,suspended', 'sort_by' => 'nullable|in:name,email,created_at',
            'sort_order' => 'nullable|in:asc,desc', 'per_page' => 'nullable|integer|min:1|max:100',
        ]);
        $query = User::query()->with(['roles', 'student' => fn ($q) => $q->without('user'), 'lecturer' => fn ($q) => $q->without('user')]);
        if (! empty($data['role'])) {
            $names = $data['role'] === 'admin' ? ['admin_capstone', 'superadmin'] : [$data['role']];
            $query->whereHas('roles', fn ($q) => $q->whereIn('name', $names));
        }
        if (! empty($data['search'])) {
            $term = '%'.mb_strtolower($data['search']).'%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(email) LIKE ?', [$term])
                ->orWhereHas('student', fn ($s) => $s->where('student_number', 'like', $term))
                ->orWhereHas('lecturer', fn ($l) => $l->where('employee_number', 'like', $term)));
        }
        if (($data['status'] ?? null) === 'active') {
            $query->whereNull('suspended_at');
        }
        if (($data['status'] ?? null) === 'suspended') {
            $query->whereNotNull('suspended_at');
        }

        return $query->orderBy($data['sort_by'] ?? 'name', $data['sort_order'] ?? 'asc')->orderBy('id')
            ->paginate($data['per_page'] ?? 10)->through(fn ($user) => $this->payload($user, $request));
    }

    public function show(Request $request, User $user)
    {
        return response()->json($this->payload($user, $request));
    }

    private function canManage(User $user): bool
    {
        return ! $user->loadMissing('roles')->roles->contains(fn ($role) => $role->name === 'superadmin' || (str_starts_with($role->name, 'admin_') && $role->name !== 'admin_capstone'));
    }

    private function payload(User $user, Request $request): array
    {
        CapstoneActor::loadProfiles($user);
        $roles = CapstoneActor::roles($user);

        return ['id' => $user->id, 'user_id' => $user->id, 'name' => $user->name, 'email' => $user->email,
            'role' => $roles[0] ?? null, 'roles' => $roles, 'other_roles' => $user->roles->pluck('name')->reject(fn ($r) => in_array($r, [...self::ROLE_NAMES, 'superadmin']))->values(),
            'nim' => $user->student?->student_number, 'nip' => $user->lecturer?->employee_number, 'cohort_year' => $user->student?->cohort_year,
            'lecturer_id' => $user->lecturer?->id, 'student_id' => $user->student?->id, 'created_at' => $user->created_at, 'last_login' => $user->last_login,
            'last_seen_at' => $user->last_seen_at, 'whatsapp' => $user->whatsapp,
            'status' => $user->suspended_at ? 'suspended' : 'active', 'is_sso' => ! empty($user->sso_data),
            'can_edit' => $this->canManage($user), 'can_delete' => $this->canManage($user) && $request->user()->id !== $user->id];
    }
}
