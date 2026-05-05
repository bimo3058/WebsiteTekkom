<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DosenPengampuMk extends Model
{
    use HasFactory;

    protected $table = 'bs_dosen_pengampu_mk';

    protected $fillable = [
        'user_id',
        'mk_id',
        'is_rps',
    ];

    protected $casts = [
        'is_rps' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Dosen yang punya akses RPS pada MK tertentu.
     */
    public function scopeRpsAccess($query)
    {
        return $query->where('is_rps', true);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByMk($query, int $mkId)
    {
        return $query->where('mk_id', $mkId);
    }
}