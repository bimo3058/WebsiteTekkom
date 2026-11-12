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

class CapstoneDocumentDownloadTest extends TestCase
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

    public function test_mahasiswa_download_uses_jenis_and_version_in_filename(): void
    {
        [$owner, $ownerProfile] = $this->actor('mahasiswa');
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
            'phase' => 'EXPO',
            'document_type' => 'Absen Kehadiran Pengunjung',
            'file_path' => 'capstone/documents/owner/absen.pdf',
            'original_name' => 'scan.pdf',
            'version' => 3,
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($owner, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->get("/api/capstone/mahasiswa/documents/{$document->id}/download")
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="Absen_Kehadiran_Pengunjung_v3.pdf"');
    }

    public function test_historical_version_is_not_downloadable(): void
    {
        [$owner, $ownerProfile] = $this->actor('mahasiswa');
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
            'phase' => 'EXPO',
            'document_type' => 'Absen Kehadiran Pengunjung',
            'file_path' => 'capstone/documents/owner/absen.pdf',
            'original_name' => 'scan.pdf',
            'version' => 3,
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($owner, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->get("/api/capstone/mahasiswa/documents/{$document->id}/download?version=1")
            ->assertNotFound();
    }

    public function test_dosen_download_uses_jenis_and_version_and_rejects_old_versions(): void
    {
        [$studentUser, $student] = $this->actor('mahasiswa');
        [$assignedUser, $assignedLecturer] = $this->actor('dosen');
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
            'phase' => 'PDC2',
            'document_type' => 'C400',
            'file_path' => 'capstone/documents/group/c400.docx',
            'original_name' => 'draft.docx',
            'version' => 2,
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($assignedUser, ['capstone:access']);
        $headers = ['X-Capstone-Role' => 'dosen', 'Accept' => 'application/json'];

        $this->withHeaders($headers)
            ->get("/api/capstone/dosen/documents/{$document->id}/download")
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="C400_v2.docx"');

        $this->withHeaders($headers)
            ->get("/api/capstone/dosen/documents/{$document->id}/download?version=1")
            ->assertNotFound();
    }

    public function test_slash_in_document_type_is_sanitized(): void
    {
        [$owner, $ownerProfile] = $this->actor('mahasiswa');
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
            'phase' => 'SIDANG',
            'document_type' => 'TOEFL/IELTS',
            'file_path' => 'capstone/documents/owner/toefl.pdf',
            'original_name' => 'sertifikat.pdf',
            'version' => 1,
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($owner, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'mahasiswa', 'Accept' => 'application/json'])
            ->get("/api/capstone/mahasiswa/documents/{$document->id}/download")
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="TOEFL_IELTS_v1.pdf"');
    }

    public function test_admin_download_appends_group_code(): void
    {
        $admin = $this->roleUser('admin_capstone', ['capstone.edit']);
        [, $student] = $this->actor('mahasiswa');
        $period = $this->period();
        $group = Group::create(['period_id' => $period->id, 'code' => 'KEL07', 'status' => 'FORMING']);
        GroupMember::create([
            'period_id' => $period->id,
            'group_id' => $group->id,
            'student_id' => $student->id,
            'is_leader' => true,
        ]);
        $document = Document::create([
            'group_id' => $group->id,
            'student_id' => $student->id,
            'phase' => 'PDC2',
            'document_type' => 'C400',
            'file_path' => 'capstone/documents/group/c400.pdf',
            'original_name' => 'draft.pdf',
            'version' => 1,
            'status' => 'SUBMITTED',
        ]);

        Sanctum::actingAs($admin, ['capstone:access']);
        $this->withHeaders(['X-Capstone-Role' => 'admin', 'Accept' => 'application/json'])
            ->get("/api/capstone/admin/document-uploads/{$document->id}/download?source=phase_documents")
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="C400_v1_KEL07.pdf"');
    }

    /** @return array{User, Student|Lecturer} */
    private function actor(string $roleName): array
    {
        $permissions = $roleName === 'dosen' ? ['capstone.edit'] : [];
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
