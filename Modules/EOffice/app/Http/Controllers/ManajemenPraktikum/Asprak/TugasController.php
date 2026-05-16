<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class TugasController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        $tugas = Tugas::whereIn('modul_id', $modulIds)->with('modul.praktikum')->orderByDesc('created_at')->get();

        return view('eoffice::manajemen-praktikum.asprak.tugas', compact('tugas'));
    }

    public function create()
    {
        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)->whereNull('deleted_at')->first();

        $moduls = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->with('modul.praktikum')->get()->pluck('modul')
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.tugas-create', compact('moduls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'     => 'required|exists:modul_praktikum,id',
            'judul'        => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'deadline'     => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        Tugas::create($request->only(['modul_id', 'judul', 'deskripsi', 'deadline', 'is_published']));

        return redirect()->route('eoffice.manprak.asprak.tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function pengumpulan($id)
    {
        $tugas       = Tugas::with('modul.praktikum')->findOrFail($id);
        $pengumpulan = PengumpulanTugas::where('tugas_id', $id)->with('daftarPraktikan.user')->get();

        return view('eoffice::manajemen-praktikum.asprak.tugas-pengumpulan', compact('tugas', 'pengumpulan'));
    }

    public function beriNilai(Request $request, $id)
    {
        $request->validate(['nilai' => 'required|numeric|min:0|max:100']);

        PengumpulanTugas::findOrFail($id)->update(['nilai' => $request->nilai]);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function beriRevisi(Request $request, $id)
    {
        $request->validate(['catatan_revisi' => 'required|string']);

        PengumpulanTugas::findOrFail($id)->update([
            'catatan_revisi' => $request->catatan_revisi,
            'is_revision'    => true,
        ]);

        return back()->with('success', 'Revisi berhasil dikirim.');
    }
}
