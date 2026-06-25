<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;

/**
 * Mahasiswa: Lihat pengumuman dari asprak/koordinator.
 * Mendukung dropdown filter per praktikum.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Ambil semua praktikum yang diikuti mahasiswa
        $daftarList = DaftarPraktikan::where('user_id', $user->id)
            ->with('praktikum')
            ->get();

        $praktikumList = $daftarList->map(fn($d) => $d->praktikum)->filter();

        // Praktikum yang dipilih (default ke yang pertama)
        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum = $praktikumList->firstWhere('id', $praktikumId);

        // Query pengumuman hanya untuk praktikum yang dipilih
        $pengumumans = $praktikumId
            ? Pengumuman::where('praktikum_id', $praktikumId)
                ->where('is_published', true)
                ->with(['user', 'praktikum'])
                ->orderByDesc('created_at')
                ->paginate(10)
            : collect();

        return view('eoffice::manajemen-praktikum.mahasiswa.pengumuman', compact(
            'pengumumans',
            'praktikumList',
            'praktikum'
        ));
    }
}
