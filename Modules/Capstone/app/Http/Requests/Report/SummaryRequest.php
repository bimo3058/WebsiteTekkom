<?php

namespace Modules\Capstone\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the report-summary period scope.
 *
 * Rules copied verbatim from ReportSummaryController::summary; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->period_id directly.
 */
class SummaryRequest extends FormRequest
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
