<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'mk_prestasi';

    protected $fillable = [
        'kemahasiswaan_id',
        'nama_prestasi',
        'tingkat',
        'tanggal',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'verified_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const TINGKAT_INTERNASIONAL = 'internasional';
    const TINGKAT_NASIONAL      = 'nasional';
    const TINGKAT_REGIONAL      = 'regional';
    const TINGKAT_UNIVERSITAS   = 'universitas';
    const TINGKAT_PRODI         = 'prodi';

    const TINGKAT_LIST = [
        self::TINGKAT_INTERNASIONAL,
        self::TINGKAT_NASIONAL,
        self::TINGKAT_REGIONAL,
        self::TINGKAT_UNIVERSITAS,
        self::TINGKAT_PRODI,
    ];

    // Verification statuses
    const VERIF_PENDING  = 'pending';
    const VERIF_APPROVED = 'approved';
    const VERIF_REJECTED = 'rejected';

    const VERIF_LIST = [
        self::VERIF_PENDING,
        self::VERIF_APPROVED,
        self::VERIF_REJECTED,
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kemahasiswaan(): BelongsTo
    {
        return $this->belongsTo(Kemahasiswaan::class, 'kemahasiswaan_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function buktiFiles()
    {
        return $this->hasMany(VerifikasiBukti::class, 'bukti_id')
            ->where('bukti_type', VerifikasiBukti::TYPE_PRESTASI);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByTingkat(Builder $query, string $tingkat): Builder
    {
        return $query->where('tingkat', $tingkat);
    }

    public function scopeByTahun(Builder $query, int $tahun): Builder
    {
        return $query->whereYear('tanggal', $tahun);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_REJECTED);
    }
}