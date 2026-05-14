<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsistenPraktikum;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\Tugas;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Data asisten praktikum user ini
        $asprak = AsistenPraktikum::with(['praktikum.dosen', 'modulAsprak.modul'])
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        // Modul yang diampu asprak ini
        $modulDiampu = ModulAsprak::with(['modul.materi', 'modul.tugas'])
            ->where('asprak_id', $asprak?->id)
            ->get()
            ->pluck('modul');

        $totalModul        = $modulDiampu->count();
        $totalMateri       = $modulDiampu->sum(fn($m) => $m->materi?->count() ?? 0);

        // Tugas yang perlu dinilai (sudah dikumpul, belum ada nilai)
        $tugasPendingNilai = PengumpulanTugas::whereHas('tugas.modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->whereNull('nilai')
            ->count();

        // Absensi hari ini
        $absensiHariIni = Absensi::whereHas('modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->whereDate('tanggal', today())
            ->count();

        // Tugas mendatang (deadline belum lewat)
        $tugasMendatang = Tugas::whereHas('modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        // Pengumpulan tugas terbaru perlu review
        $pengumpulanPending = PengumpulanTugas::with(['tugas', 'daftarPraktikan.user'])
            ->whereHas('tugas.modul.modulAsprak', fn($q) => $q->where('asprak_id', $asprak?->id))
            ->whereNull('nilai')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('eoffice::manajemen-praktikum.asprak.dashboard', compact(
            'asprak',
            'modulDiampu',
            'totalModul',
            'totalMateri',
            'tugasPendingNilai',
            'absensiHariIni',
            'tugasMendatang',
            'pengumpulanPending'
        ));
    }
}
