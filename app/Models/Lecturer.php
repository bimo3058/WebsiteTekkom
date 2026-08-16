<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    public const LEGACY_CAPSTONE_ACTOR_PREFIX = 'NEON-CAPSTONE-ACTOR-';

    protected $with = ['user'];

    protected $appends = ['name', 'email'];

    protected $fillable = [
        'user_id',
        'employee_number',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getNipAttribute(): ?string
    {
        return $this->employee_number;
    }
}
