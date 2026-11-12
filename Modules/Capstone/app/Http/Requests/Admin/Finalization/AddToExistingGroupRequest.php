<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class AddToExistingGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:capstone_groups,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
        ];
    }
}
