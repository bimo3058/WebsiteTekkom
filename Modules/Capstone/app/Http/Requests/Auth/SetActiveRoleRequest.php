<?php

namespace Modules\Capstone\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an active-role switch.
 *
 * Rules copied verbatim from AuthBridgeController::setActiveRole
 * (array form kept); this class carries shape only, no authorization
 * or side effects.
 */
class SetActiveRoleRequest extends FormRequest
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
            'role' => ['required', 'string', 'in:admin,dosen,mahasiswa'],
        ];
    }
}
