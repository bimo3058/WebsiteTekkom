<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Title;
use Tests\TestCase;

class TitleCrossPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SystemModule::create([
            'slug' => 'capstone',
            'name' => 'Capstone + TA',
            'is_active' => true,
            'is_maintenance' => false,
        ]);
    }

    public function test_dosen_store_forces_period_id_null(): void
    {
        [$dosenUser] = $this->actor('dosen');

        Sanctum::actingAs($dosenUser, ['capstone:access']);
        $response = $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->postJson('/api/capstone/dosen/titles', [
                'title' => 'Cross Period Title',
                'description' => 'Description',
                'problem_statement' => 'Problem',
                'scope' => 'Scope',
                'specializations' => ['Software'],
                'quota' => 2,
            ])
            ->assertCreated();

        $this->assertNull(Title::find($response->json('id'))->period_id);
    }

    public function test_dosen_update_rejects_period_id_and_nulls_legacy_value(): void
    {
        [$dosenUser, $lecturer] = $this->actor('dosen');
        $period = $this->period('Periode 1');
        $title = $this->lecturerTitle($lecturer->id, $period->id);

        Sanctum::actingAs($dosenUser, ['capstone:access']);

        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->putJson("/api/capstone/dosen/titles/{$title->id}", ['period_id' => $period->id])
            ->assertUnprocessable();

        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->putJson("/api/capstone/dosen/titles/{$title->id}", ['quota' => 3])
            ->assertOk();

        $this->assertNull($title->fresh()->period_id);
    }

    public function test_mahasiswa_sees_dosen_title_from_another_period(): void
    {
        [, $lecturer] = $this->actor('dosen');
        [$studentUser, $student] = $this->actor('mahasiswa');
        $firstPeriod = $this->period('Periode 1');
        $secondPeriod = $this->period('Periode 2');

        // Legacy row created while titles were period-bound.
        $legacyTitle = $this->lecturerTitle($lecturer->id, $firstPeriod->id);
        $globalTitle = $this->lecturerTitle($lecturer->id, null);

        PeriodRegistration::create([
            'user_id' => $student->id,
            'period_id' => $secondPeriod->id,
            'status' => PeriodRegistration::STATUS_APPROVED,
        ]);

        Sanctum::actingAs($studentUser, ['capstone:access']);
        $ids = $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->getJson("/api/capstone/mahasiswa/titles?period_id={$secondPeriod->id}")
            ->assertOk()
            ->json();

        $this->assertContains($legacyTitle->id, collect($ids)->pluck('id')->all());
        $this->assertContains($globalTitle->id, collect($ids)->pluck('id')->all());
    }

    public function test_student_can_bid_dosen_title_from_another_period(): void
    {
        [, $lecturer] = $this->actor('dosen');
        [$studentUser, $student] = $this->actor('mahasiswa');
        $firstPeriod = $this->period('Periode 1');
        $secondPeriod = $this->period('Periode 2');

        $legacyTitle = $this->lecturerTitle($lecturer->id, $firstPeriod->id);
        $group = $this->biddableGroup($secondPeriod, $student);

        Sanctum::actingAs($studentUser, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->postJson('/api/capstone/mahasiswa/bids', ['title_id' => $legacyTitle->id])
            ->assertCreated();

        $this->assertDatabaseHas('capstone_bids', [
            'title_id' => $legacyTitle->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_student_title_from_another_period_stays_locked_for_admin_assign(): void
    {
        [, $lecturer] = $this->actor('dosen');
        $admin = $this->roleUser('admin_capstone');
        $firstPeriod = $this->period('Periode 1');
        $secondPeriod = $this->period('Periode 2');

        $studentTitle = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Student Title '.uniqid(),
            'description' => 'Description',
            'problem_statement' => 'Problem',
            'scope' => 'Scope',
            'specializations' => ['Software'],
            'quota' => 2,
            'status' => 'open',
            'title_source' => 'STUDENT',
            'supervisor_approval_status' => 'APPROVED',
            'period_id' => $firstPeriod->id,
        ]);
        $group = Group::create(['period_id' => $secondPeriod->id, 'status' => 'FORMING']);

        Sanctum::actingAs($admin, ['capstone:access']);
        $this->withHeader('X-Capstone-Role', 'admin')
            ->postJson('/api/capstone/admin/finalization/assign-title', [
                'group_id' => $group->id,
                'title_id' => $studentTitle->id,
            ])
            ->assertStatus(400)
            ->assertSee('another period');
    }

    public function test_admin_available_titles_includes_dosen_title_from_another_period(): void
    {
        [, $lecturer] = $this->actor('dosen');
        $admin = $this->roleUser('admin_capstone');
        $firstPeriod = $this->period('Periode 1');
        $secondPeriod = $this->period('Periode 2');

        $legacyTitle = $this->lecturerTitle($lecturer->id, $firstPeriod->id);

        Sanctum::actingAs($admin, ['capstone:access']);
        $titles = $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson("/api/capstone/admin/finalization/available-titles?period_id={$secondPeriod->id}")
            ->assertOk()
            ->json('data.titles');

        $this->assertContains($legacyTitle->id, collect($titles)->pluck('id')->all());
    }

    private function lecturerTitle(int $lecturerId, ?int $periodId, array $overrides = []): Title
    {
        return Title::create(array_merge([
            'lecturer_id' => $lecturerId,
            'title' => 'Judul Teste '.$lecturerId.' '.uniqid(),
            'description' => 'Description',
            'problem_statement' => 'Problem',
            'scope' => 'Scope',
            'specializations' => ['Software'],
            'quota' => 2,
            'status' => 'open',
            'title_source' => 'LECTURER',
            'period_id' => $periodId,
        ], $overrides));
    }

    private function biddableGroup(Period $period, Student $student): Group
    {
        $group = Group::create(['period_id' => $period->id, 'status' => 'READY_FOR_BIDDING']);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $group->id,
            'student_id' => $student->id,
            'is_leader' => true,
        ]);

        return $group;
    }

    /** @return array{User, Student|Lecturer} */
    private function actor(string $roleName): array
    {
        $user = $this->roleUser($roleName);

        $profile = $roleName === 'mahasiswa'
            ? Student::create([
                'user_id' => $user->id,
                'student_number' => 'NIM-'.$user->id,
                'cohort_year' => 2024,
            ])
            : Lecturer::create([
                'user_id' => $user->id,
                'employee_number' => 'NIP-'.$user->id,
            ]);

        return [$user->fresh(['roles']), $profile];
    }

    private function roleUser(string $roleName, array $permissionNames = []): User
    {
        $permissionNames = array_values(array_unique(['capstone.view', ...$permissionNames]));
        $permissions = collect($permissionNames)->map(fn (string $name) => Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            ['display_name' => $name, 'module' => 'capstone']
        ));
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            [
                'module' => $roleName === 'admin_capstone' ? 'capstone' : 'global',
                'is_academic' => in_array($roleName, ['dosen', 'mahasiswa'], true),
            ]
        );
        $role->givePermissionTo($permissions);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user->fresh(['roles']);
    }

    private function period(string $name): Period
    {
        return Period::create([
            'name' => $name,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
            'min_group_size' => 1,
        ]);
    }
}
