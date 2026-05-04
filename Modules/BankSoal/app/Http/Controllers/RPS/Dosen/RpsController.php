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
use Modules\BankSoal\Models\Shared\MataKuliah;
use Modules\BankSoal\Models\Shared\Cpl;
use Modules\BankSoal\Models\Shared\Cpmk;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Models\PeriodeRps;
use Modules\BankSoal\Enums\RpsStatus;

class RpsController extends Controller
{
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
            ->whereRaw('semester % 2 = ?', [$semesterParity])
            ->orderBy('nama')
            ->get();

        // Fetch riwayat RPS — hanya tampilkan RPS dimana dosen ini terdaftar
        // (baik sebagai pembuat maupun dosen pengampu tambahan via pivot bs_rps_dosen)
        // Eager load mataKuliah dan dosens untuk menghindari N+1 queries
        // Riwayat Pengajuan RPS - fetch all untuk klien-side pagination
        $riwayat = RpsDetail::with('mataKuliah', 'dosens')
            ->whereHas('dosens', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
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
                        ->whereRaw('semester % 2 = ?', [$semesterParity])
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

    // Proses penyimpanan RPS baru
    public function store(Request $request): RedirectResponse
    {
        // Validasi Input 
        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'exists:bs_mata_kuliah,id'],
            'dosen_lain'     => ['nullable', 'array'],
            'dosen_lain.*'   => ['exists:users,id'],
            'semester'       => ['required', 'in:Ganjil,Genap'],
            'tahun_ajaran'   => ['required', 'string'],
            'cpl_id'         => ['required', 'array'],
            'cpl_id.*'       => ['exists:bs_cpl,id'],
            'cpmk_id'        => ['required', 'array'],
            'cpmk_id.*'      => ['exists:bs_cpmk,id'],
            'dokumen'        => ['required', 'file', 'mimes:pdf', 'max:1024'], 
        ], [
            'dokumen.max' => 'Ukuran file maksimal 1MB',
            'dokumen.mimes' => 'Hanya menerima File berformat PDF',
            'dokumen.required' => 'File RPS harus diunggah',
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

        DB::beginTransaction();

        try {
            // Upload File ke Supabase
            $file = $request->file('dokumen');
            
            // Ambil informasi yang diperlukan untuk naming
            $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
            $kodeMk = $mataKuliah->kode;  
            $tahun = now()->year;          
            $semester = $validated['semester'];
            $employeeNumber = Auth::user()->load('lecturer')->lecturer->employee_number;
            
            // Format nama file: kodeMK_tahun_semester_employeeNumber
            $fileName = "{$kodeMk}_{$tahun}_{$semester}_{$employeeNumber}";
            
            $supabaseStorage = new SupabaseStorage();
            $pathDokumen = $supabaseStorage->upload($file, 'rps', 'rps', $fileName);

            if (!$pathDokumen) {
                throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
            }

            // Simpan data ke Tabel RPS
            $rps = RpsDetail::create([
                'mk_id'        => $validated['mata_kuliah_id'],
                'semester'     => $validated['semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'dokumen'      => $pathDokumen,
                'status'       => RpsStatus::DIAJUKAN, 
            ]);

            // Informasi Data Dosen Terkait
            $dosenIds = $validated['dosen_lain'] ?? [];
            $dosenIds[] = Auth::id(); // Always include user
            $dosenIds = array_unique($dosenIds); // Remove duplicates
            
            // Simpan ke Tabel menggunakan sync()
            $rps->cpls()->sync($validated['cpl_id']);
            $rps->cpmks()->sync($validated['cpmk_id']);
            $rps->dosens()->sync($dosenIds);

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
                ]),
                'created_at' => now(),
            ]);

            // Commit perubahan ke DB
            DB::commit();

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
            
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Fetch CPL berdasarkan relasi di bs_mata_kuliah_cpl junction table
     */
    public function getCplByMk(int $mkId = null): JsonResponse
    {
        try {
            // Always return all CPL from bs_cpl table (regardless of mkId)
            // MK parameter is ignored - CPL selection is independent of MK choice
            $cpls = Cpl::orderBy('kode')
                ->get()
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

        try {
            // Jika cpl_id tidak disediakan, return semua CPMK (untuk CREATE form)
            if (!$cplIds) {
                $cpmks = Cpmk::orderBy('kode')
                    ->get()
                    ->map(function ($cpmk) {
                        return [
                            'id' => $cpmk->id,
                            'kode' => $cpmk->kode,
                            'deskripsi' => $cpmk->deskripsi,
                        ];
                    });
                return response()->json($cpmks);
            }

            // Ensure cplIds is an array
            $cplIds = is_array($cplIds) ? $cplIds : [$cplIds];

            // Filter out empty values
            $cplIds = array_filter($cplIds);

            if (empty($cplIds)) {
                $cpmks = Cpmk::orderBy('kode')
                    ->get()
                    ->map(function ($cpmk) {
                        return [
                            'id' => $cpmk->id,
                            'kode' => $cpmk->kode,
                            'deskripsi' => $cpmk->deskripsi,
                        ];
                    });
                return response()->json($cpmks);
            }

            \Log::info('getCpmkByCpl Request', ['cplIds' => $cplIds]);

            // Query CPMK melalui junction table bs_cpl_cpmk untuk semua CPL yang dipilih
            $cpmks = Cpmk::whereIn('id', function($query) use ($cplIds) {
                $query->select('cpmk_id')
                    ->from('bs_cpl_cpmk')
                    ->whereIn('cpl_id', $cplIds);
            })
                ->distinct()
                ->orderBy('kode')
                ->get()
                ->map(function ($cpmk) {
                    return [
                        'id' => $cpmk->id,
                        'kode' => $cpmk->kode,
                        'deskripsi' => $cpmk->deskripsi,
                    ];
                });

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
            // Query semua user dengan role "dosen" (role_id = 3)
            // Menggunakan direct join ke user_roles table untuk fetch all dosen
            $dosenList = User::whereIn('id', function($query) {
                    $query->select('user_id')
                        ->from('user_roles')
                        ->where('role_id', 3); // role_id = 3 is "dosen"
                })
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
                ->whereRaw('semester % 2 = ?', [$semesterParity])
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
            // Fetch RPS record atau throw 404
            $rps = RpsDetail::findOrFail($rpsId);
            
            if (!$rps->dokumen) {
                abort(404, 'Dokumen tidak ditemukan');
            }

            // Generate Supabase public URL dari path yang disimpan
            $supabaseStorage = new SupabaseStorage();
            $publicUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');
            
            // Redirect ke Supabase untuk preview
            return redirect($publicUrl);
                
        } catch (\Exception $e) {
            \Log::error('previewDokumen Error', ['rps_id' => $rpsId, 'error' => $e->getMessage()]);
            abort(404, 'Dokumen tidak ditemukan');
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

        // Fetch mata kuliah berdasarkan semester saat ini (dengan parity check)
        $semesterParity = now()->month >= 7 ? 1 : 0; // 1 = Ganjil, 0 = Genap
        $mataKuliahs = MataKuliah::whereRaw('semester % 2 = ?', [$semesterParity])->orderBy('nama')->get();

        // Fetch CPL dan CPMK yang sudah dipilih
        $selectedCplIds = $rps->cpls->pluck('id')->toArray();
        $selectedCpmkIds = $rps->cpmks->pluck('id')->toArray();
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

        return view('banksoal::pages.rps.dosen.edit', compact(
            'rps',
            'mataKuliahs',
            'tahunAjarans',
            'selectedCplIds',
            'selectedCpmkIds',
            'selectedDosenIds',
            'isUploadOpen',
            'history'
        ));
    }

    /**
     * Update RPS yang sudah ada
     */
    public function update(int $rpsId, Request $request): RedirectResponse
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

        // Validasi Input
        $validated = $request->validate([
            'mata_kuliah_id' => ['required', 'exists:bs_mata_kuliah,id'],
            'dosen_lain'     => ['nullable', 'array'],
            'dosen_lain.*'   => ['exists:users,id'],
            'semester'       => ['required', 'in:Ganjil,Genap'],
            'tahun_ajaran'   => ['required', 'string'],
            'cpl_id'         => ['required', 'array'],
            'cpl_id.*'       => ['exists:bs_cpl,id'],
            'cpmk_id'        => ['required', 'array'],
            'cpmk_id.*'      => ['exists:bs_cpmk,id'],
            'catatan'        => [$isRevisionResubmit ? 'required' : 'nullable', 'string', 'max:1000'],
            'dokumen'        => [$isRevisionResubmit ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:1024'],
        ], [
            'dokumen.max' => 'Ukuran file maksimal 1MB',
            'dokumen.mimes' => 'Hanya menerima File berformat PDF',
            'dokumen.required' => 'File RPS baru wajib diunggah saat revisi',
            'catatan.required' => 'Catatan revisi wajib diisi',
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

            // Update dokumen jika ada file baru
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                
                // Ambil informasi untuk naming
                $mataKuliah = MataKuliah::findOrFail($validated['mata_kuliah_id']);
                $kodeMk = $mataKuliah->kode;
                $tahun = now()->year;
                $semester = $validated['semester'];
                $employeeNumber = $user->lecturer->employee_number;
                
                // Format nama file
                $fileName = "{$kodeMk}_{$tahun}_{$semester}_{$employeeNumber}";
                
                $supabaseStorage = new SupabaseStorage();
                $pathDokumen = $supabaseStorage->upload($file, 'rps', 'rps', $fileName);

                if (!$pathDokumen) {
                    throw new \Exception('Gagal mengupload file ke Supabase. Silakan periksa koneksi internet atau coba lagi');
                }

                $rps->dokumen = $pathDokumen;
            }

            // Update data RPS
            $rps->mk_id = $validated['mata_kuliah_id'];
            $rps->semester = $validated['semester'];
            $rps->tahun_ajaran = $validated['tahun_ajaran'];
            $rps->catatan = $validated['catatan'];
            
            // Jika status saat ini 'revisi', ubah menjadi 'diajukan' (re-submission setelah revisi)
            if ($isRevisionResubmit) {
                $rps->status = RpsStatus::DIAJUKAN;
            }
            
            $rps->save();

            // Update relasi CPL, CPMK, dan Dosen
            $dosenIds = $validated['dosen_lain'] ?? [];
            $dosenIds[] = $user->id;
            $dosenIds = array_unique($dosenIds); // Remove duplicates

            $rps->cpls()->sync($validated['cpl_id']);
            $rps->cpmks()->sync($validated['cpmk_id']);
            $rps->dosens()->sync($dosenIds);

            // Update is_rps di bs_dosen_pengampu_mk
            DB::table('bs_dosen_pengampu_mk')
                ->whereIn('user_id', $dosenIds)
                ->where('mk_id', $validated['mata_kuliah_id'])
                ->update(['is_rps' => 'TRUE']);

            // Log audit
            BsAuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'subject_type' => 'rps',
                'subject_id' => $rpsId,
                'description' => 'RPS telah diperbarui oleh dosen' . (!empty(trim((string) $validated['catatan'])) ? '. Catatan: ' . trim((string) $validated['catatan']) : ''),
                'old_data' => $oldData,
                'new_data' => [
                    'mk_id' => $rps->mk_id,
                    'semester' => $rps->semester,
                    'tahun_ajaran' => $rps->tahun_ajaran,
                    'status' => $rps->status->value,
                    'catatan' => $rps->catatan,
                    'cpl_ids' => $rps->cpls()->pluck('id')->toArray(),
                    'cpmk_ids' => $rps->cpmks()->pluck('id')->toArray(),
                    'dosen_ids' => $rps->dosens()->pluck('id')->toArray(),
                ],
                'created_at' => now(),
            ]);

            DB::commit();

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
            
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Hapus RPS (Hard Delete)
     */
    public function destroy(int $rpsId, Request $request): RedirectResponse|JsonResponse
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

            // Hapus relasi di junction table
            $rps->cpls()->detach();
            $rps->cpmks()->detach();
            $rps->dosens()->detach();

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
        $semesterParity = now()->month >= 7 ? 1 : 0;
        $mataKuliahs = MataKuliah::whereRaw('semester % 2 = ?', [$semesterParity])->orderBy('nama')->get();

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
}
