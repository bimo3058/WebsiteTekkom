<?php

namespace Modules\BankSoal\Services\Komprehensif;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LegacyUserProvisioner
{
    /**
     * Cari atau buat user + student untuk NIM lama.
     *
     * @return array{user: User, student: Student, created_user: bool, created_student: bool}
     */
    public function resolve(string $nim, string $name): array
    {
        $student = Student::with('user')->where('student_number', $nim)->first();

        if ($student?->user) {
            return [
                'user' => $student->user,
                'student' => $student,
                'created_user' => false,
                'created_student' => false,
            ];
        }

        $email = $nim.'@legacy.kompre.local';
        $externalId = 'LEGACY-KOMPRE-'.$nim;

        $user = User::withTrashed()
            ->where('external_id', $externalId)
            ->orWhere('email', $email)
            ->first();

        $createdUser = false;

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
        } else {
            $user = User::create([
                'external_id' => $externalId,
                'name' => $name !== '' ? $name : 'Mahasiswa Legacy '.$nim,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_online' => false,
            ]);
            $createdUser = true;
        }

        $role = Role::firstOrCreate([
            'name' => 'mahasiswa',
            'guard_name' => 'web',
        ]);

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        $createdStudent = false;

        if ($student) {
            $student->update(['user_id' => $user->id]);
        } else {
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $nim,
                'cohort_year' => $this->cohortYear($nim),
            ]);
            $createdStudent = true;
        }

        return [
            'user' => $user,
            'student' => $student,
            'created_user' => $createdUser,
            'created_student' => $createdStudent,
        ];
    }

    private function cohortYear(string $nim): int
    {
        if (preg_match('/^\d{6}(\d{2})/', $nim, $matches)) {
            $year = 2000 + (int) $matches[1];

            if ($year >= 2000 && $year <= (int) date('Y') + 1) {
                return $year;
            }
        }

        return (int) date('Y');
    }
}
