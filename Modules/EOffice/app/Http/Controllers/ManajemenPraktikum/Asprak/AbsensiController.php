<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $asprak = AsistenPraktikum::where('user_id', $user->id)->whereNull('deleted_at')->first();
        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.absensi', compact('moduls'));
    }

    public function show($modulId)
    {
        $modul      = Modul::with('praktikum')->findOrFail($modulId);
        $praktikans = DaftarPraktikan::where('praktikum_id', $modul->praktikum_id)->with('user')->get();
        $absensi    = Absensi::where('modul_id', $modulId)->get()->keyBy('daftar_praktikan_id');

        return view('eoffice::manajemen-praktikum.asprak.absensi-show', compact('modul', 'praktikans', 'absensi'));
    }

    public function store(Request $request, $modulId)
    {
        $request->validate([
            'absensi'           => 'required|array',
            'absensi.*.id'      => 'required|exists:daftar_praktikan,id',
            'absensi.*.status'  => 'required|in:hadir,izin,tidak_hadir',
            'tanggal'           => 'required|date',
        ]);

        foreach ($request->absensi as $item) {
            Absensi::updateOrCreate(
                ['modul_id' => $modulId, 'daftar_praktikan_id' => $item['id']],
                ['tanggal'  => $request->tanggal, 'status' => $item['status']]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }
}
