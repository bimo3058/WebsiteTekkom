<?php

namespace Tests\Unit\BankSoal;

use Modules\BankSoal\Services\Komprehensif\Imports\LegacyRekapParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class LegacyRekapParserTest extends TestCase
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

    public function test_membaca_rekap_cpl_dengan_urutan_header_acak(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAPITULASI HASIL TES');
            $sheet->setCellValue('C3', 'Sesi 1: Kamis, 12 Februari 2026');
            $sheet->setCellValue('C4', '1 Juli 2025  -  11 November 2026');
            $sheet->setCellValue('C5', 10);

            $sheet->setCellValue('A7', 'No');
            $sheet->setCellValue('B7', 'NIM');
            $sheet->setCellValue('C7', 'Nama');
            $sheet->setCellValue('D7', 'Grup');
            $sheet->setCellValue('E7', 'Status');
            $sheet->setCellValue('F7', 'Rata-Rata');
            $sheet->setCellValue('G7', 'Jumlah');
            $sheet->setCellValue('H7', 'DAFTAR NAMA TES');

            $sheet->setCellValue('H8', 'CPL 1 (12022026)');
            $sheet->setCellValue('I8', 'CPL 10 (12022026)');
            $sheet->setCellValue('J8', 'CPL 2 (12022026)');

            $sheet->setCellValue('A9', 1);
            $sheet->setCellValue('B9', '21120121120026');
            $sheet->setCellValue('C9', 'NICHOLAS PRIYAMBODO ADI');
            $sheet->setCellValue('D9', 'Sesi 1: Kamis, 12 Februari 2026');
            $sheet->setCellValue('E9', 'LULUS');
            $sheet->setCellValue('F9', 7);
            $sheet->setCellValue('G9', 70);
            $sheet->setCellValue('H9', 10);
            $sheet->setCellValue('I9', 10);
            $sheet->setCellValue('J9', 5);
        });

        $parsed = (new LegacyRekapParser)->parse($path);

        $this->assertSame('legacy_rekap', $parsed['type']);
        $this->assertSame('Ujian Komprehensif Februari 2026', $parsed['period']['nama']);
        $this->assertSame('2026-02-12', $parsed['period']['tanggal_mulai']);
        $this->assertSame('2026-02-12', $parsed['period']['tanggal_selesai_ujian']);
        $this->assertSame('1', $parsed['session']['nama_sesi']);
        $this->assertSame('2026-02-12', $parsed['session']['tanggal_ujian']);
        $this->assertCount(1, $parsed['rows']);

        $row = $parsed['rows'][0];
        $this->assertSame('21120121120026', $row['nim']);
        $this->assertSame(70.0, $row['total']);
        $this->assertSame(10.0, $row['cpl_values'][1]);
        $this->assertSame(10.0, $row['cpl_values'][10]);
        $this->assertSame(5.0, $row['cpl_values'][2]);
    }

    public function test_membaca_susulan_total_only(): void
    {
        $path = $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAPITULASI HASIL TES');
            $sheet->setCellValue('C3', 'Susulan, 22 Desember 2025');
            $sheet->setCellValue('C4', '1 Juli 2025  -  11 November 2026');
            $sheet->setCellValue('C5', 1);

            $sheet->setCellValue('A7', 'No');
            $sheet->setCellValue('B7', 'NIM');
            $sheet->setCellValue('C7', 'Nama');
            $sheet->setCellValue('D7', 'Grup');
            $sheet->setCellValue('E7', 'Status');
            $sheet->setCellValue('F7', 'Rata-Rata');
            $sheet->setCellValue('G7', 'Jumlah');
            $sheet->setCellValue('H7', 'DAFTAR NAMA TES');
            $sheet->setCellValue('H8', 'Ujian Komprehensif Susulan (22122025)');

            $sheet->setCellValue('B9', '21120120120021');
            $sheet->setCellValue('C9', 'Daniel Ritz');
            $sheet->setCellValue('E9', 'LULUS');
            $sheet->setCellValue('F9', 77);
            $sheet->setCellValue('G9', 77);
            $sheet->setCellValue('H9', 77);
        });

        $parsed = (new LegacyRekapParser)->parse($path);

        $this->assertFalse($parsed['has_cpl']);
        $this->assertSame('Ujian Komprehensif Desember 2025', $parsed['period']['nama']);
        $this->assertSame('Susulan', $parsed['session']['nama_sesi']);
        $this->assertSame('2025-12-22', $parsed['session']['tanggal_ujian']);
        $this->assertCount(1, $parsed['rows']);
        $this->assertSame(77.0, $parsed['rows'][0]['total']);
        $this->assertSame([], $parsed['rows'][0]['cpl_values']);
    }

    private function makeWorkbook(callable $fill): string
    {
        $spreadsheet = new Spreadsheet;
        $fill($spreadsheet);

        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kompre-legacy-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $this->files[] = $path;

        return $path;
    }
}
