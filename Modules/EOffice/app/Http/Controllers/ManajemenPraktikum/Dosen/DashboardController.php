<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\AsistenPraktikum;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $praktikums = Praktikum::with(['koordinator', 'modul'])
            ->where('dosen_id', $user->id)
            ->withCount(['daftarPraktikan', 'modul', 'asistenPraktikum'])
            ->orderByDesc('created_at')
            ->get();

        $totalPraktikumDiampu  = $praktikums->count();
        $totalPraktikumAktif   = $praktikums->where('status', 'aktif')->count();
        $totalMahasiswa        = $praktikums->sum('daftar_praktikan_count');
        $totalModul            = $praktikums->sum('modul_count');

        // Praktikum yang belum punya koordinator → perlu tindakan
        $praktikumTanpaKoor = $praktikums->whereNull('koor_id')->values();

        // Nilai yang belum diinput
        $nilaiPending = \Modules\EOffice\Models\Nilai::whereHas('daftarPraktikan.praktikum', fn($q) => $q->where('dosen_id', $user->id))
            ->whereNull('nilai_akhir')
            ->count();

        $semesterLabel = 'Semester Genap 2025/2026';

        return view('eoffice::manajemen-praktikum.dosen.dashboard', compact(
            'praktikums',
            'totalPraktikumDiampu',
            'totalPraktikumAktif',
            'totalMahasiswa',
            'totalModul',
            'praktikumTanpaKoor',
            'nilaiPending',
            'semesterLabel'
        ));
    }
}
