<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Models\Shared\Rps;
use Modules\BankSoal\Services\RpsService;

/**
 * [GPM - RPS] Controller untuk review dan verifikasi RPS tingkat GPM
 * 
 * Role: GPM (Gadd Pendidikan dan Mahasiswa)
 * Fitur: RPS (Rencana Pembelajaran Semester)
 * 
 * GPM dapat melihat, mereview, dan memverifikasi RPS yang telah dikerjakan oleh dosen.
 */
class RpsController extends Controller
{
    public function index(RpsService $rpsService): \Illuminate\View\View
    {
        $rpsDiajukan = $rpsService->getDiajukan(10);
        $rpsRevisi = $rpsService->getRevisi(10);
        $rpsDisetujui = $rpsService->getDisetujui(10);
        
        return view('banksoal::gpm.validasi-rps', [
            'rpsDiajukan' => $rpsDiajukan,
            'rpsRevisi' => $rpsRevisi,
            'rpsDisetujui' => $rpsDisetujui,
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

        return view('banksoal::gpm.validasi-rps', [
            'rpsDiajukan' => $rpsDiajukan,
            'rpsRevisi' => $rpsRevisi,
            'rpsDisetujui' => $rpsDisetujui,
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

    /**
     * Preview dokumen RPS (PDF/DOCX)
     * Digunakan untuk menampilkan dokumen di dalam iframe
     */
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
            }

            // Tentukan status baru berdasarkan action
            $statusBaru = ($action === 'setuju') ? 'disetujui' : 'revisi';

            // Update status RPS
            $rps->update(['status' => $statusBaru]);

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
            DB::table('bs_audit_logs')->insert([
                'subject_type' => 'rps',
                'subject_id' => $rpsId,
                'action' => $action === 'setuju' ? 'disetujui' : 'revisi',
                'description' => $action === 'setuju' 
                    ? 'RPS telah disetujui oleh GPM. Nilai: ' . $nilaiAkhir . '/100'
                    : 'RPS dikembalikan untuk revisi. Catatan: ' . $catatan,
                'user_id' => Auth::id(),
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
