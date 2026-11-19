<?php

namespace Modules\ManajemenMahasiswa\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Modules\ManajemenMahasiswa\Support\SumberDana;

/**
 * Satu lembar rekap realisasi (S-78). Baris 1–3 = judul, filter, waktu unduh;
 * baris 5 = kepala tabel; data mulai baris 6; lalu baris TOTAL dan catatan kaki.
 *
 * Total dihitung di PHP, bukan rumus Excel, dan SEMUA teks ditulis sebagai teks
 * biasa (bindValue): judul/lokasi isian pengguna yang diawali "=" tidak boleh
 * berubah menjadi rumus saat berkas dibuka.
 *
 * WithStrictNullComparison wajib: tanpanya PhpSpreadsheet membandingkan longgar
 * (0 == null) sehingga selisih Rp 0 dan angka nol lain tertulis sebagai sel kosong.
 */
class RekapRealisasiSheet extends DefaultValueBinder implements FromArray, WithTitle, WithColumnWidths, WithEvents, WithCustomValueBinder, WithStrictNullComparison
{
    public const PER_KEGIATAN = 'kegiatan';
    public const PER_BIDANG   = 'bidang';
    public const PER_SUMBER   = 'sumber';

    private const BARIS_KEPALA = 5;

    /** Warna navy SITKOM (#0B266E) untuk kepala tabel, sama dengan aksen halaman. */
    private const NAVY = '0B266E';

    private const FORMAT_RUPIAH  = '"Rp "#,##0;[Red]-"Rp "#,##0';
    private const FORMAT_JUMLAH  = '#,##0';

    public function __construct(
        private string $judulLembar,
        private array $data,
        private string $diunduh,
        private string $jenis,
    ) {}

    public function title(): string
    {
        return $this->judulLembar;
    }

    /** Kolom: [judul kepala, lebar, format angka|null, rata] */
    private function kolom(): array
    {
        if ($this->jenis === self::PER_SUMBER) {
            return [
                ['Sumber Dana', 28, null, 'left'],
                ['Kegiatan (terpakai dirinci)', 14, self::FORMAT_JUMLAH, 'right'],
                ['Anggaran Rencana', 18, self::FORMAT_RUPIAH, 'right'],
                ['Anggaran Terpakai', 18, self::FORMAT_RUPIAH, 'right'],
                ['Selisih (Rencana − Terpakai)', 18, self::FORMAT_RUPIAH, 'right'],
            ];
        }

        $sumber = array_map(fn ($l) => ['Terpakai: ' . $l, 15, self::FORMAT_RUPIAH, 'right'], array_values(SumberDana::KOLOM));

        return $this->jenis === self::PER_KEGIATAN
            ? [
                ['No', 5, null, 'center'],
                ['Judul Kegiatan', 42, null, 'left'],
                ['Bidang/Unit', 14, null, 'left'],
                ['Periode', 11, null, 'center'],
                ['Tanggal', 22, null, 'left'],
                ['Lokasi', 30, null, 'left'],
                ['Ketua Pelaksana', 24, null, 'left'],
                ['Target Peserta', 11, self::FORMAT_JUMLAH, 'right'],
                ['Peserta Hadir', 11, self::FORMAT_JUMLAH, 'right'],
                ['Anggaran Rencana', 17, self::FORMAT_RUPIAH, 'right'],
                ['Anggaran Terpakai', 17, self::FORMAT_RUPIAH, 'right'],
                ['Selisih (Rencana − Terpakai)', 17, self::FORMAT_RUPIAH, 'right'],
                ...$sumber,
                ['Keterangan', 24, null, 'left'],
            ]
            : [
                ['Bidang/Unit', 22, null, 'left'],
                ['Jumlah Kegiatan', 11, self::FORMAT_JUMLAH, 'right'],
                ['Dengan Rencana Proker', 12, self::FORMAT_JUMLAH, 'right'],
                ['Anggaran Rencana', 18, self::FORMAT_RUPIAH, 'right'],
                ['Terpakai (kegiatan berencana)', 18, self::FORMAT_RUPIAH, 'right'],
                ['Selisih (Rencana − Terpakai)', 18, self::FORMAT_RUPIAH, 'right'],
                ['Total Terpakai (semua kegiatan)', 19, self::FORMAT_RUPIAH, 'right'],
                ['Peserta Hadir (semua kegiatan)', 13, self::FORMAT_JUMLAH, 'right'],
            ];
    }

