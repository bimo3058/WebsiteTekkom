<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\BankSoal\Database\Factories\PertanyaanFactory;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $table = 'bs_pertanyaan';
    
    protected $fillable = [
        'soal', 'gambar', 'bobot', 'cpl_id', 'mk_id', 'kesulitan', 'status', 'tipe_soal'
    ];

    public function cpl()
    {
        return $this->belongsTo(\Modules\BankSoal\Models\Shared\Cpl::class, 'cpl_id');
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class, 'pertanyaan_id');
    }

    // protected static function newFactory(): PertanyaanFactory
    // {
    //     // return PertanyaanFactory::new();
    // }
}
