<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\MateriModul;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')->whereNull('deleted_at')->first();

        $modulIds = $asprak
            ? ModulAsprak::where('asprak_id', $asprak->id)->pluck('modul_id')
            : collect();

        $materis = MateriModul::whereIn('modul_id', $modulIds)->with('modul.praktikum')->orderByDesc('created_at')->get();

        $modulsForSelect = $modulIds->isNotEmpty()
            ? Modul::whereIn('id', $modulIds)->orderBy('urutan')->get()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.materi', compact('materis', 'asprak', 'modulsForSelect'));
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

        $allowed = ModulAsprak::whereHas('asprak', fn($q) => $q
            ->where('user_id', auth()->id())
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
        )->where('modul_id', $request->modul_id)->exists();

        if (! $allowed) {
            return back()->with('error', 'Anda tidak di-assign ke modul ini.');
        }

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
        $materi = MateriModul::with('modul.modulAsprak.asprak')->findOrFail($id);

        $allowed = $materi->modul?->modulAsprak
            ->contains(fn ($ma) => $ma->asprak?->user_id === auth()->id());

        if (! $allowed) {
            abort(403, 'Anda tidak berhak menghapus materi ini.');
        }

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('success', 'Materi dihapus.');
    }
}
