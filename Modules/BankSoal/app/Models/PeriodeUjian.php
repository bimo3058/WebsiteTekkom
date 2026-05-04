<?php

namespace Modules\BankSoal\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodeUjian extends Model
{
    use SoftDeletes;

    protected $table = 'bs_periode_ujians';

    protected $fillable = [
        'nama_periode',
        'slug',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_ujian',
        'tanggal_selesai_ujian',
        'status',
        'deskripsi',
        'pendaftaran_ditutup_paksa',
    ];

    protected $casts = [
        'tanggal_mulai'             => 'date',
        'tanggal_selesai'           => 'date',
        'tanggal_mulai_ujian'       => 'date',
        'tanggal_selesai_ujian'     => 'date',
        'pendaftaran_ditutup_paksa' => 'boolean',
    ];

    /**
     * Scope: Periode yang sedang dalam rentang tanggal pendaftaran (date-driven).
     * Tidak bergantung pada nilai status — status dikelola otomatis oleh controller.
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query->where('tanggal_mulai', '<=', now())
                     ->where('tanggal_selesai', '>=', now())
                     ->where('status', '!=', 'selesai');
    }

    /**
     * Apakah pendaftaran sedang terbuka?
     * Cukup cek tanggal dan flag ditutup_paksa — tidak perlu cek status='aktif' secara eksplisit.
     */
    public function getPendaftaranTerbukaAttribute(): bool
    {
        return $this->status !== 'selesai'
            && !$this->pendaftaran_ditutup_paksa
            && now()->between(
                Carbon::parse($this->tanggal_mulai)->startOfDay(),
                Carbon::parse($this->tanggal_selesai)->endOfDay()
            );
    }
}

