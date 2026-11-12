<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Support\WorkflowPhases;
use Tests\TestCase;

/** Pure unit test: status vocabulary partitions, no database needed. */
class GroupStatusesTest extends TestCase
{
    public function test_formation_states_are_not_sticky(): void
    {
        foreach (['FORMING', 'FORMING_SOLO', 'READY_FOR_BIDDING', 'WAITING_SUPERVISOR_APPROVAL'] as $status) {
            $this->assertFalse(GroupStateMachine::isLifecycleSticky($status), "Failed for status {$status}");
        }
    }

    public function test_lifecycle_states_are_sticky(): void
    {
        foreach (['TITLE_APPROVED', 'KELOMPOK_FINAL', 'PDC2_ACTIVE', 'TA_DRAFT', 'EXPO_DONE', 'PDC2_COMPLETED', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'CLOSED', 'DISSOLVED'] as $status) {
            $this->assertTrue(GroupStateMachine::isLifecycleSticky($status), "Failed for status {$status}");
        }
    }

    public function test_ta_draft_orders_between_pdc2_active_and_expo_ready(): void
    {
        $machine = new GroupStateMachine;

        $this->assertGreaterThan($machine->statusOrder('PDC2_ACTIVE'), $machine->statusOrder('TA_DRAFT'));
        $this->assertGreaterThan($machine->statusOrder('TA_DRAFT'), $machine->statusOrder('PDC2_READY_FOR_EXPO'));
        $this->assertGreaterThan($machine->statusOrder('PDC2_COMPLETED'), $machine->statusOrder('READY_FOR_TA_INDIVIDUAL'));
    }

    public function test_is_solo_reads_flag_and_status(): void
    {
        $flagged = new Group;
        $flagged->setAttribute('is_solo', true);
        $flagged->status = 'FORMING';
        $this->assertTrue(GroupStateMachine::isSolo($flagged));

        $byStatus = new Group;
        $byStatus->status = 'FORMING_SOLO';
        $this->assertTrue(GroupStateMachine::isSolo($byStatus));

        $regular = new Group;
        $regular->status = 'FORMING';
        $this->assertFalse(GroupStateMachine::isSolo($regular));
    }

    public function test_workflow_phases_normalize_legacy_ta_alias(): void
    {
        $this->assertSame('TA_DRAFT', WorkflowPhases::normalize('TA'));
        $this->assertSame('TA_DRAFT', WorkflowPhases::normalize('TA_DRAFT'));
        $this->assertSame('PDC2', WorkflowPhases::unlockPrerequisite('TA'));
        $this->assertSame('EXPO', WorkflowPhases::nextPhase('TA_DRAFT'));
        $this->assertSame('TA_DRAFT', WorkflowPhases::nextPhase('PDC2'));
        $this->assertNull(WorkflowPhases::nextPhase('EXPO'));
    }
}
