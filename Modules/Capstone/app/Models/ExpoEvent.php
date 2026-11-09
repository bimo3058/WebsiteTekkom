<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;

class ExpoEvent extends Model
{
    protected $table = 'capstone_expo_events';

    use SoftDeletes;

    protected $fillable = [
        'period_id',
        'name',
        'date',
        'start_time',
        'end_time',
        'room',
        'eoffice_ruangan_id',
        'eoffice_peminjaman_id',
        'capacity',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'period_id' => 'integer',
        'is_published' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Cross-period (global) event: visible and registrable from every period.
     */
    public function isCrossPeriod(): bool
    {
        return $this->period_id === null;
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(ExpoRegistration::class);
    }

    public function eofficeRoom()
    {
        return $this->belongsTo(Ruangan::class, 'eoffice_ruangan_id');
    }

    public function eofficeBooking()
    {
        return $this->belongsTo(Peminjaman::class, 'eoffice_peminjaman_id');
    }

    /**
     * Check if event has remaining capacity.
     */
    public function hasCapacity(): bool
    {
        return $this->registered_count < $this->capacity;
    }

    /**
     * Get current registration count.
     */
    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->where('status', 'REGISTERED')->count();
    }
}
