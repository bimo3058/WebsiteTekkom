<?php

/*
|--------------------------------------------------------------------------
| Target / Standar Mutu Pembanding — Dashboard Analitik GPM (FASE 3A)
|--------------------------------------------------------------------------
| Standar penyelenggaraan prodi sebagai pembanding metrik evaluasi mutu.
| Bersifat statis (ditetapkan developer/penjaminan mutu), sehingga disimpan
| sebagai config alih-alih tabel DB.
|
| Tiap entri: target_value, comparison ('>=' atau '<='), label tampilan.
| Sebuah metrik dinilai "tercapai" bila memenuhi perbandingan terhadap target.
*/

return [

    'masa_studi' => [
        'target'     => 4.5,
        'comparison' => '<=',
        'label'      => 'Target ≤ 4.5 thn',
        'unit'       => 'thn',
    ],

    'waktu_tunggu_kerja' => [
        'target'     => 0.5,
        'comparison' => '<=',
        'label'      => 'Target ≤ 0.5 thn',
        'unit'       => 'thn',
    ],

    'serapan_kerja' => [
        'target'     => 80,
        'comparison' => '>=',
        'label'      => 'Target ≥ 80%',
        'unit'       => '%',
    ],

    'kelulusan_tepat_waktu' => [
        'target'     => 50,
        'comparison' => '>=',
        'label'      => 'Target ≥ 50%',
        'unit'       => '%',
    ],

];
