<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a student seminar-schedule request (SEMPRO, EXPO, TA defense).
 *
 * The three call sites (ScheduleRequestController::requestSeminar shared
 * by requestSempro/requestExpo, plus requestTaDefense) carry byte-identical
 * inline rules, so one shared class serves all three. Copied verbatim;
 * this class carries shape only. Callers discard the validated payload
 * (validation side effect only) and read $request->date etc. directly.
 */
class SeminarScheduleRequest extends FormRequest
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
            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ];
    }
}
