<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'mk_badges';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image',
        'description',
        'criteria_type',
        'criteria_value',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const CRITERIA_THREAD_COUNT        = 'thread_count';
    const CRITERIA_UPVOTE_COUNT        = 'upvote_count';
    const CRITERIA_BEST_ANSWER_COUNT   = 'best_answer_count';
    const CRITERIA_STREAK              = 'streak';
    const CRITERIA_TOTAL_XP            = 'total_xp';
    const CRITERIA_COMMENT_COUNT       = 'comment_count';
    const CRITERIA_FIRST_ANSWER_COUNT  = 'first_answer_count';
    const CRITERIA_ALUMNI_COMMENT_COUNT = 'alumni_comment_count';
    const CRITERIA_DOSEN_COMMENT_COUNT  = 'dosen_comment_count';

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'mk_user_badges', 'badge_id', 'user_id')
                    ->withPivot('earned_at');
    }
}
