<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BankSoal\Models\Shared\MataKuliah;

class PenarikanSoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bs_penarikan_soal';

    protected $fillable = [
        'dosen_id',
        'mk_id',
        'nama_ekstraksi',
        'tipe_ujian',
        'metode_ujian',
        'status_cetak',
        'tahun_akademik',
        'semester',
        'tanggal_ujian',
        'soal_data',
        'jumlah_soal',
        'total_bobot',
        'pdf_file_path',
        'pdf_file_hash',
        'status',
        'deskripsi',
        'catatan_internal',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'soal_data' => 'array',
        'total_bobot' => 'decimal:2',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'dosen_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function arsipTracking(): HasOne
    {
        return $this->hasOne(PenarikanSoalArchived::class, 'penarikan_id');
    }

    public function scopeByDosen($query, int $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function getSoalArray(): array
    {
        return is_array($this->soal_data) ? $this->soal_data : (json_decode($this->soal_data ?? '[]', true) ?: []);
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}