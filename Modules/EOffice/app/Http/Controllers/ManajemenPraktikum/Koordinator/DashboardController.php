<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        if ($fallback) {
            session(['koor_praktikum_id' => $fallback->id]);
        }
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

        $pendingAsprak = $praktikum
            ? PendaftaranAsprak::where('praktikum_id', $praktikum->id)
                ->where('status', 'pending')
                ->count()
            : 0;

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
            'semesterLabel'
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
     * Lihat daftar praktikan praktikum yang dikoordinatori.
     */
    public function praktikan(Request $request)
    {
        $user      = auth()->user();
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

        $praktikans = $query->paginate(20)->withQueryString();

        $praktikansSemua = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $praktikum?->id)
            ->get();
            
        $praktikansSemuaJSON = $praktikansSemua->map(function($p) {
            return [
                'id' => $p->id,
                'nama' => $p->user?->name ?? '-',
                'nim' => $p->user?->student?->student_number ?? '-',
                'inisial' => strtoupper(substr($p->user?->name ?? 'M', 0, 2)),
                'kel' => $p->kelompok,
                'shift' => $p->shift
            ];
        });

        return view('eoffice::manajemen-praktikum.koordinator.daftar-praktikan', compact(
            'praktikum',
            'praktikans',
            'praktikansSemua',
            'praktikansSemuaJSON',
            'search'
        ));
    }

    /**
     * Download template CSV untuk import kelompok & shift.
     * Format: nim, nama (readonly hint), kelompok, shift
     */
    public function downloadTemplatePraktikan()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=template_import_kelompok_shift.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'no-cache',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // BOM untuk Excel agar UTF-8 terbaca
            fputs($file, "\xEF\xBB\xBF");
            // Header kolom
            fputcsv($file, ['nim_atau_email', 'kelompok', 'shift']);
            // Contoh data
            fputcsv($file, ['24060122140001', 'A', 'Pagi']);
            fputcsv($file, ['mahasiswa@students.undip.ac.id', 'B', 'Siang']);
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

        $user      = auth()->user();
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
            $headerIndices['kelompok'] = ($colCount >= 6) ? 4 : 1;
        }
        if ($headerIndices['shift'] === -1) {
            $headerIndices['shift'] = ($colCount >= 6) ? 5 : 2;
        }

        $updated    = 0;
        $skipped    = 0;
        $notInClass = []; // Ada di sistem tapi belum terdaftar di praktikum ini
        $notFound   = []; // Tidak ada di sistem sama sekali

        $lastKelompok = null;
        $lastShift    = null;

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

            // Carry-forward merged cells behavior
            if ($kelompok !== '') {
                $lastKelompok = $kelompok;
            } else {
                $kelompok = $lastKelompok ?? '';
            }

            if ($shift !== '') {
                $lastShift = $shift;
            } else {
                $shift = $lastShift ?? '';
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

            // Update kelompok & shift (set to null if blank to match CSV exactly)
            $dp->update([
                'kelompok' => $kelompok !== '' ? $kelompok : null,
                'shift'    => $shift    !== '' ? $shift    : null,
            ]);
            $updated++;
        }

        if ($updated > 0) {
            $msg = "{$updated} data praktikan berhasil diperbarui (kelompok & shift).";
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
            'jumlah_kelompok' => 'required|integer|min:1',
            'jumlah_shift'    => 'required|integer|min:1|lte:jumlah_kelompok',
            'method'          => 'required|in:urutan_sistem,acak',
        ], [
            'jumlah_shift.lte' => 'Jumlah shift tidak boleh lebih besar daripada jumlah kelompok.',
        ]);

        $user = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $praktikum->update([
            'jumlah_kelompok' => $request->jumlah_kelompok,
            'jumlah_shift'    => $request->jumlah_shift,
        ]);

        // Auto-plot ulang agar isinya menyesuaikan proporsi baru
        $praktikans = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->orderBy('created_at', 'asc')
            ->get();
            
        if ($request->method === 'acak') {
            $praktikans = $praktikans->shuffle()->values();
        } else {
            $praktikans = $praktikans->values();
        }

        if ($praktikans->isNotEmpty()) {
            $numKel = $request->jumlah_kelompok;
            $numShift = $request->jumlah_shift;
            $groupsPerShift = $numKel / $numShift;
            $total = $praktikans->count();
                $baseSize = floor($total / $numKel);
                $remainder = $total % $numKel;
                
                $currentIndex = 0;
                $kelompokIndex = 1;
                
                while ($kelompokIndex <= $numKel && $currentIndex < $total) {
                    $shift = (int) ceil($kelompokIndex / max(1, $groupsPerShift));
                    if ($shift > $numShift) {
                        $shift = $numShift;
                    }

                    $currentGroupSize = $baseSize + ($kelompokIndex <= $remainder ? 1 : 0);
                    
                    for ($i = 0; $i < $currentGroupSize; $i++) {
                        if ($currentIndex >= $total) break;
                        $dp = $praktikans[$currentIndex];
                        $dp->update([
                            'kelompok' => (string) $kelompokIndex,
                            'shift'    => (string) $shift,
                        ]);
                        $currentIndex++;
                    }
                    $kelompokIndex++;
                }
            }
        return back()->with('success', 'Pengaturan disimpan dan anggota telah di-redistribusi otomatis.');
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

        $user = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $numKel = $praktikum->jumlah_kelompok ?? 1;
        $numShift = $praktikum->jumlah_shift ?? 1;

        if ($numKel < 1 || $numShift < 1) {
            return back()->with('error', 'Harap simpan pengaturan jumlah kelompok & shift terlebih dahulu.');
        }

        $praktikans = DaftarPraktikan::with(['user.student'])
            ->where('praktikum_id', $praktikum->id)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($praktikans->isEmpty()) {
            return back()->with('error', 'Belum ada praktikan terdaftar.');
        }

        if ($request->method === 'acak') {
            $praktikans = $praktikans->shuffle()->values();
        } else {
            $praktikans = $praktikans->values();
        }

        // Tentukan mapping dari kelompok ke shift (tiap shift punya rentang kelompok yang merata)
        $groupsPerShift = $numKel / $numShift;
        
        // Membagi rata mahasiswa ke dalam kelompok-kelompok secara berurutan
        $total = $praktikans->count();
        $baseSize = floor($total / $numKel);
        $remainder = $total % $numKel;
        
        $currentIndex = 0;
        $kelompokIndex = 1;
        
        while ($kelompokIndex <= $numKel && $currentIndex < $total) {
            $shift = (int) ceil($kelompokIndex / max(1, $groupsPerShift));
            if ($shift > $numShift) {
                $shift = $numShift;
            }

            $currentGroupSize = $baseSize + ($kelompokIndex <= $remainder ? 1 : 0);
            
            for ($i = 0; $i < $currentGroupSize; $i++) {
                if ($currentIndex >= $total) break;
                $dp = $praktikans[$currentIndex];
                $dp->update([
                    'kelompok' => (string) $kelompokIndex,
                    'shift'    => (string) $shift,
                ]);
                $currentIndex++;
            }
            $kelompokIndex++;
        }

        return back()->with('success', 'Auto-Plot berhasil dilakukan.');
    }

    /**
     * Menghapus seluruh plot kelompok & shift (Reset)
     */
    public function resetPlot(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
        ]);

        $user = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->update([
                'kelompok' => null,
                'shift'    => null,
            ]);

        return back()->with('success', 'Seluruh data kelompok dan shift berhasil dikosongkan.');
    }

    /**
     * Update kelompok/shift manual (via AJAX)
     */
    public function updatePlot(Request $request)
    {
        $request->validate([
            'dp_id'    => 'required|uuid|exists:daftar_praktikan,id',
            'kelompok' => 'nullable|string',
            'shift'    => 'nullable|string',
        ]);

        $user = auth()->user();
        $dp = DaftarPraktikan::with('praktikum')->findOrFail($request->dp_id);
        
        if ($dp->praktikum->koor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $dp->update([
            'kelompok' => $request->kelompok ?: null,
            'shift'    => $request->shift ?: null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Menyimpan anggota kelompok dari Modal Checkbox (Teams style)
     */
    public function saveGroupMembers(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'kelompok'     => 'required|string',
            'shift'        => 'required|string',
            'members'      => 'nullable|array',
            'members.*'    => 'uuid|exists:daftar_praktikan,id',
        ]);

        $user = auth()->user();
        $praktikum = Praktikum::where('id', $request->praktikum_id)
            ->where('koor_id', $user->id)
            ->firstOrFail();

        $kelompok = $request->kelompok;
        $shift    = $request->shift;
        $members  = $request->members ?? [];

        // 1. Cabut keanggotaan (uncheck) dari mahasiswa yang sblmnya ada di kelompok ini
        DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->where('kelompok', $kelompok)
            ->whereNotIn('id', $members)
            ->update([
                'kelompok' => null,
                'shift'    => null,
            ]);

        // 2. Tambahkan mahasiswa yang di-checklist ke kelompok ini
        if (!empty($members)) {
            DaftarPraktikan::where('praktikum_id', $praktikum->id)
                ->whereIn('id', $members)
                ->update([
                    'kelompok' => $kelompok,
                    'shift'    => $shift,
                ]);
        }

        return response()->json(['success' => true]);
    }
}