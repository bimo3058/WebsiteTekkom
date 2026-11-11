<?php

namespace Modules\BankSoal\Services\Komprehensif\Imports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateNilaiParser extends AbstractKompreParser
{
    /**
     * @return array<string, mixed>
     */
    public function parse(string $path): array
    {
        $spreadsheet = $this->loadSpreadsheet($path);
        $nilai = $spreadsheet->getSheetByName('Nilai');

        if (! $nilai) {
            throw new \InvalidArgumentException('Sheet "Nilai" tidak ditemukan. Gunakan template import yang disediakan.');
        }

        $info = $spreadsheet->getSheetByName('Info Periode') ?? $spreadsheet->getSheetByName('Info');
        $period = $this->readPeriodInfo($info);

        $headerMap = $this->readHeaderMap($nilai);
        if (! isset($headerMap['nim'])) {
            throw new \InvalidArgumentException('Kolom NIM tidak ditemukan pada sheet Nilai.');
        }

        $rows = [];
        $highestRow = $nilai->getHighestDataRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nim = $this->normalizeNim($nilai->getCell($headerMap['nim'].$row)->getValue());

            if ($nim === '') {
                continue;
            }

            $periodName = isset($headerMap['period'])
                ? trim((string) $nilai->getCell($headerMap['period'].$row)->getValue())
                : '';
            $periodName = $periodName !== '' ? $periodName : ($period['nama'] ?? 'Periode Import');

            $cplValues = [];
            foreach ($headerMap as $key => $column) {
                if (! is_int($key)) {
                    continue;
                }

                $cplValues[$key] = $this->normalizeScore($nilai->getCell($column.$row)->getValue());
            }

            $total = isset($headerMap['total'])
                ? $this->normalizeScore($nilai->getCell($headerMap['total'].$row)->getValue())
                : null;

            $filledCpl = array_filter($cplValues, fn ($value) => $value !== null);

            if ($total === null && $filledCpl !== []) {
                $total = array_sum($filledCpl);
            }

            $rows[] = [
                'row' => $row,
                'nim' => $nim,
                'nama' => isset($headerMap['name']) ? trim((string) $nilai->getCell($headerMap['name'].$row)->getValue()) : '',
                'period_name' => $periodName,
                'session' => isset($headerMap['session']) ? trim((string) $nilai->getCell($headerMap['session'].$row)->getValue()) : '',
                'tanggal_ujian' => isset($headerMap['date']) ? $this->parseDateValue($nilai->getCell($headerMap['date'].$row)->getValue()) : null,
                'status' => isset($headerMap['status']) ? strtoupper(trim((string) $nilai->getCell($headerMap['status'].$row)->getValue())) : '',
                'total' => $total !== null ? round((float) $total, 2) : null,
                'average' => isset($headerMap['average']) ? $this->normalizeScore($nilai->getCell($headerMap['average'].$row)->getValue()) : null,
                'cpl_values' => $cplValues,
            ];
        }

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['period_name']][] = $row;
        }

        $periods = [];
        foreach ($grouped as $name => $periodRows) {
            $rowDates = array_values(array_filter(array_map(
                fn (array $row) => $row['tanggal_ujian'],
                $periodRows
            )));
            sort($rowDates);

            $periods[] = [
                'period' => [
                    'nama' => $name,
                    'tanggal_mulai' => $period['tanggal_mulai'] ?? ($rowDates[0] ?? now()->toDateString()),
                    'tanggal_selesai' => $period['tanggal_selesai'] ?? ($rowDates[count($rowDates) - 1] ?? now()->toDateString()),
                    'tanggal_mulai_ujian' => $period['tanggal_mulai_ujian'] ?? ($rowDates[0] ?? now()->toDateString()),
                    'tanggal_selesai_ujian' => $period['tanggal_selesai_ujian'] ?? ($rowDates[count($rowDates) - 1] ?? now()->toDateString()),
                    'status' => $period['status'] ?? null,
                ],
                'rows' => $periodRows,
            ];
        }

        return [
            'type' => 'template',
            'file_name' => basename($path),
            'file_hash' => $this->fileHash($path),
            'periods' => $periods,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function readPeriodInfo(?Worksheet $info): array
    {
        if (! $info) {
            return [];
        }

        $labels = [
            'nama periode' => 'nama',
            'tanggal mulai' => 'tanggal_mulai',
            'tanggal selesai' => 'tanggal_selesai',
            'tanggal mulai ujian' => 'tanggal_mulai_ujian',
            'tanggal selesai ujian' => 'tanggal_selesai_ujian',
            'status' => 'status',
        ];

        $values = [];
        $highestRow = min(30, max(1, $info->getHighestDataRow()));

        for ($row = 1; $row <= $highestRow; $row++) {
            $label = $this->normalizeHeader((string) $info->getCell('A'.$row)->getValue());
            if (! isset($labels[$label])) {
                continue;
            }

            $value = $info->getCell('B'.$row)->getValue();
            $key = $labels[$label];
            $values[$key] = in_array($key, ['tanggal_mulai', 'tanggal_selesai', 'tanggal_mulai_ujian', 'tanggal_selesai_ujian'], true)
                ? $this->parseDateValue($value)
                : trim((string) $value);
        }

        return $values;
    }

    /**
     * @return array<string|int, string>
     */
    private function readHeaderMap(Worksheet $sheet): array
    {
        $map = [];
        $highestColumn = $sheet->getHighestDataColumn();
        $lastColumn = Coordinate::columnIndexFromString($highestColumn);

        for ($index = 1; $index <= $lastColumn; $index++) {
            $column = Coordinate::stringFromColumnIndex($index);
            $header = $this->normalizeHeader((string) $sheet->getCell($column.'1')->getValue());

            if ($header === '') {
                continue;
            }

            $cplNumber = $this->cplNumberFromHeader($header);
            if ($cplNumber !== null) {
                $map[$cplNumber] = $column;

                continue;
            }

            $key = match (true) {
                str_contains($header, 'periode') => 'period',
                str_contains($header, 'sesi') || str_contains($header, 'jadwal') => 'session',
                str_contains($header, 'tanggal') => 'date',
                str_contains($header, 'nim') => 'nim',
                str_contains($header, 'nama') => 'name',
                str_contains($header, 'status') => 'status',
                str_contains($header, 'total') => 'total',
                str_contains($header, 'rata') => 'average',
                default => null,
            };

            if ($key !== null && ! isset($map[$key])) {
                $map[$key] = $column;
            }
        }

        return $map;
    }
}
