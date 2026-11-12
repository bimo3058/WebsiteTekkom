<?php

namespace Modules\Capstone\Http\Requests\TaDefense;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a TA defense evaluation submission.
 *
 * Rules copied verbatim from TaDefenseController::evaluate; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class EvaluateTaDefenseRequest extends FormRequest
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
            'rubric_json' => 'required|array',
            'score' => 'required|numeric|min:0|max:100',
            'result' => 'required|in:PASS,FAIL',
        ];
    }
}
