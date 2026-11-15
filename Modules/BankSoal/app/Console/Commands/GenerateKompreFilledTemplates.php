<?php

namespace Modules\BankSoal\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Services\Komprehensif\Imports\LegacyRekapParser;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateKompreFilledTemplates extends Command
{
    protected $signature = 'kompre:generate-filled-templates
                            {--source= : Folder file rekap lama}
                            {--output= : Folder hasil file template terisi}';

    protected $description = 'Generate template Excel import yang sudah terisi dari file rekap hasil tes lama';

    public function handle(): int
    {
        $source = $this->option('source') ?: base_path('Data Hasil Kompre/Data Hasil Kompre');
        $output = $this->option('output') ?: $source.DIRECTORY_SEPARATOR.'Template Terisi';

        if (! is_dir($source)) {
            $this->error('Folder sumber tidak ditemukan: '.$source);

            return self::FAILURE;
        }

        if (! is_dir($output) && ! mkdir($output, 0775, true) && ! is_dir($output)) {
            $this->error('Gagal membuat folder output: '.$output);

            return self::FAILURE;
        }

        $files = glob($source.DIRECTORY_SEPARATOR.'*.xlsx') ?: [];
        if ($files === []) {
            $this->error('Tidak ada file .xlsx di folder sumber.');

            return self::FAILURE;
        }

        $parser = new LegacyRekapParser;
        $periods = [];
        $errors = [];

        foreach ($files as $path) {
            try {
                $parsed = $parser->parse($path);
            } catch (\Throwable $e) {
                $errors[] = basename($path).': '.$e->getMessage();

                continue;
            }

            $name = $parsed['period']['nama'];

            if (! isset($periods[$name])) {
                $periods[$name] = [
                    'period' => $parsed['period'],
                    'sessions' => [],
                    'rows' => [],
                ];
            }

            $periods[$name]['sessions'][] = $parsed['session'];

            foreach ($parsed['rows'] as $row) {
                $row['session'] = $parsed['session']['nama_sesi'];
                $row['tanggal_ujian'] = $parsed['session']['tanggal_ujian'];
                $periods[$name]['rows'][] = $row;
            }
        }

        foreach ($periods as &$group) {
            $dates = array_values(array_filter(array_column($group['sessions'], 'tanggal_ujian')));
            sort($dates);

            $start = $dates[0] ?? $group['period']['tanggal_mulai'];
            $end = $dates === [] ? $group['period']['tanggal_selesai'] : $dates[count($dates) - 1];

            $group['period']['tanggal_mulai'] = $start;
            $group['period']['tanggal_selesai'] = $end;
            $group['period']['tanggal_mulai_ujian'] = $start;
            $group['period']['tanggal_selesai_ujian'] = $end;
        }
        unset($group);

        uasort($periods, fn (array $a, array $b) => strcmp(
            $a['period']['tanggal_mulai'],
            $b['period']['tanggal_mulai']
        ));

        $cpls = Cpl::orderBy('kode')->get();
        $table = [];

        foreach ($periods as $name => $group) {
            $filename = 'Template_Import_Nilai_Kompre_'
                .str_replace(' ', '_', Str::after($name, 'Ujian Komprehensif '))
                .'.xlsx';

            $path = $output.DIRECTORY_SEPARATOR.$filename;
            (new Xlsx($this->buildWorkbook($group, $periods, $cpls)))->save($path);

            $pass = count(array_filter($group['rows'], fn (array $row) => (float) $row['total'] >= 60));

            $table[] = [
                $name,
                $filename,
                count($group['rows']),
                $pass,
                count($group['rows']) - $pass,
            ];
        }

        $this->table(['Periode', 'File', 'Baris', 'Lulus', 'Tidak Lulus'], $table);

        if ($errors !== []) {
            $this->warn('File yang gagal diproses:');
            foreach ($errors as $error) {
                $this->line('- '.$error);
            }
        }

        $this->info('Selesai. Output: '.$output);

        return self::SUCCESS;
    }

    private function buildWorkbook(array $group, array $allPeriods, $cpls): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->setTitle('Info Periode');
        $info = $spreadsheet->getActiveSheet();
        $nilai = $spreadsheet->createSheet()->setTitle('Nilai');
        $daftar = $spreadsheet->createSheet()->setTitle('Daftar Periode');
        $referensi = $spreadsheet->createSheet()->setTitle('Referensi CPL');

        $this->buildInfoSheet($info, $group);
        $this->buildNilaiSheet($nilai, $group, $cpls);
        $this->buildPeriodSheet($daftar, $allPeriods);
        $this->buildCplSheet($referensi, $cpls);

        $spreadsheet->setActiveSheetIndex(1);

