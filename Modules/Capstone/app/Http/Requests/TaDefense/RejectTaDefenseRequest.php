<?php

namespace Modules\Capstone\Http\Requests\TaDefense;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate rejecting a student-submitted TA defense schedule request.
 *
 * Rules copied verbatim from TaDefenseController::reject; this class
 * carries shape only, no authorization or side effects.
 */
class RejectTaDefenseRequest extends FormRequest
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
            'rejection_reason' => 'required|string|max:1000',
        ];
    }
}
