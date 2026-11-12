<?php

namespace Modules\Capstone\Http\Requests\Admin\Monitoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an admin message to group members.
 *
 * Rules copied verbatim from BladeMonitoringController::message; this
 * class carries shape only, no authorization or side effects.
 */
class MessageGroupRequest extends FormRequest
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
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'message' => 'required|string|max:1000',
        ];
    }
}
