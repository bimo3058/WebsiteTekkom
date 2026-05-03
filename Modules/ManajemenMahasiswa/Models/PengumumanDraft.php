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
}
