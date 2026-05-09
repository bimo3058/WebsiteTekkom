<?php

namespace Modules\BankSoal\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\BankSoal\Models\MataKuliah;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Heading row will be formatted to lower_case by WithHeadingRow
        $kode = $row['kode'] ?? null;
        $nama = $row['nama'] ?? null;
        $sks = $row['sks'] ?? null;
        $semester = $row['semester'] ?? null;

        if (!$kode || !$nama) {
            return null; // Skip if basic info missing
        }

        // Update or Create Based on Kode
        return MataKuliah::updateOrCreate(
            ['kode' => $kode],
            [
                'nama' => $nama,
                'sks' => $sks ?? 1,
                'semester' => $semester ?? 1,
            ]
        );
    }
}
