<?php

namespace Modules\Capstone\Http\Requests\Admin\Monitoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate flagging a member out of a period.
 *
 * Rules copied verbatim from BladeMonitoringController::flagMember;
 * this class carries shape only, no authorization or side effects.
 */
class FlagMemberRequest extends FormRequest
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
        return ['reason' => 'required|string|max:1000'];
    }
}
