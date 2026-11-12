<?php

namespace Modules\Capstone\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the period scope for grade-consistency listing/generation.
 *
 * The index and generate actions of GradeConsistencyController carry
 * byte-identical inline rules, so one shared class serves both. Copied
 * verbatim; this class carries shape only. Callers discard the validated
 * payload (validation side effect only) and read $request->period_id
 * directly.
 */
class GradeConsistencyQueryRequest extends FormRequest
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
            'period_id' => 'required|exists:capstone_periods,id',
        ];
    }
}
