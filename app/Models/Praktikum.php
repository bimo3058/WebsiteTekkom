<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Praktikum extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $table = 'praktikum';

    protected static array $logAttributes = ['nama', 'kode', 'deskripsi', 'dosen_id', 'koor_id', 'tahun_ajaran', 'semester', 'status'];

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'dosen_id',
        'koor_id',
        'tahun_ajaran',
        'semester',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun_ajaran' => 'integer',
            'deleted_at'   => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Dosen yang mengampu praktikum ini.
     */
    public function dosen()
    {
        return $this->belongsTo(Pengguna::class, 'dosen_id');
    }

    /**
     * Koordinator praktikum ini.
     */
    public function koordinator()
    {
        return $this->belongsTo(Pengguna::class, 'koor_id');
    }
}
