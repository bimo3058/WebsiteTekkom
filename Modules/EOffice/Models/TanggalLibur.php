<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanggalLibur extends Model
{
    use HasFactory;

    protected $table = 'eo_mr_tanggal_libur';

    protected $fillable = [
        'tanggal',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
