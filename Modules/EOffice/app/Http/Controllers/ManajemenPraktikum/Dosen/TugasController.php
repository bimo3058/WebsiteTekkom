<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Tugas;

/**
 * Dosen: Lihat daftar tugas, siapa yang submit, dan dokumen yang dikumpulkan.
 * Dosen bersifat read-only (tidak bisa CRUD tugas — itu domain asprak).
 */
class TugasController extends Controller
{
    /**
     * Daftar tugas di seluruh praktikum yang diampu dosen.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumList = Praktikum::where('dosen_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $modulIds = $praktikum
            ? Modul::where('praktikum_id', $praktikum->id)->pluck('id')
            : collect();

        $tugas = Tugas::whereIn('modul_id', $modulIds)
            ->with(['modul'])
            ->withCount('pengumpulan')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('eoffice::manajemen-praktikum.dosen.tugas', compact(
            'praktikumList',
            'praktikum',
            'tugas'
        ));
    }

    /**
     * Lihat siapa yang sudah submit + dokumen per tugas.
     */
    public function pengumpulan(Request $request, int $tugasId)
    {
        $user  = auth()->user();
        $tugas = Tugas::with(['modul.praktikum'])->findOrFail($tugasId);

        // Verifikasi tugas milik praktikum yang diampu dosen ini
        if ($tugas->modul?->praktikum?->dosen_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat tugas ini.');
        }

        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->with(['daftarPraktikan.user'])
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.dosen.tugas-pengumpulan', compact(
            'tugas',
            'pengumpulan'
        ));
    }
}
