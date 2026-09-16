<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\EOffice\Database\Factories\KpMasterRubrikFactory;

class KpMasterRubrik extends Model
{
    use HasFactory;

    protected $table = 'eo_kp_master_rubrik';

    protected $fillable = [
        'kode',
        'deskripsi',
        'bobot',
        'role_penilai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bobot' => 'integer',
    ];
}
