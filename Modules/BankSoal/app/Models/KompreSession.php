<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankSoal\Database\Factories\KompreSessionFactory;

class KompreSession extends Model
{
    use HasFactory;

    protected $table = 'bs_kompre_session';
    
    protected $fillable = [
        'user_id', 'jadwal_id', 'title', 'started_at', 'finished_at', 'score', 'status'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function jawabans()
    {
        return $this->hasMany(KompreJawaban::class, 'kompre_session_id')->orderBy('urutan_soal');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(\Modules\BankSoal\Models\JadwalUjian::class, 'jadwal_id');
    }

    // protected static function newFactory(): KompreSessionFactory
    // {
    //     // return KompreSessionFactory::new();
    // }
}
