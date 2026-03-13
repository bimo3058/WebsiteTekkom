<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DashboardAnalitik extends Model
{
    use HasFactory;

    protected $table = 'mk_dashboard_analitik';

    protected $fillable = [
        'lecturer_id',
        'generated_by_user_id',
        'tanggal_generate',
        'total_mahasiswa_aktif',
        'total_alumni',
        'total_kegiatan',
        'total_pengumuman',
    ];

    protected $casts = [
        'tanggal_generate'     => 'datetime',
        'total_mahasiswa_aktif' => 'integer',
        'total_alumni'          => 'integer',
        'total_kegiatan'        => 'integer',
        'total_pengumuman'      => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lecturer::class, 'lecturer_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by_user_id');
    }

    // -------------------------------------------------------------------------
    // Scope — ambil snapshot terbaru
    // -------------------------------------------------------------------------

    public function scopeLatest($query)
    {
        return $query->orderByDesc('tanggal_generate');
    }
}