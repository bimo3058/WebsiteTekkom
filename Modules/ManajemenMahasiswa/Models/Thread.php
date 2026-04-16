<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Thread extends Model
{
    use HasFactory;

    protected $table = 'mk_threads';

    protected $fillable = [
        'user_id',
        'kategori',
        'judul',
        'konten',
        'is_pinned',
        'is_locked',
        'best_answer_id',
        'vote_count',
        'comment_count',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const KATEGORI_LIST = [
        'loker_karir',
        'tanya_tugas',
        'info_skripsi',
        'sharing_alumni',
        'umum',
    ];

    const KATEGORI_LABELS = [
        'loker_karir'    => 'Loker & Karir',
        'tanya_tugas'    => 'Tanya Tugas',
        'info_skripsi'   => 'Info Skripsi',
        'sharing_alumni' => 'Sharing Alumni',
        'umum'           => 'Umum',
    ];

    const KATEGORI_COLORS = [
        'loker_karir'    => 'tag-green',
        'tanya_tugas'    => 'tag-red',
        'info_skripsi'   => 'tag-blue',
        'sharing_alumni' => 'tag-purple',
        'umum'           => 'tag-gray',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'thread_id');
    }

    public function topLevelComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'thread_id')->whereNull('parent_id');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function bestAnswer(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'best_answer_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotLocked(Builder $query): Builder
    {
        return $query->where('is_locked', false);
    }

    public function scopeByKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('konten', 'like', "%{$keyword}%");
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function kategoriLabel(): string
    {
        return self::KATEGORI_LABELS[$this->kategori] ?? $this->kategori;
    }

    public function kategoriColor(): string
    {
        return self::KATEGORI_COLORS[$this->kategori] ?? 'tag-gray';
    }

    public function syncVoteCount(): void
    {
        $this->update([
            'vote_count' => $this->votes()->sum('value'),
        ]);
    }

    public function syncCommentCount(): void
    {
        $this->update([
            'comment_count' => $this->comments()->count(),
        ]);
    }
}
