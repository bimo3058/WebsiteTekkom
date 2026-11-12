<?php

namespace Modules\Capstone\Http\Requests\Evaluation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validate a new lecturer evaluation.
 *
 * Rules copied verbatim from EvaluationController::store; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->group_id etc. directly.
 */
class StoreEvaluationRequest extends FormRequest
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
            'group_id' => 'required|exists:capstone_groups,id',
            'student_id' => 'required|exists:students,id',
            'type' => ['required', Rule::in(['bimbingan', 'proposal', 'skripsi'])],
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ];
    }
}
