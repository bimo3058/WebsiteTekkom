<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran   = $request->input('tahun_ajaran', now()->year);
        $semester      = $request->input('semester', now()->month <= 6 ? 'Genap' : 'Ganjil');
        $semesterLabel = "Semester {$semester} {$tahunAjaran}/" . ($tahunAjaran + 1);

        // ── Stat cards ─────────────────────────────────────────────────────────

        // Praktikum
        $totalPraktikumAktif = Praktikum::where('status', 'aktif')->count();
        $totalPraktikum      = Praktikum::count();

        // 1. HITUNG TOTAL PRAKTIKAN (Bypass model Eloquent langsung ke tabel Supabase)
        $totalPraktikan = DaftarPraktikan::whereHas('praktikum', function($query) {
            $query->where('status', 'aktif');
        })->count();

        // Pendaftaran pending
        $totalAsprakPending = PendaftaranAsprak::where('status', 'pending')->count();
        $totalKoorPending   = PendaftaranKoordinator::where('status', 'pending')->count();
        $pendingTindakan    = $totalAsprakPending + $totalKoorPending;

        // Modul, Dosen, Mahasiswa
        $totalModul     = Modul::count();
        $totalDosen     = Lecturer::count();
        $totalMahasiswa = Student::count();

        // ── Daftar praktikum terbaru ────────────────────────────────────────────

        $praktikums = Praktikum::with(['dosens', 'koordinator'])
            ->withCount('daftarPraktikan')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Daftar dosen terbaru ────────────────────────────────────────────────

        $dosenTerbaru = Lecturer::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($l) => [
                'name'            => $l->user?->name ?? '—',
                'email'           => $l->user?->email ?? '—',
                'employee_number' => $l->employee_number,
                'jumlah_praktikum'=> Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $l->user_id))->count(),
            ]);

        // ── Daftar mahasiswa terbaru ─────────────────────────────────────────────

        $mahasiswaTerbaru = Student::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($s) => [
                'name'           => $s->user?->name ?? '—',
                'email'          => $s->user?->email ?? '—',
                'student_number' => $s->student_number,
                'cohort_year'    => $s->cohort_year,
                // Hitung jumlah praktikum per mahasiswa langsung ke tabel Supabase
                'jumlah_praktikum' => \DB::table('daftar_praktikan')->where('user_id', $s->user_id)->count(),
            ]);

        // ── Pendaftaran asprak terbaru yang perlu di-approve ────────────────────

        $pendaftaranTerbaru = PendaftaranAsprak::with(['user', 'praktikum'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Pendaftaran koor terbaru yang perlu di-approve ────────────────────

        $pendaftaranKoorTerbaru = PendaftaranKoordinator::with(['user', 'praktikum'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Log aktivitas terbaru ───────────────────────────────────────────────

        $recentActivities = \DB::table('eo_audit_logs')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn($l) => [
                'text' => $l->description,
                'time' => \Carbon\Carbon::parse($l->created_at)->diffForHumans(),
                'type' => 'blue',
            ])
            ->toArray();

        return view('eoffice::manajemen-praktikum.admin.dashboard', compact(
            'semesterLabel',
            'totalPraktikumAktif',
            'totalPraktikum',
            'totalPraktikan',
            'totalAsprakPending',
            'totalKoorPending',
            'totalModul',
            'totalDosen',
            'totalMahasiswa',
            'pendingTindakan',
            'praktikums',
            'dosenTerbaru',
            'mahasiswaTerbaru',
            'pendaftaranTerbaru',
            'pendaftaranKoorTerbaru',
            'recentActivities'
        ));
    }
}