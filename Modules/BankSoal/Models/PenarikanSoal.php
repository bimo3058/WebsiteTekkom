<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenarikanSoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bs_penarikan_soal';

    protected $fillable = [
        'dosen_id', 'mk_id', 'nama_ekstraksi', 'tipe_ujian', 'tahun_akademik', 'semester', 'tanggal_ujian', 'soal_data', 'jumlah_soal', 'total_bobot', 'pdf_file_path', 'pdf_file_hash', 'status', 'deskripsi', 'catatan_internal',
    ];

    protected $casts = [
        'soal_data' => 'array',
        'total_bobot' => 'decimal:2',
    ];

    public function getSoalArray(): array
    {
        $soal = $this->soal_data;
        if (is_string($soal)) {
            $soal = json_decode($soal, true) ?: [];
        }

        return array_map(function ($s) {
            return [
                'nomor' => $s['nomor'] ?? null,
                'id' => $s['id'] ?? null,
                'soal' => $s['soal'] ?? null,
                'cpl' => $s['cpl'] ?? null,
                'cpmk' => $s['cpmk'] ?? null,
                'tipe_soal' => $s['tipe_soal'] ?? null,
                'bobot' => $s['bobot'] ?? null,
            ];
        }, $soal ?: []);
    }
}
