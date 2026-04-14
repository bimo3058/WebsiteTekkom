<?php

namespace Modules\BankSoal\Services;

use Illuminate\Support\Facades\DB;

class ValidasiBankSoalService
{
    public function getCounts()
    {
        $menunggu = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->leftJoin('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
            ->whereNull('bs_review.id')
            ->distinct('bs_mata_kuliah.id')
            ->count('bs_mata_kuliah.id');

        $selesai = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->join('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
            ->distinct('bs_mata_kuliah.id')
            ->count('bs_mata_kuliah.id');

        return (object)[
            'menunggu' => $menunggu,
            'selesai' => $selesai
        ];
    }

    public function getDaftarAntreanMataKuliah($search = null)
    {
        $query = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->leftJoin('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
            ->whereNull('bs_review.id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('bs_mata_kuliah.nama', 'ilike', '%' . $search . '%')
                  ->orWhere('bs_mata_kuliah.kode', 'ilike', '%' . $search . '%');
            });
        }

        return $query->select(
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode as mk_kode',
                'bs_mata_kuliah.nama as mk_nama',
                DB::raw('COUNT(bs_pertanyaan.id) as jumlah_soal')
            )
            ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama')
            ->get();
    }

    public function getSoalReview()
    {
        return DB::table('bs_pertanyaan')
            ->join('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->join('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            ->whereNotIn('bs_pertanyaan.id', function($query) {
                $query->select('pertanyaan_id')->from('bs_review');
            })
            ->select(
                'bs_pertanyaan.*', 
                'bs_cpl.kode as cpl_kode', 'bs_cpl.deskripsi as cpl_deskripsi',
                'bs_mata_kuliah.nama as mk_nama', 'bs_mata_kuliah.kode as mk_kode'
            )
            ->orderBy('bs_pertanyaan.id', 'asc')
            ->first();
    }

    public function getOpsiJawaban($soalId)
    {
        return DB::table('bs_jawaban')
            ->where('soal_id', $soalId)
            ->orderBy('opsi', 'asc')
            ->get();
    }

    public function simpanReview($data)
    {
        return DB::table('bs_review')->insert([
            'pertanyaan_id' => $data['pertanyaan_id'],
            'gpm_user_id'   => auth()->id() ?? 1,
            'status_review' => $data['status_review'],
            'catatan'       => $data['catatan'],
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
    }

    public function updateReview($id, $data)
    {
        return DB::table('bs_review')->where('pertanyaan_id', $id)->update([
            'status_review' => $data['status_review'],
            'catatan'       => $data['catatan'],
            'updated_at'    => now()
        ]);
    }
}