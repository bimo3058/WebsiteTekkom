<?php

namespace Modules\BankSoal\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankSoalExport implements FromArray, WithStyles
{
    public function array(): array
    {
        $rows = [];
        
        // Header
        $rows[] = ['Form Import Soal berdasarkan Topik yang dipilih (Import tidak menerima gambar)','','','','','','',''];
        $rows[] = ['','','Q','Question','Status Jawaban diisi dengan angka 1 untuk jawaban benar','','',''];
        $rows[] = ['','','A','Answer','Cell yang berwarna kuning tidak perlu diisi','','',''];
        $rows[] = ['','','','','','','',''];
        $rows[] = ['No', 'Jenis', 'Kode', 'Isi', 'Status Jawaban', 'Tingkat kesulitan (easy/intermediate/advanced atau 1/2/3)', 'CPL(contoh CPL-001)', 'Mata Kuliah'];

        // Row 1 (Contoh)
        $rows[] = ['1', 'SOAL', 'Q', 'Turunan pertama dari f(x) = 3x^2 + 2x adalah...', '', 'easy', 'CPL-001', 'Kalkulus'];
        $rows[] = ['', 'JAWABAN', 'A', '6x + 2', '1', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '3x + 2', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '6x', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'x^2', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '0', '0', '', '', ''];

        // Row 2 (Contoh Tambahan)
        $rows[] = ['2', 'SOAL', 'Q', 'Integral dari 2x dx adalah...', '', 'intermediate', 'CPL-001', 'Kalkulus'];
        $rows[] = ['', 'JAWABAN', 'A', 'x^2 + C', '1', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '2x^2 + C', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'x^2', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '2x', '0', '', '', ''];
        $rows[] = ['', 'JAWABAN', 'A', '0', '0', '', '', ''];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Warnai cell A (No) dan F (Tingkat Kesulitan) dengan warna kuning pada baris JAWABAN
        $highestRow = $sheet->getHighestRow();
        for ($row = 6; $row <= $highestRow; $row++) {
            if ($sheet->getCell('B' . $row)->getValue() == 'JAWABAN') {
                $sheet->getStyle('A' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00'); // Kode warna Hex Kuning
                
                $sheet->getStyle('F' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');

                // Juga warnai kolom CPL (G) dan MK (H)
                $sheet->getStyle('G' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');
                
                $sheet->getStyle('H' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFFF00');
            }
        }

        return [
            5    => ['font' => ['bold' => true]],
        ];
    }
}