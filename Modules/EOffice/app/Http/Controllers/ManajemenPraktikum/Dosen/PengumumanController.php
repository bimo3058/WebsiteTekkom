<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;

/**
 * Dosen: Lihat postingan pengumuman yang diunggah oleh asprak.
 * Dosen hanya bisa melihat, tidak bisa membuat/hapus.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumList = Praktikum::where('dosen_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $pengumumans = $praktikumId
            ? Pengumuman::where('praktikum_id', $praktikumId)
                ->with('user')
                ->orderByDesc('created_at')
                ->paginate(10)
            : collect();

        return view('eoffice::manajemen-praktikum.dosen.pengumuman', compact(
            'praktikumList',
            'praktikum',
            'pengumumans'
        ));
    }
}
