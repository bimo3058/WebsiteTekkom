<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment;

class FinalAssessmentController extends Controller
{
    public function save(Request $request,Praktikum $praktikum)
    {
        $payload=$request->validate(['final_payload'=>'required|json']);
        $data=json_decode($payload['final_payload'],true);
        abort_unless(is_array($data),422);
        app(FinalAssessment::class)->save($praktikum,$data,$request->route('finalRole'));
        return back()->with('success','Nilai keseluruhan berhasil disimpan.');
    }
}
