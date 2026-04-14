<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RpsTemplate extends Model
{
    protected $table = 'bs_rps_templates';
    protected $fillable = [
        'original_filename',
        'filename',
        'file_path',
        'version',
        'created_by',
        'is_latest',
        'keterangan'
    ];

    protected $casts = [
        'is_latest' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi dengan User yang upload template
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope untuk mendapatkan template terbaru
     */
    public function scopeLatest($query)
    {
        return $query->where('is_latest', true)->orderBy('version', 'desc')->first();
    }

    /**
     * Scope untuk mendapatkan semua versi template
     */
    public function scopeAllVersions($query)
    {
        return $query->with('uploadedBy')->orderBy('version', 'desc');
    }
}
