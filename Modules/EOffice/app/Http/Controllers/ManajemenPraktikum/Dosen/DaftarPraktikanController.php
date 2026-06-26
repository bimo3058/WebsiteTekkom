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

        $praktikumList = Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $search = $request->input('search');

        $query = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $praktikumId)
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at');

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
