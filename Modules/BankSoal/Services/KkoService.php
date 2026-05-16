<?php

namespace Modules\BankSoal\Services;

class KkoService
{
    // Return the display label for a KKO code like 'C1', 'P', 'A'.
    public function label(string $code): string
    {
        $map = [
            'C1' => 'Mengingat',
            'C2' => 'Memahami',
            'C3' => 'Menerapkan',
            'C4' => 'Menganalisis',
            'C5' => 'Mengevaluasi',
            'C6' => 'Mencipta',
            'P1' => 'Meniru',
            'P2' => 'Menyesuaikan',
            'P3' => 'Membiasakan',
            'P4' => 'Menguasai',
            'P5' => 'Mahir',
            'A1' => 'Menerima',
            'A2' => 'Merespon',
            'A3' => 'Menilai',
            'A4' => 'Mengorganisasi',
            'A5' => 'Menghayati',
            'P'  => 'Praktik',
            'A'  => 'Afektif',
        ];

        $code = strtoupper(trim($code));

        return $map[$code] ?? $code;
    }
}
