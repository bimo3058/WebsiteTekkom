<?php

namespace Modules\Capstone\Http\Requests\TaSubmission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a dosen review of a TA submission.
 *
 * Rules copied verbatim from TaSubmissionController::review; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class ReviewTaSubmissionRequest extends FormRequest
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
            'result' => 'required|in:APPROVE,REVISE',
            'feedback' => 'nullable|string',
        ];
    }
}
