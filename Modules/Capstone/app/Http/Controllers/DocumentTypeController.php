<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Capstone\Http\Requests\Document\StoreDocumentTypeRequest;
use Modules\Capstone\Http\Requests\Document\UpdateDocumentTypeRequest;
use Modules\Capstone\Models\DocumentType;

class DocumentTypeController extends Controller
{
    public function index()
    {
        return response()->json(DocumentType::orderBy('name')->get());
    }

    public function store(StoreDocumentTypeRequest $request)
    {
        $data = $request->validated();

        $type = DocumentType::create($data);

        return response()->json($type, 201);
    }

    public function show($id)
    {
        return response()->json(DocumentType::findOrFail($id));
    }

    public function update(UpdateDocumentTypeRequest $request, $id)
    {
        $type = DocumentType::findOrFail($id);

        $data = $request->validated();

        $type->update($data);

        return response()->json($type);
    }

    public function destroy($id)
    {
        $type = DocumentType::findOrFail($id);
        $type->delete();

        return response()->json(['message' => 'Document type deleted']);
    }
}
