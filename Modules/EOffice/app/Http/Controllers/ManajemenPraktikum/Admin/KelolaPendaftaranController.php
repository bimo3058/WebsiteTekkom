<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Praktikum;

class KelolaPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $praktikums    = Praktikum::where('status', 'aktif')->orderBy('nama')->get();
        $praktikumId   = $request->query('praktikum_id', $praktikums->first()?->id);
        $selectedPraktikum = $praktikums->firstWhere('id', $praktikumId);

        $moduls = $praktikumId
            ? Modul::where('praktikum_id', $praktikumId)
                ->with(['modulAsprak.asprak.user'])
                ->orderBy('urutan')
                ->get()
            : collect();

        $aspraks = $praktikumId
            ? AsprakPraktikum::where('praktikum_id', $praktikumId)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->with('user')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.admin.bagi-asprak', compact(
            'praktikums', 'selectedPraktikum', 'moduls', 'aspraks', 'praktikumId'
        ));
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

        return back()->with('success', 'Asprak berhasil ditugaskan ke modul.');
    }
}
