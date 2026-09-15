<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;

class PraktikumController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua praktikum yang diikuti mahasiswa ini
        $daftarPraktikan = DaftarPraktikan::with([
            'praktikum' => function ($query) {
                $query->withCount(['daftarPraktikan', 'asprakPraktikum'])
                      ->with(['dosens', 'koordinator', 'matkul']);
            }
        ])
            ->where('user_id', $user->id)
            ->get();

        return view('eoffice::manajemen-praktikum.mahasiswa.praktikum', compact('daftarPraktikan'));
    }
}
