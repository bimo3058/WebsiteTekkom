<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenarikanSoalArchived extends Model
{
    use HasFactory;

    protected $table = 'bs_penarikan_soal_archived';

    protected $fillable = [
        'penarikan_id',
        'arsip_id',
        'archived_at',
        'archived_by',
        'catatan_konversi',
    ];

    public function penarikan(): BelongsTo
    {
        return $this->belongsTo(PenarikanSoal::class, 'penarikan_id');
    }

    public function arsip(): BelongsTo
    {
        return $this->belongsTo(ArsipSoal::class, 'arsip_id');
    }
}