<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentForum extends Model
{
    use HasFactory;

    protected $table = 'mk_comment_forum';

    protected $fillable = [
        'discussion_id',
        'user_id',
        'isi_comment',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}