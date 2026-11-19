<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class BulkUserImportExcel
{
    /** @return array{header: array<int, string>, rows: array<int, array<int, string>>} */
    public function readFile(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        try {
            $sheetRows = $spreadsheet->getActiveSheet()->toArray('', true, true, false);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }

        $header = null;
        $rows = [];
        foreach ($sheetRows as $row) {
            $row = array_map(static fn ($value): string => trim((string) ($value ?? '')), $row);
            if ($header === null) {
                if (! $this->isInstructionRow($row)) {
                    $header = $row;
                }
                continue;
            }

            if (! $this->isInstructionRow($row)) {
                $rows[] = $row;
            }
        }

        return ['header' => $header ?? [], 'rows' => $rows];
    }

    public function isInstructionRow(array $row): bool
    {
        $first = trim((string) ($row[0] ?? ''));
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0
            || str_starts_with($first, '#');
    }

    /** @return array<int, string> */
    public function normalizeHeader(array $header): array
    {
        return array_map(static function ($value): string {
            $value = strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $value)));
            return preg_replace('/[\s_\-]+/', '', $value);
        }, $header);
    }

    public function index(array $header, array $aliases): int|false
    {
        foreach ($aliases as $alias) {
            $index = array_search($alias, $header, true);
            if ($index !== false) {
                return $index;
            }
        }

        return false;
    }

    public function emailRole(string $email): ?string
    {
        $email = strtolower(trim($email));

        return match (true) {
            str_ends_with($email, '@students.undip.ac.id'),
            str_ends_with($email, '@student.undip.ac.id') => 'mahasiswa',
            str_ends_with($email, '@lecturer.undip.ac.id') => 'dosen',
            default => null,
        };
    }

    /** Map the three user-facing Excel columns to the import job's internal fields. */
    public function mapRow(array $header, array $row): array
    {
        $get = static fn (int|false $index): string => $index === false
            ? ''
            : trim((string) ($row[$index] ?? ''));

        $email = strtolower($get($this->index($header, ['email'])));
        $identity = $get($this->index($header, ['nim/nip', 'nimnip', 'nomorinduk', 'nim', 'nip']));
        $role = $this->emailRole($email);
        $studentNumber = $role === 'mahasiswa' ? $identity : '';

        return [
            $get($this->index($header, ['name', 'nama'])),
            $email,
            '',
            $role ?? '',
            $identity,
            $studentNumber,
            $role === 'mahasiswa' ? ($this->cohortYearFromStudentNumber($studentNumber) ?? '') : '',
            '',
        ];
    }

    public function cohortYearFromStudentNumber(string $studentNumber): ?int
    {
        $digits = preg_replace('/\D+/', '', $studentNumber);
        if (! is_string($digits) || strlen($digits) < 2) {
            return null;
        }

        $year = 2000 + (int) substr($digits, 0, 2);
        return $year >= 2000 && $year <= ((int) date('Y') + 1) ? $year : null;
    }
}
