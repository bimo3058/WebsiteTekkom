<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RpsGenerateDetail extends Model
{
    protected $table = 'bs_rps_generate_detail';

    protected $fillable = [
        'rps_detail_id',
        'created_by',
        'deskripsi_mk',
        'penilaian_data',
        'pertemuan_data',
        'referensi_data',
    ];

    protected $casts = [
        'penilaian_data' => 'array',
        'pertemuan_data' => 'array',
        'referensi_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rpsDetail()
    {
        return $this->belongsTo(RpsDetail::class, 'rps_detail_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
