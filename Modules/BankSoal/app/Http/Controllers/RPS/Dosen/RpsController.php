<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Dosen;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\BsAuditLog;
use App\Services\SupabaseStorage;
use Modules\BankSoal\Models\MataKuliah;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Cpmk;
use Modules\BankSoal\Services\KkoService;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Models\PeriodeRps;
use Modules\BankSoal\Enums\RpsStatus;

class RpsController extends Controller
{
    /**
     * Map label KKO (string) balik ke kode (C1, C2, dst)
     */
    private function kkoLabelToCode(): array
    {
        return [
            'Mengingat'      => 'C1',
            'Memahami'       => 'C2',
            'Menerapkan'     => 'C3',
            'Menganalisis'   => 'C4',
            'Mengevaluasi'   => 'C5',
            'Mencipta'       => 'C6',
            'Meniru'         => 'P1',
            'Menyesuaikan'   => 'P2',
            'Membiasakan'    => 'P3',
            'Menguasai'      => 'P4',
            'Mahir'          => 'P5',
            'Menerima'       => 'A1',
            'Merespon'       => 'A2',
            'Menilai'        => 'A3',
            'Mengorganisasi' => 'A4',
            'Menghayati'     => 'A5',
            'Praktik'        => 'P',
            'Afektif'        => 'A',
        ];
    }

    /**
     * Reverse-engineer deskripsi CPMK → field kko, objek, konteks
     * Format tersimpan: "Mahasiswa mampu ({kkoLabel}) {objek} {konteks}"
     */
    private function parseCpmkRows(\Illuminate\Support\Collection $cpmks, int $rpsId): array
    {
        $labelToCode = $this->kkoLabelToCode();

        // Ambil CPL yang terkait dengan setiap CPMK via bs_rps_cpmk
        $cpmkCplMap = DB::table('bs_rps_cpmk')
            ->where('rps_id', $rpsId)
            ->get()
            ->keyBy('cpmk_id');

        return $cpmks->map(function ($cpmk) use ($labelToCode, $cpmkCplMap) {
            $deskripsi = $cpmk->deskripsi ?? '';
            $kko  = '';
            $objek   = $deskripsi;
            $konteks = '';

            $rawDeskripsi = $cpmk->getRawOriginal('deskripsi') ?? $deskripsi;
            
            if (preg_match('/^\((.*?)\)\s+\((.*?)\)(?:\s+\((.*?)\))?$/', $rawDeskripsi, $matches)) {
                $kkoLabel = trim($matches[1]);
                $kko  = $labelToCode[$kkoLabel] ?? $kkoLabel;
                $objek = trim($matches[2]);
                $konteks = isset($matches[3]) ? trim($matches[3]) : '';
            } elseif (preg_match('/^Mahasiswa mampu \(([^)]+)\)\s*(.*)$/u', $rawDeskripsi, $m)) {
                $kkoLabel = trim($m[1]);
                $kko  = $labelToCode[$kkoLabel] ?? $kkoLabel;
                $rest = trim($m[2]);
                $objek   = $rest;
                $konteks = '';
            }

            // Strip prefix "CPMK " dari kode
            $kodeRaw = preg_replace('/^CPMK\s*/i', '', $cpmk->kode ?? '') ?? '';

            $cplId = $cpmkCplMap->get($cpmk->id)?->cpl_id ?? null;

            return [
                'cpl_id'  => $cplId,
                'kode'    => trim($kodeRaw),
                'kko'     => $kko,
                'objek'   => $objek,
                'konteks' => $konteks,
            ];
        })->values()->all();
    }

    // Halaman utama RPS untuk Dosen
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user()->load('lecturer');

        $currentYear  = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        // Semester Ganjil: Juli-Desember (bulan 7-12)
        // Semester Genap: Januari-Juni (bulan 1-6)
        $semester = now()->month >= 7 ? 'Ganjil' : 'Genap';
        $semesterParity = now()->month >= 7 ? 1 : 0; // 1 = Ganjil, 0 = Genap
        
        // Set tahun ajaran
        $academicYear = $semester === 'Ganjil'
            ? $currentYear . '/' . ($currentYear + 1)
            : ($currentYear - 1) . '/' . $currentYear;


            $mkIdsWithActiveRps = RpsDetail::whereIn('status', [
                RpsStatus::DIAJUKAN->value,
                RpsStatus::REVISI->value,
                RpsStatus::DISETUJUI->value,
            ])
            ->where('semester', $semester)
            ->where('tahun_ajaran', $academicYear)
            ->pluck('mk_id')
            ->unique();

