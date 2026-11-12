<?php

namespace Modules\Capstone\Http\Requests\BursaIde;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a request to join a solo seeker's group.
 *
 * Rules copied verbatim from BursaIdeController::requestJoin; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class RequestJoinRequest extends FormRequest
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
            'message' => 'nullable|string|max:500',
        ];
    }
}
