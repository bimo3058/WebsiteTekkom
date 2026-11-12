<?php

namespace Modules\Capstone\Http\Requests\Admin\PeriodRegistration;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a period join-request rejection.
 *
 * Rules copied verbatim from PeriodRegistrationApprovalController::reject;
 * this class carries shape only, no authorization or side effects.
 */
class RejectPeriodRegistrationRequest extends FormRequest
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
            'rejection_reason' => 'required|string|max:1000',
        ];
    }
}
