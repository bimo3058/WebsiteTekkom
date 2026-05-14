<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\MateriModul;
use Modules\EOffice\Models\ModulAsprak;

class MateriController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        $materis = MateriModul::whereIn('modul_id', $modulIds)->with('modul.praktikum')->orderByDesc('created_at')->get();

        return view('eoffice::manajemen-praktikum.asprak.materi', compact('materis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modul_id'  => 'required|exists:modul_praktikum,id',
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file'      => 'nullable|file|max:20480',
        ]);

        $path     = null;
        $tipeFile = null;

        if ($request->hasFile('file')) {
            $path     = $request->file('file')->store('materi-modul', 'public');
            $tipeFile = $request->file('file')->getClientMimeType();
        }

        MateriModul::create([
            'modul_id'  => $request->modul_id,
            'user_id'   => auth()->id(),
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'tipe_file' => $tipeFile,
        ]);

        return back()->with('success', 'Materi berhasil diunggah.');
    }

    public function destroy($id)
    {
        $materi = MateriModul::findOrFail($id);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('success', 'Materi dihapus.');
    }
}
