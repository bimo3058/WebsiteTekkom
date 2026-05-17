<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Praktikum;

/**
 * Koordinator: Kelola modul praktikum.
 * Sesuai docx: generate kode, detail modul (jadwal, materi, asprak, nilai, absensi), CRUD asprak per modul.
 */
class ModulController extends Controller
{
    /**
     * Daftar semua modul di praktikum yang dikoordinatori.
     */
    public function index()
    {
        $user      = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->first();

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.modul', compact('praktikum', 'moduls'));
    }

    /**
     * Detail satu modul: jadwal, materi, asprak, nilai, absensi.
     */
    public function show(int $modulId)
    {
        $user  = auth()->user();
        $modul = Modul::with([
            'praktikum',
            'materi',
            'tugas.pengumpulan',
            'modulAsprak.asprak.user',
            'absensi.daftarPraktikan.user',
        ])->findOrFail($modulId);

        // Pastikan modul milik praktikum yang dikoordinatori user ini
        if ($modul->praktikum?->koor_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat modul ini.');
        }

        $daftarPraktikan = DaftarPraktikan::with(['user', 'nilai'])
            ->where('praktikum_id', $modul->praktikum_id)
            ->get();

        $aspraksAvailable = AsprakPraktikum::with('user')
            ->where('praktikum_id', $modul->praktikum_id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->get();

        return view('eoffice::manajemen-praktikum.koordinator.modul-detail', compact(
            'modul',
            'daftarPraktikan',
            'aspraksAvailable'
        ));
    }

    /**
     * Buat modul baru untuk praktikum yang dikoordinatori.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
        ]);

        $user      = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->firstOrFail();

        Modul::create([
            'praktikum_id'  => $praktikum->id,
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'urutan'        => $request->urutan,
            'jadwal_minggu' => $request->jadwal_minggu,
        ]);

        return back()->with('success', 'Modul berhasil dibuat.');
    }

    /**
     * Update modul.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
        ]);

        $user  = auth()->user();
        $modul = Modul::findOrFail($id);

        if ($modul->praktikum?->koor_id !== $user->id) {
            abort(403);
        }

        $modul->update($request->only(['nama', 'deskripsi', 'urutan', 'jadwal_minggu']));

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    /**
     * Hapus modul.
     */
    public function destroy(int $id)
    {
        $user  = auth()->user();
        $modul = Modul::findOrFail($id);

        if ($modul->praktikum?->koor_id !== $user->id) {
            abort(403);
        }

        $modul->delete();

        return back()->with('success', 'Modul berhasil dihapus.');
    }

    /**
     * Generate kode unik untuk modul (dipanggil oleh koordinator).
     */
    public function generateKode(Request $request, int $modulId): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user  = auth()->user();
        $modul = Modul::with('praktikum')->findOrFail($modulId);

        if ($modul->praktikum?->koor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $kode = Modul::generateKodeModul();
        $modul->update(['kode_modul' => $kode]);

        if (! $request->expectsJson()) {
            return back()->with('success', "Kode modul {$modul->nama} berhasil digenerate: {$kode}");
        }

        return response()->json([
            'success'    => true,
            'kode_modul' => $kode,
            'message'    => 'Kode modul berhasil digenerate.',
        ]);
    }
}
