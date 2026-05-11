<?php

namespace Modules\BankSoal\Models\Komprehensif;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BankSoal\Enums\KompreSessionStatus;

class KompreSession extends Model
{
    use HasFactory;

    protected $table = 'bs_kompre_session';

    protected $fillable = [
        'user_id', 'jadwal_id', 'title', 'started_at', 'finished_at', 'score', 'status',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'status'      => KompreSessionStatus::class,
    ];

    public function jawabans()
    {
        return $this->hasMany(KompreJawaban::class, 'kompre_session_id')->orderBy('urutan_soal');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_id');
    }

    public function cheatLogs()
    {
        return $this->hasMany(CheatLog::class, 'kompre_session_id');
    }
}
