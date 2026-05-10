<?php

namespace Modules\BankSoal\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BankSoalImport implements ToCollection
{
    protected $mkId;
    protected $cplId;
    protected $pertanyaanService;
    protected $allowedMkIds;
    public $successCount = 0;

    public function __construct($mkId, $cplId, $pertanyaanService, $allowedMkIds = null)
    {
        $this->mkId = $mkId;
        $this->cplId = $cplId;
        $this->pertanyaanService = $pertanyaanService;
        $this->allowedMkIds = $allowedMkIds;
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

                $cplKode = trim($row[6] ?? '');
                $mkNama = trim($row[7] ?? '');
                
                $cplModel = \Modules\BankSoal\Models\Cpl::where('kode', 'like', "%{$cplKode}%")->first();
                $cplId = $cplModel ? $cplModel->id : null;
                
                $mkModel = \Modules\BankSoal\Models\MataKuliah::where('nama', 'like', "%{$mkNama}%")->first();
                $mkId = $mkModel ? $mkModel->id : null;

                if (!$mkId) {
                    throw new \Exception("Mata Kuliah '{$mkNama}' tidak valid/kosong pada baris ke-" . ($index + 1) . ". Pastikan nama sesuai dengan yang ada di sistem.");
                }

                if ($this->allowedMkIds !== null && !in_array($mkId, $this->allowedMkIds)) {
                    throw new \Exception("Mata Kuliah '{$mkNama}' pada baris ke-" . ($index + 1) . " bukan merupakan Mata Kuliah yang Anda ampu.");
                }

                if (!$cplId) {
                    throw new \Exception("CPL '{$cplKode}' tidak valid/kosong pada baris ke-" . ($index + 1) . ". Pastikan kode CPL sesuai dengan yang ada di sistem.");
                }

                $currentSoal = [
                    'mk_id' => $mkId,
                    'cpl_id' => $cplId,
                    'soal' => $teksSoal,
                    'kesulitan' => $kesulitan,
                    'bobot' => $bobot,
                    'tipe_soal' => 'pilihan_ganda'
                ];
                $currentJawaban = [];
                $opsiCounter = 0;

            } elseif ($jenis === 'JAWABAN' && $currentSoal !== null) {
                $isi = trim((string)($row[3] ?? ''));
                $status = trim((string)($row[4] ?? '0'));

                if (strlen($isi) > 0 && isset($abjad[$opsiCounter])) {
                    $currentJawaban[] = [
                        'opsi' => $abjad[$opsiCounter],
                        'deskripsi' => $isi,
                        'is_benar' => ($status === '1' || $status === '1.0' || strtolower($status) === 'benar') ? true : false,
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