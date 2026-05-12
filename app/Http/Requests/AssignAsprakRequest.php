<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class AssignAsprakRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && $user->suspended_at !== null) {
                        $fail('The selected user is suspended and cannot be assigned.');
                    }
                },
            ],
            'role' => [
                'required',
                Rule::in(['asprak', 'koor']),
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}
