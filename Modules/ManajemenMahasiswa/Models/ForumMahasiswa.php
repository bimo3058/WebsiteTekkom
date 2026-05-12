<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mk_forum_mahasiswa';

    protected $fillable = [
        'nama_forum',
        'deskripsi',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class, 'forum_id');
    }
}