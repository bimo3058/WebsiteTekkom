<?php

namespace Modules\BankSoal\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\BankSoal\Models\Pertanyaan;

class ValidasiBankSoalService
{
    public function getCounts()
    {
        $menunggu = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->where('bs_pertanyaan.status', Pertanyaan::STATUS_DIAJUKAN)
            ->distinct('bs_mata_kuliah.id')
            ->count('bs_mata_kuliah.id');

        $selesai = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->whereIn('bs_pertanyaan.status', [Pertanyaan::STATUS_DISETUJUI, Pertanyaan::STATUS_REVISI, 'ditolak'])
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
            ->leftJoin('bs_dosen_pengampu_mk', 'bs_mata_kuliah.id', '=', 'bs_dosen_pengampu_mk.mk_id')
            ->leftJoin('users', 'bs_dosen_pengampu_mk.user_id', '=', 'users.id')
            ->where('bs_pertanyaan.status', Pertanyaan::STATUS_DIAJUKAN);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Gunakan LOWER() + LIKE agar portabel di MySQL & PostgreSQL.
                // Hindari 'ilike' yang hanya tersedia di PostgreSQL.
                $term = $this->likePattern($search);
                $q->whereRaw('LOWER(bs_mata_kuliah.nama) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(bs_mata_kuliah.kode) LIKE ?', [$term]);
            });
        }

        return $query->select(
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode as mk_kode',
                'bs_mata_kuliah.nama as mk_nama',
                // STRING_AGG adalah fitur PostgreSQL — digunakan secara sadar (by design).
                // Jika migrasi ke MySQL diperlukan, ganti dengan GROUP_CONCAT.
                DB::raw("STRING_AGG(DISTINCT users.name, ', ') as dosen_pengampu"),
                DB::raw('COUNT(DISTINCT bs_pertanyaan.id) as jumlah_soal')
            )
            ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama')
            ->get();
    }

    public function getSoalReview($mk_id = null)
    {
        $query = DB::table('bs_pertanyaan')
            ->join('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->leftJoin('bs_cpmk', 'bs_pertanyaan.cpmk_id', '=', 'bs_cpmk.id')
            ->join('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            ->where('bs_pertanyaan.status', Pertanyaan::STATUS_DIAJUKAN);
            
        if ($mk_id) {
            $query->where('bs_pertanyaan.mk_id', $mk_id);
        }

        return $query->select(
                'bs_pertanyaan.*', 
                'bs_cpl.kode as cpl_kode', 'bs_cpl.deskripsi as cpl_deskripsi',
                'bs_cpmk.kode as cpmk_kode', 'bs_cpmk.deskripsi as cpmk_deskripsi',
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
        $reviewId = DB::table('bs_review')->insertGetId([
            'pertanyaan_id' => $data['pertanyaan_id'],
            'gpm_user_id'   => auth()->id() ?? 1,
            'status_review' => $data['status_review'],
            'catatan'       => $data['catatan'] ?? 'Soal telah disetujui tanpa catatan.',
            'created_at'    => now(),
            'updated_at'    => now()
        ]);

        $statusPertanyaan = ($data['status_review'] === 'Sesuai') 
            ? Pertanyaan::STATUS_DISETUJUI 
            : Pertanyaan::STATUS_REVISI;

        DB::table('bs_pertanyaan')
            ->where('id', $data['pertanyaan_id'])
            ->update(['status' => $statusPertanyaan, 'updated_at' => now()]);

        return $reviewId;
    }

    public function updateReview($id, $data)
    {
        DB::table('bs_review')->where('pertanyaan_id', $id)->update([
            'status_review' => $data['status_review'],
            'catatan'       => $data['catatan'],
            'updated_at'    => now()
        ]);

        $statusPertanyaan = ($data['status_review'] === 'Sesuai') 
            ? Pertanyaan::STATUS_DISETUJUI 
            : Pertanyaan::STATUS_REVISI;

        DB::table('bs_pertanyaan')
            ->where('id', $id)
            ->update(['status' => $statusPertanyaan, 'updated_at' => now()]);
            
        return true;
    }
    /**
     * Buat pola pencarian case-insensitive yang portabel.
     *
     * Menggunakan LOWER() + LIKE standar SQL sebagai ganti 'ilike' (PostgreSQL-only).
     * Input di-lowercase dan di-sanitasi agar aman untuk raw query.
     */
    private function likePattern(string $term): string
    {
        return '%' . Str::lower(addcslashes($term, '%_')) . '%';
    }
}
