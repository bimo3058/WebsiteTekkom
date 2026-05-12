<?php

namespace Modules\BankSoal\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CpmkTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['CPMK-001', 'Mampu menjelaskan teori komputasi...'],
            ['CPMK-002', 'Mampu merancang antarmuka sistem...'],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Deskripsi',
        ];
    }
}
