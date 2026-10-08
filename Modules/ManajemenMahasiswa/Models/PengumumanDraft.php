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
        'poster_repo_ids',
        'lampiran_repo_ids',
    ];

    protected $casts = [
        'poster_repo_ids'   => 'array',
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
     * ID gambar pengumuman sesuai urutan yang dipilih — indeks 0 adalah cover.
     *
     * Draf lama hanya punya satu gambar di kolom `poster_repo_id`, jadi nilainya
     * dipakai sebagai fallback supaya draf yang dibuat sebelum fitur multi-gambar
     * tetap terbaca.
     */
    public function posterRepoIds(): array
    {
        return array_values(array_filter(
            $this->poster_repo_ids ?: [$this->poster_repo_id]
        ));
    }

    /**
     * Semua ID file (gambar + lampiran) yang menempel pada draf ini.
     *
     * Disimpan sebagai array ID, bukan relasi HasMany, karena satu baris
     * mk_repo_mulmed hanya boleh terikat ke satu pengumuman saat dipublikasikan.
     */
    public function allRepoIds(): array
    {
        return array_values(array_filter(
            array_merge($this->posterRepoIds(), $this->lampiran_repo_ids ?? [])
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
