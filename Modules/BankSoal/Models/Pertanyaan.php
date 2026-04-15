<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $table = 'bs_pertanyaan';

    const STATUS_DRAFT = 'draft';
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_REVISI = 'revisi';
    const STATUS_DISETUJUI = 'disetujui';

    protected $fillable = [
        'soal',
        'gambar',
        'bobot',
        'cpl_id',
        'cpmk_id',
        'mk_id',
        'kesulitan',
        'status',
        'tipe_soal',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function cpl(): BelongsTo
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }

    public function cpmk(): BelongsTo
    {
        return $this->belongsTo(\Modules\BankSoal\Models\Shared\Cpmk::class, 'cpmk_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'soal_id');
    }

    public function scopeByMk($query, int $mkId)
    {
        return $query->where('mk_id', $mkId);
    }

    public function scopeByCpl($query, int $cplId)
    {
        return $query->where('cpl_id', $cplId);
    }

    public function scopeByKesulitan($query, string $kesulitan)
    {
        return $query->where('kesulitan', $kesulitan);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where('soal', 'like', "%{$keyword}%");
    }

    public function isDisetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        // Simple mock of transitions
        return match($this->status) {
            self::STATUS_DRAFT => in_array($newStatus, [self::STATUS_DIAJUKAN, self::STATUS_DRAFT]),
            self::STATUS_DIAJUKAN => in_array($newStatus, [self::STATUS_DISETUJUI, self::STATUS_REVISI]),
            self::STATUS_REVISI => in_array($newStatus, [self::STATUS_DIAJUKAN, self::STATUS_DRAFT]),
            self::STATUS_DISETUJUI => false, // Final state
            default => false,
        };
    }
}
