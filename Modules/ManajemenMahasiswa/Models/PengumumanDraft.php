<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
