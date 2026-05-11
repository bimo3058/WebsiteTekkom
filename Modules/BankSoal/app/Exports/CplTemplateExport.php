<?php

namespace Modules\BankSoal\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CplTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['CPL-001', 'Mampu menerapkan pengetahuan matematika, ilmu alam...'],
            ['CPL-002', 'Mampu mendesain komponen, sistem...'],
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