    private function jumlahData(): int
    {
        return match ($this->jenis) {
            self::PER_KEGIATAN => count($this->data['baris']),
            self::PER_SUMBER   => count($this->data['perSumber']['baris']),
            default            => count($this->data['perBidang']),
        };
    }

    /** Baris total di bawah data: Per Kegiatan 2 (berencana + semua), lainnya 1. */
    private function jumlahBarisTotal(): int
    {
        return $this->jenis === self::PER_BIDANG ? 1 : 2;
    }

    public function array(): array
    {
        $baris = [
            ['Rekap Realisasi Kegiatan — ' . match ($this->jenis) {
                self::PER_KEGIATAN => 'Per Kegiatan',
                self::PER_SUMBER   => 'Per Sumber Dana',
                default            => 'Per Bidang/Unit',
            }],
            ['Filter: ' . $this->data['filter']],
            [$this->diunduh],
            [''], // baris kosong; [] dibuang pustaka sehingga kepala tabel bergeser naik
            array_column($this->kolom(), 0),
        ];

        $total = $this->data['total'];

        if ($this->jenis === self::PER_SUMBER) {
            $ps = $this->data['perSumber'];
            foreach ($ps['baris'] as $s) {
                $baris[] = [$s['sumber'], $s['kegiatan'], $s['rencana'], $s['terpakai'], $s['selisih']];
            }
            $baris[] = ['TOTAL KEGIATAN BERENCANA (' . $ps['banding']['jumlah'] . ' kegiatan)', null, $ps['banding']['rencana'], $ps['banding']['terpakai'], $ps['banding']['selisih']];
            $baris[] = ['TOTAL SEMUA KEGIATAN (' . $ps['semua_jumlah'] . ', termasuk tanpa Rencana Proker)', null, null, $ps['semua_terpakai'], null];
        } elseif ($this->jenis === self::PER_KEGIATAN) {
            $kosong = array_fill(0, count(SumberDana::KOLOM), null);
            foreach ($this->data['baris'] as $b) {
                $baris[] = [
                    $b['no'], $b['judul'], $b['bidang'], $b['periode'], $b['tanggal'], $b['lokasi'], $b['ketua'],
                    $b['peserta_target'], $b['peserta_hadir'],
                    $b['anggaran_rencana'], $b['anggaran_terpakai'], $b['selisih'],
                    ...array_values($b['dana_terpakai']),
                    $b['keterangan'],
                ];
            }
            $danaTotal = array_map(
                fn ($k) => array_sum(array_map(fn ($b) => $b['dana_terpakai'][$k] ?? 0, $this->data['baris'])),
                array_keys(SumberDana::KOLOM)
            );
            $baris[] = [
                '', 'TOTAL KEGIATAN BERENCANA (' . $total['punya_rencana'] . ' kegiatan)', '', '', '', '', '',
                $total['banding_peserta_target'], $total['banding_peserta_hadir'],
                $total['banding_rencana'], $total['banding_terpakai'], $total['banding_selisih'],
                ...$kosong,
                'Dasar perbandingan rencana',
            ];
            $baris[] = [
                '', 'TOTAL SEMUA KEGIATAN (' . $total['jumlah'] . ' kegiatan)', '', '', '', '', '',
                null, $total['semua_peserta_hadir'],
                null, $total['semua_terpakai'], null,
                ...$danaTotal,
                'Termasuk kegiatan tanpa Rencana Proker',
            ];
        } else {
            $kolomBidang = fn (array $b) => [
                $b['jumlah'], $b['punya_rencana'],
                $b['banding_rencana'], $b['banding_terpakai'], $b['banding_selisih'],
                $b['semua_terpakai'], $b['semua_peserta_hadir'],
            ];
            foreach ($this->data['perBidang'] as $b) {
                $baris[] = [$b['bidang'], ...$kolomBidang($b)];
            }
            $baris[] = ['TOTAL', ...$kolomBidang($total)];
        }

        $baris[] = [''];
        foreach (self::catatanKaki() as $catatan) {
            $baris[] = [$catatan];
        }

        return $baris;
    }

