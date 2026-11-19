<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaAbsensi extends Model
{
    use HasUuids;
    protected $table = 'eo_prak_ta_absensi';
    protected $guarded = [];
    protected $casts = ['riwayat' => 'array', 'version' => 'integer'];
}
