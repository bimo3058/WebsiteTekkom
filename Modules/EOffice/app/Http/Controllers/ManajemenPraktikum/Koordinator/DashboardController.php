<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranPraktikan;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;

class DashboardController extends Controller
{
    public function __construct(protected KoorPraktikumService $koorService) {}

    /**
     * Resolve praktikum yang sedang aktif untuk koor ini.
     * Priority: session → praktikum aktif pertama.
     */
    public static function resolvePraktikum(): ?Praktikum
    {
        $user = auth()->user();

        // Ambil semua praktikum yang dikoor oleh user ini
        $all = Praktikum::where('koor_id', $user->id)
            ->whereIn('status', ['aktif', 'nonaktif'])
            ->orderByRaw("status = 'aktif' DESC")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($all->isEmpty()) return null;

        $sessionId = session('koor_praktikum_id');
        if ($sessionId) {
            $found = $all->firstWhere('id', $sessionId);
            if ($found) return $found;
        }

        // Fallback: praktikum aktif pertama
        $fallback = $all->firstWhere('status', 'aktif') ?? $all->first();
        session(['koor_praktikum_id' => $fallback->id]);
        return $fallback;
    }

    public function index()
    {
        $user      = auth()->user();
        $praktikum = self::resolvePraktikum();

        // Semua praktikum koor ini untuk switcher
        $allPraktikum = Praktikum::where('koor_id', $user->id)
            ->whereIn('status', ['aktif', 'nonaktif'])
            ->orderByRaw("status = 'aktif' DESC")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($praktikum) {
            $praktikum->loadCount(['daftarPraktikan', 'modul', 'asprakPraktikum']);
            $this->koorService->assign($praktikum, $user);
        }

        $totalAsprak         = $praktikum?->asprak_praktikum_count ?? 0;
        $totalPraktikan      = $praktikum?->daftar_praktikan_count ?? 0;
        $totalModul          = $praktikum?->modul_count ?? 0;

        $asprakTerdistribusi = ModulAsprak::whereHas('modul', fn($q) => $q->where('praktikum_id', $praktikum?->id))
            ->distinct('asprak_id')
            ->count();

        $asprakBelumModul = $totalAsprak - $asprakTerdistribusi;

        $asistenList = AsprakPraktikum::with(['user', 'modulAsprak.modul'])
            ->where('praktikum_id', $praktikum?->id)
            ->where('role', 'asprak')
            ->get();

        $pengumuman = Pengumuman::where('praktikum_id', $praktikum?->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $pendingPraktikanIrs = $praktikum
            ? PendaftaranPraktikan::where('praktikum_id', $praktikum->id)
                ->where('status', PendaftaranPraktikan::STATUS_PENDING)
                ->count()
            : 0;

        $pendingAsprak = $praktikum
            ? PendaftaranAsprak::where('praktikum_id', $praktikum->id)
                ->where('status', 'pending')
                ->count()
            : 0;

        return view('eoffice::manajemen-praktikum.koordinator.dashboard', compact(
            'praktikum',
            'allPraktikum',
            'totalAsprak',
            'totalPraktikan',
            'totalModul',
            'asprakTerdistribusi',
            'asprakBelumModul',
            'asistenList',
            'pengumuman',
            'pendingPraktikanIrs',
            'pendingAsprak'
        ));
    }

    /**
     * Switch praktikum aktif untuk session koor ini.
     */
    public function switchPraktikum(Request $request)
    {
        $request->validate(['praktikum_id' => 'required|uuid|exists:eo_praktikum,id']);

        $user = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        session(['koor_praktikum_id' => $praktikum->id]);

        return back()->with('success', "Tampilan beralih ke: {$praktikum->nama}");
    }

    /**
     * Generate / refresh kode join kelas untuk praktikum yang dikoordinatori.
     */
    public function generateKodePraktikum()
    {
        $p = self::resolvePraktikum() ?? abort(404);
        $p->update(['kode' => Praktikum::generateKode()]);

        return back()->with('success', "Kode praktikum baru: {$p->kode} — bagikan ke praktikan yang sudah disetujui.");
    }

    /**
     * Lihat daftar praktikan praktikum yang dikoordinatori.
     */
    public function praktikan(Request $request)
    {
        $user      = auth()->user();
        $praktikum = self::resolvePraktikum();
        $search    = $request->input('search');

        $query = DaftarPraktikan::with(['user'])
            ->where('praktikum_id', $praktikum?->id);

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $praktikans = $query->paginate(20)->withQueryString();

        return view('eoffice::manajemen-praktikum.koordinator.daftar-praktikan', compact(
            'praktikum',
            'praktikans',
            'search'
        ));
    }

    /**
     * Import praktikan dari file (CSV/Excel) atau tambah manual.
     */
    public function importPraktikan(Request $request)
    {
        $request->validate([
            'file'         => 'nullable|file|mimes:csv,xlsx,xls|max:5120',
            'user_ids'     => 'nullable|array',
            'user_ids.*'   => 'exists:users,id',
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        $user      = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $added = 0;

        if ($request->has('user_ids')) {
            foreach ($request->user_ids as $userId) {
                DaftarPraktikan::firstOrCreate([
                    'praktikum_id' => $praktikum->id,
                    'user_id'      => $userId,
                ], ['status' => 'terdaftar']);
                $added++;
            }
        }

        if ($request->hasFile('file')) {
            $path   = $request->file('file')->getRealPath();
            $rows   = array_map('str_getcsv', file($path));
            $header = array_shift($rows);

            foreach ($rows as $row) {
                if (empty($row[0])) continue;
                $identifier = trim($row[0]);

                $targetUser = User::where('email', $identifier)
                    ->orWhereHas('student', fn($q) => $q->where('student_number', $identifier))
                    ->first();

                if (!$targetUser) continue;

                DaftarPraktikan::firstOrCreate([
                    'praktikum_id' => $praktikum->id,
                    'user_id'      => $targetUser->id,
                ], ['status' => 'terdaftar']);
                $added++;
            }
        }

        return back()->with('success', "{$added} praktikan berhasil ditambahkan.");
    }
}