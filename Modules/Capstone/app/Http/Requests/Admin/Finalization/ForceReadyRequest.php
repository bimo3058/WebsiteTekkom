<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class ForceReadyRequest extends FormRequest
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
            'reason' => 'nullable|string|max:1000',
        ];
    }
}
