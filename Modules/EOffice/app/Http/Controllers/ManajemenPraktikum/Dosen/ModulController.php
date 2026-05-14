<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\MateriModul;
use Modules\EOffice\Models\Tugas;

class ModulController extends Controller
{
    /**
     * Daftar modul pada praktikum tertentu (read-only untuk dosen).
     * Dosen hanya melihat, yang membuat modul adalah asprak/koor.
     */
    public function index(Request $request, string $praktikumId)
    {
        $user = auth()->user();

        $praktikums = Praktikum::where('dosen_id', $user->id)
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        $praktikum = $praktikums->firstWhere('id', $praktikumId);
        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.dosen.modul', [
                'praktikum'  => null,
                'modulList'  => collect(),
                'praktikums' => $praktikums,
            ]);
        }

        $modulList = Modul::with(['materi', 'tugas', 'modulAsprak.asprak.user'])
            ->where('praktikum_id', $praktikum->id)
            ->orderBy('urutan')
            ->get()
            ->map(function ($modul) {
                return [
                    'modul'         => $modul,
                    'jumlah_materi' => $modul->materi->count(),
                    'jumlah_tugas'  => $modul->tugas->count(),
                    'asprak'        => $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values(),
                ];
            });

        return view('eoffice::manajemen-praktikum.dosen.modul', compact(
            'praktikum',
            'modulList'
        ));
    }
}