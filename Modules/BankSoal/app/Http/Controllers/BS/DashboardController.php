<?php

namespace Modules\BankSoal\Http\Controllers\BS;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Services\RpsService;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');

        // =====================================================================
        // FIX: Cek permission SEBELUM routing berdasarkan role.
        //
        // SEBELUM (BUG):
        //   Controller hanya cek role. User dengan role 'dosen' SELALU
        //   bisa akses dashboard Bank Soal, meskipun permission
        //   banksoal.view sudah dicabut oleh superadmin.
        //
        // SESUDAH (FIX):
        //   Cek banksoal.view dulu. Jika tidak punya → 403.
        //   Superadmin di-bypass otomatis oleh hasPermissionTo().
        // =====================================================================
        if (!$user->can('banksoal.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Bank Soal (banksoal.view).');
        }

        // Superadmin & Admin
        if ($roles->intersect(['superadmin', 'admin', 'admin_banksoal'])->isNotEmpty()) {
            return view('banksoal::dashboard.admin');
        }

        // GPM
        if ($roles->contains('gpm')) {
            // 1. Ambil Antrean Bank Soal (Mata kuliah yang belum direview)
            $prioritasBankSoal = DB::table('bs_mata_kuliah')
                ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
                ->where('bs_pertanyaan.status', 'diajukan')
                ->select(
                    'bs_mata_kuliah.id as mk_id',
                    'bs_mata_kuliah.kode as mk_kode',
                    'bs_mata_kuliah.nama as mk_nama',
                    DB::raw("'Bank Soal' as tipe_dokumen"),
                    DB::raw("null as rps_id"),
                    DB::raw("null as status"),
                    DB::raw("null as dosens_list")
                )
                ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama')
                ->take(5)
                ->get();

            // 2. Ambil RPS yang sedang dalam pengajuan (DIAJUKAN) dan revisi (REVISI)
            $rpsService    = app(RpsService::class);
            $rpsDiajukan   = $rpsService->getDiajukan(50);
            $rpsRevisi     = $rpsService->getRevisi(50);

            // Map RPS menjadi format yang seragam dengan Bank Soal
            $mapRps = fn($item, $tipe) => (object)[
                'mk_id'        => $item->mk_id,
                'mk_kode'      => $item->mataKuliah?->kode ?? 'N/A',
                'mk_nama'      => $item->mataKuliah?->nama ?? 'N/A',
                'tipe_dokumen' => 'RPS',
                'rps_id'       => $item->id,
                'status'       => $item->status,
                'dosens_list'  => $item->dosens->pluck('name')->join(', ') ?? null,
                'sub_status'   => $tipe,
            ];

            $prioritasRps = $rpsDiajukan->map(fn($r) => $mapRps($r, 'diajukan'))
                ->merge($rpsRevisi->map(fn($r) => $mapRps($r, 'revisi')))
                ->take(5);

            // Stat counts untuk card
            $statRpsMenunggu  = $rpsDiajukan->count() + $rpsRevisi->count();
            $statBankSoalMenunggu = DB::table('bs_mata_kuliah')
                ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
                ->where('bs_pertanyaan.status', 'diajukan')
                ->distinct('bs_mata_kuliah.id')
                ->count('bs_mata_kuliah.id');
            
            // Data untuk card total tugas selesai
            $soalAcc = DB::table('bs_pertanyaan')
                ->where('bs_pertanyaan.status', 'disetujui')
                ->distinct('mk_id')
                ->count('mk_id');
                
            $rpsAcc = DB::table('bs_rps_detail')
                ->where('bs_rps_detail.status', 'disetujui')
                ->distinct('mk_id')
                ->count('mk_id');

            // Gabungkan kedua data menjadi satu daftar Tugas Prioritas
            $tugasPrioritas = $prioritasBankSoal->merge($prioritasRps);
            $tugasSelesai   = $soalAcc + $rpsAcc; // int + int, bukan ->merge()

            // Kirim variabel ke view GPM
            return view('banksoal::dashboard.gpm', compact(
                'tugasPrioritas',
                'statRpsMenunggu',
                'statBankSoalMenunggu',
                'tugasSelesai'
            ));
        }
        

        // Dosen
        if ($roles->contains('dosen')) {
            $mataKuliah = \Modules\BankSoal\Models\MataKuliah::whereHas('dosenPengampu', function($q) use($user) {
                $q->where('user_id', $user->id);
            })->get();

            $mkIds = $mataKuliah->pluck('id')->toArray();

            // Query Pertanyaan Status Data
            $totalSoal = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)->count();
            $approved = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)->where('status', 'disetujui')->count();
            $perluReview = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)->whereIn('status', ['diajukan'])->count();
            $revisi = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)->where('status', 'revisi')->count();
            $ditolak = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)->where('status', 'ditolak')->count();

            // Data CPL untuk Bar Chart
            $cplDistRaw = \Modules\BankSoal\Models\Pertanyaan::whereIn('mk_id', $mkIds)
                ->join('bs_cpl', 'bs_cpl.id', '=', 'bs_pertanyaan.cpl_id')
                ->selectRaw('bs_cpl.kode, COUNT(bs_pertanyaan.id) as count')
                ->groupBy('bs_cpl.id', 'bs_cpl.kode')
                ->get();
                
            $cplDist = $cplDistRaw->pluck('count', 'kode')->toArray();

            // Data MK untuk Bar Chart
            $mkDist = [];
            $mkTanpaRps = [];
            foreach ($mataKuliah as $mk) {
                $count = \Modules\BankSoal\Models\Pertanyaan::where('mk_id', $mk->id)->count();
                $mkDist[] = [
                    'mk' => $mk->kode,
                    'count' => $count,
                    'color' => $count > 0 ? '#22C55E' : '#CBD5E1'
                ];

                // Cek RPS
                $rpsCount = \Illuminate\Support\Facades\DB::table('bs_rps_detail')->where('mk_id', $mk->id)->count();
                if ($rpsCount == 0) {
                    $mkTanpaRps[] = $mk->nama;
                }
            }
            
            $donutData = [
                ['value' => $approved, 'color' => '#22C55E'],
                ['value' => $revisi, 'color' => '#F59E0B'],
                ['value' => $perluReview, 'color' => '#3B82F6'],
                ['value' => $ditolak, 'color' => '#EF4444'],
            ];

            return view('banksoal::dashboard.dosen', compact(
                'mataKuliah',
                'totalSoal',
                'approved',
                'perluReview',
                'revisi',
                'ditolak',
                'cplDist',
                'mkDist',
                'donutData',
                'mkTanpaRps'
            ));
        }

        // Mahasiswa
        if ($roles->contains('mahasiswa')) {
            return view('banksoal::dashboard.mahasiswa');
        }

        abort(403, 'Akses ditolak.');
    }
}