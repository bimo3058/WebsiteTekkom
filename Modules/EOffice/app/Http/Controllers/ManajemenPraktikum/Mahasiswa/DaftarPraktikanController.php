<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

class DaftarPraktikanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua praktikum yang diikuti mahasiswa
        $daftarPraktikan = DaftarPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->get();

        $praktikumAktifId = $request->input('praktikum_id') 
            ?? session('mhs_praktikum_id') 
            ?? $daftarPraktikan->first()?->praktikum_id;

        if ($praktikumAktifId) {
            session(['mhs_praktikum_id' => $praktikumAktifId]);
        }

        $terdaftarDi = $daftarPraktikan->firstWhere('praktikum_id', $praktikumAktifId)?->praktikum;

        if (!$terdaftarDi && $daftarPraktikan->isNotEmpty()) {
            $terdaftarDi = $daftarPraktikan->first()->praktikum;
            session(['mhs_praktikum_id' => $terdaftarDi->id]);
        }

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $praktikanList = $terdaftarDi
            ? DaftarPraktikan::with(['user.student'])
                ->where('praktikum_id', $terdaftarDi->id)
                ->when($search, function($q) use ($search) {
                    $q->whereHas('user', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhereHas('student', function($q3) use ($search) {
                               $q3->where('student_number', 'like', "%{$search}%");
                           });
                    });
                })
                ->orderBy('created_at')
                ->paginate($perPage)
            : collect();

        return view('eoffice::manajemen-praktikum.mahasiswa.daftar-praktikan', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'praktikanList',
            'search'
        ));
    }
}
