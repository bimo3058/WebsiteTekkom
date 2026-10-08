<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $table = 'capstone_locations';
    protected $fillable = [
        'name',
        'capacity',
        'is_active',
        'type',
        'description',
        'eoffice_ruangan_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'eoffice_ruangan_id' => 'integer',
    ];

    /**
     * Scope for active locations only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for offline locations only
     */
    public function scopeOffline($query)
    {
        return $query->where('type', 'offline');
    }

    /**
     * Scope for online/virtual locations only
     */
    public function scopeOnline($query)
    {
        return $query->where('type', 'online');
    }

    /**
     * Get all schedules at this location
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'room', 'name');
    }

    /**
     * Check if this is an online/virtual location
     */
    public function isOnline(): bool
    {
        return $this->type === 'online';
    }

    /**
     * Check if this is an offline location
     */
    public function isOffline(): bool
    {
        return $this->type === 'offline';
    }

    /**
     * Whether this location is linked to an EOffice room.
     * Online/virtual locations are never linked.
     */
    public function isEofficeLinked(): bool
    {
        $attributes = $this->getAttributes();

        return ! $this->isOnline() && ($attributes['eoffice_ruangan_id'] ?? null) !== null;
    }

    /**
     * The linked EOffice room (same database, cross-module relation).
     */
    public function eofficeRoom(): BelongsTo
    {
        return $this->belongsTo(\Modules\EOffice\Models\Ruangan::class, 'eoffice_ruangan_id');
    }
}
