<?php

namespace Modules\Capstone\Support;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

final class CapstoneActor
{
    public static function roles(User $user): array
    {
        $roles = [];

        if ($user->hasRole('superadmin') || $user->hasRole('admin_capstone')) {
            $roles[] = 'admin';
        }

        if ($user->hasRole('dosen')) {
            $roles[] = 'dosen';
        }

        if ($user->hasRole('mahasiswa')) {
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
        $student = $user->student;

        if (! $student) {
            throw new AuthorizationException('Profil mahasiswa untuk akun SSO ini tidak ditemukan.');
        }

        return $student;
    }

    public static function lecturer(User $user): Lecturer
    {
        $lecturer = $user->lecturer;

        if (! $lecturer) {
            throw new AuthorizationException('Profil dosen untuk akun SSO ini tidak ditemukan.');
        }

        return $lecturer;
    }

    public static function payload(User $user, ?string $activeRole = null): array
    {
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
