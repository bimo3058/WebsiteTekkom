<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Rps extends Model
{
    use HasFactory;

    protected $table = 'bs_rps';

    protected $fillable = [
        'mk_id',
        'dosen_id',
        'tenggat',
        'semester_berlaku',
    ];

    protected $casts = [
        'tenggat' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    /**
     * Dosen yang membuat/mengelola RPS ini — FK ke users, bukan lecturers.
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'dosen_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(RpsDetail::class, 'rps_id');
    }

    public function detailTerbaru(): HasMany
    {
        return $this->hasMany(RpsDetail::class, 'rps_id')->latest();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByMk(Builder $query, int $mkId): Builder
    {
        return $query->where('mk_id', $mkId);
    }

    public function scopeBySemester(Builder $query, string $semester): Builder
    {
        return $query->where('semester_berlaku', $semester);
    }

    public function scopeBelumTenggat(Builder $query): Builder
    {
        return $query->where('tenggat', '>', now());
    }
}