<?php

namespace Modules\EOffice\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Periode pendaftaran koor/asprak per praktikum.
 * Tabel: manprak_periode_pendaftaran
 * jenis: koor | asprak | praktikan
 */
class PeriodePendaftaran extends Model
{
    protected $table = 'manprak_periode_pendaftaran';

    protected $fillable = [
        'praktikum_id',
        'jenis',
        'nama',
        'dibuka_pada',
        'ditutup_pada',
        'is_aktif',
        'dibuka_oleh',
    ];

    protected $casts = [
        'dibuka_pada'  => 'datetime',
        'ditutup_pada' => 'datetime',
        'is_aktif'     => 'boolean',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function dibukaOleh()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function isSedangBuka(): bool
    {
        if (!$this->is_aktif) return false;
        $now = now();
        if ($this->dibuka_pada && $now->lt($this->dibuka_pada)) return false;
        if ($this->ditutup_pada && $now->gt($this->ditutup_pada)) return false;
        return true;
    }
}
