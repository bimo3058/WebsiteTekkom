<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Services\ManajemenPraktikum\ModuleAssessment;

class KomponenModulController extends Controller
{
    public function update(Request $request, ModuleAssessment $assessment)
    {
        $praktikum=DashboardController::resolvePraktikum() ?? abort(404);
        $data=$request->validate([
            'version'=>'required|integer|min:0',
            'tambahan'=>'nullable|array:1,2|max:2',
            'tambahan.*'=>'required|array:nama,bobot',
            'tambahan.*.nama'=>'nullable|string|max:60',
            'tambahan.*.bobot'=>'nullable|integer|min:1|max:100',
            'bobot_tp'=>'required|integer|min:0|max:100',
            'bobot_praktikum'=>'required|integer|min:0|max:100',
            'bobot_laporan'=>'required|integer|min:0|max:100',
            'bobot_responsi'=>'required|integer|min:0|max:100',
        ]);
        $assessment->save($praktikum,$data);
        return back()->with('success','Komponen dan bobot penilaian berhasil disimpan.');
    }
}
