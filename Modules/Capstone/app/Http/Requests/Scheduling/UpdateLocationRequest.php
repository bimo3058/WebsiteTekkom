<?php

namespace Modules\Capstone\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an online location update (admin only).
 *
 * Rules copied verbatim from LocationController::update; this class
 * carries shape only. The caller reads $request->all() (not just the
 * validated subset), so downstream behavior is unchanged.
 * The unique-ignore id resolves from the {location} route parameter.
 */
class UpdateLocationRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:capstone_locations,name,'.$this->route('location'),
            'capacity' => 'nullable|integer|min:1',
            'type' => 'sometimes|string|in:online',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'eoffice_ruangan_id' => 'prohibited',
        ];
    }
}
