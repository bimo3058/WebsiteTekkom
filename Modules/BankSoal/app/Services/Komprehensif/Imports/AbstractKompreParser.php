<?php

namespace Modules\BankSoal\Services\Komprehensif\Imports;

use Carbon\Carbon;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

abstract class AbstractKompreParser
{
    protected const MONTHS = [
        'januari' => 1,
        'februari' => 2,
        'maret' => 3,
        'april' => 4,
        'mei' => 5,
        'juni' => 6,
        'juli' => 7,
        'agustus' => 8,
        'september' => 9,
        'oktober' => 10,
        'november' => 11,
        'desember' => 12,
    ];

    protected function loadSpreadsheet(string $path): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        return $reader->load($path);
    }

    protected function normalizeNim(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_int($value) || is_float($value)) {
            return sprintf('%.0f', (float) $value);
        }

        $nim = trim((string) $value);

        if (preg_match('/^\d+(?:[.,]\d+)?E\+\d+$/i', $nim)) {
            return sprintf('%.0f', (float) str_replace(',', '.', $nim));
        }

        return $nim;
    }

    protected function normalizeScore(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', trim($value));
        }

        if (! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }

    protected function parseDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return $this->parseIndonesianDate($value);
        }
    }

    protected function parseIndonesianDate(string $text): ?string
    {
        $text = trim($text);

        if (preg_match('/(\d{1,2})\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+(\d{4})/i', $text, $m)) {
            $month = self::MONTHS[strtolower($m[2])] ?? null;

            return $month ? sprintf('%04d-%02d-%02d', (int) $m[3], $month, (int) $m[1]) : null;
        }

        if (preg_match('/(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+(\d{4})/i', $text, $m)) {
            $month = self::MONTHS[strtolower($m[1])] ?? null;

            return $month ? sprintf('%04d-%02d-01', (int) $m[2], $month) : null;
        }

        return null;
    }

    /**
     * @return array{start: ?string, end: ?string}
     */
    protected function parsePeriodRange(string $text): array
    {
        $parts = preg_split('/\s*[–-]\s*/u', trim($text));
        if (count($parts) < 2) {
            return ['start' => null, 'end' => null];
        }

        return [
            'start' => $this->parseIndonesianDate($parts[0]),
            'end' => $this->parseIndonesianDate($parts[1]),
        ];
    }

    /**
     * @return array{nama_sesi: string, tanggal_ujian: ?string}
     */
    protected function parseSession(string $group): array
    {
        $namaSesi = trim($group);

        if (preg_match('/Sesi\s*(\d+)/i', $group, $m)) {
            $namaSesi = (string) (int) $m[1];
        } elseif (stripos($group, 'susulan') !== false) {
            $namaSesi = 'Susulan';
        }

        return [
            'nama_sesi' => $namaSesi,
            'tanggal_ujian' => $this->parseIndonesianDate($group),
        ];
    }

    protected function cplNumberFromHeader(string $header): ?int
    {
        if (preg_match('/CPL\s*-?\s*(\d+)/i', trim($header), $m)) {
            $number = (int) $m[1];

            return $number >= 1 && $number <= 10 ? $number : null;
        }

        return null;
    }

    protected function normalizeHeader(string $header): string
    {
        return trim(preg_replace('/\s+/', ' ', strtolower($header)));
    }

    protected function monthName(int $month): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ][$month] ?? '';
    }

    protected function fileHash(string $path): string
    {
        return hash_file('sha256', $path) ?: '';
    }
}
