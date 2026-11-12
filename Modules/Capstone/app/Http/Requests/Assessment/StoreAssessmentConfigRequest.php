<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an assessment configuration save for a period + type.
 *
 * Rules copied verbatim from PeriodAssessmentConfigController::store;
 * this class carries shape only, no authorization or side effects.
 * The validated payload is discarded by the caller (validation side
 * effect only); downstream reads use $request->type / template_ids directly.
 */
class StoreAssessmentConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN_SEMPRO,BIMBINGAN_TA,NILAI_DOSEN,MILESTONE',
            'template_ids' => 'present|array',
            'template_ids.*' => 'integer|distinct|exists:capstone_assessment_component_templates,id',
        ];
    }
}
