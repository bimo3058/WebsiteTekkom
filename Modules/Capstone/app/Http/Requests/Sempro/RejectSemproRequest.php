<?php

namespace Modules\Capstone\Http\Requests\Sempro;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate rejecting a student-submitted SEMPRO schedule request.
 *
 * Rules copied verbatim from SemproController::reject; this class
 * carries shape only, no authorization or side effects.
 */
class RejectSemproRequest extends FormRequest
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
