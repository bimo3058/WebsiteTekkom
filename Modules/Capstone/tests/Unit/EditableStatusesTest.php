<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\WorkflowService;
use Tests\TestCase;

/** Pure unit test: formAccess reads EDITABLE_STATUSES only, no database needed. */
class EditableStatusesTest extends TestCase
{
    private function service(): GroupLifecycleService
    {
        return new GroupLifecycleService(new GroupStateMachine, app(WorkflowService::class));
    }

    private function groupAt(string $status): Group
    {
        $group = new Group;
        $group->status = $status;

        return $group;
    }

    public function test_nilai_dosen_editable_from_pdc2_active_through_all_later_statuses(): void
    {
        $service = $this->service();

        foreach (['PDC2_ACTIVE', 'TA_DRAFT', 'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS'] as $status) {
            $access = $service->formAccess($this->groupAt($status), 'NILAI_DOSEN');
            $this->assertTrue($access['editable'], "NILAI_DOSEN should be editable at {$status}");
            $this->assertNull($access['reason']);
        }
    }

    public function test_nilai_dosen_not_editable_before_pdc2_or_when_closed(): void
    {
        $service = $this->service();

        foreach (['PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE', 'CLOSED'] as $status) {
            $access = $service->formAccess($this->groupAt($status), 'NILAI_DOSEN');
            $this->assertFalse($access['editable'], "NILAI_DOSEN should not be editable at {$status}");
            $this->assertNotNull($access['reason']);
        }
    }

    public function test_milestone_matches_nilai_dosen_window(): void
    {
        $service = $this->service();

        $this->assertTrue($service->formAccess($this->groupAt('PDC2_ACTIVE'), 'MILESTONE')['editable']);
        $this->assertTrue($service->formAccess($this->groupAt('EXPO_REGISTERED'), 'MILESTONE')['editable']);
        $this->assertFalse($service->formAccess($this->groupAt('SEMPRO_DONE'), 'MILESTONE')['editable']);
        $this->assertFalse($service->formAccess($this->groupAt('CLOSED'), 'MILESTONE')['editable']);
    }

    public function test_expo_has_no_supervisor_editable_window(): void
    {
        $this->assertArrayNotHasKey('EXPO', GroupLifecycleService::EDITABLE_STATUSES);

        $access = $this->service()->formAccess($this->groupAt('EXPO_REGISTERED'), 'EXPO');
        $this->assertFalse($access['editable']);
    }
}
