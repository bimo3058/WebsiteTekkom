<?php

namespace Modules\Capstone\Http\Requests\IndividualTa;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an individual TA document upload.
 *
 * Rules copied verbatim from IndividualTaController::upload (compact
 * single-line form expanded, same content); this class carries shape
 * only, no authorization or side effects.
 */
class UploadTaDocumentRequest extends FormRequest
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
            'document_type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ];
    }
}
