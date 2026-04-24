<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ThreadDraft extends Model
{
    protected $table = 'mk_thread_drafts';

    protected $fillable = [
        'user_id',
        'judul',
        'kategori',
        'konten',
        'media_files',
    ];

    protected $casts = [
        'kategori' => 'array',
        'media_files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
