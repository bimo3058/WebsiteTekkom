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
            'praktikum' => $praktikum,
            'moduls' => $moduls,
            'aspraks' => $aspraks,
            'modulList' => $moduls,
            'asistenList' => $aspraks,
            'distribusiList' => $distribusiList,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'  => 'required|exists:modul_praktikum,id',
            'asprak_id' => 'required|exists:asprak_praktikum,id',
        ]);

        $user = auth()->user();
        $modul = Modul::with('praktikum')->findOrFail($request->modul_id);
        $asprak = AsistenPraktikum::findOrFail($request->asprak_id);

        if ($modul->praktikum?->koor_id !== $user->id || $asprak->praktikum_id !== $modul->praktikum_id) {
            abort(403, 'Anda tidak berhak membagikan modul ini.');
        }

        ModulAsprak::firstOrCreate([
            'modul_id'  => $request->modul_id,
            'asprak_id' => $request->asprak_id,
        ]);

        return back()->with('success', 'Modul berhasil dibagikan ke asprak.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $penugasan = ModulAsprak::with('modul.praktikum')->findOrFail($id);

        if ($penugasan->modul?->praktikum?->koor_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus penugasan ini.');
        }

        $penugasan->delete();

        return back()->with('success', 'Penugasan dihapus.');
    }
}
