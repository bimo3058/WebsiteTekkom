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
        $rows[] = ['Form Import Soal berdasarkan Topik yang dipilih (Import tidak menerima gambar)','','','','',''];
        $rows[] = ['','','Q','Question','Status Jawaban diisi dengan angka 1 untuk jawaban benar',''];
        $rows[] = ['','','A','Answer','Cell yang berwarna kuning tidak perlu diisi',''];
        $rows[] = ['','','','','',''];
        $rows[] = ['No', 'Jenis', 'Kode', 'Isi', 'Status Jawaban', 'Tingkat kesulitan Soal'];

        // Row 1 (Contoh)
        $rows[] = ['1', 'SOAL', 'Q', 'Ini adalah contoh soal...', '', '1'];
        $rows[] = ['', 'JAWABAN', 'A', 'Jawaban Salah statusnya 0', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'Jawaban Benar statusnya 1', '1', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'Jawaban Salah statusnya 0', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'Jawaban Salah statusnya 0', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', 'Jawaban Salah statusnya 0', '0', ''];

        // Row 2 (Kosong)
        $rows[] = ['2', 'SOAL', 'Q', '', '', '1'];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];

        // Row 3 (Kosong)
        $rows[] = ['3', 'SOAL', 'Q', '', '', '1'];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];
        $rows[] = ['', 'JAWABAN', 'A', '', '0', ''];

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
            }
        }

        return [
            5    => ['font' => ['bold' => true]],
        ];
    }
}