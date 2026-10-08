<?php

namespace Modules\Capstone\Tests;

use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Database\Seeders\CapstonePermissionsSeeder;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Tests\TestCase;

class GroupMessageFlagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SystemModule::updateOrCreate(['slug' => 'capstone'], ['name' => 'Capstone', 'is_active' => true, 'is_maintenance' => false]);
        $this->seed(CapstonePermissionsSeeder::class);
    }

    private function admin(): User
    {
        $user = User::factory()->create(['sso_data' => null]);
        $user->roles()->attach(Role::where('name', 'admin_capstone')->firstOrFail()->id);
        Sanctum::actingAs($user->fresh('roles'), ['capstone:access']);
        $this->withHeader('X-Capstone-Role', 'admin');

        return $user;
    }

    private function studentAccount(): Student
    {
        $user = User::factory()->create(['sso_data' => null]);

        return Student::create(['user_id' => $user->id, 'student_number' => 'NIM-'.$user->id, 'cohort_year' => 2024]);
    }

    private function groupWithMembers(int $count = 2): Group
    {
        $period = Period::create(['name' => 'Test Period', 'is_active' => true]);
        $group = Group::create(['period_id' => $period->id, 'status' => 'FORMING', 'code' => 'TEST-01']);
        for ($i = 0; $i < $count; $i++) {
            $student = $this->studentAccount();
            PeriodRegistration::create(['user_id' => $student->id, 'period_id' => $period->id, 'status' => 'APPROVED']);
            GroupMember::create(['group_id' => $group->id, 'student_id' => $student->id, 'period_id' => $period->id, 'is_leader' => $i === 0]);
        }

        return $group->fresh();
    }

    public function test_admin_can_broadcast_message_to_selected_members_only(): void
    {
        $this->admin();
        $group = $this->groupWithMembers(3);
        $members = $group->members()->with('student')->get();
        $targets = $members->take(2)->pluck('student.user_id')->all();
        $outsider = User::factory()->create(['sso_data' => null]);

        $response = $this->postJson("/api/capstone/admin/groups/{$group->id}/message", [
            'user_ids' => [...$targets, $outsider->id],
            'message' => 'Halo kelompok',
        ]);

        $response->assertOk()->assertJsonPath('count', 2);
        $this->assertSame(2, Notification::where('type', 'ADMIN_MESSAGE')->count());
        $this->assertFalse(Notification::where('user_id', $outsider->id)->exists());
    }

    public function test_message_rejects_non_members_and_empty_body(): void
    {
        $this->admin();
        $group = $this->groupWithMembers(2);
        $outsider = User::factory()->create(['sso_data' => null]);

        $this->postJson("/api/capstone/admin/groups/{$group->id}/message", [
            'user_ids' => [$outsider->id], 'message' => 'Halo',
        ])->assertStatus(422);

        $memberUid = $group->members()->with('student')->first()->student->user_id;
        $this->postJson("/api/capstone/admin/groups/{$group->id}/message", [
            'user_ids' => [$memberUid], 'message' => '',
        ])->assertStatus(422);
    }

    public function test_admin_can_flag_and_unflag_member(): void
    {
        $this->admin();
        $group = $this->groupWithMembers(2);
        $member = $group->members()->first();
        $studentId = $member->student_id;

        $this->postJson("/api/capstone/admin/groups/{$group->id}/members/{$member->id}/flag", [
            'reason' => 'Tidak aktif',
        ])->assertOk();

        $this->assertFalse(GroupMember::where('id', $member->id)->exists());
        $this->assertTrue(GroupMember::withTrashed()->where('id', $member->id)->where('status', 'flagged')->exists());
        $this->assertFalse(PeriodRegistration::where('user_id', $studentId)->where('period_id', $group->period_id)->exists());

        $this->postJson("/api/capstone/admin/groups/{$group->id}/members/{$member->id}/flag", [
            'reason' => 'Lagi',
        ])->assertStatus(422);

        $this->postJson("/api/capstone/admin/groups/{$group->id}/members/{$member->id}/unflag")
            ->assertOk();

        $this->assertTrue(GroupMember::where('id', $member->id)->where('status', 'active')->exists());
        $this->assertTrue(PeriodRegistration::where('user_id', $studentId)->where('period_id', $group->period_id)->where('status', 'APPROVED')->exists());
    }

    public function test_group_detail_includes_workflow_progress_and_flagged_members(): void
    {
        $this->admin();
        $group = $this->groupWithMembers(2);

        $response = $this->getJson("/api/capstone/admin/groups/{$group->id}");
        $response->assertOk()
            ->assertJsonPath('data.progress_percentage', $response->json('data.progress_percentage'))
            ->assertJsonCount(5, 'data.workflow.phases')
            ->assertJsonStructure(['data' => ['workflow' => ['phases', 'current_phase'], 'flagged_members']]);
    }
}
