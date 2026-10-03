<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'is_published' => 'boolean',
        'capacity' => 'integer',
    ];

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
        return $this->belongsTo(\Modules\EOffice\Models\Ruangan::class, 'eoffice_ruangan_id');
    }

    public function eofficeBooking()
    {
        return $this->belongsTo(\Modules\EOffice\Models\Peminjaman::class, 'eoffice_peminjaman_id');
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
