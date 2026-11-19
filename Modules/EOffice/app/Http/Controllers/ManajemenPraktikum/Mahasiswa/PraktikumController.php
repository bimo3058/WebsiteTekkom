<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

class PraktikumController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua praktikum yang diikuti mahasiswa ini
        $daftarPraktikan = DaftarPraktikan::with([
            'praktikum' => function ($query) {
                $query->withCount(['daftarPraktikan', 'asprakPraktikum'])
                      ->with(['dosens', 'koordinator', 'matkul', 'asistenPraktikum']);
            }
        ])
            ->where('user_id', $user->id)
            ->whereHas('praktikum')
            ->orderByDesc(Praktikum::select('is_active')->whereColumn('eo_prak_praktikum.id','eo_prak_daftar_praktikan.praktikum_id')->limit(1))
            ->orderByDesc(Praktikum::select('tahun_ajaran')->whereColumn('eo_prak_praktikum.id','eo_prak_daftar_praktikan.praktikum_id')->limit(1))
            ->orderByDesc('created_at')
            ->orderBy('id')
            ->get();

        return view('eoffice::manajemen-praktikum.mahasiswa.praktikum', compact('daftarPraktikan'));
    }
}
