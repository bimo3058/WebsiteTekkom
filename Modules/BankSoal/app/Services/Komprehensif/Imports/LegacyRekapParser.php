<?php

namespace Modules\BankSoal\Services\Komprehensif\Imports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class LegacyRekapParser extends AbstractKompreParser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $path): array
    {
        $spreadsheet = $this->loadSpreadsheet($path);
        $sheet = $spreadsheet->getSheet(0);

        $title = trim((string) $sheet->getCell('A1')->getValue());
        if (stripos($title, 'REKAPITULASI HASIL TES') === false) {
            throw new \InvalidArgumentException('File bukan rekap hasil tes komprehensif.');
        }

        $group = trim((string) $sheet->getCell('C3')->getValue());
        $range = $this->parsePeriodRange((string) $sheet->getCell('C4')->getValue());
        $testCount = (int) $sheet->getCell('C5')->getValue();

        $cplColumns = [];
        $otherTestColumns = [];
        $highestColumn = $sheet->getHighestDataColumn();
        $lastColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($index = Coordinate::columnIndexFromString('H'); $index <= $lastColumnIndex; $index++) {
            $column = Coordinate::stringFromColumnIndex($index);
            $header = trim((string) $sheet->getCell($column.'8')->getValue());

            if ($header === '') {
                continue;
            }

            $cplNumber = $this->cplNumberFromHeader($header);
            if ($cplNumber !== null) {
                $cplColumns[$column] = $cplNumber;
            } else {
                $otherTestColumns[] = $column;
            }
        }

        $session = $this->parseSession($group);
        $sessionDate = $session['tanggal_ujian'] ?? $range['start'] ?? '2025-07-01';
        $periodDate = Carbon::parse($sessionDate);

        $period = [
            'nama' => 'Ujian Komprehensif '.$this->monthName($periodDate->month).' '.$periodDate->year,
            'tanggal_mulai' => $sessionDate,
            'tanggal_selesai' => $sessionDate,
            'tanggal_mulai_ujian' => $sessionDate,
            'tanggal_selesai_ujian' => $sessionDate,
            'status' => 'selesai',
        ];

        $rows = [];
        $highestRow = $sheet->getHighestDataRow();

        for ($row = 9; $row <= $highestRow; $row++) {
            $nim = $this->normalizeNim($sheet->getCell('B'.$row)->getValue());

            if ($nim === '') {
                continue;
            }

            $cplValues = [];
            foreach ($cplColumns as $column => $cplNumber) {
                $cplValues[$cplNumber] = $this->normalizeScore($sheet->getCell($column.$row)->getValue()) ?? 0.0;
            }

            $total = $this->normalizeScore($sheet->getCell('G'.$row)->getValue());

            if ($total === null && $cplValues === [] && $otherTestColumns !== []) {
                foreach ($otherTestColumns as $column) {
                    $total = $this->normalizeScore($sheet->getCell($column.$row)->getValue());
                    if ($total !== null) {
                        break;
                    }
                }
            }

            if ($total === null) {
                $total = $cplValues === [] ? 0.0 : array_sum($cplValues);
            }

            $rows[] = [
                'row' => $row,
                'nim' => $nim,
                'nama' => trim((string) $sheet->getCell('C'.$row)->getValue()),
                'status' => strtoupper(trim((string) $sheet->getCell('E'.$row)->getValue())),
                'total' => round((float) $total, 2),
                'average' => $this->normalizeScore($sheet->getCell('F'.$row)->getValue()),
                'cpl_values' => $cplValues,
            ];
        }

        return [
            'type' => 'legacy_rekap',
            'file_name' => basename($path),
            'file_hash' => $this->fileHash($path),
            'test_count' => $testCount,
            'has_cpl' => $cplColumns !== [],
            'cpl_columns' => $cplColumns,
            'period' => $period,
            'session' => [
                'nama_sesi' => $session['nama_sesi'],
                'tanggal_ujian' => $sessionDate,
            ],
            'rows' => $rows,
        ];
    }
}
