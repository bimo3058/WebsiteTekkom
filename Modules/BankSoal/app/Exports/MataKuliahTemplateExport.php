<?php

namespace Modules\BankSoal\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MataKuliahTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['PTSK6103', 'Contoh Mata Kuliah 1', '3', '1'],
            ['PTSK6104', 'Contoh Mata Kuliah 2', '2', '2'],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'SKS',
            'Semester',
        ];
    }
}
