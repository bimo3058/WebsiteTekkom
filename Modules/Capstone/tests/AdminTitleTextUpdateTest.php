<?php

namespace Modules\Capstone\Tests;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Database\Seeders\CapstonePermissionsSeeder;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Title;
use Tests\TestCase;

class AdminTitleTextUpdateTest extends TestCase
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

    public function test_admin_can_edit_title_text_without_affecting_group_status(): void
    {
        $this->admin();
        $period = Period::create([
            'name' => 'Title Edit Period',
            'is_active' => true,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'min_group_size' => 2,
            'max_group_size' => 4,
            'max_supervise_load' => 8,
        ]);
        $lecturerUser = User::factory()->create(['sso_data' => null]);
        $lecturer = Lecturer::create(['user_id' => $lecturerUser->id, 'employee_number' => 'NIP-TITLE-1']);

        $title = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Old Research Title',
            'description' => 'Old description',
            'problem_statement' => 'Old problem',
            'scope' => 'Old scope',
            'specializations' => ['Software'],
            'quota' => 2,
            'status' => 'open',
            'title_source' => 'LECTURER',
        ]);

        // Two groups sharing the title, in late-lifecycle statuses.
        $statuses = ['KELOMPOK_FINAL', 'PDC1_ACTIVE'];
        $groups = [];
        foreach ($statuses as $status) {
            $group = Group::create(['period_id' => $period->id, 'status' => $status, 'group_mode' => 'GROUP']);
            $group->assignTitleFromFinalization($title->id);
            $group->save();
            $student = Student::create(['user_id' => User::factory()->create(['sso_data' => null])->id, 'student_number' => 'NIM-'.$group->id.$status, 'cohort_year' => 2024]);
            GroupMember::create(['group_id' => $group->id, 'student_id' => $student->id, 'period_id' => $period->id, 'is_leader' => true]);
            $groups[] = $group;
        }

        $response = $this->putJson("/api/capstone/admin/titles/{$title->id}", [
            'title' => 'New Research Title',
            'description' => 'New description',
        ]);

        $response->assertOk()->assertJsonPath('title', 'New Research Title');

        $this->assertSame('New Research Title', $title->fresh()->title);
        $this->assertSame('New description', $title->fresh()->description);
        // Governance columns untouched.
        $this->assertSame(2, (int) $title->fresh()->quota);
        $this->assertSame('open', $title->fresh()->status);

        foreach ($groups as $i => $group) {
            $fresh = $group->fresh();
            $this->assertSame($statuses[$i], $fresh->status, 'Group status must not change on title text edit.');
            $this->assertSame($title->id, (int) $fresh->title_id, 'Group title assignment must not change.');
        }

        $this->assertDatabaseHas('capstone_audit_logs', [
            'action' => 'TITLE_TEXT_EDITED_BY_ADMIN',
            'target_id' => $title->id,
        ]);
        $this->assertCount(1, AuditLog::where('action', 'TITLE_TEXT_EDITED_BY_ADMIN')->get());
    }

    public function test_admin_cannot_change_title_quota_or_status_via_text_edit(): void
    {
        $this->admin();
        $lecturerUser = User::factory()->create(['sso_data' => null]);
        $lecturer = Lecturer::create(['user_id' => $lecturerUser->id, 'employee_number' => 'NIP-TITLE-2']);
        $title = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Guarded Title',
            'description' => 'Desc',
            'problem_statement' => 'Problem',
            'scope' => 'Scope',
            'specializations' => ['AI'],
            'quota' => 1,
            'status' => 'open',
            'title_source' => 'LECTURER',
        ]);

        $this->putJson("/api/capstone/admin/titles/{$title->id}", ['quota' => 99])->assertStatus(422);
        $this->putJson("/api/capstone/admin/titles/{$title->id}", ['status' => 'closed'])->assertStatus(422);
        $this->putJson("/api/capstone/admin/titles/{$title->id}", [])->assertStatus(422);

        $this->assertSame(1, (int) $title->fresh()->quota);
        $this->assertSame('open', $title->fresh()->status);
    }
}
