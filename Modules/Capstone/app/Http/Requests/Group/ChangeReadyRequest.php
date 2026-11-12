<?php

namespace Modules\Capstone\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class ChangeReadyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:capstone_groups,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'group_id.required' => 'Group is required.',
            'group_id.integer' => 'Group must be a valid identifier.',
            'group_id.exists' => 'Selected group does not exist.',
        ];
    }
}
