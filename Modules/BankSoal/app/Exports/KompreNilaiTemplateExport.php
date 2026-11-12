<?php

namespace Modules\BankSoal\Exports;

use Illuminate\Support\Str;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KompreNilaiTemplateExport
{
    public function download(PeriodeUjian $periode): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->setTitle('Info Periode');
        $info = $spreadsheet->getActiveSheet();
        $nilai = $spreadsheet->createSheet()->setTitle('Nilai');
        $daftarPeriode = $spreadsheet->createSheet()->setTitle('Daftar Periode');
        $referensi = $spreadsheet->createSheet()->setTitle('Referensi CPL');

        $this->buildInfoSheet($info, $periode);
        $this->buildNilaiSheet($nilai, $periode);
        $this->buildPeriodSheet($daftarPeriode);
        $this->buildCplSheet($referensi);

        $spreadsheet->setActiveSheetIndex(1);

        $filename = 'Template_Import_Nilai_Kompre_'.Str::slug($periode->nama_periode).'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildInfoSheet($sheet, PeriodeUjian $periode): void
    {
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI UJIAN KOMPREHENSIF');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = [
            3 => ['Nama Periode', $periode->nama_periode],
            4 => ['Tanggal Mulai', $periode->tanggal_mulai?->format('Y-m-d')],
            5 => ['Tanggal Selesai', $periode->tanggal_selesai?->format('Y-m-d')],
            6 => ['Tanggal Mulai Ujian', $periode->tanggal_mulai_ujian?->format('Y-m-d')],
            7 => ['Tanggal Selesai Ujian', $periode->tanggal_selesai_ujian?->format('Y-m-d')],
            8 => ['Status', $periode->status],
        ];

        foreach ($rows as $row => [$label, $value]) {
            $sheet->setCellValue('A'.$row, $label);
            $sheet->setCellValue('B'.$row, $value);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        }

        $sheet->setCellValue('A10', 'Daftar Sesi / Jadwal');
        $sheet->getStyle('A10')->getFont()->setBold(true);

        $headers = ['Nama Sesi', 'Tanggal Ujian', 'Waktu Mulai', 'Waktu Selesai', 'Ruangan'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index).'11', $header);
        }
        $this->styleHeader($sheet, 'A11:E11');

        $jadwals = JadwalUjian::where('periode_ujian_id', $periode->id)
            ->orderBy('tanggal_ujian')
            ->get();

        $row = 12;
        foreach ($jadwals as $jadwal) {
            $sheet->setCellValue('A'.$row, $jadwal->nama_sesi);
            $sheet->setCellValue('B'.$row, $jadwal->tanggal_ujian?->format('Y-m-d'));
            $sheet->setCellValue('C'.$row, $jadwal->waktu_mulai);
            $sheet->setCellValue('D'.$row, $jadwal->waktu_selesai);
            $sheet->setCellValue('E'.$row, $jadwal->ruangan);
            $row++;
        }

        $sheet->setCellValue('A'.($row + 2), 'Petunjuk');
        $sheet->getStyle('A'.($row + 2))->getFont()->setBold(true);
        $sheet->setCellValue('A'.($row + 3), '1. Isi nilai CPL pada sheet "Nilai" (skala 0-10).');
        $sheet->setCellValue('A'.($row + 4), '2. Isi Total Nilai (0-100) atau biarkan kosong agar dihitung dari nilai CPL.');
        $sheet->setCellValue('A'.($row + 5), '3. Status LULUS/TIDAK LULUS mengikuti aturan lama: Total Nilai minimal 60.');
        $sheet->setCellValue('A'.($row + 6), '4. Baris dengan nilai kosong akan dilewati.');

        foreach (['A' => 24, 'B' => 22, 'C' => 16, 'D' => 16, 'E' => 20] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function buildNilaiSheet($sheet, PeriodeUjian $periode): void
    {
        $headers = [
            'A' => 'Periode Ujian Komprehensif',
            'B' => 'Sesi / Jadwal Ujian',
            'C' => 'Tanggal Ujian',
            'D' => 'NIM',
            'E' => 'Nama',
            'F' => 'Status',
        ];

        $cpls = Cpl::orderBy('kode')->get();
        $column = 'G';
        foreach ($cpls as $cpl) {
            $headers[$column] = $cpl->kode;
            $column++;
        }

        $headers[$column] = 'Total Nilai';
        $totalColumn = $column++;
        $headers[$column] = 'Rata-rata';
        $averageColumn = $column;

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell.'1', $header);
        }

        $this->styleHeader($sheet, 'A1:'.$averageColumn.'1');
        $sheet->freezePane('A2');

        $pendaftars = PendaftarUjian::with('jadwal')
            ->where('periode_ujian_id', $periode->id)
            ->orderBy('nama_lengkap')
            ->get();

        $row = 2;
        foreach ($pendaftars as $pendaftar) {
            $sheet->setCellValue('A'.$row, $periode->nama_periode);
            $sheet->setCellValue('B'.$row, $this->sessionLabel($pendaftar->jadwal?->nama_sesi));
            $sheet->setCellValue('C'.$row, $pendaftar->jadwal?->tanggal_ujian?->format('Y-m-d'));
            $sheet->setCellValueExplicit('D'.$row, (string) $pendaftar->nim, DataType::TYPE_STRING);
            $sheet->setCellValue('E'.$row, $pendaftar->nama_lengkap);
            $sheet->setCellValue($totalColumn.$row, null);
            $sheet->setCellValue($averageColumn.$row, null);
            $row++;
        }

        $lastRow = max(500, $row);
        $this->addListValidation($sheet, 'F2:F'.$lastRow, '"LULUS,TIDAK LULUS"');
        $this->addDecimalValidation($sheet, 'G2:'.chr(ord('G') + count($cpls) - 1).$lastRow, 0, 10);
        $this->addDecimalValidation($sheet, $totalColumn.'2:'.$totalColumn.$lastRow, 0, 100);

        foreach (['A' => 34, 'B' => 22, 'C' => 14, 'D' => 18, 'E' => 30, 'F' => 14] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        foreach (range('G', $averageColumn) as $column) {
            $sheet->getColumnDimension($column)->setWidth(12);
        }
    }

    private function buildPeriodSheet($sheet): void
    {
        $headers = [
            'ID',
            'Nama Periode',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Tanggal Mulai Ujian',
            'Tanggal Selesai Ujian',
            'Status',
            'Jumlah Peserta',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index).'1', $header);
        }

        $this->styleHeader($sheet, 'A1:H1');

        $periods = PeriodeUjian::withCount('pendaftars')->orderBy('tanggal_mulai')->get();
        $row = 2;

        foreach ($periods as $period) {
            $sheet->setCellValue('A'.$row, $period->id);
            $sheet->setCellValue('B'.$row, $period->nama_periode);
            $sheet->setCellValue('C'.$row, $period->tanggal_mulai?->format('Y-m-d'));
            $sheet->setCellValue('D'.$row, $period->tanggal_selesai?->format('Y-m-d'));
            $sheet->setCellValue('E'.$row, $period->tanggal_mulai_ujian?->format('Y-m-d'));
            $sheet->setCellValue('F'.$row, $period->tanggal_selesai_ujian?->format('Y-m-d'));
            $sheet->setCellValue('G'.$row, $period->status);
            $sheet->setCellValue('H'.$row, $period->pendaftars_count);
            $row++;
        }

        foreach (['A' => 8, 'B' => 40, 'C' => 16, 'D' => 16, 'E' => 18, 'F' => 20, 'G' => 12, 'H' => 16] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function buildCplSheet($sheet): void
    {
        $sheet->setCellValue('A1', 'Kode CPL');
        $sheet->setCellValue('B1', 'Deskripsi');
        $this->styleHeader($sheet, 'A1:B1');

        $cpls = Cpl::orderBy('kode')->get();
        $row = 2;

        foreach ($cpls as $cpl) {
            $sheet->setCellValue('A'.$row, $cpl->kode);
            $sheet->setCellValue('B'.$row, $cpl->deskripsi);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(100);
    }

    private function sessionLabel(?string $namaSesi): string
    {
        if ($namaSesi === null || $namaSesi === '') {
            return '-';
        }

        return is_numeric($namaSesi) ? 'Sesi '.$namaSesi : $namaSesi;
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2A3A7C');
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function addListValidation($sheet, string $range, string $formula): void
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setFormula1($formula);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setError('Pilih nilai yang tersedia.');

        $sheet->setDataValidation($range, $validation);
    }

    private function addDecimalValidation($sheet, string $range, float $min, float $max): void
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_DECIMAL);
        $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
        $validation->setFormula1((string) $min);
        $validation->setFormula2((string) $max);
        $validation->setAllowBlank(true);
        $validation->setShowErrorMessage(true);
        $validation->setError("Nilai harus antara {$min} dan {$max}.");

        $sheet->setDataValidation($range, $validation);
    }
}