        return $spreadsheet;
    }

    private function buildInfoSheet($sheet, array $group): void
    {
        $period = $group['period'];

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI UJIAN KOMPREHENSIF');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rows = [
            3 => ['Nama Periode', $period['nama']],
            4 => ['Tanggal Mulai', $period['tanggal_mulai']],
            5 => ['Tanggal Selesai', $period['tanggal_selesai']],
            6 => ['Tanggal Mulai Ujian', $period['tanggal_mulai_ujian']],
            7 => ['Tanggal Selesai Ujian', $period['tanggal_selesai_ujian']],
            8 => ['Status', $period['status'] ?? 'selesai'],
        ];

        foreach ($rows as $row => [$label, $value]) {
            $sheet->setCellValue('A'.$row, $label);
            $sheet->setCellValue('B'.$row, $value);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        }

        $sheet->setCellValue('A10', 'Daftar Sesi / Jadwal');
        $sheet->getStyle('A10')->getFont()->setBold(true);

        foreach (['Nama Sesi', 'Tanggal Ujian', 'Jumlah Peserta'] as $index => $header) {
            $sheet->setCellValue(chr(65 + $index).'11', $header);
        }
        $this->styleHeader($sheet, 'A11:C11');

        $row = 12;
        foreach ($group['sessions'] as $session) {
            $sheet->setCellValue('A'.$row, $session['nama_sesi']);
            $sheet->setCellValue('B'.$row, $session['tanggal_ujian']);
            $sheet->setCellValue('C'.$row, '');
            $row++;
        }

        $sheet->setCellValue('A'.($row + 2), 'Petunjuk');
        $sheet->getStyle('A'.($row + 2))->getFont()->setBold(true);
        $sheet->setCellValue('A'.($row + 3), '1. File ini sudah terisi dari data rekap sistem lama.');
        $sheet->setCellValue('A'.($row + 4), '2. Status dan total mengikuti aturan lama: total minimal 60.');
        $sheet->setCellValue('A'.($row + 5), '3. Baris dengan nilai kosong akan dilewati saat import.');

        foreach (['A' => 28, 'B' => 24, 'C' => 16] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function buildNilaiSheet($sheet, array $group, $cpls): void
    {
        $headers = [
            'A' => 'Periode Ujian Komprehensif',
            'B' => 'Sesi / Jadwal Ujian',
            'C' => 'Tanggal Ujian',
            'D' => 'NIM',
            'E' => 'Nama',
            'F' => 'Status',
        ];

        $cplColumns = [];
        $column = 'G';

        foreach ($cpls as $cpl) {
            $headers[$column] = $cpl->kode;
            $cplColumns[$this->cplNumber($cpl->kode)] = $column;
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

        $rowNumber = 2;

        foreach ($group['rows'] as $row) {
            $sheet->setCellValue('A'.$rowNumber, $group['period']['nama']);
            $sheet->setCellValue('B'.$rowNumber, $this->sessionLabel($row['session']));
            $sheet->setCellValue('C'.$rowNumber, $row['tanggal_ujian']);
            $sheet->setCellValueExplicit('D'.$rowNumber, (string) $row['nim'], DataType::TYPE_STRING);
            $sheet->setCellValue('E'.$rowNumber, $row['nama']);

            $total = $row['total'] !== null ? (float) $row['total'] : null;
            $status = trim((string) ($row['status'] ?? ''));

            if ($status === '') {
                $status = $total !== null && $total >= 60 ? 'LULUS' : 'TIDAK LULUS';
            }

            $sheet->setCellValue('F'.$rowNumber, $status);

            foreach ($cplColumns as $number => $columnLetter) {
                $value = $row['cpl_values'][$number] ?? null;
                if ($value !== null) {
                    $sheet->setCellValue($columnLetter.$rowNumber, $value);
                }
            }

            if ($total !== null) {
                $sheet->setCellValue($totalColumn.$rowNumber, $total);
            }

            $average = $row['average'] ?? ($total !== null ? round($total / 10, 2) : null);
            if ($average !== null) {
                $sheet->setCellValue($averageColumn.$rowNumber, $average);
            }

            $rowNumber++;
        }

        foreach (['A' => 34, 'B' => 22, 'C' => 14, 'D' => 18, 'E' => 30, 'F' => 14] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        foreach (range('G', $averageColumn) as $column) {
            $sheet->getColumnDimension($column)->setWidth(12);
        }
    }

    private function buildPeriodSheet($sheet, array $allPeriods): void
    {
        $headers = ['No', 'Nama Periode', 'Tanggal Mulai', 'Tanggal Selesai', 'Jumlah Sesi', 'Jumlah Peserta'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index).'1', $header);
        }
        $this->styleHeader($sheet, 'A1:F1');

        $row = 2;
        $no = 1;

        foreach ($allPeriods as $group) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $group['period']['nama']);
            $sheet->setCellValue('C'.$row, $group['period']['tanggal_mulai']);
            $sheet->setCellValue('D'.$row, $group['period']['tanggal_selesai']);
            $sheet->setCellValue('E'.$row, count($group['sessions']));
            $sheet->setCellValue('F'.$row, count($group['rows']));
            $row++;
        }

        foreach (['A' => 6, 'B' => 40, 'C' => 16, 'D' => 16, 'E' => 14, 'F' => 16] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    private function buildCplSheet($sheet, $cpls): void
    {
        $sheet->setCellValue('A1', 'Kode CPL');
        $sheet->setCellValue('B1', 'Deskripsi');
        $this->styleHeader($sheet, 'A1:B1');

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

    private function cplNumber(string $kode): ?int
    {
        return preg_match('/(\d+)\s*$/', $kode, $m) ? (int) $m[1] : null;
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2A3A7C');
        $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }
}
