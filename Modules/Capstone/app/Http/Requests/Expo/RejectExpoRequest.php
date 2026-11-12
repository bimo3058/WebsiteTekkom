<?php

namespace Modules\Capstone\Http\Requests\Expo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate rejecting a student-submitted EXPO schedule request.
 *
 * Rules copied verbatim from ExpoController::reject; this class
 * carries shape only, no authorization or side effects.
 */
class RejectExpoRequest extends FormRequest
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
