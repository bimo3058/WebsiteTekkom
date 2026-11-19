<?php

namespace Tests\Unit\BankSoal;

use Modules\BankSoal\Services\Komprehensif\Imports\TemplateNilaiParser;
use Modules\BankSoal\Services\Komprehensif\KompreImportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionMethod;
use Tests\TestCase;

class KompreImportServiceTest extends TestCase
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

    public function test_rekap_lama_diparse_sebagai_legacy_bukan_template(): void
    {
        $path = $this->makeLegacyWorkbook();

        $parsed = $this->parseFile($path);

        $this->assertSame('legacy_rekap', $parsed['type']);
        $this->assertSame('Ujian Komprehensif Februari 2026', $parsed['period']['nama']);
        $this->assertSame('1', $parsed['session']['nama_sesi']);
        $this->assertSame('2026-02-12', $parsed['session']['tanggal_ujian']);
        $this->assertCount(1, $parsed['rows']);
    }

    public function test_template_tetap_diparse_sebagai_template(): void
    {
        $path = $this->makeTemplateWorkbook();

        $parsed = $this->parseFile($path);

        $this->assertSame('template', $parsed['type']);
        $this->assertSame('Ujian Komprehensif Agustus 2025', $parsed['periods'][0]['period']['nama']);
        $this->assertCount(1, $parsed['periods'][0]['rows']);
    }

    public function test_template_parser_menolak_file_rekap_lama(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TemplateNilaiParser)->parse($this->makeLegacyWorkbook());
    }

    private function parseFile(string $path): array
    {
        $service = new KompreImportService;
        $method = new ReflectionMethod($service, 'parseFile');
        $method->setAccessible(true);

        return $method->invoke($service, $path);
    }

    private function makeLegacyWorkbook(): string
    {
        return $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAPITULASI HASIL TES');
            $sheet->setCellValue('C3', 'Sesi 1: Kamis, 12 Februari 2026');
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
            $sheet->setCellValue('H8', 'CPL-1');

            $sheet->setCellValue('A9', 1);
            $sheet->setCellValue('B9', '21120121120026');
            $sheet->setCellValue('C9', 'NICHOLAS PRIYAMBODO ADI');
            $sheet->setCellValue('D9', 'Sesi 1: Kamis, 12 Februari 2026');
            $sheet->setCellValue('E9', 'LULUS');
            $sheet->setCellValue('F9', 7);
            $sheet->setCellValue('G9', 70);
            $sheet->setCellValue('H9', 10);
        });
    }

    private function makeTemplateWorkbook(): string
    {
        return $this->makeWorkbook(function (Spreadsheet $spreadsheet) {
            $sheet = $spreadsheet->getActiveSheet()->setTitle('Nilai');

            $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI UJIAN KOMPREHENSIF');
            $sheet->setCellValue('A3', 'Nama Periode');
            $sheet->setCellValue('B3', 'Ujian Komprehensif Agustus 2025');

            $headers = ['Sesi / Jadwal Ujian', 'Tanggal Ujian', 'NIM', 'Nama', 'Status', 'CPL-001', 'Total Nilai', 'Rata-rata'];
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index).'7', $header);
            }

            $sheet->fromArray([
                'Sesi 1',
                '2025-08-13',
                '21120121120026',
                'NICHOLAS PRIYAMBODO ADI',
                'LULUS',
                8,
                80,
                8,
            ], null, 'A8');
        });
    }

    private function makeWorkbook(callable $fill): string
    {
        $spreadsheet = new Spreadsheet;
        $fill($spreadsheet);

        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kompre-import-'.uniqid().'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $this->files[] = $path;

        return $path;
    }
}
