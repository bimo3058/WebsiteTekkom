<?php

namespace Modules\EOffice\Exports;

use Modules\EOffice\Models\KerjaPraktik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DataMahasiswaExport implements FromCollection, WithHeadings, WithMapping
{
    private $rowNum = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama',
            'm.nim as nim',
            'd.nama_lengkap as dosen_pembimbing',
            'p.nilai_akhir'
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('eo_kp_dosen as d', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'd.id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
            ->with(['nilaiDetail.komponen'])
            ->orderBy('eo_kerja_praktik.created_at', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'NIM',
            'NAMA MAHASISWA',
            'TAHAP KP',
            'PEMBIMBING',
            'NILAI SEMINAR',
            'NILAI LAPANGAN',
            'NILAI AKHIR'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $this->rowNum++;

        // Calculate phase
        $tahap = 'Pra KP';
        if ($row->status_kp === 'pending') {
            $tahap = 'Pra KP';
        } elseif ($row->status_kp === 'active') {
            $tahap = 'Saat KP';
        } elseif ($row->status_kp === 'completed') {
            $tahap = 'Pasca KP';
        }

        return [
            $this->rowNum,
            $row->nim ?? '-',
            $row->nama ?? '-',
            $tahap,
            $row->dosen_pembimbing ?? 'Belum ada pembimbing',
            // $row->nilai_seminar_pembimbing ?? '-',
            // $row->nilai_lapangan ?? '-',
            '-',
            '-',
            $row->nilai_akhir ?? '-'
        ];
    }
}
