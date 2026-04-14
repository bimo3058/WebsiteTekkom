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
                ->leftJoin('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
                ->whereNull('bs_review.id')
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
                ->take(5) // Ambil 5 teratas saja untuk dashboard
                ->get();

            // 2. Ambil RPS yang sedang dalam pengajuan (DIAJUKAN) dan revisi (REVISI)
            $rpsService    = app(RpsService::class);
            $rpsDiajukan   = $rpsService->getDiajukan(50)->getCollection();
            $rpsRevisi     = $rpsService->getRevisi(50)->getCollection();

            // Map RPS menjadi format yang seragam dengan Bank Soal
            $mapRps = fn($item, $tipe) => (object)[
                'mk_id'        => $item->mk_id,
                'mk_kode'      => $item->kode,
                'mk_nama'      => $item->mk_nama,
                'tipe_dokumen' => 'RPS',
                'rps_id'       => $item->rps_id,
                'status'       => $item->status,
                'dosens_list'  => $item->dosens_list ?? null,
                'sub_status'   => $tipe,   // 'diajukan' | 'revisi'
            ];

            $prioritasRps = $rpsDiajukan->map(fn($r) => $mapRps($r, 'diajukan'))
                ->merge($rpsRevisi->map(fn($r) => $mapRps($r, 'revisi')))
                ->take(5);

            // Stat counts untuk card
            $statRpsMenunggu  = $rpsDiajukan->count() + $rpsRevisi->count();
            $statBankSoalMenunggu = DB::table('bs_mata_kuliah')
                ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
                ->leftJoin('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
                ->whereNull('bs_review.id')
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
            return view('banksoal::dashboard.dosen');
        }

        // Mahasiswa
        if ($roles->contains('mahasiswa')) {
            return view('banksoal::dashboard.mahasiswa');
        }

        abort(403, 'Akses ditolak.');
    }
}