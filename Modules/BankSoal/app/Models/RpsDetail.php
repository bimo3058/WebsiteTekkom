<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class RpsDetail extends Model
{
    use HasFactory;

    protected $table = 'bs_rps_detail';

    protected $fillable = [
        'rps_id',
        'dokumen',
        'status_rps',
        'catatan',
        'nilai_akhir',
    ];

    protected $casts = [
        'nilai_akhir' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Constants — mirror dari Pertanyaan agar konsisten
    // -------------------------------------------------------------------------

    const STATUS_DRAFT     = 'draft';
    const STATUS_DIAJUKAN  = 'diajukan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_REVISI    = 'revisi';

    const STATUS_LIST = [
        self::STATUS_DRAFT,
        self::STATUS_DIAJUKAN,
        self::STATUS_DISETUJUI,
        self::STATUS_REVISI,
    ];

    const STATUS_TRANSITIONS = [
        self::STATUS_DRAFT     => [self::STATUS_DIAJUKAN],
        self::STATUS_DIAJUKAN  => [self::STATUS_DISETUJUI, self::STATUS_REVISI],
        self::STATUS_REVISI    => [self::STATUS_DIAJUKAN],
        self::STATUS_DISETUJUI => [],
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function rps(): BelongsTo
    {
        return $this->belongsTo(Rps::class, 'rps_id');
    }

    public function hasilReview(): HasMany
    {
        return $this->hasMany(HasilReviewRps::class, 'rps_detail_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status_rps', self::STATUS_DISETUJUI);
    }

    public function scopeMenungguReview(Builder $query): Builder
    {
        return $query->where('status_rps', self::STATUS_DIAJUKAN);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_TRANSITIONS[$this->status_rps] ?? []);
    }

    /**
     * Total skor review dari semua parameter.
     */
    public function getTotalSkorReviewAttribute(): int
    {
        return $this->hasilReview->sum('skor');
    }
}