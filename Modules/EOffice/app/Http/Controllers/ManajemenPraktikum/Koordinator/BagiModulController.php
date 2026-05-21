<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Praktikum;

class BagiModulController extends Controller
{
    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();

        $moduls = $praktikum
            ? Modul::where('praktikum_id', $praktikum->id)
                ->with('modulAsprak.asprak.user')
                ->orderBy('urutan')
                ->get()
            : collect();

        $aspraks = $praktikum
            ? AsistenPraktikum::where('praktikum_id', $praktikum->id)
                ->where('role', 'asprak')
                ->with(['user', 'modulAsprak.modul'])
                ->get()
            : collect();

        $distribusiList = $praktikum
            ? ModulAsprak::whereHas('modul', fn ($q) => $q->where('praktikum_id', $praktikum->id))
                ->with(['modul', 'asprak.user'])
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.bagi-modul', [
            'praktikum'     => $praktikum,
            'modulList'     => $moduls,
            'asistenList'   => $aspraks,
            'distribusiList'=> $distribusiList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asprak_id' => 'required|exists:eo_asprak_praktikum,id',
            'modul_id'  => 'required|exists:eo_modul,id',
        ]);

        ModulAsprak::firstOrCreate([
            'asprak_id' => $request->asprak_id,
            'modul_id'  => $request->modul_id,
        ]);

        return back()->with('success', 'Modul berhasil di-assign ke asisten.');
    }

    public function destroy($id)
    {
        ModulAsprak::findOrFail($id)->delete();
        return back()->with('success', 'Distribusi dihapus.');
    }
}