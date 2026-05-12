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
        $rps = RpsDetail::findOrFail($rpsId);

        if (!$rps->dokumen) {
            abort(404, 'Dokumen RPS tidak ditemukan');
        }

        $supabaseStorage = new SupabaseStorage();
        $publicUrl = $supabaseStorage->getPublicUrl($rps->dokumen, 'rps');

        return redirect($publicUrl);
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
