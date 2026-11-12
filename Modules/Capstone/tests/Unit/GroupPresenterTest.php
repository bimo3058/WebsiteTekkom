<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Groups\GroupPresenter;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\WorkflowService;
use Tests\TestCase;

/** Pure unit test: status labels, no database needed. */
class GroupPresenterTest extends TestCase
{
    private function presenter(): GroupPresenter
    {
        return new GroupPresenter(new WorkflowService(new GroupStateMachine));
    }

    private function groupAt(string $status, ?int $titleId = null): Group
    {
        $group = new Group;
        $group->status = $status;
        $group->title_id = $titleId;

        return $group;
    }

    public function test_ready_for_bidding_label_depends_on_title_assignment(): void
    {
        $this->assertSame('Ready for Bidding', $this->presenter()->resolveStatusLabel($this->groupAt('READY_FOR_BIDDING')));
        $this->assertSame('Ready for Finalization', $this->presenter()->resolveStatusLabel($this->groupAt('READY_FOR_BIDDING', 7)));
    }

    public function test_other_statuses_use_the_admin_map(): void
    {
        $this->assertSame('Incomplete Group', $this->presenter()->resolveStatusLabel($this->groupAt('FORMING')));
        $this->assertSame('Solo Seeker', $this->presenter()->resolveStatusLabel($this->groupAt('FORMING_SOLO')));
        $this->assertSame('Kelompok Final', $this->presenter()->resolveStatusLabel($this->groupAt('KELOMPOK_FINAL')));
        $this->assertSame('SEMPRO DONE', $this->presenter()->resolveStatusLabel($this->groupAt('SEMPRO_DONE')));
    }
}
