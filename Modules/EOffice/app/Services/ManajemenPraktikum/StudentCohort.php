<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use App\Models\Student;
use Illuminate\Validation\ValidationException;

class StudentCohort
{
    public function fromNim(?string $nim): int
    {
        if (! preg_match('/^211201(\d{2})\d{6}$/D', trim($nim ?? ''), $match)) {
            $this->fail('NIM Teknik Komputer harus terdiri dari 14 digit dengan kode fakultas/departemen/prodi 211201.');
        }
        $year = 2000 + (int) $match[1];
        if ($year > (int) now()->format('Y')) {
            $this->fail('Angkatan pada NIM belum sesuai dengan tahun masuk yang berlaku.');
        }

        return $year;
    }

    public function verified(?Student $student): int
    {
        if (! $student) {
            $this->fail('Data NIM mahasiswa belum tersedia.');
        }
        $year = $this->fromNim($student->student_number);
        if ((string) $student->cohort_year !== (string) $year) {
            $this->fail('Angkatan tersimpan tidak sesuai dengan digit ke-7 dan ke-8 NIM. Minta Admin Praktikum memverifikasi data angkatan.');
        }

        return $year;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['angkatan' => $message]);
    }
}
