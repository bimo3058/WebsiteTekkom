<?php

namespace Modules\BankSoal\Support;

/**
 * Kalkulator semester akademik berdasarkan tahun angkatan (cohort_year).
 *
 * Kalender akademik Indonesia:
 *   Semester Ganjil  (1,3,5,...) : Agustus – Januari
 *   Semester Genap   (2,4,6,...) : Februari – Juli
 *
 * Aturan:
 *   - Bulan ≥ 8 (Agt–Des) → semester ganjil, tahun ajaran baru sudah mulai
 *   - Bulan < 8 (Jan–Jul)  → semester genap, masih tahun ajaran yang dimulai tahun sebelumnya
 */
class SemesterCalculator
{
    /**
     * Hitung semester aktif saat ini berdasarkan cohort_year.
     *
     * @param  int|null  $cohortYear  Tahun angkatan mahasiswa (misal: 2022)
     * @param  int|null  $defaultSemester  Nilai default jika cohort_year tidak valid
     * @return int
     */
    public static function fromCohortYear(?int $cohortYear, int $defaultSemester = 1): int
    {
        if (!$cohortYear || $cohortYear <= 2000) {
            return $defaultSemester;
        }

        $now          = now();
        $currentYear  = $now->year;
        $currentMonth = $now->month;

        if ($currentMonth >= 8) {
            // Semester ganjil: tahun ajaran baru sudah mulai di bulan Agustus
            $academicYears = $currentYear - $cohortYear;
            $semester      = ($academicYears * 2) + 1;
        } else {
            // Semester genap: masih dalam tahun ajaran yang dimulai tahun sebelumnya
            $academicYears = ($currentYear - 1) - $cohortYear;
            $semester      = ($academicYears * 2) + 2;
        }

        return max(1, $semester);
    }
}
