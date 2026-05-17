<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Pengumuman;

/**
 * Mahasiswa: Lihat pengumuman dari asprak.
 */
class PengumumanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $praktikumIds = DaftarPraktikan::where('user_id', $user->id)->pluck('praktikum_id');

        $pengumumans = Pengumuman::whereIn('praktikum_id', $praktikumIds)
            ->where('is_published', true)
            ->with(['user', 'praktikum'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('eoffice::manajemen-praktikum.mahasiswa.pengumuman', compact('pengumumans'));
    }
}
