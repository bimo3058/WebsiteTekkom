<?php

namespace Modules\EOffice\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class RiwayatPeminjamanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $peminjamans;

    public function __construct(Collection $peminjamans)
    {
        $this->peminjamans = $peminjamans;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->peminjamans;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA PEMINJAM',
            'NIM/NIP',
            'ROLE',
            'RUANGAN',
            'TANGGAL PINJAM',
            'JAM MULAI',
            'JAM SELESAI',
            'DURASI (JAM)',
            'KEGIATAN',
            'STATUS',
            'WAKTU PENGAJUAN',
            'WAKTU DIPROSES',
            'ALASAN PENOLAKAN'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        $fullName = $row->user->name ?? 'User Tidak Diketahui';
        $isDosen = $row->user && $row->user->hasRole('dosen');
        $identityNumber = $row->user ? ($row->user->student->student_number ?? $row->user->lecturer->employee_number ?? $row->user->external_id) : '-';
        $roleName = $isDosen ? 'Dosen' : 'Mahasiswa';

        $durasi = \Carbon\Carbon::parse($row->jam_mulai)->diffInHours(\Carbon\Carbon::parse($row->jam_selesai));

        return [
            $no,
            $fullName,
            $identityNumber,
            $roleName,
            $row->ruangan->nama ?? '-',
            \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d/m/Y'),
            \Carbon\Carbon::parse($row->jam_mulai)->format('H:i'),
            \Carbon\Carbon::parse($row->jam_selesai)->format('H:i'),
            $durasi,
            $row->tujuan ?? '-',
            ucfirst($row->status),
            $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-',
            $row->waktu_approval ? \Carbon\Carbon::parse($row->waktu_approval)->format('d/m/Y H:i') : '-',
            $row->alasan_penolakan ?? '-'
        ];
    }
}
