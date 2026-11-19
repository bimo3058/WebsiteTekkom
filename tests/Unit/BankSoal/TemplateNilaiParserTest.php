<?php

namespace Tests\Unit\BankSoal;

use Modules\BankSoal\Services\Komprehensif\Imports\TemplateNilaiParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TemplateNilaiParserTest extends TestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_membaca_template_periode_dan_nilai_cpl(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $info = $spreadsheet->getActiveSheet();
            $info->setTitle('Info Periode');
            $info->setCellValue('A3', 'Nama Periode');
            $info->setCellValue('B3', 'Ujian Komprehensif November 2026');
            $info->setCellValue('A4', 'Tanggal Mulai');
            $info->setCellValue('B4', '2026-11-01');
            $info->setCellValue('A5', 'Tanggal Selesai');
            $info->setCellValue('B5', '2026-11-19');
            $info->setCellValue('A6', 'Tanggal Mulai Ujian');
            $info->setCellValue('B6', '2026-11-10');
            $info->setCellValue('A7', 'Tanggal Selesai Ujian');
            $info->setCellValue('B7', '2026-11-19');

            $nilai = $spreadsheet->createSheet()->setTitle('Nilai');
            $nilai->fromArray([
                'Periode Ujian Komprehensif',
                'Sesi / Jadwal Ujian',
                'Tanggal Ujian',
                'NIM',
                'Nama',
                'Status',
                'CPL-001',
                'CPL-002',
                'CPL-003',
                'CPL-004',
                'CPL-005',
                'CPL-006',
                'CPL-007',
                'CPL-008',
                'CPL-009',
                'CPL-010',
                'Total Nilai',
                'Rata-rata',
            ], null, 'A1');

            $nilai->fromArray([
                'Ujian Komprehensif November 2026',
                'Sesi 1',
                '2026-11-10',
                '24060122110201',
                'Maba 22',
                'LULUS',
                10, 10, 10, 10, 10, 10, 10, 10, 10, 10,
                100,
                10,
            ], null, 'A2');
        });

        $parsed = (new TemplateNilaiParser)->parse($path);

        $this->assertSame('template', $parsed['type']);
        $this->assertCount(1, $parsed['periods']);

        $period = $parsed['periods'][0];
        $this->assertSame('Ujian Komprehensif November 2026', $period['period']['nama']);
        $this->assertSame('2026-11-10', $period['period']['tanggal_mulai_ujian']);
        $this->assertCount(1, $period['rows']);

        $row = $period['rows'][0];
        $this->assertSame('24060122110201', $row['nim']);
        $this->assertSame(100.0, $row['total']);
        $this->assertSame(10.0, $row['cpl_values'][1]);
        $this->assertSame(10.0, $row['cpl_values'][10]);
    }

    public function test_baris_dengan_nilai_kosong_dikenali(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $info = $spreadsheet->getActiveSheet();
            $info->setTitle('Info Periode');
            $info->setCellValue('A3', 'Nama Periode');
            $info->setCellValue('B3', 'Ujian Komprehensif November 2026');

            $nilai = $spreadsheet->createSheet()->setTitle('Nilai');
            $nilai->fromArray([
                'Periode Ujian Komprehensif',
                'NIM',
                'Nama',
                'CPL-001',
                'Total Nilai',
            ], null, 'A1');

            $nilai->fromArray([
                'Ujian Komprehensif November 2026',
                '24060122110201',
                'Maba 22',
                null,
                null,
            ], null, 'A2');
        });

        $parsed = (new TemplateNilaiParser)->parse($path);
        $row = $parsed['periods'][0]['rows'][0];

        $this->assertNull($row['total']);
        $this->assertNull($row['cpl_values'][1]);
    }

    public function test_membaca_template_satu_sheet_dengan_header_di_baris_20(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Nilai');

            $sheet->setCellValue('A3', 'Nama Periode');
            $sheet->setCellValue('B3', 'Ujian Komprehensif Agustus 2025');
            $sheet->setCellValue('A4', 'Tanggal Mulai');
            $sheet->setCellValue('B4', '2025-08-13');
            $sheet->setCellValue('A5', 'Tanggal Selesai');
            $sheet->setCellValue('B5', '2025-08-14');
            $sheet->setCellValue('A6', 'Tanggal Mulai Ujian');
            $sheet->setCellValue('B6', '2025-08-13');
            $sheet->setCellValue('A7', 'Tanggal Selesai Ujian');
            $sheet->setCellValue('B7', '2025-08-14');
            $sheet->setCellValue('A8', 'Status');
            $sheet->setCellValue('B8', 'selesai');

            $headers = [
                'Periode Ujian Komprehensif',
                'Sesi / Jadwal Ujian',
                'Tanggal Ujian',
                'NIM',
                'Nama',
                'Status',
                'CPL-001',
                'CPL-002',
                'CPL-003',
                'CPL-004',
                'CPL-005',
                'CPL-006',
                'CPL-007',
                'CPL-008',
                'CPL-009',
                'CPL-010',
                'Total Nilai',
                'Rata-rata',
            ];

            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index).'20', $header);
            }

            $sheet->fromArray([
                'Ujian Komprehensif Agustus 2025',
                'Sesi 1',
                '2025-08-13',
                '21120121120026',
                'NICHOLAS PRIYAMBODO ADI',
                'LULUS',
                8, 8, 7, 6, 7, 10, 5, 8, 9, 9,
                77,
                7.7,
            ], null, 'A21');
        });

        $parsed = (new TemplateNilaiParser)->parse($path);

        $this->assertCount(1, $parsed['periods']);

        $period = $parsed['periods'][0];
        $this->assertSame('Ujian Komprehensif Agustus 2025', $period['period']['nama']);
        $this->assertSame('2025-08-13', $period['period']['tanggal_mulai_ujian']);
        $this->assertCount(1, $period['rows']);

        $row = $period['rows'][0];
        $this->assertSame('21120121120026', $row['nim']);
        $this->assertSame(77.0, $row['total']);
        $this->assertSame(5.0, $row['cpl_values'][7]);
        $this->assertSame(9.0, $row['cpl_values'][10]);
    }

    public function test_membaca_template_satu_sheet_tanpa_kolom_periode(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Nilai');

            $sheet->setCellValue('A3', 'Nama Periode');
            $sheet->setCellValue('B3', 'Ujian Komprehensif Agustus 2025');
            $sheet->setCellValue('A6', 'Tanggal Mulai Ujian');
            $sheet->setCellValue('B6', '2025-08-13');

            $headers = [
                'No',
                'Sesi / Jadwal Ujian',
                'Tanggal Ujian',
                'NIM',
                'Nama',
                'Status',
                'CPL-001',
                'CPL-002',
                'CPL-003',
                'CPL-004',
                'CPL-005',
                'CPL-006',
                'CPL-007',
                'CPL-008',
                'CPL-009',
                'CPL-010',
                'Total Nilai',
                'Rata-rata',
            ];

            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index).'20', $header);
            }

            $sheet->fromArray([
                1,
                'Sesi 1',
                '2025-08-13',
                '21120121120026',
                'NICHOLAS PRIYAMBODO ADI',
                'LULUS',
                8, 8, 7, 6, 7, 10, 5, 8, 9, 9,
                77,
                7.7,
            ], null, 'A21');
        });

        $parsed = (new TemplateNilaiParser)->parse($path);

        $this->assertCount(1, $parsed['periods']);

        $period = $parsed['periods'][0];
        $this->assertSame('Ujian Komprehensif Agustus 2025', $period['period']['nama']);
        $this->assertCount(1, $period['rows']);

        $row = $period['rows'][0];
        $this->assertSame('21120121120026', $row['nim']);
        $this->assertSame('Sesi 1', $row['session']);
        $this->assertSame(77.0, $row['total']);
        $this->assertSame(5.0, $row['cpl_values'][7]);
    }

    public function test_satu_file_dengan_dua_periode_ditolak(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Nilai');

            $sheet->setCellValue('A3', 'Nama Periode');
            $sheet->setCellValue('B3', 'Ujian Komprehensif Agustus 2025');

            $headers = ['Periode Ujian Komprehensif', 'NIM', 'Nama'];
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index).'10', $header);
            }

            $sheet->fromArray(['Ujian Komprehensif Agustus 2025', '21120121120026', 'Satu'], null, 'A11');
            $sheet->fromArray(['Ujian Komprehensif September 2025', '21120121120027', 'Dua'], null, 'A12');
        });

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Satu file hanya boleh berisi satu periode');

        (new TemplateNilaiParser)->parse($path);
    }

    private function makeWorkbook(callable $fill): string
    {
        $spreadsheet = new Spreadsheet;
        $fill($spreadsheet);

        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kompre-template-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $this->files[] = $path;

        return $path;
    }
}
