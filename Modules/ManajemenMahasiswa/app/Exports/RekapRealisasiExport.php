<?php

namespace Modules\ManajemenMahasiswa\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Ekspor Excel rekap realisasi Laporan & Arsip (S-78): lembar "Per Kegiatan"
 * dan "Per Bidang". Datanya disusun RekapRealisasiService, sama dengan PDF.
 */
class RekapRealisasiExport implements WithMultipleSheets
{
    /**
     * @param  array  $data      hasil RekapRealisasiService::susun()
     * @param  string $diunduh   baris keterangan "Diunduh … oleh …"
     */
    public function __construct(private array $data, private string $diunduh) {}

    public function sheets(): array
    {
        return [
            new RekapRealisasiSheet('Per Kegiatan', $this->data, $this->diunduh, RekapRealisasiSheet::PER_KEGIATAN),
            new RekapRealisasiSheet('Per Bidang', $this->data, $this->diunduh, RekapRealisasiSheet::PER_BIDANG),
            // S-80: kontrol anggaran himpunan per sumber dana (L-41)
            new RekapRealisasiSheet('Per Sumber Dana', $this->data, $this->diunduh, RekapRealisasiSheet::PER_SUMBER),
        ];
    }
}
