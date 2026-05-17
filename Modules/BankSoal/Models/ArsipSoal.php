<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArsipSoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bs_arsip_soal';

    protected $fillable = [
        'mk_id', 'dosen_id', 'tahun_akademik', 'semester', 'nama_arsip', 'tipe_ujian', 'tanggal_ujian', 'jumlah_soal', 'total_bobot', 'soal_data', 'pdf_file_path', 'pdf_file_hash', 'status', 'deskripsi', 'catatan_internal',
    ];

    protected $casts = [
        'soal_data' => 'array',
        'total_bobot' => 'decimal:2',
    ];
}
