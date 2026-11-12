<?php

namespace Modules\Capstone\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the admin user-list query.
 *
 * Rules copied verbatim from BladeUserController::index; this class
 * carries shape only, no authorization or side effects.
 */
class IndexUsersRequest extends FormRequest
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
            'role' => 'nullable|in:admin,dosen,mahasiswa',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,suspended',
            'sort_by' => 'nullable|in:name,email,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
