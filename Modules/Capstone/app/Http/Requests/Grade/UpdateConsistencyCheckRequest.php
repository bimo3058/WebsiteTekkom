<?php

namespace Modules\Capstone\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a grade-consistency check update.
 *
 * Rules copied verbatim from GradeConsistencyController::update; this
 * class carries shape only, no authorization or side effects.
 */
class UpdateConsistencyCheckRequest extends FormRequest
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
            'status' => 'sometimes|string|in:UNCHECKED,CONSISTENT,INCONSISTENT',
            'notes' => 'nullable|string',
        ];
    }
}
