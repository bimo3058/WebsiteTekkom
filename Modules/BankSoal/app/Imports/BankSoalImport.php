<?php

namespace Modules\BankSoal\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BankSoalImport implements ToCollection
{
    protected $mkId;
    protected $cplId;
    protected $pertanyaanService;
    public $successCount = 0;

    public function __construct($mkId, $cplId, $pertanyaanService)
    {
        $this->mkId = $mkId;
        $this->cplId = $cplId;
        $this->pertanyaanService = $pertanyaanService;
    }

    public function collection(Collection $rows)
    {
        $currentSoal = null;
        $currentJawaban = [];
        $opsiCounter = 0;
        $abjad = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($rows as $index => $row) {
            $jenis = strtoupper(trim($row[1] ?? ''));

            if ($jenis === 'SOAL') {
                if ($currentSoal !== null && !empty($currentSoal['soal'])) {
                    $this->pertanyaanService->create($currentSoal, $currentJawaban);
                    $this->successCount++;
                }

                $teksSoal = trim($row[3] ?? '');
                if (empty($teksSoal)) {
                    $currentSoal = null; 
                    continue;
                }

                $rawK = trim($row[5] ?? '1');
                $kesulitan = 'easy'; $bobot = 10;
                if ($rawK == '2') { $kesulitan = 'intermediate'; $bobot = 15; }
                if ($rawK == '3') { $kesulitan = 'advanced'; $bobot = 20; }

                $currentSoal = [
                    'mk_id' => $this->mkId,
                    'cpl_id' => $this->cplId,
                    'soal' => $teksSoal,
                    'kesulitan' => $kesulitan,
                    'bobot' => $bobot,
                    'tipe_soal' => 'pilihan_ganda'
                ];
                $currentJawaban = [];
                $opsiCounter = 0;

            } elseif ($jenis === 'JAWABAN' && $currentSoal !== null) {
                $isi = trim($row[3] ?? '');
                $status = trim($row[4] ?? '0');

                if (!empty($isi) && isset($abjad[$opsiCounter])) {
                    $currentJawaban[] = [
                        'opsi' => $abjad[$opsiCounter],
                        'deskripsi' => $isi,
                        'is_benar' => ($status === '1' || strtolower($status) === 'benar') ? true : false,
                    ];
                    $opsiCounter++;
                }
            }
        }

        if ($currentSoal !== null && !empty($currentSoal['soal'])) {
            $this->pertanyaanService->create($currentSoal, $currentJawaban);
            $this->successCount++;
        }

        if ($this->successCount === 0) {
            throw new \Exception("Tidak ada format SOAL yang benar. Pastikan Kolom 'Jenis' terisi kata 'SOAL' dan 'JAWABAN'.");
        }
    }
}