<?php

namespace Modules\Capstone\Http\Requests\TaSubmission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a TA draft upload or revision (file path only).
 *
 * The upload and revise actions of TaSubmissionController carry
 * byte-identical inline rules, so one shared class serves both.
 * Copied verbatim; this class carries shape only. Callers discard
 * the validated payload (validation side effect only).
 */
class TaSubmissionFileRequest extends FormRequest
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
            'file_path' => 'required|string',
        ];
    }
}
