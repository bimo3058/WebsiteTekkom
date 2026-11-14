<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasilReviewRps extends Model
{
    use HasFactory;

    protected $table = 'bs_hasil_review_rps';

    protected $fillable = [
        'rps_detail_id',
        'parameter_id',
        'skor',
    ];

    protected $casts = [
        'skor' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function rpsDetail(): BelongsTo
    {
        return $this->belongsTo(RpsDetail::class, 'rps_detail_id');
    }

    /**
     * Parameter yang dinilai.
     *
     * withTrashed() dipakai supaya histori penilaian tetap utuh: parameter yang
     * "dihapus" (soft delete) hanya disembunyikan dari daftar/form frontend, bukan
     * menghilangkan bobot pada review yang sudah tersimpan.
     */
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class, 'parameter_id')->withTrashed();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Skor tertimbang = skor × bobot parameter.
     */
    public function getSkorTertimbangAttribute(): float
    {
        return $this->skor * ($this->parameter->bobot ?? 1);
    }
}
