<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Controllers\DocumentController;
use Tests\TestCase;

/**
 * Guards the mahasiswa dashboard stepper against phase-key drift.
 *
 * The stepper looks up each step in `workflow.phases` by exact phase code,
 * so its key set must stay 1:1 with DocumentController::PHASES. A phantom
 * key (e.g. TA_DRAFT) renders permanently grey, and a mislabeled key
 * (e.g. TA shown as "Sidang TA") marks the wrong stage complete.
 */
class StepperPhaseContractTest extends TestCase
{
    public function test_dashboard_stepper_keys_match_workflow_phases(): void
    {
        $blade = file_get_contents(base_path('Modules/Capstone/resources/views/pages/mahasiswa/dashboard/index.blade.php'));
        $this->assertNotFalse($blade);

        $this->assertSame(1, preg_match('/@foreach\(\[(.*?)\] as \$phase=>\$label\)/s', $blade, $m));

        preg_match_all("/'([A-Z0-9_]+)'\s*=>\s*'([^']+)'/", $m[1], $pairs, PREG_SET_ORDER);
        $keys = array_column($pairs, 1);
        $labels = array_combine(array_column($pairs, 1), array_column($pairs, 2));

        $this->assertSame(DocumentController::PHASES, $keys);
        $this->assertSame('TA Draft', $labels['TA']);
        $this->assertSame('Sidang TA', $labels['SIDANG']);
        $this->assertCount(count($keys), array_unique(array_values($labels)), 'Stepper labels must be distinct.');
    }
}
