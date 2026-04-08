<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Dosen;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Modules\BankSoal\Models\Shared\MataKuliah;
use Modules\BankSoal\Models\Shared\Cpl;
use Modules\BankSoal\Models\Shared\Cpmk;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Enums\RpsStatus;

/**
 * RpsController - RPS (Rencana Pembelajaran Semester) Management
 * Role Access: Dosen
 * Workflow:
 * 1. index() -> Tampilkan halaman form + history
 * 2. store() -> Simpan dokumen RPS baru 
 * 3. getCplByMk() -> Fetch available CPL untuk form
 * 4. getCpmkByCpl() -> Fetch available CPMK berdasarkan CPL yang dipilih
 * 5. getDosenByMk() -> Fetch list dosen untuk multi-select
 * 6. previewDokumen() -> Stream dokumen PDF untuk preview di modal
 */
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

        $rpsUploaded = $riwayat->isNotEmpty();

        return view('banksoal::pages.rps.dosen.index', compact(
            'mataKuliahs',
            'riwayat',
            'tahunAjarans',
            'semester',
            'academicYear',
            'rpsUploaded',
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
            'dokumen'        => ['required', 'file', 'mimes:pdf,docx,doc', 'max:5120'], 
        ], [
            'dokumen.max' => 'Ukuran file maksimal 5MB',
            'dokumen.mimes' => 'File harus berformat PDF atau DOCX',
            'dokumen.required' => 'File RPS harus diunggah',
        ]);

        DB::beginTransaction();

        try {
            // Upload File
            $file = $request->file('dokumen');
            $filename = 'RPS_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke Storage disk 'bank-soal'
            $pathDokumen = $file->storeAs('rps', $filename, 'bank-soal');

            // Simpan data ke Tabel RPS
            $rps = RpsDetail::create([
                'mk_id'        => $validated['mata_kuliah_id'],
                'semester'     => $validated['semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'dokumen'  => $pathDokumen,
                'status'       => RpsStatus::DIAJUKAN, 
            ]);

            // Informasi Data Dosen Terkait
            $dosenIds = $request->dosen_lain ?? []; // Dosen lain
            $dosenIds[] = Auth::id(); // User
            
            // Simpan ke Tabel menggunakan sync()
            $rps->cpls()->sync($validated['cpl_ids']);
            $rps->cpmks()->sync($validated['cpmk_ids']);
            $rps->dosens()->sync($dosenIds);

            // Commit perubahan ke DB
            DB::commit();

            return redirect()->route('banksoal.rps.dosen.index')
                ->with('success', 'RPS berhasil disimpan dan sedang menunggu verifikasi GPM.');

        } catch (\Exception $e) {            
            // Rollback jika error
            DB::rollBack();
            
            // Cleanup: Delete uploaded file jika ada error
            if (isset($pathDokumen)) {
                Storage::disk('bank-soal')->delete($pathDokumen);
            }
            
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
            
            // Check dokumen exist di storage
            if (!$rps->dokumen || !Storage::disk('bank-soal')->exists($rps->dokumen)) {
                abort(404, 'Dokumen tidak ditemukan');
            }

            // Get file content dan mime type
            $file = Storage::disk('bank-soal')->get($rps->dokumen);
            $mimeType = Storage::disk('bank-soal')->mimeType($rps->dokumen);
            
            // Return response dengan inline disposition (preview, tidak download)
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($rps->dokumen) . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            \Log::error('previewDokumen Error', ['rps_id' => $rpsId, 'error' => $e->getMessage()]);
            abort(404, 'Dokumen tidak ditemukan');
        }
    }
}
