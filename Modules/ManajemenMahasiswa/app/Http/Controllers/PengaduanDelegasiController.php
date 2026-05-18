<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ManajemenMahasiswa\Models\PengaduanDelegasi;
use Modules\ManajemenMahasiswa\Services\PengaduanService;

class PengaduanDelegasiController extends Controller
{
    public function __construct(private PengaduanService $pengaduanService)
    {
    }


    /**
     * Dosen kirim tanggapan balik ke admin.
     */
    public function respond(Request $request, PengaduanDelegasi $delegasi)
    {
        $user = $request->user();

        if ($delegasi->delegated_to !== $user->id) {
            abort(403);
        }
        if ($delegasi->status !== PengaduanDelegasi::STATUS_AKTIF) {
            abort(403, 'Delegasi ini sudah tidak aktif.');
        }

        $validated = $request->validate([
            'tanggapan'   => ['required', 'string', 'min:10', 'max:5000'],
            'notes_balik' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $this->pengaduanService->dosenRespond(
            $delegasi,
            $validated['tanggapan'],
            $validated['notes_balik']
        );

        return back()->with('success', 'Tanggapan berhasil dikirim ke Admin.');
    }

    /**
     * Dosen menolak delegasi disertai alasan.
     */
    public function reject(Request $request, PengaduanDelegasi $delegasi)
    {
        $user = $request->user();

        if ($delegasi->delegated_to !== $user->id) {
            abort(403);
        }
        if ($delegasi->status !== PengaduanDelegasi::STATUS_AKTIF) {
            abort(403, 'Delegasi ini sudah tidak aktif.');
        }

        $validated = $request->validate([
            'alasan_tolak' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $this->pengaduanService->dosenReject($delegasi, $validated['alasan_tolak']);

        return redirect()
            ->route('manajemenmahasiswa.pengaduan.index')
            ->with('success', 'Delegasi berhasil ditolak. Tiket dikembalikan ke Admin.');
    }
}
