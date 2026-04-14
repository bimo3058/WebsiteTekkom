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

        // Fetch mata kuliah — exclude MK yang sudah punya RPS aktif
        // (status: diajukan, revisi, atau disetujui) untuk cegah duplikasi
        $mkIdsWithActiveRps = RpsDetail::whereIn('status', [
                RpsStatus::DIAJUKAN->value,
                RpsStatus::REVISI->value,
                RpsStatus::DISETUJUI->value,
            ])
            ->pluck('mk_id')
            ->unique();

        $mataKuliahs = MataKuliah::whereNotIn('id', $mkIdsWithActiveRps)->get();

        // Fetch riwayat RPS — hanya tampilkan RPS dimana dosen ini terdaftar
        // (baik sebagai pembuat maupun dosen pengampu tambahan via pivot bs_rps_dosen)
        $riwayat = RpsDetail::with('mataKuliah')
            ->whereHas('dosens', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $currentYear  = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];

        // Semester Ganjil: Juli-Desember (bulan 7-12)
        // Semester Genap: Januari-Juni (bulan 1-6)
        $semester = now()->month >= 7 ? 'Ganjil' : 'Genap';
        
        // Set tahun ajaran
        $academicYear = $semester === 'Ganjil'
            ? $currentYear . '/' . ($currentYear + 1)
            : ($currentYear - 1) . '/' . $currentYear;

        // Fetch Active Periode
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        
        $isUploadOpen = false;
        $tenggatH7 = false;
        $isHourFormat = false; // Track apakah daysLeft dalam format jam atau hari
        $unsubmittedMk = [];
        $daysLeft = 0;
        
        if ($activePeriode) {
            $now   = now('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end   = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();

            // Cek apakah sekarang dalam rentang periode aktif
            if ($now->between($start, $end)) {
                $isUploadOpen = true;
                
                // Cek H-7 Reminder - hitung sisa waktu
                $deadlineDate = $activePeriode->tanggal_selesai->startOfDay();
                $todayDate = $now->startOfDay();
                
                // Jika deadline adalah hari yang sama dengan hari ini
                if ($deadlineDate->isSameDay($todayDate)) {
                    // Hitung sisa jam
                    $hoursLeft = (int) $now->diffInHours($activePeriode->tanggal_selesai->endOfDay(), false);
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
                        ->pluck('bs_mata_kuliah.nama')
                        ->toArray();
                }
            }
        }

        $rpsUploaded = $riwayat->isNotEmpty();

        return view('banksoal::pages.rps.dosen.index', compact(
            'mataKuliahs',
            'riwayat',
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
            'cpl_ids'        => ['required', 'array'],
            'cpl_ids.*'      => ['exists:bs_cpl,id'],
            'cpmk_ids'       => ['required', 'array'],
            'cpmk_ids.*'     => ['exists:bs_cpmk,id'],
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
            $dosenIds = $request->dosen_lain ?? []; // Dosen lain
            $dosenIds[] = Auth::id(); // User
            
            // Simpan ke Tabel menggunakan sync()
            $rps->cpls()->sync($validated['cpl_ids']);
            $rps->cpmks()->sync($validated['cpmk_ids']);
            $rps->dosens()->sync($dosenIds);

            // Update is_rps menjadi 'TRUE' di bs_dosen_pengampu_mk untuk semua dosen
            DB::table('bs_dosen_pengampu_mk')
                ->whereIn('user_id', $dosenIds)
                ->where('mk_id', $validated['mata_kuliah_id'])
                ->update(['is_rps' => 'TRUE']);

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
     * Fetch semua CPL
     */
    public function getCplByMk(int $mkId = null): JsonResponse
    {
        try {
            // Fetch semua CPL tanpa filter MK
            $cpls = Cpl::all()->map(function ($cpl) {
                return [
                    'id' => $cpl->id,
                    'kode' => $cpl->kode,
                    'deskripsi' => $cpl->deskripsi,
                ];
            });

            return response()->json($cpls);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching CPL'], 500);
        }
    }


    public function getCpmkByCpl(Request $request): JsonResponse
    {
        $cplIds = (array) $request->query('cpl_ids', []);

        if (empty($cplIds)) {
            return response()->json([]);
        }

        try {
            // Query CPMK melalui junction table bs_cpl_cpmk
            $cpmks = Cpmk::whereIn('id', function($query) use ($cplIds) {
                $query->select('cpmk_id')
                    ->from('bs_cpl_cpmk')
                    ->whereIn('cpl_id', $cplIds);
            })
                ->distinct()
                ->orderBy('id')
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
            \Log::error('getCpmkByCpl Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching CPMK: ' . $e->getMessage()], 500);
        }
    }


    public function getDosenByMk(): JsonResponse
    {
        try {
            $query = User::whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['dosen']);
            })
                ->where('id', '!=', Auth::id())  // Exclude user yang sedang login
                ->with('roles');

            // DEBUG: Log SQL query dan hasil
            \Log::info('getDosenByMk Query', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);

            $dosenList = $query->get();

            // DEBUG: Log hasil dan roles dari setiap user
            $dosenList->each(function ($user) {
                \Log::info('User Found', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ]);
            });

            return response()->json($dosenList->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }));
        } catch (\Exception $e) {
            \Log::error('getDosenByMk Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error fetching dosen'], 500);
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
}
