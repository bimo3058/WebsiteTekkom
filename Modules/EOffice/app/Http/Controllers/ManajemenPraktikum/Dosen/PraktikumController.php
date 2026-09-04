<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;

class PraktikumController extends Controller
{
    /**
     * Menampilkan daftar semua praktikum yang diampu oleh Dosen.
     */
    public function index()
    {
        $user = auth()->user();

        $praktikums = Praktikum::with(['koordinator', 'modul'])
            ->whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->withCount(['daftarPraktikan', 'modul', 'asprakPraktikum'])
            ->orderByDesc('created_at')
            ->get();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.dosen.praktikum', compact(
            'praktikums',
            'semesterLabel'
        ));
    }

    /**
     * Menampilkan halaman detail drill-down untuk satu praktikum tertentu.
     */
    public function show($id)
    {
        $user = auth()->user();

        // Pastikan praktikum ini memang diampu oleh dosen yang sedang login
        $praktikum = Praktikum::with(['koordinator'])
            ->where('id', $id)
            ->whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->firstOrFail();

        // Load pengumumans for this praktikum
        $pengumumans = \Modules\EOffice\Models\Pengumuman::where('praktikum_id', $id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('eoffice::manajemen-praktikum.dosen.praktikum-detail', compact('praktikum', 'pengumumans'));
    }
}
