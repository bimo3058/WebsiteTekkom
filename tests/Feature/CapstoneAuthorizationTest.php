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
            ->get("/api/capstone/dosen/documents/{$document->id}/download")
            ->assertForbidden();

        Sanctum::actingAs($assignedUser, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'])
            ->get("/api/capstone/dosen/documents/{$document->id}/download")
            ->assertOk();
    }

    /** @return array{User, Student|Lecturer} */
    private function actor(string $roleName): array
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'capstone.view', 'guard_name' => 'web'],
            ['display_name' => 'Lihat Capstone', 'module' => 'capstone']
        );
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            ['module' => 'global', 'is_academic' => true]
        );
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

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