    /** Dipakai juga oleh tampilan PDF supaya penjelasannya sama. */
    public static function catatanKaki(): array
    {
        return [
            'Target peserta & anggaran rencana diambil dari Rencana Proker saat diajukan; peserta hadir & anggaran terpakai dari isian Pelaksanaan yang diarsipkan.',
            'Selisih positif = hemat (sisa anggaran), negatif = melebihi rencana. Total "kegiatan berencana" hanya menjumlahkan kegiatan yang punya Rencana Proker, sehingga rencana − terpakai = selisih; total "semua kegiatan" menjumlahkan seluruh anggaran terpakai.',
            'Kegiatan "Tanpa Rencana Proker" dicatat langsung di Laporan & Arsip (data sebelum alur Rencana Proker, atau kegiatan Prodi), jadi kolom rencananya kosong.',
            'Sumber dana (Dana Departemen, Kas Himpunan, Kas Bidang) dirinci sejak 10 Nov 2026; anggaran kegiatan sebelumnya masuk "Belum dirinci (data lama)".',
        ];
    }

    public function columnWidths(): array
    {
        $lebar = [];
        foreach ($this->kolom() as $i => $k) {
            $lebar[Coordinate::stringFromColumnIndex($i + 1)] = $k[1];
        }

        return $lebar;
    }

    /** Semua teks = teks biasa; angka & null tetap diproses bawaan. */
    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $kolom    = $this->kolom();
                $akhir    = Coordinate::stringFromColumnIndex(count($kolom));
                $kepala   = self::BARIS_KEPALA;
                $jumlahData = $this->jumlahData();
                $totalPertama = $kepala + $jumlahData + 1;
                $barisTotal   = $kepala + $jumlahData + $this->jumlahBarisTotal(); // baris total terakhir

                // Judul, filter, waktu unduh
                foreach ([1, 2, 3] as $r) {
                    $sheet->mergeCells("A{$r}:{$akhir}{$r}");
                }
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB(self::NAVY);
                $sheet->getStyle('A2:A3')->getFont()->setSize(10)->getColor()->setRGB('475467');

                // Kepala tabel
                $sheet->getStyle("A{$kepala}:{$akhir}{$kepala}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::NAVY]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension($kepala)->setRowHeight(32);

                // Isi + total: garis tipis, rata per kolom, format angka
                $sheet->getStyle("A{$kepala}:{$akhir}{$barisTotal}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D5DD']]],
                ]);
                $sheet->getStyle("A" . ($kepala + 1) . ":{$akhir}{$barisTotal}")
                    ->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

                foreach ($kolom as $i => [, , $format, $rata]) {
                    $huruf = Coordinate::stringFromColumnIndex($i + 1);
                    $rentang = "{$huruf}" . ($kepala + 1) . ":{$huruf}{$barisTotal}";
                    if ($format) {
                        $sheet->getStyle($rentang)->getNumberFormat()->setFormatCode($format);
                    }
                    $sheet->getStyle($rentang)->getAlignment()->setHorizontal(match ($rata) {
                        'center' => Alignment::HORIZONTAL_CENTER,
                        'right'  => Alignment::HORIZONTAL_RIGHT,
                        default  => Alignment::HORIZONTAL_LEFT,
                    });
                }

                // Baris total
                $sheet->getStyle("A{$totalPertama}:{$akhir}{$barisTotal}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2F8']],
                ]);
                $sheet->getStyle("A{$totalPertama}:{$akhir}{$totalPertama}")->applyFromArray([
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::NAVY]]],
                ]);

                // Catatan kaki: kecil, abu, tanpa bungkus supaya tidak memanjangkan kolom A
                $mulaiCatatan = $barisTotal + 2;
                $sheet->getStyle("A{$mulaiCatatan}:A" . ($mulaiCatatan + count(self::catatanKaki()) - 1))
                    ->applyFromArray(['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '667085']]]);

                // Saring & bekukan kepala tabel (baris total tidak ikut disaring)
                if ($jumlahData > 0) {
                    $sheet->setAutoFilter("A{$kepala}:{$akhir}" . ($kepala + $jumlahData));
                }
                $sheet->freezePane('A' . ($kepala + 1));

                // Siap cetak: lanskap A4, muat selebar halaman, kepala tabel diulang
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0)
                    ->setRowsToRepeatAtTopByStartAndEnd($kepala, $kepala);
            },
        ];
    }
}
