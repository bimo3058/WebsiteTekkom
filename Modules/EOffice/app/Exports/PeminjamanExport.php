<?php

namespace Modules\EOffice\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $peminjamans;

    public function __construct($peminjamans)
    {
        $this->peminjamans = $peminjamans;
    }

    public function collection()
    {
        return $this->peminjamans;
    }

    public function headings(): array
    {
        return [
            ['Laporan Peminjaman Ruangan'],
            ['Dicetak pada: ' . now()->translatedFormat('d F Y H:i')],
            [''], // Empty row for spacing
            [
                'No',
                'Nama Peminjam',
                'Identitas (NIM/NIP)',
                'Role',
                'No Telepon',
                'Ruangan',
                'Tanggal',
                'Mulai',
                'Selesai',
                'Kegiatan',
                'Status',
                'Alasan Penolakan',
                'Waktu Diajukan',
                'Waktu Proses'
            ]
        ];
    }

    public function map($pinjam): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $fullName = $pinjam->user->name ?? 'Tidak Diketahui';
        $roleName = 'Mahasiswa';
        $identityNumber = '-';

        if ($pinjam->user) {
            if ($pinjam->user->hasRole('dosen')) {
                $roleName = 'Dosen';
                $identityNumber = $pinjam->user->lecturer->employee_number ?? '-';
            } elseif ($pinjam->user->hasRole('mahasiswa') || $pinjam->user->hasRole('praktikan')) {
                $identityNumber = $pinjam->user->student->student_number ?? '-';
            } else {
                $roleName = 'Eksternal / Lainnya';
                $identityNumber = $pinjam->user->external_id ?? '-';
            }
        }

        return [
            $rowNumber,
            $fullName,
            $identityNumber,
            $roleName,
            $pinjam->nomor_telepon ?? '-',
            $pinjam->ruangan->nama ?? 'Dihapus',
            \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y'),
            \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i'),
            \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i'),
            $pinjam->tujuan,
            strtoupper($pinjam->status),
            $pinjam->alasan_penolakan ?? '-',
            $pinjam->created_at ? $pinjam->created_at->translatedFormat('d M Y, H:i') : '-',
            $pinjam->waktu_approval ? \Carbon\Carbon::parse($pinjam->waktu_approval)->translatedFormat('d M Y, H:i') : '-'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,    // No
            'B' => 30,   // Nama Peminjam
            'C' => 20,   // Identitas
            'D' => 15,   // Role
            'E' => 18,   // No Telepon
            'F' => 20,   // Ruangan
            'G' => 18,   // Tanggal
            'H' => 10,   // Mulai
            'I' => 10,   // Selesai
            'J' => 22.5, // Kegiatan
            'K' => 15,   // Status
            'L' => 25,   // Alasan Penolakan
            'M' => 20,   // Waktu Diajukan
            'N' => 20,   // Waktu Proses
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for the title and date
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');

        // Apply text wrapping to the entire sheet so long text goes to the next line
        $sheet->getStyle('A1:N' . $sheet->getHighestRow())
            ->getAlignment()->setWrapText(true);

        // Ensure Status column is centered vertically and horizontally so it looks good
        $sheet->getStyle('K1:K' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            
        // Ensure Ruangan column is explicitly left-aligned
        $sheet->getStyle('F4:F' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Ensure Identitas (NIM/NIP) column is explicitly left-aligned
        $sheet->getStyle('C4:C' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        return [
            // Title styles
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            2 => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],

            // Header row styles (now on row 4)
            4 => ['font' => ['bold' => true]],
        ];
    }
}