        $mataKuliahs = MataKuliah::whereNotIn('id', $mkIdsWithActiveRps)
            ->whereHas('dosenPengampu', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        // Fetch riwayat RPS — hanya tampilkan RPS dimana dosen ini terdaftar
        // (baik sebagai pembuat maupun dosen pengampu tambahan via pivot bs_rps_dosen)
        // Eager load mataKuliah dan dosens untuk menghindari N+1 queries
        // Riwayat Pengajuan RPS - fetch all untuk klien-side pagination
        $riwayat = RpsDetail::with('mataKuliah', 'dosens')
            ->select('bs_rps_detail.*')
            ->where(function($q) use ($user) {
                // Tampilkan jika user adalah pengunggah/penulis (ada di bs_rps_dosen)
                $q->whereHas('dosens', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                // ATAU jika user adalah dosen pengampu MK tersebut (ada di bs_dosen_pengampu_mk)
                ->orWhereHas('mataKuliah.dosenPengampu', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })
            ->addSelect([
                'uploader_id' => DB::table('bs_audit_logs')
                    ->select('user_id')
                    ->whereColumn('subject_id', 'bs_rps_detail.id')
                    ->where('subject_type', 'rps')
                    ->where('action', 'created')
                    ->limit(1),
                'uploader_name' => DB::table('bs_audit_logs')
                    ->join('users', 'users.id', '=', 'bs_audit_logs.user_id')
                    ->select('users.name')
                    ->whereColumn('bs_audit_logs.subject_id', 'bs_rps_detail.id')
                    ->where('bs_audit_logs.subject_type', 'rps')
                    ->where('bs_audit_logs.action', 'created')
                    ->limit(1)
            ])
            // Custom sorting: Revisi (1) > Disetujui (2) > Diajukan (3) > others (4)
            ->orderByRaw("CASE 
                WHEN status = ? THEN 1
                WHEN status = ? THEN 2
                WHEN status = ? THEN 3
                ELSE 4
            END ASC", [RpsStatus::REVISI->value, RpsStatus::DISETUJUI->value, RpsStatus::DIAJUKAN->value])
            ->orderBy('created_at', 'desc')
            ->get();

        // Riwayat RPS per mata kuliah yang SUDAH disetujui - fetch all untuk klien-side
        // Grouped by MK untuk dosen pengampu saat ini
        $riwayatMkDisetujui = DB::table('bs_rps_detail as rps')
            ->join('bs_mata_kuliah as mk', 'mk.id', '=', 'rps.mk_id')
            ->join('bs_dosen_pengampu_mk as dpm', function ($join) use ($user) {
                $join->on('dpm.mk_id', '=', 'rps.mk_id')
                    ->where('dpm.user_id', '=', $user->id);
            })
            ->leftJoin('bs_rps_review as review', function ($join) {
                $join->on('review.rps_id', '=', 'rps.id')
                    ->where('review.status_review', '=', RpsStatus::DISETUJUI->value);
            })
            ->where('rps.status', RpsStatus::DISETUJUI->value)
            ->select(
                'rps.id',
                'rps.mk_id',
                'rps.tahun_ajaran',
                'rps.semester',
                'rps.dokumen',
                'mk.kode as mk_kode',
                'mk.nama as mk_nama',
                'mk.id as mk_id_unique',
                DB::raw('COALESCE(MAX(review.updated_at), rps.updated_at) as tanggal_disetujui')
            )
            ->addSelect(['uploader_name' => DB::table('bs_audit_logs')
                ->join('users', 'users.id', '=', 'bs_audit_logs.user_id')
                ->select('users.name')
                ->whereColumn('bs_audit_logs.subject_id', 'rps.id')
                ->where('bs_audit_logs.subject_type', 'rps')
                ->where('bs_audit_logs.action', 'created')
                ->limit(1)
            ])
            ->groupBy('rps.id', 'rps.mk_id', 'rps.tahun_ajaran', 'rps.semester', 'rps.dokumen', 'mk.kode', 'mk.nama', 'mk.id', 'rps.updated_at')
            ->orderBy('mk.nama')
            ->orderByDesc('tanggal_disetujui')
            ->get()
            ->groupBy('mk_id');

        // Fetch Active Periode
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        
        $isUploadOpen = false;
        $tenggatH7 = false;
        $isHourFormat = false; // Track apakah daysLeft dalam format jam atau hari
        $unsubmittedMk = [];
        $daysLeft = 0;
        
        if ($activePeriode) {
            $now   = \Carbon\Carbon::now('Asia/Jakarta');
            $deadline = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end = $deadline->endOfDay();

            // Cek apakah sekarang dalam rentang periode aktif
            if ($now->between($start, $end)) {
                $isUploadOpen = true;
                
                // Cek H-7 Reminder - hitung sisa waktu
                $deadlineDate = $deadline->copy()->startOfDay();
                $todayDate = $now->copy()->startOfDay();
                
                // Jika deadline adalah hari yang sama dengan hari ini
                if ($deadlineDate->isSameDay($todayDate)) {
                    // Hitung sisa jam dari sekarang sampai akhir hari deadline
                    $endOfDay = $deadline->endOfDay();
                    $hoursLeft = (int) $now->diffInHours($endOfDay);
                    
                    if ($hoursLeft > 0) {
                        $tenggatH7 = true;
                        $daysLeft = $hoursLeft; // Simpan sebagai jam
                        $isHourFormat = true; // Mark sebagai format jam
                    }
                } else {
                    // Hitung sisa hari
                    $daysLeft = (int) $todayDate->diffInDays($deadlineDate);
                    if ($daysLeft > 0) {
                        if ($daysLeft <= 7) {
                            $tenggatH7 = true;
                        }
                    }
                    $isHourFormat = false; // Mark sebagai format hari
                }
                
                if ($tenggatH7) {
                    // Ambil daftar kode MK yang diampu user ini tapi RPS-nya belum disubmit/aktif
                    $unsubmittedMk = DB::table('bs_mata_kuliah')
                        ->join('bs_dosen_pengampu_mk', 'bs_mata_kuliah.id', '=', 'bs_dosen_pengampu_mk.mk_id')
                        ->where('bs_dosen_pengampu_mk.user_id', $user->id)
                        ->whereNotIn('bs_mata_kuliah.id', $mkIdsWithActiveRps)
                        ->where('bs_mata_kuliah.is_active', true)
                        ->pluck('bs_mata_kuliah.nama')
                        ->toArray();
                }
            }
        }

        $rpsUploaded = $riwayat->isNotEmpty();

        return view('banksoal::pages.rps.dosen.index', compact(
            'mataKuliahs',
            'riwayat',
            'riwayatMkDisetujui',
            'tahunAjarans',
            'semester',
            'academicYear',
            'rpsUploaded',
            'activePeriode',
            'isUploadOpen',
            'tenggatH7',
            'isHourFormat',
            'unsubmittedMk',
            'daysLeft'
        ));
    }

    /**
     * Halaman form Buat RPS baru (full page)
     */
    public function create(): \Illuminate\View\View
    {
        $user = Auth::user()->load('lecturer');

        $currentYear  = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        $semester = now()->month >= 7 ? 'Ganjil' : 'Genap';
        $semesterParity = now()->month >= 7 ? 1 : 0;
        $academicYear = $semester === 'Ganjil'
            ? $currentYear . '/' . ($currentYear + 1)
            : ($currentYear - 1) . '/' . $currentYear;

        $mkIdsWithActiveRps = RpsDetail::whereIn('status', [
                RpsStatus::DIAJUKAN->value,
                RpsStatus::REVISI->value,
                RpsStatus::DISETUJUI->value,
            ])
            ->where('semester', $semester)
            ->where('tahun_ajaran', $academicYear)
            ->pluck('mk_id')
            ->unique();

        $mataKuliahs = MataKuliah::whereNotIn('id', $mkIdsWithActiveRps)
            ->whereHas('dosenPengampu', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        $isUploadOpen  = false;

        if ($activePeriode) {
            $now   = \Carbon\Carbon::now('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end   = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();
            $isUploadOpen = $now->between($start, $end);
        }

        return view('banksoal::pages.rps.dosen.create', compact(
            'mataKuliahs',
            'tahunAjarans',
            'semester',
            'academicYear',
            'activePeriode',
            'isUploadOpen'
        ));
    }

    // Proses penyimpanan RPS baru
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $creationMethod = $request->input('creation_method', 'upload');

        // Validasi Input 
        $rules = [
            'mata_kuliah_id' => ['required', 'exists:bs_mata_kuliah,id'],
            'dosen_lain'     => ['nullable', 'array'],
            'dosen_lain.*'   => ['exists:users,id'],
            'semester'       => ['required', 'in:Ganjil,Genap'],
            'tahun_ajaran'   => ['required', 'string'],
            'cpmk_rows'              => ['required', 'array', 'min:1'],
            'cpmk_rows.*.cpl_id'     => ['required', 'exists:bs_cpl,id'],
            'cpmk_rows.*.kode'       => ['required', 'string', 'max:20'],
            'cpmk_rows.*.kko'        => ['required', 'string', 'max:20'],
            'cpmk_rows.*.objek'      => ['required', 'string', 'max:1000'],
            'cpmk_rows.*.konteks'    => ['nullable', 'string', 'max:1000'],
            'catatan'        => ['nullable', 'string', 'max:1000'],
        ];

        if ($creationMethod === 'upload') {
            $rules['dokumen'] = ['required', 'file', 'mimes:pdf', 'max:5120'];
        } else {
            $rules['deskripsi_mk'] = ['required', 'string'];
            $rules['penilaian_data'] = ['required', 'array', 'min:1'];
            $rules['pertemuan_data'] = ['required', 'array', 'min:1'];
            $rules['referensi_data'] = ['required', 'array', 'min:1'];
        }

        $validated = $request->validate($rules, [
            'dokumen.max' => 'Ukuran file maksimal 5MB',
            'dokumen.mimes' => 'Hanya menerima File berformat PDF',
            'dokumen.required' => 'File RPS harus diunggah',
            'cpmk_rows.required' => 'Minimal satu baris CPMK harus diisi',
            'deskripsi_mk.required' => 'Deskripsi MK wajib diisi',
            'penilaian_data.required' => 'Data penilaian wajib diisi',
            'pertemuan_data.required' => 'Data pertemuan wajib diisi',
            'referensi_data.required' => 'Data referensi wajib diisi',
        ]);

        // Cek apakah Periode RPS Aktif dan Valid
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        if (!$activePeriode) {
            return back()->withInput()->with('error', 'Sesi unggah RPS sedang ditutup atau belum ada jadwal yang aktif.');
        }

        $now   = now('Asia/Jakarta');
        $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
        $end   = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();

        if (!$now->between($start, $end)) {
            return back()->withInput()->with('error', 'Di luar jadwal unggah RPS. Tenggat waktu sudah terlewati atau jadwal belum dimulai.');
        }

        // Cek duplikasi RPS untuk mata kuliah + semester + tahun ajaran + dosen yang sama
        $existingRps = RpsDetail::where('mk_id', $validated['mata_kuliah_id'])
            ->where('semester', $validated['semester'])
            ->where('tahun_ajaran', $validated['tahun_ajaran'])
            ->whereHas('dosens', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->whereIn('status', [RpsStatus::DIAJUKAN->value, RpsStatus::REVISI->value, RpsStatus::DISETUJUI->value])
            ->exists();

        if ($existingRps) {
            return back()->withInput()->with('error', 'RPS untuk mata kuliah, semester, dan tahun ajaran ini sudah pernah diunggah. Tidak boleh upload RPS ganda untuk kurikulum yang sama.');
        }

        $duplicateCpmkCodes = collect($validated['cpmk_rows'])
            ->map(fn (array $row) => $this->normalizeCpmkKode((string) ($row['kode'] ?? '')))
            ->filter()
            ->duplicates();

        if ($duplicateCpmkCodes->isNotEmpty()) {
            return back()->withInput()->with('error', 'Kode CPMK tidak boleh sama dalam satu RPS. Silakan periksa kembali baris CPMK yang diisi.');
        }

        DB::beginTransaction();

        try {
            $uploadedDokumenPath = null;
            $supabaseStorage = new SupabaseStorage();

            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            $mkNama = trim($mataKuliah->nama);
            $mkNama = preg_replace('/\s+/', ' ', $mkNama);
            $mkNama = preg_replace('/[\\\\:*?"<>|]+/', '', $mkNama);
            $tahunAjaranSafe = str_replace('/', '-', $validated['tahun_ajaran']);
            $semesterSafe = ucfirst(strtolower($validated['semester']));
            
            $fileName = "RPS_{$mkNama}_{$tahunAjaranSafe}_{$semesterSafe}_V1";

            if ($creationMethod === 'generator') {
                // Map CPMK rows for PDF
                $createdCpmkRowsTemp = [];
                $kkoService = new KkoService();
                foreach ($validated['cpmk_rows'] as $row) {
                    $kkoLabel = $kkoService->label($row['kko']);
                    $deskripsi = $this->buildCpmkDeskripsi($kkoLabel, $row['objek'], $row['konteks']);
                    $cplObj = Cpl::find($row['cpl_id']);
                    $cplKode = $cplObj ? $cplObj->kode : '';
                    
                    $cplNum = '';
                    if (preg_match('/\d+/', $cplKode, $matches)) {
                        $cplNum = $matches[0];
                    } else {
                        $cplNum = $cplKode;
                    }
                    $cpmkKode = "CPMK " . intval($cplNum) . "." . trim($row['kode']);

                    $createdCpmkRowsTemp[] = [
                        'cpl_kode' => $cplKode,
                        'cpmk_kode' => $cpmkKode,
                        'cpmk_deskripsi' => $deskripsi,
                    ];
                }

                $cplIds = collect($validated['cpmk_rows'])->pluck('cpl_id')->filter()->unique()->values()->all();
                $cpls = Cpl::whereIn('id', $cplIds)->orderBy('kode')->get(['kode', 'deskripsi'])->toArray();

                $dosenIds = $validated['dosen_lain'] ?? [];
                $dosenIds[] = Auth::id();
                $dosenIds = array_unique($dosenIds);
                $dosens = User::whereIn('id', $dosenIds)->orderBy('name')->get(['name'])->toArray();

                // Normalisasi target_cpmk on pertemuan_data
                $normalizedPertemuan = $validated['pertemuan_data'];
                foreach ($normalizedPertemuan as $i => $meeting) {
                    if (isset($meeting['target_cpmk'])) {
                        $cpmkVal = $meeting['target_cpmk'];
                        if (is_array($cpmkVal)) {
                            $normalizedPertemuan[$i]['target_cpmk'] = implode(', ', array_map(function($v) {
                                return 'CPMK ' . $v;
                            }, $cpmkVal));
                        } else {
                            $normalizedPertemuan[$i]['target_cpmk'] = 'CPMK ' . $cpmkVal;
                        }
                    }
                }

                // Normalisasi cpmk on penilaian_data
                $normalizedPenilaian = $validated['penilaian_data'];
                foreach ($normalizedPenilaian as $i => $item) {
                    if (isset($item['cpmk']) && is_array($item['cpmk'])) {
                        $normalizedPenilaian[$i]['cpmk'] = implode(', ', array_map(function($v) {
                            return 'CPMK ' . $v;
                        }, $item['cpmk']));
                    }
                    if (isset($item['sub_rows'])) {
                        foreach ($item['sub_rows'] as $j => $sub) {
                            if (isset($sub['cpmk']) && is_array($sub['cpmk'])) {
                                $normalizedPenilaian[$i]['sub_rows'][$j]['cpmk'] = implode(', ', array_map(function($v) {
                                    return 'CPMK ' . $v;
                                }, $sub['cpmk']));
                            }
                        }
                    }
                }

                $pdfData = [
                    'tahun_akademik' => $validated['tahun_ajaran'],
                    'mata_kuliah' => [
                        'nama' => $mataKuliah->nama,
                        'kode' => $mataKuliah->kode,
                        'sks' => $mataKuliah->sks,
                    ],
                    'dosens' => $dosens,
                    'penilaian' => $normalizedPenilaian,
                    'cpl' => $cpls,
                    'cpmk' => $createdCpmkRowsTemp,
                    'deskripsi_mk' => $validated['deskripsi_mk'],
                    'pertemuan' => $normalizedPertemuan,
                    'referensi' => $validated['referensi_data'],
                    'semester' => $validated['semester'],
                ];

                $pdfContent = $this->generateRpsPdf($pdfData);

                // Save PDF to temp file
                $tempPath = tempnam(sys_get_temp_dir(), 'rps_');
                rename($tempPath, $tempPath . '.pdf');
                $tempPath = $tempPath . '.pdf';
                file_put_contents($tempPath, $pdfContent);

                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    "{$mataKuliah->kode}_{$validated['semester']}.pdf",
                    'application/pdf',
                    null,
                    true
                );

                $pathDokumen = $supabaseStorage->upload($uploadedFile, 'rps', 'rps', $fileName);

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                if (!$pathDokumen) {
                    throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
                }

                $uploadedDokumenPath = $pathDokumen;

            } else {
                // Upload File ke Supabase secara manual
                $file = $request->file('dokumen');
                $pathDokumen = $supabaseStorage->upload($file, 'rps', 'rps', $fileName);

                if (!$pathDokumen) {
                    throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
                }

                $uploadedDokumenPath = $pathDokumen;
            }

            // Simpan data ke Tabel RPS
            $rps = RpsDetail::create([
                'mk_id'           => $validated['mata_kuliah_id'],
                'semester'        => $validated['semester'],
                'tahun_ajaran'    => $validated['tahun_ajaran'],
                'dokumen'         => $uploadedDokumenPath,
                'status'          => RpsStatus::DIAJUKAN, 
                'catatan'         => $validated['catatan'] ?? null,
                'creation_method' => $creationMethod,
            ]);

            // Informasi Data Dosen Terkait
            $dosenIds = $validated['dosen_lain'] ?? [];
            $dosenIds[] = Auth::id(); // Always include user
            $dosenIds = array_unique($dosenIds); // Remove duplicates

            $createdCpmkRows = $this->createCpmksFromRows($validated['cpmk_rows'], (int) $validated['mata_kuliah_id']);

            $createdCpmkIds = collect($createdCpmkRows)
                ->pluck('cpmk_id')
                ->filter()
                ->values()
                ->all();
            
            // Simpan ke Tabel menggunakan sync() untuk cpls dan dosens
            $rps->cpls()->sync(collect($createdCpmkRows)->pluck('cpl_id')->filter()->unique()->values()->all());
            $rps->dosens()->sync($dosenIds);

            DB::table('bs_rps_cpmk')->insert(array_map(function (array $row) use ($rps) {
                return [
                    'rps_id' => $rps->id,
                    'cpl_id' => $row['cpl_id'],
                    'cpmk_id' => $row['cpmk_id'],
                ];
            }, $createdCpmkRows));

            // Simpan data terstruktur generator jika menggunakan mode generator
            if ($creationMethod === 'generator') {
                $rps->generateDetail()->create([
                    'created_by'     => Auth::id(),
                    'deskripsi_mk'   => $validated['deskripsi_mk'],
                    'penilaian_data' => $validated['penilaian_data'],
                    'pertemuan_data' => $validated['pertemuan_data'],
                    'referensi_data' => $validated['referensi_data'],
                ]);

                // Clear wizard cache
                session()->forget("rps_wizard_cache_" . Auth::id() . "_" . $validated['mata_kuliah_id']);
            }

            // Update is_rps menjadi 'TRUE' di bs_dosen_pengampu_mk untuk semua dosen
            DB::table('bs_dosen_pengampu_mk')
                ->whereIn('user_id', $dosenIds)
                ->where('mk_id', $validated['mata_kuliah_id'])
                ->update(['is_rps' => 'TRUE']);

            // Log audit
            DB::table('bs_audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'created',
                'subject_type' => 'rps',
                'subject_id' => $rps->id,
                'description' => 'RPS baru telah dibuat dan diajukan',
                'old_data' => null,
                'new_data' => json_encode([
                    'mk_id' => $rps->mk_id,
                    'semester' => $rps->semester,
                    'tahun_ajaran' => $rps->tahun_ajaran,
                    'status' => $rps->status->value,
                    'catatan' => $rps->catatan,
                ]),
                'created_at' => now(),
            ]);

            // Commit perubahan ke DB
            DB::commit();

            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('banksoal.rps.dosen.index'),
                    'message' => 'RPS berhasil disimpan dan sedang menunggu verifikasi GPM.'
                ]);
            }

            return redirect()->route('banksoal.rps.dosen.index')
                ->with('success', 'RPS berhasil disimpan dan sedang menunggu verifikasi GPM.');

        } catch (\Exception $e) {            
            // Rollback jika error
            DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('RPS Store Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Fetch CPL berdasarkan relasi di bs_mata_kuliah_cpl junction table
     */
    public function getCplByMk(int $mkId = null): JsonResponse
    {
        try {
            $query = Cpl::orderBy('kode');
            
            if ($mkId) {
                $query->whereHas('mataKuliahs', function ($q) use ($mkId) {
                    $q->where('bs_mata_kuliah.id', $mkId);
                });
            }

            $cpls = $query->get()
                ->map(function ($cpl) {
                    return [
                        'id' => $cpl->id,
                        'kode' => $cpl->kode,
                        'deskripsi' => $cpl->deskripsi,
                    ];
                });

            return response()->json($cpls);
        } catch (\Exception $e) {
            \Log::error('getCplByMk Error', ['error' => $e->getMessage(), 'mkId' => $mkId]);
            return response()->json(['error' => 'Error fetching CPL: ' . $e->getMessage()], 500);
        }
    }


    public function getCpmkByCpl(Request $request): JsonResponse
    {
        // Handle both single and array cpl_id parameters
        // Laravel automatically converts cpl_id[]=1&cpl_id[]=2 to array
        $cplIds = $request->input('cpl_id'); // Gunakan input() untuk handle array
        $mkId = $request->integer('mk_id') ?: null;

        $mapToResponse = function ($items) {
            return $items->map(function ($cpmk) {
                return [
                    'id' => $cpmk->id,
                    'kode' => $cpmk->kode,
                    'deskripsi' => $cpmk->deskripsi,
                    'cpl_id' => $cpmk->cpl_id ?? null,
                    'mk_id' => $cpmk->mk_id ?? null,
                ];
            });
        };

        try {
            $query = Cpmk::query()->orderBy('kode');

            if ($mkId) {
                $query->where(function ($subQuery) use ($mkId) {
                    $subQuery->where('mk_id', $mkId)
                        ->orWhereIn('id', function ($legacyQuery) use ($mkId) {
                            $legacyQuery->select('cpmk_id')
                                ->from('bs_cpl_cpmk')
                                ->where('mk_id', $mkId);
                        });
                });
            }

            // Jika cpl_id tidak disediakan, return CPMK sesuai filter MK atau semua data.
            if (!$cplIds) {
                return response()->json($mapToResponse($query->distinct()->get()));
            }

            // Ensure cplIds is an array
            $cplIds = is_array($cplIds) ? $cplIds : [$cplIds];

            // Filter out empty values
            $cplIds = array_filter($cplIds);

            if (empty($cplIds)) {
                return response()->json($mapToResponse($query->distinct()->get()));
            }

            \Log::info('getCpmkByCpl Request', ['cplIds' => $cplIds]);

            // Query CPMK dari kolom langsung, dengan fallback ke junction table untuk data lama.
            $query->where(function ($subQuery) use ($cplIds) {
                $subQuery->whereIn('cpl_id', $cplIds)
                    ->orWhereIn('id', function ($legacyQuery) use ($cplIds) {
                        $legacyQuery->select('cpmk_id')
                            ->from('bs_cpl_cpmk')
                            ->whereIn('cpl_id', $cplIds);
                    });
            });

            $cpmks = $mapToResponse($query->distinct()->get());

            \Log::info('getCpmkByCpl Response', ['count' => count($cpmks), 'cpmks' => $cpmks]);

            return response()->json($cpmks);
        } catch (\Exception $e) {
            \Log::error('getCpmkByCpl Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching CPMK: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Fetch semua user dengan role "dosen"
     * Exclude user yang sedang login
     * Untuk CREATE form: tampilkan semua dosen tanpa filter MK
     */
    public function getDosenByMk(Request $request): JsonResponse
    {
        try {
            // Query semua user dengan role "dosen" via Spatie model_has_roles
            $dosenList = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
                ->where('id', '!=', Auth::id())
                ->whereNull('suspended_at')
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                });

            \Log::info('getDosenByMk Response', ['count' => count($dosenList), 'dosenList' => $dosenList]);

            return response()->json($dosenList);
        } catch (\Exception $e) {
            \Log::error('getDosenByMk Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error fetching dosen'], 500);
        }
    }

    /**
     * Fetch CPMK yang sudah dipilih untuk RPS tertentu (untuk form edit)
     */
    public function getCpmkByRps(int $rpsId): JsonResponse
    {
        try {
            // Fetch CPMK dari junction table bs_rps_cpmk
            $cpmks = Cpmk::whereIn('id', function($query) use ($rpsId) {
                $query->select('cpmk_id')
                    ->from('bs_rps_cpmk')
                    ->where('rps_id', $rpsId);
            })
            ->orderBy('kode')
            ->get()
            ->map(function ($cpmk) {
                return [
                    'id' => $cpmk->id,
                    'kode' => $cpmk->kode,
                    'deskripsi' => $cpmk->deskripsi,
                ];
            });

            return response()->json($cpmks);
        } catch (\Exception $e) {
            \Log::error('getCpmkByRps Error', ['error' => $e->getMessage(), 'rpsId' => $rpsId]);
            return response()->json(['error' => 'Error fetching CPMK: ' . $e->getMessage()], 500);
        }
    }



    private function createCpmksFromRows(array $cpmkRows, ?int $mkId = null): array
    {
        $rows = collect($cpmkRows)
            ->map(function (array $row) {
                return [
                    'cpl_id' => (int) ($row['cpl_id'] ?? 0),
                    'kode' => $this->normalizeCpmkKode((string) ($row['kode'] ?? '')),
                    'kko' => trim((string) ($row['kko'] ?? '')),
                    'objek' => trim((string) ($row['objek'] ?? '')),
                    'konteks' => trim((string) ($row['konteks'] ?? '')),
                ];
            })
            ->filter(fn (array $row) => $row['cpl_id'] > 0 && $row['kode'] !== '' && $row['kko'] !== '' && $row['objek'] !== '')
            ->values();

        if ($rows->isEmpty()) {
            return [];
        }

        $createdRows = [];

        $kkoService = new KkoService();

        foreach ($rows as $row) {
            $kode = 'CPMK ' . $row['kode'];
            // Map KKO code to descriptive label for storage
            $kkoLabel = $kkoService->label($row['kko']);
            $deskripsi = $this->buildCpmkDeskripsi($kkoLabel, $row['objek'], $row['konteks']);

            $cpmk = Cpmk::create([
                'kode' => $kode,
                'deskripsi' => $deskripsi,
                'mk_id' => $mkId,
                'cpl_id' => $row['cpl_id'],
            ]);

            $createdRows[] = [
                'cpl_id' => $row['cpl_id'],
                'cpmk_id' => $cpmk->id,
                'kode' => $kode,
                'deskripsi' => $deskripsi,
            ];
        }

        return $createdRows;
    }

    private function normalizeCpmkKode(string $kode): string
    {
        $kode = trim($kode);
        $kode = preg_replace('/^CPMK\s*/i', '', $kode) ?? $kode;

        return preg_replace('/\s+/', '', $kode) ?? $kode;
    }

    private function buildCpmkDeskripsi(string $kkoLabel, string $objek, string $konteks = ''): string
    {
        $kkoLabel = trim($kkoLabel);
        $objek = trim($objek);
        $konteks = trim($konteks);
        
        if ($konteks !== '') {
            return "({$kkoLabel}) ({$objek}) ({$konteks})";
        }
        
        return "({$kkoLabel}) ({$objek})";
    }

    public function getMkByDosen(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Selalu gunakan tahun dan semester SEKARANG (server time)
            // Bukan dari query parameter frontend
            $currentYear = (int) now()->format('Y');
            $semester = now()->month >= 7 ? 'Ganjil' : 'Genap';
            $semesterParity = now()->month >= 7 ? 1 : 0; // 1 = Ganjil, 0 = Genap
            $academicYear = $semester === 'Ganjil'
                ? $currentYear . '/' . ($currentYear + 1)
                : ($currentYear - 1) . '/' . $currentYear;

            // Cari MK dengan RPS aktif di semester/tahun SAAT INI
            $mkIdsWithActiveRps = RpsDetail::whereIn('status', [
                    RpsStatus::DIAJUKAN->value,
                    RpsStatus::REVISI->value,
                    RpsStatus::DISETUJUI->value,
                ])
                ->where('semester', $semester)
                ->where('tahun_ajaran', $academicYear)
                ->pluck('mk_id')
                ->unique();

            $mataKuliahs = MataKuliah::whereNotIn('id', $mkIdsWithActiveRps)
                ->whereHas('dosenPengampu', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('is_active', true)
                ->orderBy('nama')
                ->get()
                ->map(function ($mk) {
                    return [
                        'id' => $mk->id,
                        'kode' => $mk->kode,
                        'nama' => $mk->nama,
                        'sks' => $mk->sks,
                    ];
                });

            return response()->json($mataKuliahs);
        } catch (\Exception $e) {
            Log::error('getMkByDosen Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error fetching mata kuliah'], 500);
        }
    }

    public function previewDokumen(int $rpsId)
    {
        try {
            $rpsService = app(\Modules\BankSoal\Services\RpsService::class);
            $data = $rpsService->getRpsReviewData($rpsId);
            $rps = $data['rps'];

            $fileUrl = null;
            $downloadUrl = null;
            $errorMessage = null;

            if ($rps->dokumen) {
                $supabaseStorage = new SupabaseStorage();
                $fileUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');
                
                try {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(2)->get($fileUrl);
                    if ($response->status() === 404) {
                        // Coba lakukan pencarian self-healing dengan nama berkas baru yang mungkin sudah direname
                        $mkName = trim($rps->mk_nama ?? 'MataKuliah');
                        $mkName = preg_replace('/\s+/', ' ', $mkName);
                        $tahunAjaranSafe = str_replace('/', '-', (string) $rps->tahun_ajaran);
                        $semesterSafe = ucfirst(strtolower((string) $rps->semester));
                        
                        $baseFileName = sprintf('RPS_%s_%s_%s', $mkName, $tahunAjaranSafe, $semesterSafe);
                        $baseFileName = preg_replace('/[\\\\:*?"<>|]+/', '', $baseFileName);
                        $baseFileName = trim((string) $baseFileName, " \t\n\r\0\x0B._");
                        
                        $healedPath = 'rps/' . $baseFileName . '.pdf';
                        $healedUrl = $supabaseStorage->getPublicUrl($healedPath, 'rps');
                        
                        $healedResponse = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(2)->get($healedUrl);
                        if ($healedResponse->status() === 200) {
                            // Berkas ditemukan di Supabase! Lakukan self-healing sinkronisasi database
                            DB::table('bs_rps_detail')->where('id', $rpsId)->update(['dokumen' => $healedPath]);
                            $rps->dokumen = $healedPath;
                            $fileUrl = $healedUrl;
                            $errorMessage = null;
                        } else {
                            $fileUrl = null;
                            $errorMessage = 'Berkas PDF tidak ditemukan di server penyimpanan (Supabase Storage). Silakan hubungi dosen pengampu atau unggah ulang dokumen RPS.';
                        }
                    }
                } catch (\Exception $ex) {
                    // Fallback to let the browser attempt loading if network check fails/timeouts
                }

                if ($fileUrl) {
                    $downloadUrl = route('banksoal.rps.dosen.download', ['rpsId' => $rpsId]);
                }
            } else {
                $errorMessage = 'Dokumen RPS belum diunggah atau tidak dapat ditemukan.';
            }

            $data['fileUrl'] = $fileUrl;
            $data['downloadUrl'] = $downloadUrl;
            $data['errorMessage'] = $errorMessage;

            return view('banksoal::pages.rps.preview-page', $data);

        } catch (\Exception $e) {
            \Log::error('previewDokumen Error', ['rps_id' => $rpsId, 'error' => $e->getMessage()]);
            $rps = null;
            $fileUrl = null;
            $downloadUrl = null;
            $errorMessage = 'Terjadi kesalahan saat memuat dokumen: ' . $e->getMessage();
            
            $data = [
                'rps' => null,
                'fileUrl' => null,
                'downloadUrl' => null,
                'errorMessage' => $errorMessage,
                'parameters' => collect(),
                'existingReview' => null,
                'history' => collect(),
                'selectedCpls' => collect(),
                'cplCpmkMappings' => collect(),
                'draftCpmkItems' => collect(),
                'dosenPengampu' => collect(),
                'totalBobot' => 0
            ];
            return view('banksoal::pages.rps.preview-page', $data);
        }
    }

    public function downloadDokumen(int $rpsId)
    {
        try {
            $rps = RpsDetail::with('mataKuliah')->findOrFail($rpsId);

            if (!$rps->dokumen) {
                abort(404, 'Dokumen tidak ditemukan');
            }

            $supabaseStorage = new SupabaseStorage();
            $publicUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');

            $downloadName = basename((string) $rps->dokumen);
            $separator = str_contains($publicUrl, '?') ? '&' : '?';

            return redirect($publicUrl . $separator . 'download=' . urlencode($downloadName));
        } catch (\Exception $e) {
            \Log::error('downloadDokumen Error', ['rps_id' => $rpsId, 'error' => $e->getMessage()]);
            abort(404, 'Dokumen tidak ditemukan');
        }
    }

    /**
     * Tampilkan form edit RPS
     */
    public function edit(int $rpsId): \Illuminate\View\View
    {
        $user = Auth::user()->load('lecturer');
        $rps = RpsDetail::with(['mataKuliah', 'cpls', 'cpmks', 'dosens'])->findOrFail($rpsId);

        // Cek apakah user adalah salah satu dosen yang terkait dengan RPS ini
        $isAuthorized = $rps->dosens->contains('id', $user->id);
        abort_if(!$isAuthorized, 403, 'Anda tidak memiliki akses untuk mengedit RPS ini.');

        // Cek apakah status memungkinkan edit (DIAJUKAN atau REVISI)
        $editableStatuses = [RpsStatus::DIAJUKAN->value, RpsStatus::REVISI->value];
        abort_if(!in_array($rps->status->value, $editableStatuses), 403, 'RPS dengan status ' . $rps->status->label() . ' tidak dapat diedit.');

        // Fetch dosen yang sudah terpilih (exclude current user)
        $selectedDosenIds = $rps->dosens->pluck('id')->toArray();

        // Fetch tahun ajaran
        $currentYear = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        // Fetch Active Periode (untuk cek apakah upload masih dibuka)
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        $isUploadOpen = true;
        
        if ($activePeriode) {
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            $deadline = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end = $deadline->endOfDay();

            $isUploadOpen = $now->between($start, $end);
        }

        // Reverse-engineer CPMK yang sudah ada menjadi baris form
        $existingCpmkRows = old('cpmk_rows', $this->parseCpmkRows($rps->cpmks, $rpsId));

        // Fetch RPS audit history (activity log)
        $history = DB::table('bs_audit_logs')
            ->where('subject_type', 'rps')
            ->where('subject_id', $rpsId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                // Parse new_data jika string JSON
                $newData = $item->new_data;
                if (is_string($newData)) {
                    $newData = json_decode($newData, true) ?? [];
                } elseif (is_array($newData)) {
                    $newData = $newData;
                } else {
                    $newData = [];
                }
                
                if ($item->action === 'created') {
                    $item->processed_description = 'RPS baru telah dibuat dan diajukan';
                } elseif ($item->action === 'updated') {
                    $item->processed_description = 'RPS telah diperbarui';
                    if (!empty($newData)) {
                        $changes = array_keys($newData);
                        if (in_array('status', $changes)) {
                            $status = $newData['status'] ?? null;
                            $item->processed_description = 'Status RPS diubah ke: ' . ucfirst($status ?? 'tidak diketahui');
                        }

                        if (!empty($newData['catatan'])) {
                            $item->processed_description .= ' Catatan: ' . $newData['catatan'];
                        }
                    }
                } elseif ($item->action === 'disetujui') {
                    // Cek di new_data terlebih dahulu
                    if (!empty($newData['nilai_akhir']) || !empty($newData['catatan'])) {
                        $nilaiAkhir = $newData['nilai_akhir'] ?? 0;
                        $item->processed_description = 'RPS telah disetujui oleh GPM. Nilai: ' . $nilaiAkhir . '/100';
                        if (!empty($newData['catatan'])) {
                            $item->processed_description .= ' Catatan: ' . $newData['catatan'];
                        }
                    } else {
                        // Fallback ke description jika new_data kosong
                        $item->processed_description = $item->description ?? 'RPS telah disetujui oleh GPM';
                    }
                } elseif ($item->action === 'revisi') {
                    // Cek di new_data terlebih dahulu
                    if (!empty($newData['catatan'])) {
                        $item->processed_description = 'RPS dikembalikan untuk revisi. Catatan: ' . $newData['catatan'];
                    } else {
                        // Fallback ke description jika new_data kosong
                        $item->processed_description = $item->description ?? 'RPS dikembalikan untuk revisi';
                    }
                } else {
                    $item->processed_description = $item->description ?? ucfirst($item->action);
                }
                
                return $item;
            });

        $generateDetail = null;
        if ($rps->creation_method === 'generator') {
            $generateDetail = $rps->generateDetail;
        }

        return view('banksoal::pages.rps.dosen.edit', compact(
            'rps',
            'tahunAjarans',
            'selectedDosenIds',
            'existingCpmkRows',
            'isUploadOpen',
            'history',
            'generateDetail'
        ));
    }

            /**
     * Update RPS yang sudah ada
     */
    public function update(int $rpsId, Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user()->load('lecturer');
        $rps = RpsDetail::with('dosens')->findOrFail($rpsId);
        $isRevisionResubmit = $rps->status->value === RpsStatus::REVISI->value;

        // Cek autorisasi
        $isAuthorized = $rps->dosens->contains('id', $user->id);
        abort_if(!$isAuthorized, 403, 'Anda tidak memiliki akses untuk mengedit RPS ini.');

        // Cek status
        $editableStatuses = [RpsStatus::DIAJUKAN->value, RpsStatus::REVISI->value];
        abort_if(!in_array($rps->status->value, $editableStatuses), 403, 'RPS dengan status ' . $rps->status->label() . ' tidak dapat diedit.');

        $creationMethod = $request->input('creation_method', $rps->creation_method ?? 'upload');

        // Validasi Input
        $rules = [
            'mata_kuliah_id'         => ['required', 'exists:bs_mata_kuliah,id'],
            'dosen_lain'             => ['nullable', 'array'],
            'dosen_lain.*'           => ['exists:users,id'],
            'semester'               => ['required', 'in:Ganjil,Genap'],
            'tahun_ajaran'           => ['required', 'string'],
            'cpmk_rows'              => ['required', 'array', 'min:1'],
            'cpmk_rows.*.cpl_id'     => ['required', 'exists:bs_cpl,id'],
            'cpmk_rows.*.kode'       => ['required', 'string', 'max:20'],
            'cpmk_rows.*.kko'        => ['required', 'string', 'max:20'],
            'cpmk_rows.*.objek'      => ['required', 'string', 'max:1000'],
            'cpmk_rows.*.konteks'    => ['nullable', 'string', 'max:1000'],
            'catatan'                => [$isRevisionResubmit ? 'required' : 'nullable', 'string', 'max:1000'],
        ];

        if ($creationMethod === 'upload') {
            $rules['dokumen'] = [$isRevisionResubmit ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:5120'];
        } else {
            $rules['deskripsi_mk'] = ['required', 'string'];
            $rules['penilaian_data'] = ['required', 'array', 'min:1'];
            $rules['pertemuan_data'] = ['required', 'array', 'min:1'];
            $rules['referensi_data'] = ['required', 'array', 'min:1'];
        }

        $validated = $request->validate($rules, [
            'dokumen.max'            => 'Ukuran file maksimal 5MB',
            'dokumen.mimes'          => 'Hanya menerima File berformat PDF',
            'dokumen.required'       => 'File RPS baru wajib diunggah saat revisi',
            'catatan.required'       => 'Catatan revisi wajib diisi',
            'cpmk_rows.required'     => 'Minimal satu baris CPMK harus diisi',
            'deskripsi_mk.required'  => 'Deskripsi MK wajib diisi',
            'penilaian_data.required'=> 'Data penilaian wajib diisi',
            'pertemuan_data.required'=> 'Data pertemuan wajib diisi',
            'referensi_data.required'=> 'Data referensi wajib diisi',
        ]);

        // Cek apakah Periode RPS Aktif
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        if (!$activePeriode) {
            return back()->withInput()->with('error', 'Sesi unggah RPS sedang ditutup atau belum ada jadwal yang aktif.');
        }

        $now = now('Asia/Jakarta');
        $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
        $end = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();

        if (!$now->between($start, $end)) {
            return back()->withInput()->with('error', 'Di luar jadwal unggah RPS. Tenggat waktu sudah terlewati atau jadwal belum dimulai.');
        }

        DB::beginTransaction();

        try {
            $oldData = [
                'mk_id' => $rps->mk_id,
                'semester' => $rps->semester,
                'tahun_ajaran' => $rps->tahun_ajaran,
                'status' => $rps->status->value,
                'dokumen' => $rps->dokumen,
                'catatan' => $rps->catatan,
                'cpl_ids' => $rps->cpls()->pluck('id')->toArray(),
                'cpmk_ids' => $rps->cpmks()->pluck('id')->toArray(),
                'dosen_ids' => $rps->dosens()->pluck('id')->toArray(),
            ];

            // Naming components
            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            $mkNama = trim($mataKuliah->nama);
            $mkNama = preg_replace('/\s+/', ' ', $mkNama);
            $mkNama = preg_replace('/[\\\\:*?"<>|]+/', '', $mkNama);
            $tahunAjaranSafe = str_replace('/', '-', $validated['tahun_ajaran']);
            $semesterSafe = ucfirst(strtolower($validated['semester']));
            
            // Version count based on audit logs of submissions
            $versionCount = DB::table('bs_audit_logs')
                ->where('subject_type', 'rps')
                ->where('subject_id', $rps->id)
                ->whereIn('action', ['created', 'updated'])
                ->count();
            $version = 'V' . ($versionCount + 1);
            
            // Format nama file: RPS_Nama MK_Tahun Ajaran_Semester_Versi
            $fileName = "RPS_{$mkNama}_{$tahunAjaranSafe}_{$semesterSafe}_{$version}";

            $supabaseStorage = new SupabaseStorage();
            $uploadedDokumenPath = $rps->dokumen;

            if ($creationMethod === 'generator') {
                // Generate PDF from wizard input
                $createdCpmkRowsTemp = [];
                $kkoService = new KkoService();
                foreach ($validated['cpmk_rows'] as $row) {
                    $kkoLabel = $kkoService->label($row['kko']);
                    $deskripsi = $this->buildCpmkDeskripsi($kkoLabel, $row['objek'], $row['konteks']);
                    $cplObj = Cpl::find($row['cpl_id']);
                    $cplKode = $cplObj ? $cplObj->kode : '';
                    
                    $cplNum = '';
                    if (preg_match('/\d+/', $cplKode, $matches)) {
                        $cplNum = $matches[0];
                    } else {
                        $cplNum = $cplKode;
                    }
                    $cpmkKode = "CPMK " . intval($cplNum) . "." . trim($row['kode']);

                    $createdCpmkRowsTemp[] = [
                        'cpl_kode' => $cplKode,
                        'cpmk_kode' => $cpmkKode,
                        'cpmk_deskripsi' => $deskripsi,
                    ];
                }

                $cplIds = collect($validated['cpmk_rows'])->pluck('cpl_id')->filter()->unique()->values()->all();
                $cpls = Cpl::whereIn('id', $cplIds)->orderBy('kode')->get(['kode', 'deskripsi'])->toArray();

                $dosenIds = $validated['dosen_lain'] ?? [];
                $dosenIds[] = Auth::id();
                $dosenIds = array_unique($dosenIds);
                $dosens = User::whereIn('id', $dosenIds)->orderBy('name')->get(['name'])->toArray();

                // Normalisasi target_cpmk on pertemuan_data
                $normalizedPertemuan = $validated['pertemuan_data'];
                foreach ($normalizedPertemuan as $i => $meeting) {
                    if (isset($meeting['target_cpmk'])) {
                        $cpmkVal = $meeting['target_cpmk'];
                        if (is_array($cpmkVal)) {
                            $normalizedPertemuan[$i]['target_cpmk'] = implode(', ', array_map(function($v) {
                                return 'CPMK ' . $v;
                            }, $cpmkVal));
                        } else {
                            $normalizedPertemuan[$i]['target_cpmk'] = 'CPMK ' . $cpmkVal;
                        }
                    }
                }

                // Normalisasi cpmk on penilaian_data
                $normalizedPenilaian = $validated['penilaian_data'];
                foreach ($normalizedPenilaian as $i => $item) {
                    if (isset($item['cpmk']) && is_array($item['cpmk'])) {
                        $normalizedPenilaian[$i]['cpmk'] = implode(', ', array_map(function($v) {
                            return 'CPMK ' . $v;
                        }, $item['cpmk']));
                    }
                    if (isset($item['sub_rows'])) {
                        foreach ($item['sub_rows'] as $j => $sub) {
                            if (isset($sub['cpmk']) && is_array($sub['cpmk'])) {
                                $normalizedPenilaian[$i]['sub_rows'][$j]['cpmk'] = implode(', ', array_map(function($v) {
                                    return 'CPMK ' . $v;
                                }, $sub['cpmk']));
                            }
                        }
                    }
                }

                $pdfData = [
                    'tahun_akademik' => $validated['tahun_ajaran'],
                    'mata_kuliah' => [
                        'nama' => $mataKuliah->nama,
                        'kode' => $mataKuliah->kode,
                        'sks' => $mataKuliah->sks,
                    ],
                    'dosens' => $dosens,
                    'penilaian' => $normalizedPenilaian,
                    'cpl' => $cpls,
                    'cpmk' => $createdCpmkRowsTemp,
                    'deskripsi_mk' => $validated['deskripsi_mk'],
                    'pertemuan' => $normalizedPertemuan,
                    'referensi' => $validated['referensi_data'],
                    'semester' => $validated['semester'],
                ];

                $pdfContent = $this->generateRpsPdf($pdfData);

                # Save PDF to temp file
                $tempPath = tempnam(sys_get_temp_dir(), 'rps_');
                rename($tempPath, $tempPath . '.pdf');
                $tempPath = $tempPath . '.pdf';
                file_put_contents($tempPath, $pdfContent);

                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    "{$mataKuliah->kode}_{$validated['semester']}.pdf",
                    'application/pdf',
                    null,
                    true
                );

                $pathDokumen = $supabaseStorage->upload($uploadedFile, 'rps', 'rps', $fileName);

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                if (!$pathDokumen) {
                    throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
                }

                $uploadedDokumenPath = $pathDokumen;

            } else {
                # Update dokumen jika ada file baru
                if ($request->hasFile('dokumen')) {
                    $file = $request->file('dokumen');
                    $pathDokumen = $supabaseStorage->upload($file, 'rps', 'rps', $fileName);

                    if (!$pathDokumen) {
                        throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
                    }

                    $uploadedDokumenPath = $pathDokumen;
                }
            }

            # Update data RPS
            $rps->mk_id = $validated['mata_kuliah_id'];
            $rps->semester = $validated['semester'];
            $rps->tahun_ajaran = $validated['tahun_ajaran'];
            $rps->catatan = $validated['catatan'];
            $rps->dokumen = $uploadedDokumenPath;
            
            # Jika status saat ini 'revisi', ubah menjadi 'diajukan' (re-submission setelah revisi)
            if ($isRevisionResubmit) {
                $rps->status = RpsStatus::DIAJUKAN;
            }
            
            $rps->save();

            # Buat CPMK baru dari baris form (format sama dengan store)
            $duplicateCpmkCodes = collect($validated['cpmk_rows'])
                ->map(fn (array $row) => $this->normalizeCpmkKode((string) ($row['kode'] ?? '')))
                ->filter()
                ->duplicates();

            if ($duplicateCpmkCodes->isNotEmpty()) {
                throw new \Exception('Kode CPMK tidak boleh sama dalam satu RPS. Silakan periksa kembali baris CPMK yang diisi.');
            }

            $createdCpmkRows = $this->createCpmksFromRows($validated['cpmk_rows'], (int) $validated['mata_kuliah_id']);

            $createdCpmkIds = collect($createdCpmkRows)
                ->pluck('cpmk_id')
                ->filter()
                ->values()
                ->all();

            # Update relasi CPL, CPMK, dan Dosen
            $dosenIds = $validated['dosen_lain'] ?? [];
            $dosenIds[] = $user->id;
            $dosenIds = array_unique($dosenIds); # Remove duplicates

            $rps->cpls()->sync(collect($createdCpmkRows)->pluck('cpl_id')->filter()->unique()->values()->all());
            $rps->dosens()->sync($dosenIds);

            DB::table('bs_rps_cpmk')->where('rps_id', $rps->id)->delete();
            DB::table('bs_rps_cpmk')->insert(array_map(function (array $row) use ($rps) {
                return [
                    'rps_id'  => $rps->id,
                    'cpl_id'  => $row['cpl_id'],
                    'cpmk_id' => $row['cpmk_id'],
                ];
            }, $createdCpmkRows));

            # Simpan / update generator detail
            if ($creationMethod === 'generator') {
                $rps->generateDetail()->updateOrCreate(
                    ['rps_detail_id' => $rps->id],
                    [
                        'created_by'     => Auth::id(),
                        'deskripsi_mk'   => $validated['deskripsi_mk'],
                        'penilaian_data' => $validated['penilaian_data'],
                        'pertemuan_data' => $validated['pertemuan_data'],
                        'referensi_data' => $validated['referensi_data'],
                    ]
                );

                # Clear wizard cache
                session()->forget("rps_wizard_cache_" . Auth::id() . "_" . $validated['mata_kuliah_id']);
            }

            # Update is_rps di bs_dosen_pengampu_mk
            DB::table('bs_dosen_pengampu_mk')
                ->whereIn('user_id', $dosenIds)
                ->where('mk_id', $validated['mata_kuliah_id'])
                ->update(['is_rps' => 'TRUE']);

            # Log audit
            BsAuditLog::create([
                'user_id'      => Auth::id(),
                'action'       => 'updated',
                'subject_type' => 'rps',
                'subject_id'   => $rpsId,
                'description'  => 'RPS telah diperbarui oleh dosen' . (!empty(trim((string) ($validated['catatan'] ?? ''))) ? '. Catatan: ' . trim((string) $validated['catatan']) : ''),
                'old_data'     => $oldData,
                'new_data'     => [
                    'mk_id'       => $rps->mk_id,
                    'semester'    => $rps->semester,
                    'tahun_ajaran'=> $rps->tahun_ajaran,
                    'status'      => $rps->status->value,
                    'catatan'     => $rps->catatan,
                    'cpl_ids'     => $rps->cpls()->pluck('id')->toArray(),
                    'cpmk_ids'    => $rps->cpmks()->pluck('id')->toArray(),
                    'dosen_ids'   => $rps->dosens()->pluck('id')->toArray(),
                ],
                'created_at'   => now(),
            ]);

            DB::commit();

            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('banksoal.rps.dosen.index'),
                    'message' => 'RPS berhasil diperbarui.'
                ]);
            }

