<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Admin;

use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Modules\BankSoal\Models\RpsDetail;

/**
 * [Admin - RPS] Controller untuk manajemen RPS tingkat Admin
 * 
 * Role: Admin
 * Fitur: RPS (Rencana Pembelajaran Semester)
 * 
 * Admin dapat melihat, mengelola, dan menghapus semua RPS di sistem.
 */
class RpsController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('banksoal::pages.admin.kontrol-banksoal.rps');
    }

    public function listApproved(): JsonResponse
    {
        $rows = DB::table('bs_rps_detail as rps')
            ->join('bs_mata_kuliah as mk', 'mk.id', '=', 'rps.mk_id')
            ->leftJoin('bs_rps_review as review', function ($join) {
                $join->on('review.rps_id', '=', 'rps.id')
                    ->where('review.status_review', '=', 'disetujui');
            })
            ->where('rps.status', 'disetujui')
            ->select(
                'rps.id',
                'rps.dokumen',
                'rps.tahun_ajaran',
                'mk.nama as mk_nama',
                DB::raw('COALESCE(review.updated_at, rps.updated_at) as tanggal_disetujui')
            )
            ->orderByDesc(DB::raw('COALESCE(review.updated_at, rps.updated_at)'))
            ->get()
            ->map(function ($item) {
                $fileName = '';
                if (!empty($item->dokumen)) {
                    $fileName = basename((string) $item->dokumen);
                }

                $item->file_name = $fileName;
                return $item;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
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
                    $downloadUrl = route('banksoal.admin.kontrol-banksoal.rps.download', ['rpsId' => $rpsId]);
                }
            } else {
                $errorMessage = 'Dokumen RPS belum diunggah atau tidak dapat ditemukan.';
            }

            $data['fileUrl'] = $fileUrl;
            $data['downloadUrl'] = $downloadUrl;
            $data['errorMessage'] = $errorMessage;

            return view('banksoal::pages.rps.preview-page', $data);

        } catch (\Exception $e) {
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
        $rps = RpsDetail::with('mataKuliah')->findOrFail($rpsId);

        if (!$rps->dokumen) {
            abort(404, 'Dokumen RPS tidak ditemukan');
        }

        $supabaseStorage = new SupabaseStorage();
        $publicUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');

        $downloadName = basename((string) $rps->dokumen);
        $separator = str_contains($publicUrl, '?') ? '&' : '?';

        return redirect($publicUrl . $separator . 'download=' . urlencode($downloadName));
    }
}
