<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankSoal\Database\Factories\JawabanFactory;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'bs_jawaban';
    
    protected $fillable = [
        'soal_id', 'opsi', 'deskripsi', 'is_benar'
    ];

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'soal_id');
    }

    // protected static function newFactory(): JawabanFactory
    // {
    //     // return JawabanFactory::new();
    // }
}