            return redirect()->route('banksoal.rps.dosen.index')
                ->with('success', 'RPS berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('RPS Update Error', [
                'rps_id' => $rpsId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }public function destroy(int $rpsId, Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user()->load('lecturer');
        $rps = RpsDetail::with('dosens')->findOrFail($rpsId);

        // Cek autorisasi
        $isAuthorized = $rps->dosens->contains('id', $user->id);
        abort_if(!$isAuthorized, 403, 'Anda tidak memiliki akses untuk menghapus RPS ini.');

        // Cek status - hanya DIAJUKAN dan REVISI yang bisa dihapus
        $deletableStatuses = [RpsStatus::DIAJUKAN->value, RpsStatus::REVISI->value];
        abort_if(!in_array($rps->status->value, $deletableStatuses), 403, 'RPS dengan status ' . $rps->status->label() . ' tidak dapat dihapus.');

        DB::beginTransaction();

        try {
            // Hapus file dari Supabase
            if ($rps->dokumen) {
                $supabaseStorage = new SupabaseStorage();
                $supabaseStorage->delete($rps->dokumen);
            }

            // Dapatkan ID CPMK sebelum detach
            $cpmkIds = $rps->cpmks()->pluck('bs_cpmk.id')->toArray();

            // Hapus relasi di junction table
            $rps->cpls()->detach();
            $rps->cpmks()->detach();
            $rps->dosens()->detach();

            // Hapus CPMK master record
            if (!empty($cpmkIds)) {
                DB::table('bs_cpmk')->whereIn('id', $cpmkIds)->delete();
            }

            // Hapus RPS record (hard delete)
            $rps->forceDelete();

            // Log audit
            DB::table('bs_audit_logs')->insert([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'subject_type' => 'rps',
                'subject_id' => $rpsId,
                'description' => 'RPS telah dihapus oleh dosen',
                'old_data' => null,
                'new_data' => null,
                'created_at' => now(),
            ]);

            DB::commit();

            // Cek apakah request dari AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RPS berhasil dihapus.'
                ]);
            }

            return redirect()->route('banksoal.rps.dosen.index')
                ->with('success', 'RPS berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('RPS Delete Error', [
                'rps_id' => $rpsId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Cek apakah request dari AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus RPS: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menghapus RPS: ' . $e->getMessage());
        }
    }

    /**
     * Return form edit RPS untuk modal (partial view)
     */
    public function editModal(int $rpsId): \Illuminate\View\View
    {
        $user = Auth::user()->load('lecturer');
        $rps = RpsDetail::with(['mataKuliah', 'cpls', 'cpmks', 'dosens'])->findOrFail($rpsId);

        // Cek autorisasi
        $isAuthorized = $rps->dosens->contains('id', $user->id);
        abort_if(!$isAuthorized, 403, 'Anda tidak memiliki akses untuk mengedit RPS ini.');

        // Cek status
        $editableStatuses = [RpsStatus::DIAJUKAN->value, RpsStatus::REVISI->value];
        abort_if(!in_array($rps->status->value, $editableStatuses), 403, 'RPS dengan status ' . $rps->status->label() . ' tidak dapat diedit.');

        // Fetch data yang sama dengan edit method
        $mataKuliahs = MataKuliah::where('is_active', true)->orderBy('nama')->get();

        $selectedCplIds = $rps->cpls->pluck('id')->toArray();
        $selectedCpmkIds = $rps->cpmks->pluck('id')->toArray();
        $selectedDosenIds = $rps->dosens->pluck('id')->toArray();

        $currentYear = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        $isUploadOpen = true;
        
        if ($activePeriode) {
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            $deadline = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end = $deadline->endOfDay();

            $isUploadOpen = $now->between($start, $end);
        }

        return view('banksoal::partials.dosen.rps-edit-modal-form', compact(
            'rps',
            'mataKuliahs',
            'tahunAjarans',
            'selectedCplIds',
            'selectedCpmkIds',
            'selectedDosenIds',
            'isUploadOpen',
            'rpsId'
        ));
    }

    public function saveWizardCache(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $mkId = $request->input('mata_kuliah_id');
        if (!$mkId) {
            return response()->json(['success' => false, 'message' => 'Mata kuliah ID wajib diisi.'], 400);
        }

        $sessionKey = "rps_wizard_cache_{$userId}_{$mkId}";
        $existingData = session()->get($sessionKey, []);
        $newData = $request->except(['_token']);
        $mergedData = array_merge($existingData, $newData);
        
        session()->put($sessionKey, $mergedData);
        session()->save();

        return response()->json(['success' => true, 'message' => 'Draft progress berhasil disimpan.']);
    }

    public function clearWizardCache(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $mkId = $request->input('mata_kuliah_id');
        if ($mkId) {
            session()->forget("rps_wizard_cache_{$userId}_{$mkId}");
        }
        return response()->json(['success' => true]);
    }

    public function getWizardCache(int $mkId): JsonResponse
    {
        $userId = Auth::id();
        $sessionKey = "rps_wizard_cache_{$userId}_{$mkId}";
        $data = session()->get($sessionKey, null);
        return response()->json(['success' => true, 'data' => $data]);
    }

    private function generateRpsPdf(array $data)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('banksoal::pdf.rps', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->output();
    }
}
