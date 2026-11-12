<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Controllers\DocumentController;
use Modules\Capstone\Support\WorkflowPhases;
use Tests\TestCase;

/**
 * Guards the UI/engine unlock-map overlap.
 *
 * The dashboard workflow (`DocumentController::UNLOCK_RULES`) and the
 * approval engine (`WorkflowPhases::UNLOCK_RULES`) serve different purposes
 * and stay separate maps, but their shared phases must agree — otherwise a
 * document approved in one view reads as locked in the other. `SIDANG` is
 * stepper-only by design and allow-listed, not absorbed.
 */
class WorkflowUnlockAgreementTest extends TestCase
{
    public function test_shared_phases_agree_on_prerequisites(): void
    {
        $uiRules = DocumentController::UNLOCK_RULES;

        foreach (WorkflowPhases::UNLOCK_RULES as $enginePhase => $enginePrereq) {
            $uiKey = array_search($enginePhase, array_map(
                fn ($uiPhase) => WorkflowPhases::normalize($uiPhase),
                array_keys($uiRules)
            ), true);

            $this->assertNotFalse($uiKey, "Engine phase {$enginePhase} has no UI counterpart.");
            $uiPhase = array_keys($uiRules)[$uiKey];

            $this->assertSame(
                $enginePrereq ? WorkflowPhases::normalize($enginePrereq) : null,
                $uiRules[$uiPhase] ? WorkflowPhases::normalize($uiRules[$uiPhase]) : null,
                "Prerequisite mismatch for phase {$enginePhase} (UI key {$uiPhase})."
            );
        }
    }

    public function test_sidang_is_stepper_only_by_design(): void
    {
        $this->assertArrayHasKey('SIDANG', DocumentController::UNLOCK_RULES);
        $this->assertFalse(WorkflowPhases::isValid('SIDANG'));
        $this->assertSame('EXPO', DocumentController::UNLOCK_RULES['SIDANG']);
    }

    public function test_status_gates_cover_engine_phases_only(): void
    {
        foreach (WorkflowPhases::STATUS_GATES as $phase => $gate) {
            $this->assertTrue(WorkflowPhases::isValid($phase), "Gate phase {$phase} is not an engine phase.");
        }
    }
}
