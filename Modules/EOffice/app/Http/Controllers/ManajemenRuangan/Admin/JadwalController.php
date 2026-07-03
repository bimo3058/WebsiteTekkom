<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Ruangan;
use Illuminate\Support\Str;

class JadwalController extends Controller
{
    /**
     * Dispatcher to Academic or Event view.
     */
    public function index(Request $request)
    {
        $routeName = $request->route()->getName();
        $isAkademik = strpos($routeName, 'jadwal-akademik') !== false;

        $tipe = $request->query('tipe', 'rutin');
        $viewMode = $isAkademik ? 'akademik' : 'event';

        $search = $request->query('search');
        $filterHari = $request->query('hari');
        $filterRuangan = $request->query('ruangan_id');

        $query = MrJadwalInternal::with('ruangan')->orderBy('created_at', 'desc');

        if ($isAkademik) {
            $query->where('kategori', 'Jadwal Akademik (Kuliah)');

            if ($search) {
                // Gunakan LOWER() untuk memastikan case-insensitive searching lintas Database (terutama PostgreSQL)
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(mata_kuliah) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(kode_mk) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(kelas) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(pengampu) LIKE ?', ["%{$searchTerm}%"]);
                });
            }
            if ($filterHari) {
                $query->where('hari', $filterHari);
            }
            if ($filterRuangan) {
                $query->where('ruangan_id', $filterRuangan);
            }
        } else {
            $query->where('kategori', '!=', 'Jadwal Akademik (Kuliah)');
        }

        if ($tipe === 'rutin' || $tipe === 'spesifik') {
            $query->where('tipe_jadwal', $tipe);
        }

        $jadwals = $query->paginate(15)->withQueryString();
        $ruangans = Ruangan::where('is_active', true)->get();

        $bladeFile = $isAkademik ? 'index' : 'maintenance';
        return view('eoffice::manajemen-ruangan.admin.jadwal.' . $bladeFile, compact('jadwals', 'tipe', 'ruangans', 'viewMode', 'search', 'filterHari', 'filterRuangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required_unless:kategori,Jadwal Akademik (Kuliah)|nullable|string|max:255',
            'tgl_mulai_efektif' => 'nullable|date',
            'tgl_selesai_efektif' => 'nullable|date|after_or_equal:tgl_mulai_efektif',
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255',
        ]);

        if ($request->tipe_jadwal === 'rutin') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
        } else {
            $request->validate(['tanggal_spesifik' => 'required|date']);
        }

        MrJadwalInternal::create([
            'ruangan_id' => $request->ruangan_id,
            'tipe_jadwal' => $request->tipe_jadwal,
            'kategori' => $request->kategori,
            'hari' => ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') ? $request->hari : null,
            'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $request->tanggal_spesifik : null,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->kategori === 'Jadwal Akademik (Kuliah)' ? ($request->mata_kuliah . '-' . $request->kelas) : $request->keterangan,
            'tgl_mulai_efektif' => $request->tgl_mulai_efektif,
            'tgl_selesai_efektif' => $request->tgl_selesai_efektif,
            'mata_kuliah' => $request->mata_kuliah,
            'kode_mk' => $request->kode_mk,
            'kelas' => $request->kelas,
            'sks' => $request->sks,
            'kuota' => $request->kuota,
            'pengampu' => $request->pengampu,
        ]);

        $redirectRoute = $request->kategori === 'Jadwal Akademik (Kuliah)'
            ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
            : 'eoffice.peminjaman.admin.jadwal-internal.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Jadwal berhasil ditambahkan. Ruangan terkait otomatis terblokir pada waktu tersebut.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);

        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required_unless:kategori,Jadwal Akademik (Kuliah)|nullable|string|max:255',
            'tgl_mulai_efektif' => 'nullable|date',
            'tgl_selesai_efektif' => 'nullable|date|after_or_equal:tgl_mulai_efektif',
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255',
        ]);

        if ($request->tipe_jadwal === 'rutin') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
        } else {
            $request->validate(['tanggal_spesifik' => 'required|date']);
        }

        $jadwal->update([
            'ruangan_id' => $request->ruangan_id,
            'tipe_jadwal' => $request->tipe_jadwal,
            'kategori' => $request->kategori,
            'hari' => ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') ? $request->hari : null,
            'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $request->tanggal_spesifik : null,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->kategori === 'Jadwal Akademik (Kuliah)' ? ($request->mata_kuliah . '-' . $request->kelas) : $request->keterangan,
            'tgl_mulai_efektif' => $request->tgl_mulai_efektif,
            'tgl_selesai_efektif' => $request->tgl_selesai_efektif,
            'mata_kuliah' => $request->mata_kuliah,
            'kode_mk' => $request->kode_mk,
            'kelas' => $request->kelas,
            'sks' => $request->sks,
            'kuota' => $request->kuota,
            'pengampu' => $request->pengampu,
        ]);

        $redirectRoute = $request->kategori === 'Jadwal Akademik (Kuliah)'
            ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
            : 'eoffice.peminjaman.admin.jadwal-internal.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Konfigurasi Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);
        $redirectRoute = $jadwal->kategori === 'Jadwal Akademik (Kuliah)'
            ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
            : 'eoffice.peminjaman.admin.jadwal-internal.index';

        $jadwal->delete();
        return redirect()->route($redirectRoute)
            ->with('success', 'Jadwal berhasil dihapus. Pemblokiran ruangan telah dicabut.');
    }

    public function resetAkademik()
    {
        MrJadwalInternal::where('kategori', 'Jadwal Akademik (Kuliah)')->delete();
        return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
            ->with('success', 'Seluruh jadwal perkuliahan akademik berhasil dihapus (Reset Semester).');
    }

    public function downloadTemplateCSV()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Template_Jadwal_Kuliah_E_Office.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Hari (1=Sen, 7=Min)',
                'Ruangan_ID (Lihat Daftar Ruang)',
                'Jam_Mulai (HH:MM)',
                'Jam_Selesai (HH:MM)',
                'Mata_Kuliah',
                'Kode_MK',
                'Kelas',
                'SKS',
                'Kuota',
                'Dosen_Pengampu'
            ]);
            fputcsv($file, [
                '1',
                '1',
                '08:00',
                '10:30',
                'Pemrograman Web',
                'TKK211',
                'A',
                '3',
                '40',
                'Dr. Budi'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $file = $request->file('file_excel');
        $csvData = [];
        $ext = strtolower($file->getClientOriginalExtension());

        // WE WILL ALWAYS USE PhpSpreadsheet SO IT SUPPORTS CSV and XLSX UNIVERSALLY 
        // AND APPLIES THE SIAP EXTRACTION ENGINE TO ALL!
        // This prevents the bug where uploading a CSV bypasses the SIAP structure match.

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getSheet(0)->toArray();

            $debugLog = "=== UPLOAD DEBUG ===\n";
            $debugLog .= "Total Rows in Sheet 0: " . count($sheet) . "\n";
            if (count($sheet) > 0) {
                $debugLog .= "Row[0] Raw JSON: " . json_encode($sheet[0]) . "\n";
                $debugLog .= "Row[1] Raw JSON: " . (isset($sheet[1]) ? json_encode($sheet[1]) : 'null') . "\n";
                $debugLog .= "Row[20] Raw JSON: " . (isset($sheet[20]) ? json_encode($sheet[20]) : 'null') . "\n";
            }

            $semuaRuangan = Ruangan::where('is_active', true)->get();
            $hariMap = ['SEN' => 1, 'SENIN' => 1, 'SEL' => 2, 'SELASA' => 2, 'RAB' => 3, 'RABU' => 3, 'KAM' => 4, 'KAMIS' => 4, 'JUM' => 5, 'JUMAT' => 5, 'SAB' => 6, 'SABTU' => 6];

            $failHariCount = 0;
            $failGateCount = 0;

            foreach ($sheet as $i => $row) {
                if (empty($row[0]) || strpos((string) $row[0], ',') === false) {
                    continue;
                }

                $splitDate = explode(',', (string) $row[0]);
                $hariStr = strtoupper(trim($splitDate[0]));

                if (!isset($hariMap[$hariStr])) {
                    $failHariCount++;
                    continue;
                }

                $hariId = $hariMap[$hariStr];

                $jamStr = explode('-', trim($splitDate[1] ?? ''));
                $jamMulai = isset($jamStr[0]) ? trim($jamStr[0]) : '00:00';
                $jamSelesai = isset($jamStr[1]) ? trim($jamStr[1]) : '00:00';

                $sheetWidth = count($row);
                $roomIdx = $sheetWidth >= 10 ? 9 : 7;
                $pengIdx = $sheetWidth >= 10 ? 8 : 6;

                $rawRoom = strtoupper((string) ($row[$roomIdx] ?? ''));
                $cleanRawRoom = str_replace([' ', '.', '-'], '', $rawRoom);

                $ruanganIdTarget = null;
                foreach ($semuaRuangan as $r) {
                    $cleanName = strtoupper(str_replace([' ', '.', '-'], '', $r->nama));
                    if (!empty($cleanName) && strpos($cleanRawRoom, $cleanName) !== false) {
                        $ruanganIdTarget = $r->id; // ID might be UUID string
                        break;
                    }
                }

                $matkulRaw = trim((string) ($row[1] ?? ''));
                $kelasRaw = trim((string) ($row[3] ?? ''));
                $sks = (int) filter_var($row[4] ?? '0', FILTER_SANITIZE_NUMBER_INT);
                $pengampu = str_replace("\n", " / ", (string) ($row[$pengIdx] ?? ''));

                if (empty($ruanganIdTarget) || empty($matkulRaw) || empty($kelasRaw) || empty($jamMulai)) {
                    if ($failGateCount < 10) {
                        $debugLog .= "Fail Gate pada Row $i. Data: RTarget($ruanganIdTarget) Matkul($matkulRaw) Kelas($kelasRaw) Jam($jamMulai)\n";
                    }
                    $failGateCount++;
                    continue;
                }

                $csvData[] = [
                    $hariId,
                    $ruanganIdTarget,   // NO CAST to (int)
                    $jamMulai,
                    $jamSelesai,
                    $matkulRaw,
                    trim((string) ($row[2] ?? '')),
                    $kelasRaw,
                    $sks,
                    (int) ($row[5] ?? 0),
                    trim($pengampu),
                ];
            }

            $debugLog .= "Total Succcess: " . count($csvData) . "\n";
            $debugLog .= "Total Fail HariMap: $failHariCount\n";
            $debugLog .= "Total Fail Gate: $failGateCount\n";
            \Log::info($debugLog);
            file_put_contents(storage_path('logs/siap_debug.log'), $debugLog);

        } catch (\Exception $e) {
            \Log::error('[SIAP Import] PhpSpreadsheet gagal: ' . $e->getMessage());
            return back()->withErrors(['file_excel' => 'Gagal membaca file SIAP: ' . $e->getMessage()]);
        }

        $ruangans = Ruangan::where('is_active', true)->get()->keyBy('id');
        return view('eoffice::manajemen-ruangan.admin.jadwal.import-preview', compact('csvData', 'ruangans'));
    }

    public function executeImport(Request $request)
    {
        $request->validate([
            'validated_payload' => 'required|string',
            'tgl_mulai_efektif_global' => 'required|date',
            'tgl_selesai_efektif_global' => 'required|date|after_or_equal:tgl_mulai_efektif_global',
        ]);

        $payload = json_decode($request->input('validated_payload'), true);

        if (!$payload || !is_array($payload)) {
            return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
                ->withErrors(['File atau format payload jadwal tidak valid.']);
        }

        $insertBatch = [];
        $now = now();

        foreach ($payload as $row) {
            $insertBatch[] = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'ruangan_id' => $row['ruangan_id'],
                'tipe_jadwal' => 'rutin',
                'kategori' => 'Jadwal Akademik (Kuliah)',
                'hari' => $row['hari'],
                'tanggal_spesifik' => null,
                'jam_mulai' => $row['jam_mulai'],
                'jam_selesai' => $row['jam_selesai'],
                'keterangan' => $row['mata_kuliah'] . '-' . $row['kelas'],
                'tgl_mulai_efektif' => $request->tgl_mulai_efektif_global,
                'tgl_selesai_efektif' => $request->tgl_selesai_efektif_global,
                'mata_kuliah' => $row['mata_kuliah'],
                'kode_mk' => $row['kode_mk'] ?? null,
                'kelas' => $row['kelas'] ?? null,
                'sks' => $row['sks'] ?? null,
                'kuota' => $row['kuota'] ?? null,
                'pengampu' => $row['pengampu'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        MrJadwalInternal::insert($insertBatch);

        return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
            ->with('success', count($insertBatch) . ' row jadwal kelas massal berhasil diimpor!');
    }
}
