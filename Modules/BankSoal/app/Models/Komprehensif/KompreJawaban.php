<?php

namespace Modules\BankSoal\Models\Komprehensif;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BankSoal\Models\Jawaban;
use Modules\BankSoal\Models\Pertanyaan;

class KompreJawaban extends Model
{
    use HasFactory;

    protected $table = 'bs_kompre_jawaban';

    protected $fillable = [
        'kompre_session_id', 'pertanyaan_id', 'jawaban_dipilih',
        'urutan_soal', 'kesulitan_now', 'is_benar_now', 'urutan_opsi', 'is_ragu',
    ];

    protected $casts = [
        'urutan_opsi'  => 'array',
        'is_benar_now' => 'boolean',
        'is_ragu'      => 'boolean',
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

    /**
     * Alias untuk jawaban() — nama lebih deskriptif untuk context CBT scoring.
     */
    public function opsiTerpilih()
    {
        return $this->belongsTo(Jawaban::class, 'jawaban_dipilih');
    }
}
