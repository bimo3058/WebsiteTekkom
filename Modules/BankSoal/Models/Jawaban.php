<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'bs_jawaban';

    protected $fillable = [
        'soal_id',
        'opsi',
        'deskripsi',
        'gambar',
        'is_benar',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(Pertanyaan::class, 'soal_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeBenar($query)
    {
        return $query->where('is_benar', true);
    }
}