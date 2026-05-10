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
     * Daftar delegasi yang ditujukan ke dosen yang sedang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $delegasi = PengaduanDelegasi::query()
            ->where('delegated_to', $user->id)
            ->with(['pengaduan', 'delegatedBy'])
            ->orderByDesc('delegated_at')
            ->paginate(20);

        return view('manajemenmahasiswa::pengaduan.delegasi.index', compact('delegasi'));
    }

    /**
     * Detail satu delegasi — dosen lihat isi tiket.
     * Identitas pelapor disembunyikan jika is_anonim = true.
     */
    public function show(Request $request, PengaduanDelegasi $delegasi)
    {
        $user = $request->user();

        if ($delegasi->delegated_to !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke delegasi ini.');
        }

        $delegasi->load(['pengaduan.delegasiAktif', 'delegatedBy']);

        return view('manajemenmahasiswa::pengaduan.delegasi.show', compact('delegasi'));
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
            ->route('manajemenmahasiswa.pengaduan.delegasi.index')
            ->with('success', 'Delegasi berhasil ditolak. Tiket dikembalikan ke Admin.');
    }
}
