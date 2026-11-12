<?php

namespace Modules\Capstone\Http\Requests\Admin\Stakeholder;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a stakeholder update.
 *
 * Rules copied verbatim from StakeholderController::update; this class
 * carries shape only, no authorization or side effects.
 */
class UpdateStakeholderRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
