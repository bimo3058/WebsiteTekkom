<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;

/**
 * Admin: Lihat list asisten praktikum per praktikum.
 * Sesuai docx Role Admin: "Lihat list praktikum, Lihat asisten praktikum di tiap praktikum".
 */
class AsprakController extends Controller
{
    public function index(Request $request)
    {
        $praktikumList = Praktikum::with(['dosen', 'koordinator'])
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $aspraks = $praktikumId
            ? AsprakPraktikum::with(['user', 'modulAsprak.modul'])
                ->where('praktikum_id', $praktikumId)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.admin.asprak', compact(
            'praktikumList',
            'praktikum',
            'aspraks'
        ));
    }
}
