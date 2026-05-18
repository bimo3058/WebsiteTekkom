<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;

class DashboardController extends Controller
{
    public function __construct(protected KoorPraktikumService $koorService) {}

    public function index()
    {
        $user = auth()->user();

        $praktikums = Praktikum::with(['koordinator', 'modul'])
            ->where('dosen_id', $user->id)
            ->withCount(['daftarPraktikan', 'modul', 'asprakPraktikum'])
            ->orderByDesc('created_at')
            ->get();

        $totalPraktikumDiampu = $praktikums->count();
        $totalPraktikumAktif  = $praktikums->where('status', 'aktif')->count();
        $totalMahasiswa       = $praktikums->sum('daftar_praktikan_count');
        $totalModul           = $praktikums->sum('modul_count');

        // Praktikum yang belum punya koordinator
        $praktikumTanpaKoor = $praktikums->whereNull('koor_id')->values();

        // Pendaftaran koordinator yang pending (perlu tindakan dosen)
        $pendaftaranKoorPending = PendaftaranKoordinator::with(['user', 'praktikum'])
            ->whereHas('praktikum', fn($q) => $q->where('dosen_id', $user->id))
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        // Nilai yang belum diapprove dosen
        $nilaiMenungguApproval = Nilai::whereHas('daftarPraktikan.praktikum', fn($q) => $q->where('dosen_id', $user->id))
            ->where('disetujui_koor', true)
            ->where('disetujui_dosen', false)
            ->count();

        $semesterLabel = 'Semester Genap 2025/2026';

        return view('eoffice::manajemen-praktikum.dosen.dashboard', compact(
            'praktikums',
            'totalPraktikumDiampu',
            'totalPraktikumAktif',
            'totalMahasiswa',
            'totalModul',
            'praktikumTanpaKoor',
            'nilaiMenungguApproval',
            'pendaftaranKoorPending',
            'semesterLabel'
        ));
    }

    /**
     * Tunjuk koordinator dari NIM mahasiswa.
     */
    public function tunjukKoor(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'nim'          => 'required|string',
        ]);

        $user = auth()->user();

        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('dosen_id', $user->id)
            ->firstOrFail();

        // Cari user berdasarkan NIM (student_number di tabel students)
        $targetUser = User::whereHas('student', fn($q) => $q->where('student_number', $request->nim))
            ->first();

        if (!$targetUser) {
            return back()->with('error', "Mahasiswa dengan NIM {$request->nim} tidak ditemukan.");
        }

        // Pastikan mahasiswa sudah terdaftar di praktikum ini
        $terdaftar = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->where('user_id', $targetUser->id)
            ->exists();

        if (!$terdaftar) {
            return back()->with('error', 'Mahasiswa ini tidak terdaftar di praktikum tersebut.');
        }

        $this->koorService->assign($praktikum, $targetUser);

        return back()->with('success', "Mahasiswa {$targetUser->name} berhasil ditunjuk sebagai koordinator dan otomatis aktif sebagai asprak.");
    }
}
