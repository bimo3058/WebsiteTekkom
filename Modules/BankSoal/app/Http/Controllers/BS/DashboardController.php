<?php

namespace Modules\BankSoal\Http\Controllers\BS;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

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
                    DB::raw("'Bank Soal' as tipe_dokumen") // Penanda untuk di UI
                )
                ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama')
                ->take(5) // Ambil 5 teratas saja untuk dashboard
                ->get();

            // TODO: Nanti kita tambahkan query untuk prioritas RPS di sini
            $prioritasRps = collect([]); // Sementara kosong dulu

            // Gabungkan kedua data menjadi satu daftar Tugas Prioritas
            $tugasPrioritas = $prioritasBankSoal->merge($prioritasRps);

            // Kirim variabel ke view GPM
            return view('banksoal::dashboard.gpm', compact('tugasPrioritas'));
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