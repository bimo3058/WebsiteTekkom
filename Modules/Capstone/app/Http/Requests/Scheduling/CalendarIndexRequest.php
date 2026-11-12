<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the optional calendar-feed period filter.
 *
 * Rules copied verbatim from CalendarController::index; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->filled/integer
 * directly.
 */
class CalendarIndexRequest extends FormRequest
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
        return ['period_id' => 'nullable|integer|min:1'];
    }
}
