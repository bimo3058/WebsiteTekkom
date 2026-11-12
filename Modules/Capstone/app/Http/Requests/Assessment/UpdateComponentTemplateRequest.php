<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an assessment component template update.
 *
 * Rules copied verbatim from AssessmentComponentTemplateController::update;
 * this class carries shape only, no authorization or side effects.
 */
class UpdateComponentTemplateRequest extends FormRequest
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
            'code' => 'sometimes|string|max:50|unique:capstone_assessment_component_templates,code,'.$this->route('assessment_template'),
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'sometimes|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
