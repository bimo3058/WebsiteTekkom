<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankSoal\Database\Factories\KompreJawabanFactory;

class KompreJawaban extends Model
{
    use HasFactory;

    protected $table = 'bs_kompre_jawaban';
    
    protected $fillable = [
        'kompre_session_id', 'pertanyaan_id', 'jawaban_dipilih', 'urutan_soal', 'kesulitan_now', 'is_benar_now', 'urutan_opsi'
    ];

    protected $casts = [
        'urutan_opsi' => 'array',
        'is_benar_now' => 'boolean'
    ];

    public function session()
    {
        return $this->belongsTo(KompreSession::class, 'kompre_session_id');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function jawaban()
    {
        return $this->belongsTo(Jawaban::class, 'jawaban_dipilih');
    }

    // protected static function newFactory(): KompreJawabanFactory
    // {
    //     // return KompreJawabanFactory::new();
    // }
}
