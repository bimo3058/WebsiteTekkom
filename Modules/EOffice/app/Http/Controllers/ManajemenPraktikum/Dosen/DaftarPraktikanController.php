<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

/**
 * Dosen: Lihat daftar praktikan berdasarkan list praktikum.
 */
class DaftarPraktikanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumList = Praktikum::where('dosen_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $search = $request->input('search');

        $query = DaftarPraktikan::with(['user'])
            ->where('praktikum_id', $praktikumId);

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $praktikans = $query->paginate(20)->withQueryString();

        return view('eoffice::manajemen-praktikum.dosen.daftar-praktikan', compact(
            'praktikumList',
            'praktikum',
            'praktikans',
            'search'
        ));
    }
}
