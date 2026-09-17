<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Tugas;
use Modules\EOffice\Models\DaftarPraktikan;

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

        $praktikumList = Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $modulList = collect();
        if ($praktikum) {
            $modulList = Modul::with(['tugas' => function($q) {
                $q->withCount('pengumpulan')->orderBy('created_at');
            }, 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $praktikum->id)
                ->orderBy('urutan')
                ->get()
                ->map(function ($modul) {
                    return [
                        'modul'         => $modul,
                        'tugas'         => $modul->tugas,
                        'asprak'        => $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values(),
                    ];
                });
        }

        return view('eoffice::manajemen-praktikum.dosen.tugas', compact(
            'praktikumList',
            'praktikum',
            'modulList'
        ));
    }

    /**
     * Lihat siapa yang sudah submit + dokumen per tugas.
     */
    public function pengumpulan(Request $request, int $tugasId)
    {
        $user  = auth()->user();
        $tugas = Tugas::with(['modul.praktikum', 'modul.modulAsprak.asprak.user'])->findOrFail($tugasId);

        // Verifikasi tugas milik praktikum yang diampu dosen ini
        if (!$tugas->modul?->praktikum?->dosens->contains('id', $user->id)) {
            abort(403, 'Anda tidak berhak melihat tugas ini.');
        }

        // Ambil semua praktikan terdaftar di praktikum tugas ini
        $praktikans = DaftarPraktikan::where('praktikum_id', $tugas->modul?->praktikum_id)
            ->with(['user', 'user.student'])
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        // Ambil data pengumpulan tugas untuk tugas_id ini
        $pengumpulan = PengumpulanTugas::where('tugas_id', $tugasId)
            ->with(['riwayat'])
            ->get()
            ->keyBy('daftar_praktikan_id');

        return view('eoffice::manajemen-praktikum.dosen.tugas-pengumpulan', compact(
            'tugas',
            'praktikans',
            'pengumpulan'
        ));
    }
}
