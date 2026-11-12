<?php

namespace Modules\Capstone\Http\Requests\TaDefense;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate approving a student-submitted TA defense schedule request.
 *
 * Rules copied verbatim from TaDefenseController::approve; this class
 * carries shape only, no authorization or side effects.
 */
class ApproveTaDefenseRequest extends FormRequest
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
            'room' => 'nullable|string',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ];
    }
}
