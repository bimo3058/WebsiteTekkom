<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Assessment\CopyAssessmentConfigRequest;
use Modules\Capstone\Http\Requests\Assessment\ShowAssessmentConfigRequest;
use Modules\Capstone\Http\Requests\Assessment\StoreAssessmentConfigRequest;
use Modules\Capstone\Http\Requests\Assessment\StoreComponentTemplateRequest;
use Modules\Capstone\Http\Requests\Assessment\StoreIndicatorTemplateRequest;
use Modules\Capstone\Http\Requests\Assessment\UpdateComponentTemplateRequest;
use Modules\Capstone\Http\Requests\Assessment\UpdateIndicatorTemplateRequest;
use Modules\Capstone\Http\Requests\Document\StoreDocumentTypeRequest;
use Modules\Capstone\Http\Requests\Document\UpdateDocumentTypeRequest;
use Tests\TestCase;

/** Pure unit test: config/template request contracts mirror the former inline rules. No database needed. */
class ConfigRequestContractsTest extends TestCase
{
    public function test_store_component_template_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'code' => 'required|string|max:50|unique:capstone_assessment_component_templates',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'weight' => 'required|numeric|min:0|max:100',
                'is_active' => 'sometimes|boolean',
                'sort_order' => 'nullable|integer',
            ],
            (new StoreComponentTemplateRequest)->rules()
        );
    }

    public function test_update_component_template_rules_match_inline_contract(): void
    {
        $rules = (new UpdateComponentTemplateRequest)->rules();

        $this->assertSame('sometimes|string|max:255', $rules['name']);
        $this->assertSame('sometimes|numeric|min:0|max:100', $rules['weight']);
        // Unique-ignore id resolves from the {assessment_template} route parameter.
        $this->assertStringStartsWith('sometimes|string|max:50|unique:capstone_assessment_component_templates,code,', $rules['code']);
    }

    public function test_store_indicator_template_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'code' => 'required|string|max:50|unique:capstone_peer_review_indicator_templates',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'weight' => 'required|numeric|min:0|max:100',
                'sort_order' => 'nullable|integer',
            ],
            (new StoreIndicatorTemplateRequest)->rules()
        );
    }

    public function test_update_indicator_template_rules_match_inline_contract(): void
    {
        $rules = (new UpdateIndicatorTemplateRequest)->rules();

        $this->assertSame('sometimes|string|max:255', $rules['name']);
        $this->assertStringStartsWith('sometimes|string|max:50|unique:capstone_peer_review_indicator_templates,code,', $rules['code']);
        $this->assertSame('sometimes|boolean', $rules['is_active']);
    }

    public function test_store_document_type_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'phase' => 'nullable|string|in:PDC1,SEMPRO,PDC2,TA,EXPO,SIDANG',
                'is_active' => 'boolean',
            ],
            (new StoreDocumentTypeRequest)->rules()
        );
    }

    public function test_update_document_type_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'phase' => 'nullable|string|in:PDC1,SEMPRO,PDC2,TA,EXPO,SIDANG',
                'is_active' => 'boolean',
            ],
            (new UpdateDocumentTypeRequest)->rules()
        );
    }

    public function test_show_assessment_config_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN_SEMPRO,BIMBINGAN_TA,NILAI_DOSEN,MILESTONE'],
            (new ShowAssessmentConfigRequest)->rules()
        );
    }

    public function test_store_assessment_config_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN_SEMPRO,BIMBINGAN_TA,NILAI_DOSEN,MILESTONE',
                'template_ids' => 'present|array',
                'template_ids.*' => 'integer|distinct|exists:capstone_assessment_component_templates,id',
            ],
            (new StoreAssessmentConfigRequest)->rules()
        );
    }

    public function test_copy_assessment_config_rules_match_inline_contract(): void
    {
        $rules = (new CopyAssessmentConfigRequest)->rules();

        // First three segments are verbatim; not_in resolves the {period}
        // route parameter (same value the controller's $periodId held).
        $this->assertSame('required', $rules['source_period_id'][0]);
        $this->assertSame('integer', $rules['source_period_id'][1]);
        $this->assertSame('exists:capstone_periods,id', $rules['source_period_id'][2]);
        $this->assertStringStartsWith('not_in:', $rules['source_period_id'][3]);
    }
}
