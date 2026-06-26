<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TemplateDokumenKP extends Model
{
    protected $table = 'eo_kp_template';

    protected $fillable = [
        'title',
        'description',
        'phase',
        'file_name',
        'file_path',
        'file_type',
        'is_required',
        'is_downloadable',
        'is_uploadable',
        'uploaded_by'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_downloadable' => 'boolean',
        'is_uploadable' => 'boolean',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
