<?php

namespace Tests\Feature;

use App\Models\Lecturer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Supervision;
use Tests\TestCase;

class CapstoneAuthorizationTest extends TestCase
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
        config()->set('services.supabase', [
            'url' => 'https://supabase.test',
            'key' => 'test-service-role',
            'bucket' => 'storage_web',
        ]);
        Http::fake([
            'https://supabase.test/storage/v1/object/*' => Http::response(
                'private-document',
                200,
                ['Content-Type' => 'application/pdf']
            ),
        ]);
    }

    public function test_student_can_download_only_documents_from_their_group(): void
    {
        [$owner, $ownerProfile] = $this->actor('mahasiswa');
        [$outsider] = $this->actor('mahasiswa');
        $period = $this->period();
        $group = Group::create(['period_id' => $period->id, 'status' => 'FORMING']);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $group->id,
            'student_id' => $ownerProfile->id,
            'is_leader' => true,
        ]);
        $document = Document::create([
            'group_id' => $group->id,
            'student_id' => $ownerProfile->id,
            'phase' => 'PDC1',
            'document_type' => 'GENERAL',
            'file_path' => 'capstone/documents/owner/pdc1.pdf',
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($outsider, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->get("/api/capstone/mahasiswa/documents/{$document->id}/download")
            ->assertForbidden();

        Sanctum::actingAs($owner, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->get("/api/capstone/mahasiswa/documents/{$document->id}/download")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_lecturer_can_download_only_documents_from_assigned_groups(): void
    {
        [$studentUser, $student] = $this->actor('mahasiswa');
        [$assignedUser, $assignedLecturer] = $this->actor('dosen');
        [$otherUser] = $this->actor('dosen');
        $period = $this->period();
        $group = Group::create([
            'period_id' => $period->id,
            'status' => 'PDC1_ACTIVE',
            'supervisor_1_id' => $assignedLecturer->id,
        ]);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $group->id,
            'student_id' => $student->id,
            'is_leader' => true,
        ]);
        $document = Document::create([
            'group_id' => $group->id,
            'student_id' => $student->id,
            'phase' => 'PDC1',
            'document_type' => 'GENERAL',
            'file_path' => 'capstone/documents/group/pdc1.pdf',
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($otherUser, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->getJson("/api/capstone/dosen/documents?group_id={$group->id}")
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->get("/api/capstone/dosen/documents/{$document->id}/download")
            ->assertForbidden();

        Sanctum::actingAs($assignedUser, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->getJson("/api/capstone/dosen/documents?group_id={$group->id}")
            ->assertOk()
            ->assertJsonPath('data.0.id', $document->id);

        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->get("/api/capstone/dosen/documents/{$document->id}/download")
            ->assertOk();
    }

    public function test_admin_capstone_and_superadmin_can_read_all_groups_and_details(): void
    {
        $admin = $this->roleUser('admin_capstone', ['capstone.groups.view']);
        $superadmin = $this->roleUser('superadmin', ['capstone.groups.view']);
        [, $firstStudent] = $this->actor('mahasiswa');
        [, $secondStudent] = $this->actor('mahasiswa');
        $period = $this->period();
        $firstGroup = Group::create(['period_id' => $period->id, 'status' => 'FORMING']);
        $secondGroup = Group::create(['period_id' => $period->id, 'status' => 'CLOSED']);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $firstGroup->id,
            'student_id' => $firstStudent->id,
            'is_leader' => true,
        ]);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $secondGroup->id,
            'student_id' => $secondStudent->id,
            'is_leader' => true,
        ]);

        foreach ([$admin, $superadmin] as $user) {
            Sanctum::actingAs($user, ['capstone:access']);

            $this->withHeader('X-Capstone-Role', 'admin')
                ->getJson('/api/capstone/admin/groups?per_page=1')
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('pagination.total', 2);

            $this->withHeader('X-Capstone-Role', 'admin')
                ->getJson("/api/capstone/admin/groups/{$secondGroup->id}")
                ->assertOk()
                ->assertJsonPath('data.id', $secondGroup->id)
                ->assertJsonPath('data.members.0.student.id', $secondStudent->id);
        }
    }

    public function test_group_read_permission_is_required_for_admin(): void
    {
        $admin = $this->roleUser('admin_capstone');

        Sanctum::actingAs($admin, ['capstone:access']);
        $this->withHeader('X-Capstone-Role', 'admin')
            ->getJson('/api/capstone/admin/groups')
            ->assertForbidden();
    }

    public function test_lecturer_reads_only_supervised_groups_and_students(): void
    {
        $lecturerUser = $this->roleUser('dosen', ['capstone.groups.view']);
        $lecturer = Lecturer::create([
            'user_id' => $lecturerUser->id,
            'employee_number' => 'NIP-SUPERVISOR',
        ]);
        [, $firstStudent] = $this->actor('mahasiswa');
        [, $secondStudent] = $this->actor('mahasiswa');
        [, $otherStudent] = $this->actor('mahasiswa');
        $period = $this->period();

        $supervisionGroup = Group::create(['period_id' => $period->id, 'status' => 'PDC1_ACTIVE']);
        $legacyGroup = Group::create([
            'period_id' => $period->id,
            'status' => 'PDC1_ACTIVE',
            'supervisor_2_id' => $lecturer->id,
        ]);
        $otherGroup = Group::create(['period_id' => $period->id, 'status' => 'PDC1_ACTIVE']);
        Supervision::create([
            'group_id' => $supervisionGroup->id,
            'supervisor_id' => $lecturer->id,
            'role' => 'SUPERVISOR_1',
            'assigned_by' => $lecturerUser->id,
        ]);

        foreach ([
            [$supervisionGroup, $firstStudent],
            [$legacyGroup, $secondStudent],
            [$otherGroup, $otherStudent],
        ] as [$group, $student]) {
            GroupMember::create([
                'period_id' => $period->id,
                'group_id' => $group->id,
                'student_id' => $student->id,
                'is_leader' => true,
            ]);
        }

        Sanctum::actingAs($lecturerUser, ['capstone:access']);
        $headers = ['X-Capstone-Role' => 'dosen'];

        $groupsResponse = $this->withHeaders($headers)
            ->getJson('/api/capstone/dosen/groups?per_page=100')
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->assertEqualsCanonicalizing(
            [$supervisionGroup->id, $legacyGroup->id],
            collect($groupsResponse->json('data'))->pluck('id')->all()
        );

        $supervisedResponse = $this->withHeaders($headers)
            ->getJson('/api/capstone/dosen/groups/supervised')
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->assertEqualsCanonicalizing(
            [$supervisionGroup->id, $legacyGroup->id],
            collect($supervisedResponse->json('data'))->pluck('id')->all()
        );

        $studentsResponse = $this->withHeaders($headers)
            ->getJson('/api/capstone/dosen/students')
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->assertEqualsCanonicalizing(
            [$firstStudent->id, $secondStudent->id],
            collect($studentsResponse->json('data'))->pluck('id')->all()
        );

        $this->withHeaders($headers)
            ->getJson("/api/capstone/dosen/groups/{$supervisionGroup->id}")
            ->assertOk();

        $this->withHeaders($headers)
            ->getJson("/api/capstone/dosen/groups/{$otherGroup->id}")
            ->assertForbidden();
    }

    /** @return array{User, Student|Lecturer} */
    private function actor(string $roleName): array
    {
        $permissions = $roleName === 'dosen' ? ['capstone.documents.review'] : [];
        $user = $this->roleUser($roleName, $permissions);

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

    private function period(): Period
    {
        return Period::create([
            'name' => 'Periode Test',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);
    }
}
