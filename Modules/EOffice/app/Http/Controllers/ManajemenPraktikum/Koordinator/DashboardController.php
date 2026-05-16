<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\DaftarPraktikan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Praktikum yang dikoordinatori user ini
        $praktikum = Praktikum::with(['dosen', 'modul.asprak'])
            ->where('koor_id', $user->id)
            ->where('status', 'aktif')
            ->withCount(['daftarPraktikan', 'modul', 'asistenPraktikum'])
            ->first();

        $totalAsprak          = $praktikum?->asisten_praktikum_count ?? 0;
        $totalPraktikan       = $praktikum?->daftar_praktikan_count ?? 0;
        $totalModul           = $praktikum?->modul_count ?? 0;

        // Asprak yang sudah terdistribusi ke modul
        $asprakTerdistribusi = ModulAsprak::whereHas('modul', fn($q) => $q->where('praktikum_id', $praktikum?->id))
            ->distinct('asprak_id')
            ->count();

        // Asprak belum dapat modul
        $asprakBelumModul = $totalAsprak - $asprakTerdistribusi;

        // Daftar asprak
        $asistenList = AsistenPraktikum::with(['user', 'modulAsprak.modul'])
            ->where('praktikum_id', $praktikum?->id)
            ->get();

        // Pengumuman terbaru
        $pengumuman = Pengumuman::where('praktikum_id', $praktikum?->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('eoffice::manajemen-praktikum.koordinator.dashboard', compact(
            'praktikum',
            'totalAsprak',
            'totalPraktikan',
            'totalModul',
            'asprakTerdistribusi',
            'asprakBelumModul',
            'asistenList',
            'pengumuman'
        ));
    }
}
