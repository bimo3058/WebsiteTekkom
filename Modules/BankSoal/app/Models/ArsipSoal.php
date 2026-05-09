<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BankSoal\Models\Shared\MataKuliah;

class ArsipSoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bs_arsip_soal';

    protected $fillable = [
        'mk_id',
        'dosen_id',
        'tahun_akademik',
        'semester',
        'nama_arsip',
        'tipe_ujian',
        'tanggal_ujian',
        'jumlah_soal',
        'total_bobot',
        'soal_data',
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

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'dosen_id');
    }

    public function penarikanSoal(): HasMany
    {
        return $this->hasMany(PenarikanSoalArchived::class, 'arsip_id');
    }

    public function scopeByDosen($query, int $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function scopeByTahunSemester($query, string $tahunAkademik, string $semester)
    {
        return $query->where('tahun_akademik', $tahunAkademik)->where('semester', $semester);
    }

    public function getSoalArray(): array
    {
        return is_array($this->soal_data) ? $this->soal_data : (json_decode($this->soal_data ?? '[]', true) ?: []);
    }
}