<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new assessment component template (bank soal).
 *
 * Rules copied verbatim from AssessmentComponentTemplateController::store;
 * this class carries shape only, no authorization or side effects.
 */
class StoreComponentTemplateRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:capstone_assessment_component_templates',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer',
        ];
    }
}
