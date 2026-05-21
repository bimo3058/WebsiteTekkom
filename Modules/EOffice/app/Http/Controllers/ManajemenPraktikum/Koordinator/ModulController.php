<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Praktikum;

class ModulController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    public function index()
    {
        $praktikum = DashboardController::resolvePraktikum();

        $moduls = $praktikum
            ? Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.koordinator.modul', compact('praktikum', 'moduls'));
    }

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

        if ($modul->praktikum?->koor_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat modul ini.');
        }

        $daftarPraktikan = DaftarPraktikan::with(['user', 'nilai'])
            ->where('praktikum_id', $modul->praktikum_id)
            ->get();

        return view('eoffice::manajemen-praktikum.koordinator.modul-detail', compact('modul', 'daftarPraktikan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
            'deskripsi'     => 'nullable|string',
        ]);

        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);

        Modul::create([
            'praktikum_id'  => $praktikum->id,
            'nama'          => $request->nama,
            'urutan'        => $request->urutan,
            'jadwal_minggu' => $request->jadwal_minggu,
            'deskripsi'     => $request->deskripsi,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'urutan'        => 'required|integer|min:1',
            'jadwal_minggu' => 'nullable|string|max:100',
            'deskripsi'     => 'nullable|string',
        ]);

        $user  = auth()->user();
        $modul = Modul::with('praktikum')->findOrFail($id);

        if ($modul->praktikum?->koor_id !== $user->id) abort(403);

        $modul->update($request->only(['nama', 'urutan', 'jadwal_minggu', 'deskripsi']));

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $user  = auth()->user();
        $modul = Modul::with('praktikum')->findOrFail($id);

        if ($modul->praktikum?->koor_id !== $user->id) abort(403);

        $modul->delete();

        return back()->with('success', 'Modul berhasil dihapus.');
    }

    public function generateKode(int $id)
    {
        $user  = auth()->user();
        $modul = Modul::with('praktikum')->findOrFail($id);

        if ($modul->praktikum?->koor_id !== $user->id) abort(403);

        $modul->update(['kode_modul' => strtoupper(substr(md5(uniqid()), 0, 6))]);

        return back()->with('success', "Kode modul: {$modul->kode_modul}");
    }
}