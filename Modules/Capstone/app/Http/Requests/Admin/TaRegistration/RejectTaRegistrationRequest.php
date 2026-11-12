<?php

namespace Modules\Capstone\Http\Requests\Admin\TaRegistration;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a sidang TA registration rejection.
 *
 * Rules copied verbatim from TaRegistrationApprovalController::reject;
 * this class carries shape only, no authorization or side effects.
 */
class RejectTaRegistrationRequest extends FormRequest
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
