<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;

class PengumumanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->first();

        $pengumumans = $praktikum
            ? Pengumuman::where('praktikum_id', $praktikum->id)->orderByDesc('created_at')->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.pengumuman', compact('praktikum', 'pengumumans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:eo_praktikum,id',
            'judul'        => 'required|string|max:255',
            'konten'       => 'required|string',
            'is_published' => 'boolean',
        ]);

        Pengumuman::create([
            'praktikum_id' => $request->praktikum_id,
            'user_id'      => auth()->id(),
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::with('praktikum')->findOrFail($id);

        if ($pengumuman->praktikum?->koor_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak menghapus pengumuman ini.');
        }

        $pengumuman->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
