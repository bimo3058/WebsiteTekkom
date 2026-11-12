<?php

namespace Modules\Capstone\Http\Requests\Admin\Monitoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an admin force-delete of a group.
 *
 * Rules copied verbatim from BladeMonitoringController::destroy; this
 * class carries shape only, no authorization or side effects.
 */
class DestroyGroupRequest extends FormRequest
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
        return ['reason' => 'required|string|min:10|max:1000'];
    }
}
