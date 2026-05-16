<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'mk_pengumuman';

    protected $fillable = [
        'user_id',
        'judul',
        'konten',
        'kategori',
        'target_audience',
        'status_publish',
        'scheduled_at',
        'published_at',
        'is_pinned',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'is_pinned'    => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_PENDING_REVIEW = 'pending_review';

    const STATUS_LIST = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_SCHEDULED,
        self::STATUS_ARCHIVED,
        self::STATUS_PENDING_REVIEW,
    ];

    const AUDIENCE_ALL = 'all';
    const AUDIENCE_MAHASISWA = 'mahasiswa';
    const AUDIENCE_ALUMNI = 'alumni';
    const AUDIENCE_PENGURUS = 'pengurus';
    const AUDIENCE_DOSEN = 'dosen';

    const AUDIENCE_LIST = [
        self::AUDIENCE_ALL,
        self::AUDIENCE_MAHASISWA,
        self::AUDIENCE_ALUMNI,
        self::AUDIENCE_PENGURUS,
        self::AUDIENCE_DOSEN,
    ];

    const KATEGORI_AKADEMIK = 'akademik';
    const KATEGORI_HIMPUNAN = 'himpunan';
    const KATEGORI_LOWONGAN = 'lowongan';
    const KATEGORI_EVENT_PRODI = 'event_prodi';
    const KATEGORI_LIST = [
        self::KATEGORI_AKADEMIK,
        self::KATEGORI_HIMPUNAN,
        self::KATEGORI_LOWONGAN,
        self::KATEGORI_EVENT_PRODI,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function repoMulmed(): HasMany
    {
        return $this->hasMany(RepoMulmed::class, 'pengumuman_id');
    }

    public function personalPins(): HasMany
    {
        return $this->hasMany(PengumumanPersonalPin::class, 'pengumuman_id');
    }

    /**
     * Semua approval request untuk pengumuman ini.
     */
    public function approvalRequests(): HasMany
    {
        return $this->hasMany(PengumumanApprovalRequest::class, 'pengumuman_id');
    }

    /**
     * Approval request terbaru (paling relevan).
     */
    public function latestApprovalRequest(): HasOne
    {
        return $this->hasOne(PengumumanApprovalRequest::class, 'pengumuman_id')->latestOfMany();
    }

    // -------------------------------------------------------------------------
    // Accessors / Mutators
    // -------------------------------------------------------------------------

    public function getKategoriAttribute($value): ?string
    {
        if (is_null($value)) return null;
        $decoded = json_decode($value, true);
        if (is_array($decoded) && !empty($decoded)) return $decoded[0];
        return $value;
    }

    public function setKategoriAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['kategori'] = null;
        } else {
            $val = is_array($value) ? $value : [$value];
            $this->attributes['kategori'] = json_encode($val);
        }
    }

    // ---------------------------------------- ---------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status_publish', self::STATUS_PUBLISHED);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->where(function ($q) use ($audience) {
            $q->where('target_audience', self::AUDIENCE_ALL)
              ->orWhere('target_audience', $audience);
        });
    }
  
    public function scopeScheduledReady(Builder $query): Builder
    {
        return $query->where('status_publish', self::STATUS_SCHEDULED)
                     ->where('scheduled_at', '<=', now());
    }

    // ----------------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------------------------

    public function isPublished(): bool
    {
        return $this->status_publish === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status_publish === self::STATUS_DRAFT;
    }

    public function isPendingReview(): bool
    {
        return $this->status_publish === self::STATUS_PENDING_REVIEW;
    }
}