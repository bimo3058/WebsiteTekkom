<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class KompreSession extends Model
{
    use HasFactory;

    protected $table = 'bs_kompre_session';

    protected $fillable = [
        'user_id',
        'title',
        'started_at',
        'finished_at',
        'score',
        'status',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'score'       => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_ONGOING  = 'ongoing';
    const STATUS_FINISHED = 'finished';
    const STATUS_ABORTED  = 'aborted';

    const STATUS_LIST = [
        self::STATUS_ONGOING,
        self::STATUS_FINISHED,
        self::STATUS_ABORTED,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(KompreJawaban::class, 'kompre_session_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Jumlah soal yang sudah dijawab dalam sesi ini.
     */
    public function getTotalSoalAttribute(): int
    {
        return $this->jawaban()->count();
    }

    /**
     * Jumlah jawaban benar.
     */
    public function getTotalBenarAttribute(): int
    {
        return $this->jawaban()->where('is_benar_now', true)->count();
    }
}