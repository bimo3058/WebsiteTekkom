<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class AllocateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'bid_id' => 'required|exists:capstone_bids,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
        ];
    }
}
