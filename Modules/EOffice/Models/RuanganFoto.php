<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RuanganFoto extends Model
{
    use HasFactory;

    protected $table = 'eo_mr_ruangan_fotos';

    protected $fillable = [
        'ruangan_id',
        'path_foto',
        'urutan',
    ];

    public function getFotoUrlAttribute(): string
    {
        if (empty($this->path_foto)) {
            return '';
        }

        return app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->path_foto, 'eoffice');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
