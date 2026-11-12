<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new online location (admin only).
 *
 * Rules copied verbatim from LocationController::store; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->name etc. directly.
 */
class StoreLocationRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:capstone_locations,name',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|string|in:online',
            'description' => 'nullable|string|max:1000',
            'eoffice_ruangan_id' => 'prohibited',
        ];
    }
}
