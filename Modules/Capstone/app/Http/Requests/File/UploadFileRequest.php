<?php

namespace Modules\Capstone\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a file upload.
 *
 * Rules copied verbatim from FileController::upload (array form kept);
 * this class carries shape only, no authorization or side effects.
 */
class UploadFileRequest extends FormRequest
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
            'file' => ['required', 'file', 'max:10240'],
        ];
    }
}
