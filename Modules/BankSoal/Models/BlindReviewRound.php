<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlindReviewRound extends Model
{
    use HasFactory;

    protected $table = 'bs_blind_review_rounds';

    protected $fillable = [
        'mk_id',
        'created_by',
        'nama_round',
        'required_reviewers',
        'is_blind',
        'status',
        'deadline_at',
        'ujian_meta',
    ];

    protected $casts = [
        'required_reviewers' => 'integer',
        'is_blind'           => 'boolean',
        'deadline_at'        => 'datetime',
        'ujian_meta'         => 'array',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BlindReviewItem::class, 'round_id');
    }
}
