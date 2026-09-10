<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlindReviewItem extends Model
{
    use HasFactory;

    protected $table = 'bs_blind_review_items';

    protected $fillable = [
        'round_id',
        'pertanyaan_id',
        'reviewer_id',
        'assigned_by',
        'is_blind',
        'status',
        'catatan',
        'reviewed_at',
    ];

    protected $casts = [
        'is_blind' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function round(): BelongsTo
    {
        return $this->belongsTo(BlindReviewRound::class, 'round_id');
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewer_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }
}
