<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PengumumanDraft extends Model
{
    protected $table = 'mk_pengumuman_drafts';

    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'target_audience',
        'konten',
        'poster_repo_id',
        'lampiran_repo_ids',
    ];

    protected $casts = [
        'lampiran_repo_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Poster yang sudah diupload saat draf disimpan.
     */
    public function posterRepo(): BelongsTo
    {
        return $this->belongsTo(RepoMulmed::class, 'poster_repo_id');
    }

    /**
     * Semua ID file (poster + lampiran) yang menempel pada draf ini.
     *
     * Lampiran disimpan sebagai array ID, bukan relasi HasMany, karena satu baris
     * mk_repo_mulmed hanya boleh terikat ke satu pengumuman saat dipublikasikan.
     */
    public function allRepoIds(): array
    {
        return array_values(array_filter(
            array_merge([$this->poster_repo_id], $this->lampiran_repo_ids ?? [])
        ));
    }

    // Accessor: kembalikan string tunggal (bukan raw JSON array)
    public function getKategoriAttribute($value): ?string
    {
        if (is_null($value)) return null;
        $decoded = json_decode($value, true);
        if (is_array($decoded) && !empty($decoded)) return $decoded[0];
        return $value;
    }

    // Mutator: simpan sebagai JSON array agar kompatibel dengan kolom JSONB
    public function setKategoriAttribute($value): void
    {
        if (is_null($value) || $value === '') {
            $this->attributes['kategori'] = null;
        } else {
            $val = is_array($value) ? $value : [$value];
            $this->attributes['kategori'] = json_encode($val);
        }
    }
}
