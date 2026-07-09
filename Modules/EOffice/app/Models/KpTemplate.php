<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpTemplate extends Model
{
    protected $table = 'eo_kp_template';

    protected $fillable = [
        'periode_id',
        'title',
        'description',
        'phase',
        'file_name',
        'file_path',
        'file_type',
        'is_required',
        'is_downloadable',
        'is_uploadable',
        'uploaded_by',
        'approver_role',
    ];

    public function periode()
    {
        return $this->belongsTo(KpPeriode::class, 'periode_id');
    }
}
