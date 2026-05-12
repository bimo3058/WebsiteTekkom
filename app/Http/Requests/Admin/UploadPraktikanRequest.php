<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadPraktikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles role check
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:2048', // CSV usually detected as text/plain or csv
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File CSV wajib diunggah.',
            'file.file'     => 'Data yang diunggah harus berupa file.',
            'file.mimes'    => 'Format file harus .csv atau .txt.',
            'file.max'      => 'Ukuran file maksimal adalah 2MB.',
        ];
    }
}
