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
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Title;
use Tests\TestCase;

class BladeFinalizationTest extends TestCase
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

    private function lecturerAccount(): Lecturer
    {
        $user = User::factory()->create(['sso_data' => null]);
        $user->roles()->attach(Role::where('name', 'dosen')->firstOrFail()->id);

        return Lecturer::create(['user_id' => $user->id, 'employee_number' => 'NIP-'.$user->id]);
    }

    private function studentAccount(): Student
    {
        $user = User::factory()->create(['sso_data' => null]);

        return Student::create(['user_id' => $user->id, 'student_number' => 'NIM-'.$user->id, 'cohort_year' => 2024]);
    }

    private function period(): Period
    {
        return Period::create([
            'name' => 'Finalization Period',
            'is_active' => true,
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(5)->toDateString(),
            'min_group_size' => 2,
            'max_group_size' => 4,
            'max_supervise_load' => 8,
        ]);
    }

    private function groupWithMembers(Period $period, int $count = 2, string $status = 'TITLE_APPROVED'): Group
    {
        $group = Group::create(['period_id' => $period->id, 'status' => $status, 'group_mode' => 'GROUP']);
        for ($i = 0; $i < $count; $i++) {
            $student = $this->studentAccount();
            GroupMember::create([
                'group_id' => $group->id,
                'student_id' => $student->id,
                'period_id' => $period->id,
                'is_leader' => $i === 0,
            ]);
        }

        return $group->fresh();
    }

    public function test_dashboard_returns_contract_envelope(): void
    {
        $this->admin();
        $period = $this->period();
        $this->groupWithMembers($period, 2, 'READY_FOR_FINALIZATION');

        $response = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=ready&per_page=20");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tab', 'ready')
            ->assertJsonPath('data.stats.total_ready', 1)
            ->assertJsonPath('data.flow.can_modify', true)
            ->assertJsonStructure(['data' => ['period', 'stats', 'flow', 'data' => ['data', 'current_page', 'last_page', 'total']]]);
    }

    public function test_set_supervisor_rejects_identical_supervisors(): void
    {
        $this->admin();
        $period = $this->period();
        $group = $this->groupWithMembers($period);
        $lecturer = $this->lecturerAccount();

        $this->postJson('/api/capstone/admin/finalization/set-supervisor', [
            'group_id' => $group->id,
            'supervisor_1_id' => $lecturer->id,
            'supervisor_2_id' => $lecturer->id,
        ])->assertStatus(422);
    }

    public function test_execute_requires_confirmation(): void
    {
        $this->admin();
        $period = $this->period();

        $this->postJson('/api/capstone/admin/finalization/execute', [
            'period_id' => $period->id,
        ])->assertStatus(422);
    }

    public function test_full_flow_assign_promote_supervise_execute_cancel(): void
    {
        $this->admin();
        $period = $this->period();
        $group = $this->groupWithMembers($period, 2, 'TITLE_APPROVED');
        $lecturer = $this->lecturerAccount();
        $title = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Judul Uji Finalisasi',
            'quota' => 2,
            'title_source' => 'LECTURER',
            'approved_by_admin' => true,
        ]);

        $this->postJson('/api/capstone/admin/finalization/assign-title', [
            'group_id' => $group->id,
            'title_id' => $title->id,
        ])->assertOk()->assertJsonPath('success', true);

        $this->postJson('/api/capstone/admin/finalization/promote-to-ready', [
            'group_id' => $group->id,
        ])->assertOk()->assertJsonPath('data.group.status', 'READY_FOR_FINALIZATION');

        // Ready groups must not accept (re)assignment of titles.
        $otherTitle = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Judul Lain',
            'quota' => 2,
            'title_source' => 'LECTURER',
            'approved_by_admin' => true,
        ]);
        $this->postJson('/api/capstone/admin/finalization/assign-title', [
            'group_id' => $group->id,
            'title_id' => $otherTitle->id,
        ])->assertStatus(400);

        $lecturer2 = $this->lecturerAccount();
        $this->postJson('/api/capstone/admin/finalization/set-supervisor', [
            'group_id' => $group->id,
            'supervisor_1_id' => $lecturer->id,
            'supervisor_2_id' => $lecturer2->id,
        ])->assertOk()->assertJsonPath('success', true);

        $this->postJson('/api/capstone/admin/finalization/execute', [
            'period_id' => $period->id,
            'confirmation' => true,
        ])->assertOk()->assertJsonPath('data.finalized_count', 1);

        $this->assertSame('KELOMPOK_FINAL', $group->fresh()->status);
        $this->assertTrue($period->fresh()->is_finalized);

        $this->postJson('/api/capstone/admin/finalization/cancel-kelompok-final', [
            'period_id' => $period->id,
            'group_id' => $group->id,
        ])->assertOk()->assertJsonPath('data.new_status', 'READY_FOR_FINALIZATION');
    }

    public function test_promote_and_force_ready_require_title_and_member_range(): void
    {
        $this->admin();
        $period = $this->period();

        // No title → promote rejected.
        $noTitle = $this->groupWithMembers($period, 2, 'TITLE_APPROVED');
        $this->postJson('/api/capstone/admin/finalization/promote-to-ready', [
            'group_id' => $noTitle->id,
        ])->assertStatus(400);
        $this->postJson('/api/capstone/admin/finalization/force-ready', [
            'group_id' => $noTitle->id,
        ])->assertStatus(400);

        // Undersized even with title → rejected.
        $lecturer = $this->lecturerAccount();
        $title = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Judul Kecil',
            'quota' => 2,
            'title_source' => 'LECTURER',
            'approved_by_admin' => true,
        ]);
        $small = $this->groupWithMembers($period, 1, 'TITLE_APPROVED');
        $small->assignTitleFromFinalization($title->id);
        $small->save();
        $this->postJson('/api/capstone/admin/finalization/promote-to-ready', [
            'group_id' => $small->id,
        ])->assertStatus(400);
        $this->postJson('/api/capstone/admin/finalization/force-ready', [
            'group_id' => $small->id,
        ])->assertStatus(400);
    }

    public function test_set_supervisor_mark_final_requires_sv1_and_sv2(): void
    {
        $this->admin();
        $period = $this->period();
        $lecturer = $this->lecturerAccount();
        $lecturer2 = $this->lecturerAccount();
        $title = Title::create([
            'lecturer_id' => $lecturer->id,
            'title' => 'Judul Mark Final',
            'quota' => 2,
            'title_source' => 'LECTURER',
            'approved_by_admin' => true,
        ]);

        $group = $this->groupWithMembers($period, 2, 'TITLE_APPROVED');
        $group->assignTitleFromFinalization($title->id);
        $group->save();
        $this->postJson('/api/capstone/admin/finalization/promote-to-ready', [
            'group_id' => $group->id,
        ])->assertOk();
        $group = $group->fresh();

        // Flag consumed by the UI "Final" button.
        $dashboard = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=ready&per_page=20");
        $row = collect($dashboard->json('data.data.data'))->firstWhere('id', $group->id);
        $this->assertTrue($row['allowed_actions']['can_mark_kelompok_final']);

        // SV1 only → rejected, stays READY.
        $this->postJson('/api/capstone/admin/finalization/set-supervisor', [
            'group_id' => $group->id,
            'supervisor_1_id' => $lecturer->id,
            'mark_final' => true,
        ])->assertStatus(400);
        $this->assertSame('READY_FOR_FINALIZATION', $group->fresh()->status);

        // SV1 + SV2 → transitions to KELOMPOK_FINAL.
        $this->postJson('/api/capstone/admin/finalization/set-supervisor', [
            'group_id' => $group->id,
            'supervisor_1_id' => $lecturer->id,
            'supervisor_2_id' => $lecturer2->id,
            'mark_final' => true,
        ])->assertOk()->assertJsonPath('data.group.status', 'KELOMPOK_FINAL');
        $this->assertNotNull($group->fresh()->finalized_at);
    }

    public function test_set_supervisor_rejected_before_ready(): void
    {
        $this->admin();
        $period = $this->period();
        $lecturer = $this->lecturerAccount();

        foreach (['FORMING', 'READY_FOR_BIDDING', 'TITLE_APPROVED'] as $status) {
            $group = $this->groupWithMembers($period, 2, $status);
            $this->postJson('/api/capstone/admin/finalization/set-supervisor', [
                'group_id' => $group->id,
                'supervisor_1_id' => $lecturer->id,
            ])->assertStatus(400);
            $this->assertNull($group->fresh()->supervisor_1_id);
        }

        $ready = $this->groupWithMembers($period, 2, 'READY_FOR_FINALIZATION');
        $dashboard = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=ready&per_page=20");
        $row = collect($dashboard->json('data.data.data'))->firstWhere('id', $ready->id);
        $this->assertTrue($row['allowed_actions']['can_set_supervisor']);

        $notReady = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=others&sub_tab=not_ready&per_page=20");
        foreach ($notReady->json('data.data.data') as $item) {
            $this->assertFalse($item['allowed_actions']['can_set_supervisor']);
        }
    }

    public function test_group_payload_suggests_title_lecturer_as_sv1(): void
    {
        $this->admin();
        $period = $this->period();
        $owner = $this->lecturerAccount();
        $proposer = $this->lecturerAccount();

        $makeReady = function (Title $title) use ($period) {
            $group = $this->groupWithMembers($period, 2, 'TITLE_APPROVED');
            $group->assignTitleFromFinalization($title->id);
            $group->save();
            $this->postJson('/api/capstone/admin/finalization/promote-to-ready', [
                'group_id' => $group->id,
            ])->assertOk();

            return $group->fresh();
        };

        $lecturerTitle = Title::create([
            'lecturer_id' => $owner->id,
            'title' => 'Judul Dosen',
            'quota' => 2,
            'title_source' => 'LECTURER',
            'approved_by_admin' => true,
        ]);
        $studentTitle = Title::create([
            'lecturer_id' => $proposer->id,
            'proposed_supervisor_id' => $proposer->id,
            'title' => 'Judul Mahasiswa',
            'quota' => 2,
            'title_source' => 'STUDENT',
            'supervisor_approval_status' => 'APPROVED',
            'proposed_by_group_id' => $makeReady($lecturerTitle)->id,
        ]);

        $g1 = $makeReady($lecturerTitle);
        $g2 = $makeReady($studentTitle);

        $dashboard = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=ready&per_page=20");
        $rows = collect($dashboard->json('data.data.data'))->keyBy('id');
        $this->assertSame($owner->id, $rows[$g1->id]['suggested_supervisor_1_id']);
        $this->assertSame($proposer->id, $rows[$g2->id]['suggested_supervisor_1_id']);
    }

    public function test_no_group_counts_only_approved_students(): void
    {
        $this->admin();
        $period = $this->period();

        $registered = $this->studentAccount();
        PeriodRegistration::create(['user_id' => $registered->id, 'period_id' => $period->id, 'status' => 'APPROVED']);

        $pending = $this->studentAccount();
        PeriodRegistration::create(['user_id' => $pending->id, 'period_id' => $period->id, 'status' => 'PENDING']);

        $this->studentAccount(); // never registered in this period

        $response = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=others&sub_tab=no_group&per_page=20");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.stats.total_no_group', 1)
            ->assertJsonPath('data.data.total', 1);
    }

    public function test_empty_period_reports_zero_and_warns(): void
    {
        $this->admin();
        $period = $this->period();

        $response = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=ready&per_page=20");

        $response->assertOk()
            ->assertJsonPath('data.stats.total_ready', 0)
            ->assertJsonPath('data.stats.total_kelompok_final', 0)
            ->assertJsonPath('data.stats.total_no_group', 0);

        $blockers = collect($response->json('data.flow.blockers'))->pluck('type');
        $this->assertContains('NO_GROUPS_IN_PERIOD', $blockers);
    }

    public function test_final_tab_excludes_post_finalization_and_post_tab_lists_them(): void
    {
        $this->admin();
        $period = $this->period();
        $this->groupWithMembers($period, 2, 'KELOMPOK_FINAL');
        $this->groupWithMembers($period, 2, 'PDC1_ACTIVE');

        $final = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=final&per_page=20");
        $final->assertOk()
            ->assertJsonPath('data.stats.total_kelompok_final', 1)
            ->assertJsonPath('data.data.total', 1);
        $this->assertSame(['KELOMPOK_FINAL'], collect($final->json('data.data.data'))->pluck('status')->all());

        $post = $this->getJson("/api/capstone/admin/finalization/dashboard?period_id={$period->id}&tab=post&per_page=20");
        $post->assertOk()
            ->assertJsonPath('data.stats.total_pdc1_active', 1)
            ->assertJsonPath('data.stats.total_post_finalization', 1)
            ->assertJsonPath('data.data.total', 1);
        $this->assertSame(['PDC1_ACTIVE'], collect($post->json('data.data.data'))->pluck('status')->all());
    }

    public function test_blade_page_renders(): void
    {
        $user = User::factory()->create(['sso_data' => null]);
        $user->roles()->attach(Role::where('name', 'admin_capstone')->firstOrFail()->id);

        $this->actingAs($user->fresh('roles'))
            ->withSession(['session_version' => 0, 'capstone.blade_role' => 'admin'])
            ->get('/capstone/admin/finalization')
            ->assertOk()
            ->assertSee('Finalisasi Kelompok', false);
    }

    public function test_manual_group_with_enough_members_becomes_ready_for_bidding(): void
    {
        $this->admin();
        $period = $this->period();
        $students = [$this->studentAccount(), $this->studentAccount()];

        $response = $this->postJson('/api/capstone/admin/finalization/create-manual-group', [
            'student_ids' => [$students[0]->id, $students[1]->id],
            'period_id' => $period->id,
            'option' => 'no_title',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertSame('READY_FOR_BIDDING', Group::findOrFail($response->json('data.group.id'))->status);
    }

    public function test_add_to_existing_group_promotes_to_ready_when_min_met(): void
    {
        $this->admin();
        $period = $this->period();
        $group = $this->groupWithMembers($period, 1, 'FORMING');
        $extra = $this->studentAccount();

        $this->postJson('/api/capstone/admin/finalization/add-to-existing-group', [
            'group_id' => $group->id,
            'student_ids' => [$extra->id],
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertSame('READY_FOR_BIDDING', $group->fresh()->status);
    }
}
