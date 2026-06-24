<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;

class DaftarPraktikanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil asprak yang sudah di-resolve oleh EnsureAsprakOwnership middleware
        $asprak = $request->attributes->get('asprak')
            ?? AsistenPraktikum::where('user_id', auth()->id())
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->first();

        $search = $request->input('search');

        $query = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $asprak?->praktikum_id)
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at');

        if ($search) {
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $praktikans = $query->paginate(20)->withQueryString();

        // Hitung persentase kehadiran per praktikan
        $absensiMap = [];
        if ($asprak) {
            $dpIds = DaftarPraktikan::where('praktikum_id', $asprak->praktikum_id)->pluck('id');
            $absensiData = Absensi::whereIn('daftar_praktikan_id', $dpIds)->get()
                ->groupBy('daftar_praktikan_id');

            foreach ($absensiData as $dpId => $records) {
                $total  = $records->count();
                $hadir  = $records->where('status', 'hadir')->count();
                $absensiMap[$dpId] = $total > 0 ? round($hadir / $total * 100) : null;
            }
        }

        return view('eoffice::manajemen-praktikum.asprak.daftar-praktikan', compact(
            'praktikans',
            'asprak',
            'search',
            'absensiMap'
        ));
    }
}
