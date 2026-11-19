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
        $perPage = max(1,min(100,$request->integer('per_page',10)));

        $praktikanList = $terdaftarDi
            ? DaftarPraktikan::with(['user.student'])
                ->where('praktikum_id', $terdaftarDi->id)
                ->tap(fn($query)=>\Modules\EOffice\Services\ManajemenPraktikum\ParticipantListFilters::apply($query,$request))
                ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
                ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
                ->orderBy('created_at')
                ->paginate($perPage)->withQueryString()
            : collect();

        return view('eoffice::manajemen-praktikum.mahasiswa.daftar-praktikan', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'praktikanList',
            'search'
        ));
    }
}
