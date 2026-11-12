<?php

namespace Modules\Capstone\Http\Requests\Sempro;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an admin-created SEMPRO schedule.
 *
 * Rules copied verbatim from SemproController::schedule; this class
 * carries shape only, no authorization or side effects.
 */
class ScheduleSemproRequest extends FormRequest
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
            'group_id' => 'required|exists:capstone_groups,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'eoffice_ruangan_id' => 'required|integer|exists:eo_mr_ruangans,id',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ];
    }
}
