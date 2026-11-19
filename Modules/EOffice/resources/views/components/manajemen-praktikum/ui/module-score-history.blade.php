@props(['moduleId','scoreKey','studentId'=>null,'clientStudent'=>null,'scoreLabel'=>'Nilai','studentName'=>'Mahasiswa'])
@php
    $map=app(\Modules\EOffice\Services\ManajemenPraktikum\ScoreHistory::class)->formatted('module',(string)$moduleId);
    $byStudent=array_map(fn($columns)=>$columns[$scoreKey]??[],$map);
    $expression=$clientStudent
        ? '('.json_encode($byStudent,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT).')['.$clientStudent.']||[]'
        : json_encode($map[$studentId][$scoreKey]??[],JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
@endphp
<x-eoffice::manajemen-praktikum.ui.grade-history :client-entries="$expression" :score-label="$scoreLabel" :label="'Riwayat '.$scoreLabel.' '.$studentName" :client-label="$clientStudent ? json_encode('Riwayat '.$scoreLabel.' ').'+r.name' : null" />
