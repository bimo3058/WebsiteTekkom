<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Tugas;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil dari middleware EnsureAsprakOwnership (sudah di-resolve via session/param)
        $asprak    = $request->attributes->get('asprak');
        $allAsprak = $request->attributes->get('all_asprak') ?? collect();

        // Fallback jika bypass (koor/admin masuk halaman asprak)
        if (! $asprak) {
            $user   = auth()->user();
            $asprak = AsistenPraktikum::with(['praktikum.dosens', 'modulAsprak.modul'])
                ->where('user_id', $user->id)->where('role', 'asprak')->whereNull('deleted_at')->first();
        }

        $modulDiampu = ModulAsprak::with(['modul.materi', 'modul.tugas'])
            ->where('asprak_id', $asprak?->id)
            ->get()
            ->pluck('modul')
            ->filter()
            ->values();

        $totalModul  = $modulDiampu->count();
        $totalMateri = $modulDiampu->sum(fn($m) => $m?->materi?->count() ?? 0);

        if ($asprak && $totalModul === 0) {
            session()->now('error', 'Akses terbatas: Anda belum di-assign sebagai pengampu pada modul manapun di praktikum ini.');
        }

        // Pengumpulan tugas yang belum dinilai (status: belum_dicek)
        $tugasPendingNilai = PengumpulanTugas::whereHas(
                'tugas.modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id)
            )
            ->where('status_pengumpulan', PengumpulanTugas::STATUS_BELUM_DICEK)
            ->count();

        $absensiHariIni = Absensi::whereHas('modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->whereDate('tanggal', today())
            ->count();

        $tugasMendatang = Tugas::whereHas('modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $pengumpulanPending = PengumpulanTugas::with(['tugas', 'daftarPraktikan.user'])
            ->whereHas('tugas.modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->where('status_pengumpulan', PengumpulanTugas::STATUS_BELUM_DICEK)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Pengumuman terbaru di praktikum asprak ini
        $pengumumanTerbaru = $asprak
            ? Pengumuman::where('praktikum_id', $asprak->praktikum_id)
                ->orderByDesc('created_at')
                ->limit(3)
                ->get()
            : collect();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.asprak.dashboard', compact(
            'asprak',
            'allAsprak',
            'modulDiampu',
            'totalModul',
            'totalMateri',
            'tugasPendingNilai',
            'absensiHariIni',
            'tugasMendatang',
            'pengumpulanPending',
            'pengumumanTerbaru',
            'semesterLabel'
        ));
    }
}
