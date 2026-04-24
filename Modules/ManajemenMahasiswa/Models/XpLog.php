<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class XpLog extends Model
{
    use HasFactory;

    protected $table = 'mk_xp_logs';

    protected $fillable = [
        'user_id',
        'action',
        'xp_amount',
        'reference_type',
        'reference_id',
    ];

    // -------------------------------------------------------------------------
    // Constants — XP amounts per action
    // -------------------------------------------------------------------------

    const ACTION_CREATE_THREAD         = 'create_thread';
    const ACTION_COMMENT               = 'comment';
    const ACTION_RECEIVE_UPVOTE        = 'receive_upvote';
    const ACTION_RECEIVE_THREAD_UPVOTE = 'receive_thread_upvote';
    const ACTION_BEST_ANSWER           = 'best_answer';
    const ACTION_DAILY_LOGIN           = 'daily_login';
    const ACTION_STREAK_BONUS          = 'streak_bonus';
    const ACTION_STREAK_BONUS_30       = 'streak_bonus_30';
    const ACTION_DOWNVOTE_PENALTY      = 'downvote_penalty';

    const XP_MAP = [
        self::ACTION_CREATE_THREAD         => 10,
        self::ACTION_COMMENT               => 5,
        self::ACTION_RECEIVE_UPVOTE        => 2,
        self::ACTION_RECEIVE_THREAD_UPVOTE => 3,
        self::ACTION_BEST_ANSWER           => 15,
        self::ACTION_DAILY_LOGIN           => 3,
        self::ACTION_STREAK_BONUS          => 20,
        self::ACTION_STREAK_BONUS_30       => 100,
        self::ACTION_DOWNVOTE_PENALTY      => -1,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
