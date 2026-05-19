<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpTemplate extends Model
{
    protected $table = 'eo_kp_template';
    
    protected $fillable = [
        'nama_template',
        'fase',
        'file_path'
    ];
}
