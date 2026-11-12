<?php

namespace Modules\Capstone\Http\Requests\Expo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate expo self-evaluation score submission.
 *
 * Rules copied verbatim from ExpoStudentController::evaluate; this
 * class carries shape only, no authorization or side effects.
 */
class SubmitExpoScoresRequest extends FormRequest
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
            'scores' => 'required|array|min:1',
            'scores.*.period_component_id' => 'required|integer|distinct',
            'scores.*.score' => 'required|numeric|min:1|max:100',
            'scores.*.notes' => 'nullable|string|max:5000',
        ];
    }
}
