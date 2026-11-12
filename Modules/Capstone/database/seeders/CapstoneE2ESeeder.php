<?php

namespace Modules\Capstone\Database\Seeders;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Title;
use Spatie\Permission\Models\Role;

class CapstoneE2ESeeder extends Seeder
{
    public const PASSWORD = 'Password123!';

    public const ADMIN_EMAIL = 'e2e.admin.capstone@undip.ac.id';

    public const DOSEN_EMAIL = 'e2e.dosen.capstone@undip.ac.id';

    public const MAHASISWA_LEADER_EMAIL = 'e2e.mhs.leader@students.undip.ac.id';

    public const MAHASISWA_MEMBER2_EMAIL = 'e2e.mhs.member2@students.undip.ac.id';

    public const MAHASISWA_MEMBER3_EMAIL = 'e2e.mhs.member3@students.undip.ac.id';

    public const MAHASISWA_SOLO_EMAIL = 'e2e.mhs.solo@students.undip.ac.id';

    public function run(): void
    {
        foreach (['admin_capstone', 'dosen', 'mahasiswa'] as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        $admin = $this->makeUser(self::ADMIN_EMAIL, 'E2E Admin Capstone', ['admin_capstone']);
        $dosen = $this->makeUser(self::DOSEN_EMAIL, 'E2E Dosen Capstone', ['dosen']);
        $leader = $this->makeUser(self::MAHASISWA_LEADER_EMAIL, 'E2E Mhs Leader', ['mahasiswa']);
        $member2 = $this->makeUser(self::MAHASISWA_MEMBER2_EMAIL, 'E2E Mhs Member2', ['mahasiswa']);
        $member3 = $this->makeUser(self::MAHASISWA_MEMBER3_EMAIL, 'E2E Mhs Member3', ['mahasiswa']);
        $solo = $this->makeUser(self::MAHASISWA_SOLO_EMAIL, 'E2E Mhs Solo', ['mahasiswa']);

        Lecturer::firstOrCreate(
            ['user_id' => $dosen->id],
            ['employee_number' => 'E2E-DOSEN-001']
        );

        $this->makeStudent($leader, 'E2E000001', 2026);
        $this->makeStudent($member2, 'E2E000002', 2026);
        $this->makeStudent($member3, 'E2E000003', 2026);
        $this->makeStudent($solo, 'E2E000004', 2026);

        $period = Period::where('name', 'Capstone 2026/2027 Ganjil')->first()
            ?? Period::where('is_finalized', false)->orderBy('id')->first()
            ?? Period::orderBy('id')->first();

        if (! $period) {
            $this->command?->warn('CapstoneE2ESeeder: no period found, skipping group/title seeding.');

            return;
        }

        $period->update([
            'is_finalized' => false,
            'min_group_size' => 2,
            'max_group_size' => 4,
        ]);

        $lecturer = Lecturer::where('user_id', $dosen->id)->first();

        $title = Title::updateOrCreate(
            ['lecturer_id' => $lecturer->id, 'title' => 'E2E Platform Monitoring Capstone'],
            [
                'period_id' => $period->id,
                'description' => 'E2E fixture title for Playwright chained lifecycle.',
                'problem_statement' => 'E2E needs deterministic title for bidding.',
                'scope' => 'Dashboard, dokumen, penilaian.',
                'specializations' => ['Software Engineering'],
                'quota' => 2,
                'status' => 'OPEN',
                'approved_by_admin' => true,
                'title_source' => 'LECTURER',
            ]
        );

        $group = Group::updateOrCreate(
            ['code' => 'CAP-E2E-01'],
            [
                'period_id' => $period->id,
                'status' => 'FORMING',
                'group_mode' => 'GROUP',
                'has_existing_group' => true,
            ]
        );

        foreach ([$leader, $member2, $member3] as $index => $user) {
            $student = Student::where('user_id', $user->id)->first();
            GroupMember::updateOrCreate(
                ['group_id' => $group->id, 'student_id' => $student->id],
                ['period_id' => $period->id, 'is_leader' => $index === 0]
            );
            PeriodRegistration::updateOrCreate(
                ['user_id' => $student->id, 'period_id' => $period->id],
                ['status' => 'active']
            );
        }

        $soloGroup = Group::updateOrCreate(
            ['code' => 'CAP-E2E-SOLO-01'],
            [
                'period_id' => $period->id,
                'status' => 'FORMING_SOLO',
                'group_mode' => 'SOLO',
                'has_existing_group' => true,
            ]
        );

        $soloStudent = Student::where('user_id', $solo->id)->first();
        GroupMember::updateOrCreate(
            ['group_id' => $soloGroup->id, 'student_id' => $soloStudent->id],
            ['period_id' => $period->id, 'is_leader' => true]
        );
        PeriodRegistration::updateOrCreate(
            ['user_id' => $soloStudent->id, 'period_id' => $period->id],
            ['status' => 'active']
        );

        $this->command?->info('CapstoneE2ESeeder done. Period='.$period->id.' Title='.$title->id.' Group='.$group->id);
        $this->command?->info('Login via /super with password: '.self::PASSWORD);
    }

    private function makeUser(string $email, string $name, array $roles): User
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'external_id' => (string) Str::uuid(),
                'name' => $name,
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
            ]
        );

        $user->forceFill(['password' => Hash::make(self::PASSWORD)])->save();
        $user->syncRoles($roles);
        $user->clearUserCache();

        return $user->fresh();
    }

    private function makeStudent(User $user, string $nim, int $cohort): Student
    {
        return Student::updateOrCreate(
            ['user_id' => $user->id],
            ['student_number' => $nim, 'cohort_year' => $cohort]
        );
    }
}
