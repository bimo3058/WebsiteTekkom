<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the EOffice room-availability query.
 *
 * Rules copied verbatim from LocationController::available; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->date etc. directly.
 */
class AvailableLocationsRequest extends FormRequest
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
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_schedule_id' => 'nullable|integer',
            'exclude_seminar_id' => 'nullable|integer',
            'exclude_ta_defense_id' => 'nullable|integer',
            'exclude_expo_id' => 'nullable|integer',
        ];
    }
}
