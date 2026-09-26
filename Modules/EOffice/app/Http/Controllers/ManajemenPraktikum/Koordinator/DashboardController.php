<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\ModulAsprak;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranPraktikan;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\PraktikanGroupingService;

class DashboardController extends Controller
{
    public function __construct(
        protected KoorPraktikumService $koorService,
        protected PraktikanGroupingService $groupingService,
    ) {}

    private static function authenticatedUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    /**
     * Resolve praktikum yang sedang aktif untuk koor ini.
     * Priority: session → praktikum aktif pertama.
     */
    public static function resolvePraktikum(): ?Praktikum
    {
        $user = self::authenticatedUser();

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
        if ($fallback) {
            session(['koor_praktikum_id' => $fallback->id]);
        }
        return $fallback;
    }

    public function index()
    {
        $user      = self::authenticatedUser();
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

        $totalAsprak = \Modules\EOffice\Models\AsistenPraktikum::where('praktikum_id', $praktikum?->id)
            ->where('role', 'asprak')
            ->count();
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

        $pendaftarAsprakList = $praktikum
            ? PendaftaranAsprak::with(['user.student', 'praktikum'])
                ->where('praktikum_id', $praktikum->id)
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();
        $pendingAsprak = $pendaftarAsprakList->count();

        $kelompokStats = collect();
        $praktikanTanpaKelompok = 0;
        if ($praktikum) {
            $stats = DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->select('kelompok', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('kelompok')
                ->get();

            $praktikanTanpaKelompok = $stats->firstWhere('kelompok', null)?->total ?? 0;
            $kelompokStats = $stats->whereNotNull('kelompok')->pluck('total', 'kelompok');
        }

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

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
            'pendingAsprak',
            'semesterLabel',
            'pendaftarAsprakList',
            'kelompokStats',
            'praktikanTanpaKelompok'
        ));
    }

    /**
     * Switch praktikum aktif untuk session koor ini.
     */
    public function switchPraktikum(Request $request)
    {
        $request->validate(['praktikum_id' => 'required|uuid|exists:eo_praktikum,id']);

        $user = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        session(['koor_praktikum_id' => $praktikum->id]);

        return back()->with('success', "Tampilan beralih ke: {$praktikum->nama}");
    }


    /**
     * Lihat daftar praktikan praktikum yang dikoordinatori.
     */
    public function praktikan(Request $request)
    {
        $user      = self::authenticatedUser();
        $praktikum = self::resolvePraktikum();
        $search    = $request->input('search');

        $query = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $praktikum?->id)
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at');

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $praktikans = $query->paginate($request->input('per_page', 10))->withQueryString();

        $praktikansSemua = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $praktikum?->id)
            ->get();

        $hasDraft = $praktikum?->draft_pembagian !== null;
        $draft = $hasDraft
            ? $this->groupingService->normalize($praktikum->draft_pembagian)
            : $this->groupingService->fromActive($praktikansSemua, $praktikum?->metode_pembagian ?? 'manual');
        $draftGroups = collect($draft['groups']);
        $draftGroupShifts = $draftGroups->pluck('shift', 'name');
        $draftErrors = $hasDraft
            ? $this->groupingService->validationErrors($draft, $praktikansSemua->pluck('id'))
            : [];

        $praktikansSemuaJSON = $praktikansSemua->map(function ($p) use ($draft, $draftGroupShifts, $hasDraft) {
            $draftGroup = $draft['assignments'][(string) $p->id] ?? null;

            return [
                'id' => $p->id,
                'nama' => $p->user?->name ?? '-',
                'nim' => $p->user?->student?->student_number ?? '-',
                'inisial' => strtoupper(substr($p->user?->name ?? 'M', 0, 2)),
                'kel' => $hasDraft ? $draftGroup : $p->kelompok,
                'shift' => $hasDraft ? ($draftGroupShifts[$draftGroup] ?? null) : $p->shift,
            ];
        });

        return view('eoffice::manajemen-praktikum.koordinator.daftar-praktikan', compact(
            'praktikum',
            'praktikans',
            'praktikansSemua',
            'praktikansSemuaJSON',
            'draftGroups',
            'draftErrors',
            'hasDraft',
            'search'
        ));
    }

    /**
     * Download template CSV untuk import kelompok & shift.
     * Format: nim, nama (readonly hint), kelompok, shift
     */
    public function downloadTemplatePraktikan()
    {
        $praktikum = self::resolvePraktikum() ?? abort(404);
        $praktikans = DaftarPraktikan::with(['user.student'])
            ->where('praktikum_id', $praktikum->id)
            ->orderBy('created_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=template_import_kelompok_shift.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'no-cache',
        ];

        $callback = function () use ($praktikans) {
            $file = fopen('php://output', 'w');
            // BOM untuk Excel agar UTF-8 terbaca
            fputs($file, "\xEF\xBB\xBF");
            // Header kolom
            fputcsv($file, ['nim_atau_email', 'nama', 'kelompok', 'shift']);

            foreach ($praktikans as $praktikan) {
                fputcsv($file, [
                    $praktikan->user?->student?->student_number ?? $praktikan->user?->email,
                    $praktikan->user?->name,
                    '',
                    '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export seluruh praktikan terdaftar sebagai CSV.
     * Kolom: No, NIM, Nama, Email, Kelompok, Shift, Status, Tanggal Masuk
     */
    public function exportPraktikan()
    {
        $praktikum = self::resolvePraktikum() ?? abort(404);

        $praktikans = DaftarPraktikan::with(['user.student'])
            ->where('praktikum_id', $praktikum->id)
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at')
            ->get();

        $safeName = preg_replace('/[^a-zA-Z0-9_]/', '_', $praktikum->nama);
        $filename  = "praktikan_{$safeName}_" . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'no-cache',
        ];

        $callback = function () use ($praktikans) {
            $file = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['No', 'NIM', 'Nama', 'Email', 'Kelompok', 'Shift', 'Status', 'Tanggal Masuk']);

            foreach ($praktikans as $i => $dp) {
                $nim = $dp->user?->student?->student_number ?? '-';
                fputcsv($file, [
                    $i + 1,
                    $nim,
                    $dp->user?->name ?? '-',
                    $dp->user?->email ?? '-',
                    $dp->kelompok ?? '',
                    $dp->shift ?? '',
                    $dp->status,
                    $dp->created_at?->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import kelompok & shift dari CSV.
     * Format CSV: nim_atau_email, kelompok, shift
     * Hanya UPDATE mahasiswa yang SUDAH terdaftar di praktikum ini.
     * Tidak menambah mahasiswa baru.
     */
    public function importPraktikan(Request $request)
    {
        $request->validate([
            'file'         => 'required|file|max:5120',
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        // Validasi ekstensi manual (lebih fleksibel dari mimes)
        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt'])) {
            return back()->withErrors(['file' => 'Format file tidak valid. Harap upload file .csv']);
        }

        $user      = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $path    = $request->file('file')->getRealPath();
        $content = file_get_contents($path);

        // Strip BOM jika ada (dari Excel)
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Detect & convert encoding
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        }

        // Parse baris
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", $content))),
            fn($l) => $l !== ''
        ));

        if (empty($lines)) {
            return back()->with('error', 'File CSV kosong.');
        }

        // Deteksi header secara dinamis
        $firstRow = str_getcsv($lines[0]);
        $firstVal = mb_strtolower(trim($firstRow[0] ?? ''));
        
        $headerIndices = [
            'identifier' => -1,
            'kelompok'   => -1,
            'shift'      => -1,
        ];

        // Cek apakah baris pertama adalah header
        $isHeader = (
            str_contains($firstVal, 'nim') || 
            str_contains($firstVal, 'email') || 
            str_contains($firstVal, 'identifier') ||
            $firstVal === 'no' ||
            $firstVal === 'no.' ||
            $firstVal === '#'
        );

        if ($isHeader) {
            foreach ($firstRow as $idx => $colName) {
                $colClean = mb_strtolower(trim($colName));
                if ($colClean === 'nim' || $colClean === 'nim_atau_email' || $colClean === 'identifier') {
                    $headerIndices['identifier'] = $idx;
                } elseif ($colClean === 'email') {
                    // Set email sebagai identifier jika belum menemukan NIM
                    if ($headerIndices['identifier'] === -1) {
                        $headerIndices['identifier'] = $idx;
                    }
                } elseif ($colClean === 'kelompok' || $colClean === 'group') {
                    $headerIndices['kelompok'] = $idx;
                } elseif ($colClean === 'shift' || $colClean === 'jadwal') {
                    $headerIndices['shift'] = $idx;
                }
            }
            array_shift($lines);
        }

        // Tentukan fallback index berdasarkan jumlah kolom baris pertama jika tidak terdeteksi via header
        $colCount = count($firstRow);
        if ($headerIndices['identifier'] === -1) {
            $headerIndices['identifier'] = ($colCount >= 6) ? 1 : 0;
        }
        if ($headerIndices['kelompok'] === -1) {
            $headerIndices['kelompok'] = ($colCount >= 6) ? 4 : ($colCount >= 4 ? 2 : 1);
        }
        if ($headerIndices['shift'] === -1) {
            $headerIndices['shift'] = ($colCount >= 6) ? 5 : ($colCount >= 4 ? 3 : 2);
        }

        $updated    = 0;
        $skipped    = 0;
        $notInClass = []; // Ada di sistem tapi belum terdaftar di praktikum ini
        $notFound   = []; // Tidak ada di sistem sama sekali

        $assignments  = [];
        $groupShifts  = [];
        $importErrors = [];

        foreach ($lines as $line) {
            $row        = str_getcsv($line);
            
            $idxIdentifier = $headerIndices['identifier'];
            $idxKelompok   = $headerIndices['kelompok'];
            $idxShift      = $headerIndices['shift'];

            $identifier = trim($row[$idxIdentifier] ?? '');
            $kelompok   = trim($row[$idxKelompok] ?? '');
            $shift      = trim($row[$idxShift] ?? '');

            // Defensif: filter header-like values atau kosong
            $lowerId = mb_strtolower($identifier);
            if ($identifier === '' || in_array($lowerId, ['no', 'nim', 'nim_atau_email', 'identifier', 'email'])) {
                $skipped++;
                continue;
            }

            if ($kelompok === '') {
                $importErrors[] = "Kelompok untuk {$identifier} belum diisi.";
                $skipped++;
                continue;
            }

            if (mb_strlen($kelompok) > 50 || mb_strlen($shift) > 50) {
                $importErrors[] = "Kelompok atau shift untuk {$identifier} melebihi 50 karakter.";
                $skipped++;
                continue;
            }

            // Cari user berdasarkan NIM atau email
            $targetUser = User::where('email', $identifier)->first();
            if (!$targetUser) {
                $student    = Student::where('student_number', $identifier)->with('user')->first();
                $targetUser = $student?->user;
            }

            if (!$targetUser) {
                $notFound[] = $identifier;
                $skipped++;
                continue;
            }

            // Cari di daftar_praktikan — hanya update yang SUDAH terdaftar
            $dp = DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->where('user_id', $targetUser->id)
                ->first();

            if (!$dp) {
                $notInClass[] = $targetUser->name . ' (' . $identifier . ')';
                $skipped++;
                continue;
            }

            if (isset($assignments[(string) $dp->id])) {
                $importErrors[] = "{$identifier} tercantum lebih dari satu kali.";
                $skipped++;
                continue;
            }

            if ($shift !== '' && isset($groupShifts[$kelompok]) && $groupShifts[$kelompok] !== '' && $groupShifts[$kelompok] !== $shift) {
                $importErrors[] = "Kelompok {$kelompok} memiliki lebih dari satu shift.";
                $skipped++;
                continue;
            }

            $assignments[(string) $dp->id] = $kelompok;
            $groupShifts[$kelompok] = $shift !== '' ? $shift : ($groupShifts[$kelompok] ?? '');
            $updated++;
        }

        if (! empty($importErrors)) {
            return back()->withErrors(['file' => implode(' ', array_slice($importErrors, 0, 10))]);
        }

        if ($updated > 0) {
            $groups = collect($groupShifts)
                ->map(fn ($shift, $name) => ['name' => (string) $name, 'shift' => (string) $shift])
                ->values()
                ->all();

            $praktikum->update([
                'jumlah_kelompok' => count($groups),
                'metode_pembagian' => 'manual',
                'draft_pembagian' => [
                    'method' => 'manual',
                    'groups' => $groups,
                    'assignments' => $assignments,
                ],
            ]);

            $msg = "{$updated} data praktikan berhasil dimasukkan ke draft pembagian.";
            if ($skipped > 0) {
                $msg .= " {$skipped} baris dilewati.";
            }
            if (!empty($notInClass)) {
                $msg .= ' Belum terdaftar di kelas ini: ' . implode(', ', array_slice($notInClass, 0, 3));
                if (count($notInClass) > 3) $msg .= ' ...(+' . (count($notInClass) - 3) . ' lagi)';
            }
            if (!empty($notFound)) {
                $msg .= ' Tidak ditemukan di sistem: ' . implode(', ', array_slice($notFound, 0, 3));
                if (count($notFound) > 3) $msg .= ' ...(+' . (count($notFound) - 3) . ' lagi)';
            }
            $msg .= ' Periksa shift dan preview sebelum menerapkan pembagian.';

            return back()->with('success', $msg);
        }

        // Tidak ada yang berhasil diupdate
        $msg = 'Tidak ada data yang berhasil diperbarui.';
        if (!empty($notInClass)) {
            $msg .= ' Mahasiswa berikut belum terdaftar di praktikum ini: ' . implode(', ', array_slice($notInClass, 0, 5));
        }
        if (!empty($notFound)) {
            $msg .= ' Tidak ditemukan di sistem: ' . implode(', ', array_slice($notFound, 0, 5));
        }
        return back()->with('error', $msg);
    }

    /**
     * Simpan pengaturan jumlah kelompok dan shift
     */
    public function updatePlotSettings(Request $request)
    {
        $request->validate([
            'praktikum_id'    => 'required|uuid|exists:eo_praktikum,id',
            'jumlah_kelompok' => 'required|integer|min:1|max:100',
            'jumlah_shift'    => 'required|integer|min:1|max:20|lte:jumlah_kelompok',
            'method'          => 'required|in:urutan_sistem,acak,manual',
        ], [
            'jumlah_shift.lte' => 'Jumlah shift tidak boleh lebih besar daripada jumlah kelompok.',
        ]);

        $user = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $praktikanIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->orderBy('created_at', 'asc')
            ->pluck('id');

        $method = $request->string('method')->toString();
        $groupCount = $request->integer('jumlah_kelompok');
        $shiftCount = $request->integer('jumlah_shift');
        $draft = $method === 'manual'
            ? $this->groupingService->manual($groupCount, $shiftCount)
            : $this->groupingService->automatic($praktikanIds, $groupCount, $shiftCount, $method);

        $praktikum->update([
            'jumlah_kelompok' => $groupCount,
            'jumlah_shift' => $shiftCount,
            'metode_pembagian' => $method,
            'draft_pembagian' => $draft,
        ]);

        $message = $method === 'manual'
            ? 'Draft kelompok kosong berhasil dibuat. Silakan susun anggota secara manual atau impor file.'
            : 'Draft pembagian berhasil dibuat. Periksa hasilnya sebelum diterapkan.';

        return back()->with('success', $message);
    }

    /**
     * Plotting otomatis mahasiswa ke kelompok & shift
     */
    public function autoPlot(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'method'       => 'required|in:urutan_sistem,acak',
        ]);

        $user = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $numKel = $praktikum->jumlah_kelompok ?? 1;
        $numShift = $praktikum->jumlah_shift ?? 1;

        if ($numKel < 1 || $numShift < 1) {
            return back()->with('error', 'Harap simpan pengaturan jumlah kelompok & shift terlebih dahulu.');
        }

        $praktikanIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->orderBy('created_at', 'asc')
            ->pluck('id');

        if ($praktikanIds->isEmpty()) {
            return back()->with('error', 'Belum ada praktikan terdaftar.');
        }

        $method = $request->string('method')->toString();
        $praktikum->update([
            'metode_pembagian' => $method,
            'draft_pembagian' => $this->groupingService->automatic(
                $praktikanIds,
                $numKel,
                $numShift,
                $method,
            ),
        ]);

        return back()->with('success', 'Draft pembagian otomatis berhasil dibuat. Periksa hasilnya sebelum diterapkan.');
    }

    /**
     * Terapkan draft kelompok dan shift ke data praktikan aktif.
     */
    public function applyPlot(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        $userId = self::authenticatedUser()->id;
        $errors = DB::transaction(function () use ($request, $userId) {
            $praktikum = Praktikum::where('id', $request->praktikum_id)
                ->where('koor_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();
            $praktikans = DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->lockForUpdate()
                ->get(['id']);
            $draft = $this->groupingService->normalize($praktikum->draft_pembagian);
            $validationErrors = $this->groupingService->validationErrors($draft, $praktikans->pluck('id'));

            if (! empty($validationErrors)) {
                return $validationErrors;
            }

            $groupShifts = collect($draft['groups'])->pluck('shift', 'name');
            foreach ($praktikans as $praktikan) {
                $group = $draft['assignments'][(string) $praktikan->id];
                DaftarPraktikan::whereKey($praktikan->id)->update([
                    'kelompok' => $group,
                    'shift' => $groupShifts[$group],
                ]);
            }

            $praktikum->update([
                'metode_pembagian' => $draft['method'],
                'draft_pembagian' => null,
            ]);

            return [];
        });

        if (! empty($errors)) {
            return back()->withErrors(['pembagian' => implode(' ', $errors)]);
        }

        return back()->with('success', 'Pembagian kelompok dan shift berhasil diterapkan. Nilai, absensi, dan pengumpulan tugas tetap tersimpan.');
    }

    /**
     * Buang draft tanpa mengubah pembagian yang sedang aktif.
     */
    public function discardPlotDraft(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', self::authenticatedUser()->id)
            ->firstOrFail()
            ->update(['draft_pembagian' => null]);

        return back()->with('success', 'Draft dibatalkan. Pembagian aktif tidak berubah.');
    }

    /**
     * Menghapus seluruh plot kelompok & shift (Reset)
     */
    public function resetPlot(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        $user = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        DB::transaction(function () use ($praktikum) {
            DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->update([
                    'kelompok' => null,
                    'shift' => null,
                ]);

            $praktikum->update([
                'jumlah_kelompok' => 0,
                'jumlah_shift' => 0,
                'metode_pembagian' => null,
                'draft_pembagian' => null,
            ]);
        });

        return back()->with('success', 'Seluruh data kelompok dan shift berhasil dikosongkan.');
    }

    /**
     * Update kelompok/shift manual (via AJAX)
     */
    public function updatePlot(Request $request)
    {
        $request->validate([
            'dp_id'    => 'required|uuid|exists:daftar_praktikan,id',
            'kelompok' => 'nullable|string|max:50',
            'shift'    => 'nullable|string|max:50',
        ]);

        $user = self::authenticatedUser();
        $dp = DaftarPraktikan::with('praktikum')->findOrFail($request->dp_id);
        
        if ($dp->praktikum->koor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $praktikum = $dp->praktikum;
        $draft = $praktikum->draft_pembagian !== null
            ? $this->groupingService->normalize($praktikum->draft_pembagian)
            : $this->groupingService->fromActive(
                DaftarPraktikan::where('praktikum_id', $praktikum->id)->get(),
                $praktikum->metode_pembagian ?? 'manual',
            );
        $group = $request->string('kelompok')->trim()->toString();

        if ($group !== '' && ! collect($draft['groups'])->contains('name', $group)) {
            return response()->json(['success' => false, 'message' => 'Draft atau kelompok tidak valid.'], 422);
        }

        if ($group === '') {
            unset($draft['assignments'][(string) $dp->id]);
        } else {
            $draft['assignments'][(string) $dp->id] = $group;
            foreach ($draft['groups'] as &$draftGroup) {
                if ($draftGroup['name'] === $group && $request->filled('shift')) {
                    $draftGroup['shift'] = $request->string('shift')->trim()->toString();
                }
            }
            unset($draftGroup);
        }

        $praktikum->update(['draft_pembagian' => $draft]);

        return response()->json(['success' => true]);
    }

    /**
     * Menyimpan anggota kelompok dari Modal Checkbox (Teams style)
     */
    public function saveGroupMembers(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'kelompok'     => 'required|string|max:50',
            'shift'        => 'required|string|max:50',
            'members'      => 'nullable|array',
            'members.*'    => 'uuid|exists:daftar_praktikan,id',
        ]);

        $user = self::authenticatedUser();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $kelompok = $request->string('kelompok')->trim()->toString();
        $shift = $request->string('shift')->trim()->toString();
        $members = collect($request->input('members', []))->map(fn ($id) => (string) $id)->unique()->values();

        $result = DB::transaction(function () use ($praktikum, $kelompok, $shift, $members) {
            $lockedPraktikum = Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            $draft = $lockedPraktikum->draft_pembagian !== null
                ? $this->groupingService->normalize($lockedPraktikum->draft_pembagian)
                : $this->groupingService->fromActive(
                    DaftarPraktikan::where('praktikum_id', $lockedPraktikum->id)->get(),
                    $lockedPraktikum->metode_pembagian ?? 'manual',
                );
            $groupIndex = collect($draft['groups'])->search(fn ($group) => $group['name'] === $kelompok);

            if ($groupIndex === false) {
                return ['success' => false, 'message' => 'Kelompok tidak ditemukan pada pembagian aktif.'];
            }

            $validMemberIds = DaftarPraktikan::where('praktikum_id', $lockedPraktikum->id)
                ->whereIn('id', $members)
                ->pluck('id')
                ->map(fn ($id) => (string) $id);

            if ($validMemberIds->count() !== $members->count()) {
                return ['success' => false, 'message' => 'Terdapat anggota yang bukan praktikan pada praktikum ini.'];
            }

            foreach ($draft['assignments'] as $praktikanId => $assignedGroup) {
                if ($assignedGroup === $kelompok && ! $members->contains((string) $praktikanId)) {
                    unset($draft['assignments'][$praktikanId]);
                }
            }

            foreach ($validMemberIds as $praktikanId) {
                $draft['assignments'][$praktikanId] = $kelompok;
            }

            $draft['groups'][$groupIndex]['shift'] = $shift;
            $lockedPraktikum->update(['draft_pembagian' => $draft]);

            return ['success' => true];
        });

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
