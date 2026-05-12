<?php

namespace Modules\BankSoal\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\BankSoal\Models\Cpmk;

class CpmkImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $kode = $row['kode'] ?? null;
        $deskripsi = $row['deskripsi'] ?? null;

        if (!$kode || !$deskripsi) {
            return null;
        }

        return Cpmk::updateOrCreate(
            ['kode' => $kode],
            ['deskripsi' => $deskripsi]
        );
    }
}
