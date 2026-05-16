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
        $user = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->first();

        $moduls  = $praktikum ? Modul::where('praktikum_id', $praktikum->id)->with('modulAsprak.asprak.user')->get() : collect();
        $aspraks = $praktikum
            ? AsistenPraktikum::where('praktikum_id', $praktikum->id)->where('role', 'asprak')->with('user')->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.bagi-modul', compact('praktikum', 'moduls', 'aspraks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'  => 'required|exists:modul_praktikum,id',
            'asprak_id' => 'required|exists:asprak_praktikum,id',
        ]);

        ModulAsprak::firstOrCreate([
            'modul_id'  => $request->modul_id,
            'asprak_id' => $request->asprak_id,
        ]);

        return back()->with('success', 'Modul berhasil dibagikan ke asprak.');
    }

    public function destroy($id)
    {
        ModulAsprak::findOrFail($id)->delete();

        return back()->with('success', 'Penugasan dihapus.');
    }
}
