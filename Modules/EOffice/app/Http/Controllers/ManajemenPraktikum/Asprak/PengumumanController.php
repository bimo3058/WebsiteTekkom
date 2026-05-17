<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;

/**
 * Asprak: Pengumuman (semua asprak bisa upload/hapus).
 * Sesuai docx: unggah/hapus postingan yang dapat dilakukan oleh semua asprak.
 */
class PengumumanController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->first();

        $pengumumans = $asprak
            ? Pengumuman::where('praktikum_id', $asprak->praktikum_id)
                ->with('user')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.asprak.pengumuman', compact('pengumumans', 'asprak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'judul'        => 'required|string|max:255',
            'konten'       => 'required|string',
            'is_published' => 'boolean',
        ]);

        $user   = auth()->user();
        $asprak = AsistenPraktikum::where('user_id', $user->id)
            ->where('role', 'asprak')
            ->where('praktikum_id', $request->praktikum_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$asprak) {
            return back()->with('error', 'Anda tidak terdaftar sebagai asprak di praktikum ini.');
        }

        Pengumuman::create([
            'praktikum_id' => $request->praktikum_id,
            'user_id'      => $user->id,
            'judul'        => $request->judul,
            'konten'       => $request->konten,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Pengumuman berhasil diunggah.');
    }

    public function destroy(int $id)
    {
        $user        = auth()->user();
        $pengumuman  = Pengumuman::findOrFail($id);

        // Asprak hanya bisa hapus miliknya sendiri, kecuali koor/admin
        if ($pengumuman->user_id !== $user->id && !$user->hasAnyRole(['koor_prak', 'admin_eoffice', 'superadmin'])) {
            return back()->with('error', 'Anda hanya bisa menghapus pengumuman milik sendiri.');
        }

        $pengumuman->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
