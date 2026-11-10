<?php

namespace App\Services;

class BulkUserImportCsv
{
    public function isInstructionRow(array $row): bool
    {
        $first = trim((string) ($row[0] ?? ''));
        return count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0
            || str_starts_with($first, '#');
    }

    /** @return array<int, string> */
    public function normalizeHeader(array $header): array
    {
        return array_map(function ($value) {
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

    /** Map Tekkom and legacy headings into the import job's established field order. */
    public function mapRow(array $header, array $row): array
    {
        $get = static fn (int|false $index): string => $index === false
            ? ''
            : trim((string) ($row[$index] ?? ''));

        $emailIndex = $this->index($header, ['email']);
        $email = strtolower($get($emailIndex));
        $identityIndex = $this->index($header, ['nim/nip', 'nimnip', 'nomorinduk', 'nim', 'nip']);
        $identity = $get($identityIndex);
        $role = $this->emailRole($email);
        if ($role === null) {
            $declaredRoles = array_map('trim', explode(',', strtolower($get($this->index($header, ['role', 'roles'])))));
            $role = in_array('mahasiswa', $declaredRoles, true)
                ? 'mahasiswa'
                : (in_array('dosen', $declaredRoles, true) ? 'dosen' : null);
        }

        $externalId = $get($this->index($header, ['externalid', 'external_id']));
        $studentNumber = $get($this->index($header, ['studentnumber', 'nim']));
        if ($identity !== '') {
            if ($externalId === '') {
                $externalId = $identity;
            }
            if ($role === 'mahasiswa' && $studentNumber === '') {
                $studentNumber = $identity;
            }
        }

        return [
            $get($this->index($header, ['name', 'nama'])),
            $email,
            $get($this->index($header, ['password', 'kata sandi'])),
            $role ?? '',
            $externalId,
            $studentNumber,
            $get($this->index($header, ['cohortyear', 'angkatan', 'tahunangkatan']))
                ?: ($role === 'mahasiswa' ? $this->cohortYearFromStudentNumber($studentNumber) : ''),
            $get($this->index($header, ['permissions', 'permission'])),
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
