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
    public function koor()
    {
        return $this->belongsTo(Pengguna::class, 'koor_id');
    }

    /**
     * Daftar praktikan yang terdaftar di praktikum ini.
     */
    public function praktikans()
    {
        return $this->belongsToMany(Pengguna::class, 'daftar_praktikan', 'praktikum_id', 'pengguna_id')
                    ->withPivot('id', 'status')
                    ->withTimestamps();
    }

    /**
     * Daftar asprak/koor yang ditugaskan ke praktikum ini.
     */
    public function asparaks()
    {
        return $this->belongsToMany(User::class, 'asprak_praktikum', 'praktikum_id', 'user_id')
                    ->withPivot('id', 'role', 'deskripsi', 'deleted_at')
                    ->withTimestamps()
                    ->whereNull('asprak_praktikum.deleted_at');
    }
}
