<?php

namespace Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignKoorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Pakai user_id (konsisten dengan App\Models\User global, bukan pengguna_id)
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
