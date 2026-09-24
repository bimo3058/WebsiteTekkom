<?php

namespace Tests\Unit;

use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\WorkflowService;
use PHPUnit\Framework\TestCase;

class GroupLifecycleServiceTest extends TestCase
{
    private function service(): GroupLifecycleService
    {
        $stateMachine = new GroupStateMachine;

        return new GroupLifecycleService($stateMachine, new WorkflowService($stateMachine));
    }

    private function groupIn(string $status): Group
    {
        return new Group(['status' => $status]);
    }

    public function test_form_access_matrix(): void
    {
        $service = $this->service();

        $cases = [
            // [type, status, expected editable]
            ['BIMBINGAN_SEMPRO', 'PDC1_ACTIVE', true],
            ['BIMBINGAN_SEMPRO', 'READY_FOR_SEMPRO', true],
            ['BIMBINGAN_SEMPRO', 'SEMPRO_DONE', true],
            ['BIMBINGAN_SEMPRO', 'PDC2_ACTIVE', false],
            ['NILAI_DOSEN', 'PDC2_ACTIVE', true],
            ['NILAI_DOSEN', 'TA_DRAFT', true],
            ['NILAI_DOSEN', 'PDC2_READY_FOR_EXPO', false],
            ['NILAI_DOSEN', 'READY_FOR_SEMPRO', false],
            ['MILESTONE', 'PDC2_ACTIVE', true],
            ['MILESTONE', 'TA_DRAFT', true],
            ['MILESTONE', 'EXPO_DONE', false],
            ['EXPO', 'EXPO_REGISTERED', true],
            ['EXPO', 'EXPO_DONE', true],
            ['EXPO', 'PDC2_READY_FOR_EXPO', false],
            ['BIMBINGAN_TA', 'READY_FOR_TA_INDIVIDUAL', true],
            ['BIMBINGAN_TA', 'TA_IN_PROGRESS', true],
            ['BIMBINGAN_TA', 'PDC2_COMPLETED', true],
            ['BIMBINGAN_TA', 'TA_DRAFT', false],
        ];

        foreach ($cases as [$type, $status, $expected]) {
            $access = $service->formAccess($this->groupIn($status), $type);

            $this->assertSame($expected, $access['editable'], "{$type} in {$status}");
            $this->assertSame($status, $access['group_status']);

            if ($expected) {
                $this->assertNull($access['reason']);
            } else {
                $this->assertNotEmpty($access['reason']);
            }
        }
    }

    public function test_ta_draft_readiness_requires_pdc2_active(): void
    {
        $result = $this->service()->isTaDraftReady($this->groupIn('READY_FOR_SEMPRO'));

        $this->assertFalse($result['ready']);
        $this->assertNotEmpty($result['pending']);
    }

    public function test_state_machine_supports_ta_draft_step(): void
    {
        $stateMachine = new GroupStateMachine;

        $this->assertTrue($stateMachine->canTransition('PDC2_ACTIVE', 'TA_DRAFT'));
        $this->assertTrue($stateMachine->canTransition('TA_DRAFT', 'PDC2_READY_FOR_EXPO'));
        // Fallback edge is kept for groups that complete docs before grading.
        $this->assertTrue($stateMachine->canTransition('PDC2_ACTIVE', 'PDC2_READY_FOR_EXPO'));

        $this->assertGreaterThan(
            $stateMachine->statusOrder('PDC2_ACTIVE'),
            $stateMachine->statusOrder('TA_DRAFT')
        );
        $this->assertGreaterThan(
            $stateMachine->statusOrder('TA_DRAFT'),
            $stateMachine->statusOrder('PDC2_READY_FOR_EXPO')
        );
    }

    public function test_controller_has_no_dead_transition_calls(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 2).'/Modules/Capstone/app/Http/Controllers/SupervisorEvaluationController.php'
        );

        foreach (['tryTransitionToExpoDone', 'checkAndCompleteSempro', 'tryTransitionToTaDraft'] as $dead) {
            $this->assertStringNotContainsString($dead, $source);
        }

        $this->assertStringContainsString('GroupLifecycleService', $source);
    }
}
