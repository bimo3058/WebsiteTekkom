<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Pengumuman;

class PraktikumController extends Controller
{
    /**
     * Menampilkan daftar semua praktikum yang dikoordinasikan.
     */
    public function index()
    {
        $user = auth()->user();

        $praktikums = Praktikum::with(['dosens', 'modul'])
            ->where('koor_id', $user->id)
            ->withCount(['daftarPraktikan', 'modul', 'asprakPraktikum'])
            ->orderByDesc('status') // 'aktif' di atas
            ->orderByDesc('created_at')
            ->get();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.koordinator.praktikum', compact(
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

        // Pastikan praktikum ini memang diampu oleh koor yang sedang login
        $praktikum = Praktikum::with(['dosens'])
            ->where('id', $id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        // Menyimpan context session agar menu-menu koor yang lain (Pendaftaran, Modul, dll) membaca praktikum ini
        session(['koor_praktikum_id' => $praktikum->id]);

        // Redirect langsung ke halaman Seleksi Asisten
        return redirect()->route('eoffice.manprak.koor.pendaftaran-asprak.index');
    }
}
