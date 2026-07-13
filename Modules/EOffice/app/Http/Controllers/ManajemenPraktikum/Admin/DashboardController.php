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
use Modules\EOffice\Models\PeriodePendaftaran;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        
        $tahunAjaran   = $request->input('tahun_ajaran', $defaultTahunAjaran);
        $semester      = $request->input('semester', $currentSemester);
        $semesterLabel = "Semester {$semester} {$tahunAjaran}/" . ($tahunAjaran + 1);

        // ── Stat cards ─────────────────────────────────────────────────────────

        // Praktikum
        $totalPraktikumAktif = Praktikum::where('status', 'aktif')->count();
        $totalPraktikum      = Praktikum::count();

        // 1. HITUNG TOTAL PRAKTIKAN (Bypass model Eloquent langsung ke tabel Supabase)
        $totalPraktikan = DaftarPraktikan::whereHas('praktikum', function($query) {
            $query->where('status', 'aktif');
        })->count();

        // Pendaftaran pending yang siap dieksekusi Admin
        $totalAsprakPending = PendaftaranAsprak::where('status', 'pending')
                                ->where('status_koor', 'disetujui')
                                ->count();
        $totalKoorPending   = PendaftaranKoordinator::where('status', 'pending')
                                ->where('status_dosen', 'disetujui')
                                ->count();
        $pendingTindakan    = $totalAsprakPending + $totalKoorPending;

        // Modul, Dosen, Mahasiswa
        $totalModul     = Modul::count();
        $totalDosen     = Lecturer::count();
        $totalMahasiswa = Student::count();

        // ── Daftar praktikum terbaru ────────────────────────────────────────────

        $praktikums = Praktikum::with(['dosens', 'koordinator'])
            ->withCount('daftarPraktikan')
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $searchDosen = $request->input('search_dosen');
        $perPage = $request->input('per_page', 5);

        $dosenQuery = Lecturer::with('user');

        if ($searchDosen) {
            $dosenQuery->where(function($q) use ($searchDosen) {
                $q->whereHas('user', function($qu) use ($searchDosen) {
                    $qu->where('name', 'like', "%{$searchDosen}%")
                       ->orWhere('email', 'like', "%{$searchDosen}%");
                })->orWhere('employee_number', 'like', "%{$searchDosen}%");
            });
        }

        $dosenPaginate = $dosenQuery->orderByDesc('created_at')->paginate($perPage)->withQueryString()->fragment('daftar-dosen');
        
        $dosenTerbaru = collect($dosenPaginate->items())->map(fn($l) => [
            'name'            => $l->user?->name ?? '—',
            'email'           => $l->user?->email ?? '—',
            'employee_number' => $l->employee_number,
            'avatar_url'      => $l->user?->avatar_url,
            'jumlah_praktikum'=> Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $l->user_id))->count(),
        ]);
        
        // Simpan paginatornya untuk view
        $dosenPaginator = $dosenPaginate;

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

        // ── Periode Pendaftaran yang sedang buka ────────────────────

        $now = now();
        $periodeBuka = PeriodePendaftaran::with('praktikum')
            ->where('is_aktif', true)
            ->whereIn('jenis', ['koor', 'asprak'])
            ->where(function($q) use ($now) {
                $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>=', $now);
            })
            ->orderBy('ditutup_pada', 'asc')
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
            'praktikums',
            'dosenTerbaru',
            'dosenPaginator',
            'mahasiswaTerbaru',
            'periodeBuka',
            'recentActivities',
            'pendingTindakan'
        ));
    }
}