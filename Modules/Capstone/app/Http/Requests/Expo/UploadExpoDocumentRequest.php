<?php

namespace Modules\Capstone\Http\Requests\Expo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an expo student document upload.
 *
 * Rules copied verbatim from ExpoStudentController::document; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class UploadExpoDocumentRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ];
    }
}
