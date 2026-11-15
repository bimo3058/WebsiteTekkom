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
