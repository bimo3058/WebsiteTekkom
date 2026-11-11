<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;

        $tahunAjaran = $request->input('tahun_ajaran', $defaultTahunAjaran);
        $semester = $request->input('semester', $currentSemester);
        $semesterLabel = "Semester {$semester} {$tahunAjaran}/".($tahunAjaran + 1);

        // Fetch every figure displayed by the stat cards in one round-trip.
        $stats = DB::query()
            ->selectSub(
                Praktikum::query()->selectRaw('COUNT(*)')->where('status', 'aktif'),
                'total_praktikum_aktif'
            )
            ->selectSub(
                Lecturer::query()->selectRaw('COUNT(*)'),
                'total_dosen'
            )
            ->selectSub(
                PendaftaranAsprak::query()
                    ->selectRaw('COUNT(*)')
                    ->where('status', 'pending')
                    ->where('status_koor', 'disetujui'),
                'total_asprak_pending'
            )
            ->selectSub(
                PendaftaranKoordinator::query()
                    ->selectRaw('COUNT(*)')
                    ->where('status', 'pending')
                    ->where('status_dosen', 'disetujui'),
                'total_koor_pending'
            )
            ->first();

        $totalPraktikumAktif = (int) $stats->total_praktikum_aktif;
        $totalDosen = (int) $stats->total_dosen;
        $totalAsprakPending = (int) $stats->total_asprak_pending;
        $totalKoorPending = (int) $stats->total_koor_pending;
        $pendingTindakan = $totalAsprakPending + $totalKoorPending;

        // Only select fields rendered by the dashboard; relations are not used here.
        $praktikums = Praktikum::query()
            ->select(['id', 'nama', 'status', 'created_at'])
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $searchDosen = trim((string) $request->input('search_dosen', ''));
        $perPage = (int) $request->input('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 20], true) ? $perPage : 5;

        // Count the practicum assignments inside the lecturer query, avoiding N+1.
        $dosenQuery = Lecturer::query()
            ->with('user')
            ->addSelect([
                'jumlah_praktikum' => DB::table('eo_prak_praktikum_dosen')
                    ->join('eo_prak_praktikum', 'eo_prak_praktikum.id', '=', 'eo_prak_praktikum_dosen.praktikum_id')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('eo_prak_praktikum_dosen.dosen_id', 'lecturers.user_id')
                    ->whereNull('eo_prak_praktikum.deleted_at'),
            ]);

        if ($searchDosen !== '') {
            $dosenQuery->where(function ($query) use ($searchDosen) {
                $query->whereHas('user', function ($userQuery) use ($searchDosen) {
                    $userQuery->where('name', 'like', "%{$searchDosen}%")
                        ->orWhere('email', 'like', "%{$searchDosen}%");
                })->orWhere('employee_number', 'like', "%{$searchDosen}%");
            });
        }

        $dosenPaginate = $dosenQuery
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString()
            ->fragment('daftar-dosen');

        $dosenTerbaru = collect($dosenPaginate->items())->map(fn ($lecturer) => [
            'name' => $lecturer->user?->name ?? '—',
            'email' => $lecturer->user?->email ?? '—',
            'employee_number' => $lecturer->employee_number,
            'avatar_url' => $lecturer->user?->avatar_url,
            'jumlah_praktikum' => (int) $lecturer->jumlah_praktikum,
        ]);

        $dosenPaginator = $dosenPaginate;
        $now = now();

        $periodeBuka = PeriodePendaftaran::with('praktikum')
            ->where('is_aktif', true)
            ->whereIn('jenis', ['koor', 'asprak', 'praktikan'])
            ->where(function ($query) use ($now) {
                $query->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>=', $now);
            })
            ->orderBy('ditutup_pada')
            ->limit(5)
            ->get();

        return view('eoffice::manajemen-praktikum.admin.dashboard', compact(
            'semesterLabel',
            'totalPraktikumAktif',
            'totalAsprakPending',
            'totalKoorPending',
            'totalDosen',
            'praktikums',
            'dosenTerbaru',
            'dosenPaginator',
            'periodeBuka',
            'pendingTindakan'
        ));
    }
}
