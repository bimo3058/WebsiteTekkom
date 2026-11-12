<?php

namespace Modules\Capstone\Http\Requests\Sempro;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a SEMPRO evaluation submission.
 *
 * Rules copied verbatim from SemproController::evaluate; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class EvaluateSemproRequest extends FormRequest
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
