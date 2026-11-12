<?php

namespace Modules\Capstone\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'period_id' => ['required', 'integer', 'exists:capstone_periods,id'],
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
            'period_id.required' => 'Period is required.',
            'period_id.integer' => 'Period must be a valid identifier.',
            'period_id.exists' => 'Selected period does not exist.',
        ];
    }
}
