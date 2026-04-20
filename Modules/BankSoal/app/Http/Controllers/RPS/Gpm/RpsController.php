<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SupabaseStorage;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Models\PeriodeRps;
use Modules\BankSoal\Services\RpsService;

class RpsController extends Controller
{
    public function index(RpsService $rpsService): \Illuminate\View\View
    {
        $rpsDiajukan = $rpsService->getDiajukan(10);
        $rpsRevisi = $rpsService->getRevisi(10);
        $rpsDisetujui = $rpsService->getDisetujui(10);
        
        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        $isPeriodeRunning = false;
        
        if ($activePeriode) {
            $now   = now('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end   = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();
            $isPeriodeRunning = $now->between($start, $end);
        }

        return view('banksoal::gpm.validasi-rps', [
            'rpsDiajukan' => $rpsDiajukan,
            'rpsRevisi' => $rpsRevisi,
            'rpsDisetujui' => $rpsDisetujui,
            'activePeriode' => $activePeriode,
            'isPeriodeRunning' => $isPeriodeRunning,
        ]);
    }

    /*
     * Menampilkan daftar RPS yang menunggu validasi
     */
    public function validasiRps(RpsService $rpsService)
    {
        $rpsDiajukan = $rpsService->getDiajukan(10);
        $rpsRevisi = $rpsService->getRevisi(10);
        $rpsDisetujui = $rpsService->getDisetujui(10);

        $activePeriode = PeriodeRps::where('is_active', 'true')->first();
        $isPeriodeRunning = false;
        
        if ($activePeriode) {
            $now   = now('Asia/Jakarta');
            $start = $activePeriode->tanggal_mulai->timezone('Asia/Jakarta')->startOfDay();
            $end   = $activePeriode->tanggal_selesai->timezone('Asia/Jakarta')->endOfDay();
            $isPeriodeRunning = $now->between($start, $end);
        }
        
        // Ambil periode yang tidak aktif untuk tombol "Nyalakan Sesi"
        // Hanya tampilkan sesi terakhir (paling baru dibuat)
        $inactivePeriodes = PeriodeRps::where('is_active', 'false')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get();
        
        // Generate tahun ajaran options
        $currentYear = (int) now()->format('Y');
        $tahunAjarans = [
            ($currentYear - 1) . '/' . $currentYear,
            $currentYear . '/' . ($currentYear + 1),
            ($currentYear + 1) . '/' . ($currentYear + 2),
        ];
        
        // Auto-detect current semester
        // Semester Ganjil: Juli-Desember (bulan 7-12)
        // Semester Genap: Januari-Juni (bulan 1-6)
        $currentSemester = now()->month >= 7 ? 'Ganjil' : 'Genap';

        return view('banksoal::gpm.validasi-rps', [
            'rpsDiajukan' => $rpsDiajukan,
            'rpsRevisi' => $rpsRevisi,
            'rpsDisetujui' => $rpsDisetujui,
            'activePeriode' => $activePeriode,
            'isPeriodeRunning' => $isPeriodeRunning,
            'inactivePeriodes' => $inactivePeriodes,
            'tahunAjarans' => $tahunAjarans,
            'currentSemester' => $currentSemester,
        ]);
    }

    /**
     * Menampilkan detail RPS untuk di-review
     */
    public function validasiRpsReview($rpsId)
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name, '|', users.email), ', ') as dosens_list");
        
        $rps = DB::table('bs_rps_detail')
            ->join('bs_mata_kuliah', 'bs_rps_detail.mk_id', '=', 'bs_mata_kuliah.id')
            ->leftJoin('bs_rps_dosen', 'bs_rps_detail.id', '=', 'bs_rps_dosen.rps_id')
            ->leftJoin('users', 'bs_rps_dosen.dosen_id', '=', 'users.id')
            ->select(
                'bs_rps_detail.id as rps_id',
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode',
                'bs_mata_kuliah.nama as mk_nama',
                'bs_rps_detail.semester',
                'bs_rps_detail.tahun_ajaran',
                'bs_rps_detail.status',
                'bs_rps_detail.dokumen',
                'bs_rps_detail.created_at as tanggal_diajukan',
                $dosenAggregate
            )
            ->where('bs_rps_detail.id', '=', $rpsId)
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.created_at')
            ->first();

        if (!$rps) {
            abort(404, 'RPS tidak ditemukan');
        }

        // Fetch parameters and existing review results
        $parameters = DB::table('bs_parameter')->get();
        $existingReview = DB::table('bs_rps_review')
            ->where('rps_id', $rpsId)
            ->first();

        // Fetch history/log
        $history = DB::table('bs_audit_logs')
            ->where('subject_type', 'rps')
            ->where('subject_id', $rpsId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('banksoal::gpm.validasi-rps-review', compact('rps', 'parameters', 'existingReview', 'history'));
    }

    public function previewDokumen(int $rpsId)
    {
        try {
            // Fetch RPS record dengan eager loading atau throw 404
            $rps = RpsDetail::with('mataKuliah', 'dosens')->findOrFail($rpsId);
            
            if (!$rps->dokumen) {
                abort(404, 'Dokumen tidak ditemukan');
            }

            // Generate Supabase public URL dari path yang disimpan
            $supabaseStorage = new SupabaseStorage();
            $publicUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');
            
            // Redirect ke Supabase untuk preview
            return redirect($publicUrl);
                
        } catch (\Exception $e) {
            Log::error('previewDokumen Error', ['rps_id' => $rpsId, 'error' => $e->getMessage()]);
            abort(404, 'Dokumen tidak ditemukan');
        }
    }

    /**
     * Simpan hasil validasi RPS dari GPM
     * - Setuju: Update status menjadi "disetujui"
     * - Tolak: Update status menjadi "revisi" (dengan validasi catatan)
     */
    public function storeValidasi(Request $request)
    {
        $rpsId = $request->input('rps_id');
        $action = $request->input('action'); // 'setuju' atau 'revisi'
        $catatan = $request->input('catatan', '');

        // Validasi input
        if (!in_array($action, ['setuju', 'revisi'])) {
            return response()->json(['message' => 'Action tidak valid'], 400);
        }

        if ($action === 'revisi' && empty(trim($catatan))) {
            return response()->json(['message' => 'Catatan revisi harus diisi'], 422);
        }

        DB::beginTransaction();

        try {
            // Fetch RPS
            $rps = RpsDetail::findOrFail($rpsId);

            // Fetch semua parameter dengan bobot
            $parameters = DB::table('bs_parameter')->get();

            // Hitung nilai_akhir berdasarkan penilaian user (di backend)
            $nilaiAkhir = 0;
            $hasilReviewData = [];

            foreach ($parameters as $param) {
                $paramKey = 'parameter_' . $param->id;
                $nilaiParam = $request->input($paramKey);

                // Jika parameter tidak dijawab, anggap sebagai tidak sesuai (0 poin)
                if ($nilaiParam === null || $nilaiParam === '') {
                    $nilaiParam = 0;
                }

                // Jika sesuai (1), tambahkan bobot ke nilai_akhir
                if ($nilaiParam == 1) {
                    $nilaiAkhir += $param->bobot;
                }

                // Simpan data untuk bs_hasil_review_rps
                $hasilReviewData[] = [
                    'rps_detail_id' => $rpsId,
                    'parameter_id' => $param->id,
                    'skor' => (string) $nilaiParam,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Tentukan status baru berdasarkan action
            $statusBaru = ($action === 'setuju') ? 'disetujui' : 'revisi';

            // Update status RPS
            $rps->update(['status' => $statusBaru]);

            // Delete hasil review lama dan insert yang baru untuk update case
            DB::table('bs_hasil_review_rps')->where('rps_detail_id', $rpsId)->delete();
            DB::table('bs_hasil_review_rps')->insert($hasilReviewData);

            // Map action to enum status_review value
            $statusReview = ($action === 'setuju') ? 'disetujui' : 'revisi';

            // Simpan atau update review record - hanya simpan nilai_akhir (integer)
            DB::table('bs_rps_review')->updateOrInsert(
                ['rps_id' => $rpsId],
                [
                    'gpm_user_id' => Auth::id(),
                    'status_review' => $statusReview,
                    'catatan' => $catatan,
                    'nilai_akhir' => $nilaiAkhir,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Log audit trail
            $descriptionLog = $action === 'setuju' 
                ? 'RPS telah disetujui oleh GPM. Nilai: ' . $nilaiAkhir . '/100'
                : 'RPS dikembalikan untuk revisi.';
            
            if (!empty(trim($catatan))) {
                $descriptionLog .= ' Catatan: ' . $catatan;
            }
            
            DB::table('bs_audit_logs')->insert([
                'subject_type' => 'rps',
                'subject_id' => $rpsId,
                'action' => $action === 'setuju' ? 'disetujui' : 'revisi',
                'description' => $descriptionLog,
                'user_id' => Auth::id(),
                'new_data' => json_encode([
                    'status' => $statusBaru,
                    'nilai_akhir' => $nilaiAkhir,
                    'catatan' => $catatan,
                ]),
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => $action === 'setuju' 
                    ? 'RPS berhasil disetujui'
                    : 'RPS berhasil dikembalikan untuk revisi',
                'status' => $statusBaru,
                'nilai_akhir' => $nilaiAkhir,
                'redirect' => route('banksoal.rps.gpm.validasi-rps')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('storeValidasi Error', [
                'rps_id' => $rpsId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
