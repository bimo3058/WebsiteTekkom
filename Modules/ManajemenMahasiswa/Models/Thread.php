<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
            'kategori' => 'array',
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
        'loker_karir' => 'Loker & Karir',
        'tanya_tugas' => 'Tanya Tugas',
        'info_skripsi' => 'Info Skripsi',
        'sharing_alumni' => 'Sharing Alumni',
        'umum' => 'Umum',
    ];

    const KATEGORI_COLORS = [
        'loker_karir' => 'tag-green',
        'tanya_tugas' => 'tag-red',
        'info_skripsi' => 'tag-blue',
        'sharing_alumni' => 'tag-purple',
        'umum' => 'tag-gray',
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

    public function poll(): HasOne
    {
        return $this->hasOne(ThreadPoll::class, 'thread_id');
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
        return $query->whereJsonContains('kategori', $kategori);
    }

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        $keyword = strtolower($keyword);
        return $query->where(function ($q) use ($keyword) {
            $q->whereRaw('LOWER(mk_threads.judul) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(mk_threads.konten) LIKE ?', ["%{$keyword}%"])
                ->orWhereHas('author', function ($uq) use ($keyword) {
                    $uq->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"]);
                });
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * @return array<string>
     */
    public function getKategoriLabels(): array
    {
        if (!is_array($this->kategori))
            return [];
        return array_map(fn($k) => self::KATEGORI_LABELS[$k] ?? $k, $this->kategori);
    }

    /**
     * @return array<string>
     */
    public function getKategoriColors(): array
    {
        if (!is_array($this->kategori))
            return [];
        return array_map(fn($k) => self::KATEGORI_COLORS[$k] ?? 'tag-gray', $this->kategori);
    }

    /**
     * Cek apakah thread pernah diedit (updated_at > created_at + 2 detik).
     * Toleransi 2 detik untuk menghindari false positive saat pembuatan thread.
     */
    public function isEdited(): bool
    {
        return $this->updated_at
            && $this->created_at
            && $this->updated_at->gt($this->created_at->copy()->addSeconds(2));
    }

    /**
     * Extract semua URL media (img src, video source src) dari konten HTML.
     */
    public function extractMediaUrls(): array
    {
        $urls = [];
        if (!$this->konten) {
            return $urls;
        }

        // Extract <img src="...">
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $this->konten, $matches)) {
            foreach ($matches[1] as $url) {
                $urls[] = ['type' => 'image', 'url' => $url];
            }
        }

        // Extract <video>...<source src="...">
        if (preg_match_all('/<source[^>]+src=["\']([^"\']+)["\']/i', $this->konten, $matches)) {
            foreach ($matches[1] as $url) {
                $urls[] = ['type' => 'video', 'url' => $url];
            }
        }

        return $urls;
    }

    /**
     * Hitung jumlah media di konten.
     */
    public function getMediaCount(): int
    {
        return count($this->extractMediaUrls());
    }

    /**
     * Ambil teks murni dari konten (tanpa media tags).
     */
    public function getTextContent(): string
    {
        if (!$this->konten) {
            return '';
        }

        // Hapus img tags
        $text = preg_replace('/<img[^>]*>/i', '', $this->konten);
        // Hapus video tags beserta isinya
        $text = preg_replace('/<video[^>]*>.*?<\/video>/is', '', $text);
        // Hapus link cards (anchor tag dengan class tertentu)
        $text = preg_replace('/<a[^>]*class="[^"]*d-inline-flex[^"]*"[^>]*>.*?<\/a>/is', '', $text);
        // Bersihkan br berlebih
        $text = preg_replace('/(<br\s*\/?>)+/', "\n", $text);

        return trim(strip_tags($text));
    }

    /**
     * Ambil URL pertama gambar dari konten (untuk thumbnail di listing).
     */
    public function getFirstImageUrl(): ?string
    {
        $media = $this->extractMediaUrls();
        foreach ($media as $item) {
            if ($item['type'] === 'image') {
                return $item['url'];
            }
        }
        return null;
    }

    public function syncVoteCount(): void
    {
        // Use query builder to avoid touching updated_at
        static::where('id', $this->id)->update([
            'vote_count' => $this->votes()->where('value', 1)->count(),
        ]);
    }

    public function syncCommentCount(): void
    {
        // Use query builder to avoid touching updated_at
        static::where('id', $this->id)->update([
            'comment_count' => $this->comments()->count(),
        ]);
    }
}
