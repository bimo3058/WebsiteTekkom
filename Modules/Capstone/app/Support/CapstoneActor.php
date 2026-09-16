<?php

namespace Modules\Capstone\Support;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final class CapstoneActor
{
    /** Load academic identities without fetching the owning user again. */
    public static function loadProfiles(User $user, array $profiles = ['student', 'lecturer']): User
    {
        foreach ($profiles as $profile) {
            $user->loadMissing([$profile => fn ($query) => $query->without('user')]);
        }

        return $user;
    }

    public static function roles(User $user): array
    {
        $roles = [];
        $roleNames = $user->loadMissing('roles')->roles->pluck('name');

        // The legacy Capstone database can contain a module role and a global
        // role with the same name. Spatie's hasRole(string) resolves only one
        // matching role ID, so inspect every role attached to the user here.
        if ($roleNames->contains('superadmin') || $roleNames->contains('admin_capstone')) {
            $roles[] = 'admin';
        }

        if ($roleNames->contains('dosen')) {
            $roles[] = 'dosen';
        }

        if ($roleNames->contains('mahasiswa')) {
            $roles[] = 'mahasiswa';
        }

        return $roles;
    }

    public static function role(User $user, ?string $requestedRole = null): ?string
    {
        $roles = self::roles($user);

        if ($requestedRole !== null && in_array($requestedRole, $roles, true)) {
            return $requestedRole;
        }

        return $roles[0] ?? null;
    }

    public static function student(User $user): Student
    {
        $student = self::loadProfiles($user, ['student'])->student;

        if (! $student) {
            throw new AuthorizationException('Profil mahasiswa untuk akun SSO ini tidak ditemukan.');
        }

        return $student;
    }

    public static function lecturer(User $user): Lecturer
    {
        $lecturer = self::loadProfiles($user, ['lecturer'])->lecturer;

        if (! $lecturer) {
            if (! in_array('dosen', self::roles($user), true)) {
                throw new AuthorizationException('Akun ini tidak memiliki role dosen.');
            }

            // Manually assigned academic roles may predate the lecturer row.
            // Reuse the SSO identifier convention; never claim another user's profile.
            $lecturer = DB::transaction(function () use ($user) {
                $account = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
                $existing = Lecturer::without('user')->where('user_id', $account->id)->first();
                if ($existing) return $existing;

                $raw = $account->sso_data ?? [];
                $number = trim((string) ($raw['onPremisesSamAccountName'] ?? $raw['employeeId'] ?? explode('@', $account->email)[0]));
                if ($number === '' || strlen($number) > 100 || Lecturer::where('employee_number', $number)->exists()) {
                    throw new AuthorizationException('Profil dosen perlu dilengkapi dengan NIP unik melalui User Management.');
                }

                return Lecturer::create(['user_id' => $account->id, 'employee_number' => $number]);
            });
            $user->setRelation('lecturer', $lecturer);
        }

        return $lecturer;
    }

    public static function payload(User $user, ?string $activeRole = null): array
    {
        self::loadProfiles($user);
        $roles = self::roles($user);
        $activeRole = in_array($activeRole, $roles, true)
            ? $activeRole
            : ($roles[0] ?? null);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $activeRole,
            'roles' => $roles,
            'active_role' => $activeRole,
            'lecturer_id' => $user->lecturer?->id,
            'student_id' => $user->student?->id,
            'nim' => $user->student?->student_number,
            'nip' => $user->lecturer?->employee_number,
        ];
    }
}
