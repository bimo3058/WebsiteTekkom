<?php

namespace App\Models;

use App\Enums\DaftarPraktikanStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DaftarPraktikan extends Model
{
    use HasUuids;

    protected $table = 'daftar_praktikan';

    protected $fillable = [
        'praktikum_id',
        'pengguna_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DaftarPraktikanStatus::class,
        ];
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}
