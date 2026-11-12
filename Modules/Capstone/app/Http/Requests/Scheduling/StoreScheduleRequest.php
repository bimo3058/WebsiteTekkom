<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Capstone\Support\CapstoneActor;

/**
 * Validate a new schedule.
 *
 * Copied from ScheduleController::store with one mechanical adaptation:
 * the inline `type` rule embedded $allowedTypes, which the controller
 * derives from the caller's role at runtime. The same derivation runs
 * here verbatim (same calls, same ternary) so the accepted set is
 * identical per role; no authorization logic moved, only rule shape.
 */
class StoreScheduleRequest extends FormRequest
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
        $role = CapstoneActor::role(
            Auth::user(),
            $this->attributes->get('capstone_role') ?? $this->header('X-Capstone-Role')
        );
        $allowedTypes = $role === 'dosen'
            ? ['BIMBINGAN']
            : ['SEMPRO', 'SIDANG', 'EXPO'];

        return [
            'group_id' => 'required|exists:capstone_groups,id',
            'type' => ['required', 'string', 'in:'.implode(',', $allowedTypes)],
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i,H:i:s',
            'end_time' => 'required|date_format:H:i,H:i:s|after:start_time',
            'room' => 'required|string',
            'mode' => 'nullable|string|in:online,offline',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
