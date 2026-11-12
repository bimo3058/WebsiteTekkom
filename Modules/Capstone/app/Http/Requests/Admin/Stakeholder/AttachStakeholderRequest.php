<?php

namespace Modules\Capstone\Http\Requests\Admin\Stakeholder;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate attaching a stakeholder to a student-proposed title.
 *
 * Rules copied verbatim from StakeholderController::attachToTitle;
 * this class carries shape only, no authorization or side effects.
 */
class AttachStakeholderRequest extends FormRequest
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
            'stakeholder_id' => 'required|exists:stakeholders,id',
            'role' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
