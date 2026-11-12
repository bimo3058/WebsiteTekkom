<?php

namespace Modules\Capstone\Http\Requests\TaDefense;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an admin-created TA defense schedule.
 *
 * Rules copied verbatim from TaDefenseController::schedule; this class
 * carries shape only, no authorization or side effects.
 */
class ScheduleTaDefenseRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ];
    }
}
