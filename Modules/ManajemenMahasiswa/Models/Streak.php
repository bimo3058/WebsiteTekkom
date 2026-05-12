<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Streak extends Model
{
    use HasFactory;

    protected $table = 'mk_streaks';

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_activity_date',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Cek apakah user sudah aktif hari ini.
     */
    public function isActiveToday(): bool
    {
        return $this->last_activity_date && $this->last_activity_date->isToday();
    }

    /**
     * Record aktivitas hari ini. Return true jika streak baru di-increment (bukan duplikat hari ini).
     */
    public function recordActivity(): bool
    {
        $today = Carbon::today();

        // Sudah aktif hari ini — skip
        if ($this->isActiveToday()) {
            return false;
        }

        // Jika kemarin aktif → lanjut streak
        if ($this->last_activity_date && $this->last_activity_date->isYesterday()) {
            $this->current_streak += 1;
        } else {
            // Reset streak (lewat lebih dari 1 hari)
            $this->current_streak = 1;
        }

        // Update longest streak jika baru pecah rekor
        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }

        $this->last_activity_date = $today;
        $this->save();

        return true;
    }
}
