<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\Praktikum;

class PraktikumController extends Controller
{
    /**
     * Menampilkan daftar praktikum yang diampu sebagai asisten.
     */
    public function index()
    {
        $user = auth()->user();

        // Get all Asprak roles for this user that correspond to a active/existing Praktikum
        $allAsprak = AsistenPraktikum::with(['praktikum.dosens', 'praktikum.modul'])
            ->where('user_id', $user->id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->whereHas('praktikum')
            ->get();
            
        // Map them just to the Praktikum models but keep the relationship count
        $praktikums = $allAsprak->map(function ($asprak) {
            $p = $asprak->praktikum;
            // Load some counts if needed by view
            $p->loadCount(['daftarPraktikan', 'modul', 'asprakPraktikum']);
            return $p;
        })->sortByDesc('status')->sortByDesc('created_at')->values();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.asprak.praktikum', compact(
            'praktikums',
            'semesterLabel'
        ));
    }

    /**
     * Set session for the selected praktikum context
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $asprak = AsistenPraktikum::where('user_id', $user->id)
            ->where('praktikum_id', $id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->firstOrFail();

        session(['manprak_asprak_praktikum_id' => $id]);
        
        // After setting context, immediately redirect to Pengumuman, 
        // since the user wants the tabs to be pengumuman, modul, tugas, absensi, daftar praktikan
        // and usually the first one is the default
        return redirect()->route('eoffice.manprak.asprak.pengumuman.index');
    }
}
