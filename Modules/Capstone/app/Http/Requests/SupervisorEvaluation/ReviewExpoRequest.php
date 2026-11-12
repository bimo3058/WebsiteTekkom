<?php

namespace Modules\Capstone\Http\Requests\SupervisorEvaluation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewExpoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * One decision per group: APPROVED locks the submission so the EXPO
     * score counts towards PDC2; REJECTED reopens it for re-score.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['APPROVED', 'REJECTED'])],
            'feedback' => ['required_if:status,REJECTED', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Review status is required.',
            'status.in' => 'Review status must be APPROVED or REJECTED.',
            'feedback.required_if' => 'Feedback is required when rejecting.',
        ];
    }
}
