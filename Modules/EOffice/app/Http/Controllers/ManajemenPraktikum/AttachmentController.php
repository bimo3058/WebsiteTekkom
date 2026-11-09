<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments;

class AttachmentController
{
    public function __invoke(Request $request, SupabaseStorage $storage)
    {
        abort_unless($request->hasValidSignature() && auth()->check() && (string)$request->query('viewer')===(string)auth()->id(),403);
        $data=$request->validate(['path'=>'required|string|max:2000','name'=>'required|string|max:255','download'=>'sometimes|boolean']);
        abort_if(str_contains($data['path'],'..') || str_starts_with($data['path'],'/') || str_contains($data['path'],'://'),400);
        $file=$storage->download($data['path'],'eoffice');
        abort_unless($file,404);
        $downloadUrl=URL::temporarySignedRoute('eoffice.manprak.attachment',now()->addHours(2),[
            'path'=>$data['path'],'name'=>$data['name'],'viewer'=>(string)auth()->id(),'download'=>1,
        ]);
        return PracticumAttachments::response($file['content'],$data['name'],$request->boolean('download'),$downloadUrl);
    }
}
