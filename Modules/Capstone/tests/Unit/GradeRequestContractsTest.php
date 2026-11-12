<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Assessment\ScoreSummaryRequest;
use Modules\Capstone\Http\Requests\Grade\UpdateWeightsRequest;
use Tests\TestCase;

/** Pure unit test: grade request contracts mirror the former inline rules. No database needed. */
class GradeRequestContractsTest extends TestCase
{
    public function test_update_weights_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'pdc1_weights' => ['nullable', 'array'],
                'pdc1_weights.SEMPRO' => ['nullable', 'numeric', 'min:0'],
                'pdc1_weights.BIMBINGAN_SEMPRO' => ['nullable', 'numeric', 'min:0'],
                'pdc2_weights' => ['nullable', 'array'],
                'pdc2_weights.NILAI_DOSEN' => ['nullable', 'numeric', 'min:0'],
                'pdc2_weights.MILESTONE' => ['nullable', 'numeric', 'min:0'],
                'pdc2_weights.EXPO' => ['nullable', 'numeric', 'min:0'],
                'pdc2_weights.PEER_REVIEW' => ['nullable', 'numeric', 'min:0'],
                'ta_weights' => ['nullable', 'array'],
                'ta_weights.BIMBINGAN_TA' => ['nullable', 'numeric', 'min:0'],
                'ta_weights.SIDANG_TA' => ['nullable', 'numeric', 'min:0'],
            ],
            (new UpdateWeightsRequest)->rules()
        );
    }

    public function test_score_summary_rules_keep_expo_read_only(): void
    {
        $rules = (new ScoreSummaryRequest)->rules();

        $this->assertSame(['required', 'exists:capstone_periods,id'], $rules['period_id']);
        $this->assertSame('required', $rules['type'][0]);
        $this->assertSame('string', $rules['type'][1]);
        // EXPO stays accepted: the summary serves old rows read-only (ADR 0012).
        $this->assertStringContainsString('EXPO', (string) $rules['type'][2]);
        $this->assertStringContainsString('BIMBINGAN', (string) $rules['type'][2]);
    }
}
