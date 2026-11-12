<?php

namespace Modules\Capstone\Http\Requests\SupervisorEvaluation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the admin evaluation-summary schedule-source filter.
 *
 * Rules copied verbatim from SupervisorEvaluationAdminController::adminScheduleSummary;
 * this class carries shape only. The caller discards the validated
 * payload (validation side effect only) and reads $request->input()
 * directly.
 */
class AdminScheduleSummaryRequest extends FormRequest
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
            'schedule_source' => 'nullable|in:schedule,seminar,ta',
        ];
    }
}
