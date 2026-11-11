@props(['status'])
<span class="mp-schedule-status" data-status="{{ $status }}">{{ ['draft'=>'Rancangan','pending'=>'Menunggu Persetujuan','approved'=>'Disetujui','rejected'=>'Ditolak','partial'=>'Sebagian Diputuskan','cancelled'=>'Dibatalkan'][$status]??$status }}</span>
