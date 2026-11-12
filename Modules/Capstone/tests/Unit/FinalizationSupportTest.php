<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Finalization\FinalizationSupport;
use Modules\Capstone\Models\Group;
use Tests\TestCase;

/** Pure unit test: finalization action matrix, no database needed. */
class FinalizationSupportTest extends TestCase
{
    private function support(): FinalizationSupport
    {
        return app(FinalizationSupport::class);
    }

    private function groupAt(string $status): Group
    {
        $group = new Group;
        $group->status = $status;

        return $group;
    }

    public function test_ready_group_can_be_marked_kelompok_final(): void
    {
        $actions = $this->support()->resolveFinalizationActions($this->groupAt('READY_FOR_FINALIZATION'));

        $this->assertTrue($actions['can_mark_kelompok_final']);
        $this->assertFalse($actions['can_cancel_kelompok_final']);
        $this->assertNull($actions['reason']);
    }

    public function test_final_group_can_be_cancelled(): void
    {
        $actions = $this->support()->resolveFinalizationActions($this->groupAt('KELOMPOK_FINAL'));

        $this->assertTrue($actions['can_cancel_kelompok_final']);
        $this->assertFalse($actions['can_mark_kelompok_final']);
    }

    public function test_approved_title_group_can_be_assigned(): void
    {
        foreach (['TITLE_APPROVED', 'READY_FOR_BIDDING', 'FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL'] as $status) {
            $actions = $this->support()->resolveFinalizationActions($this->groupAt($status));

            $this->assertTrue($actions['can_assign_title'], "Failed for status {$status}");
        }
    }

    public function test_post_finalization_group_reports_reason(): void
    {
        $actions = $this->support()->resolveFinalizationActions($this->groupAt('PDC1_ACTIVE'));

        $this->assertFalse($actions['can_assign_title']);
        $this->assertSame('POST_FINALIZATION', $actions['reason']);
    }

    public function test_ghost_status_is_not_assignable(): void
    {
        $actions = $this->support()->resolveFinalizationActions($this->groupAt('TITLE_PROPOSED'));

        $this->assertFalse($actions['can_assign_title']);
    }

    public function test_ta_end_states_count_as_post_finalization(): void
    {
        foreach (['READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'DISSOLVED'] as $status) {
            $actions = $this->support()->resolveFinalizationActions($this->groupAt($status));

            $this->assertFalse($actions['can_assign_title'], "Failed for status {$status}");
            $this->assertSame('POST_FINALIZATION', $actions['reason'], "Failed for status {$status}");
        }
    }
}
