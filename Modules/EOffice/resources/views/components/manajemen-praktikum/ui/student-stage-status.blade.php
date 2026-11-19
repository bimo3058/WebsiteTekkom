@props(['ta','stage','submission'=>null,'grade'=>null])
@php
    $entries=$submission?->entries()??collect();
    $components=collect($ta->componentsFor($stage['id']));
    $pending=data_get(collect($grade?->riwayat??[])->last(),'belum_ditinjau',[]);
    $graded=$components->isNotEmpty() && $grade && $components->every(fn($c)=>isset($grade->skor[$c['id']])) && !in_array($stage['id'],$pending);
    $deadline=($stage['revisi_aktif']??false) ? ($stage['revisi_deadline']??$stage['deadline']??null) : ($stage['deadline']??null);
    [$tone,$label]=match(true){
        !($stage['pengumpulan']??true)=>['neutral','Tanpa Pengumpulan'],
        $submission?->status==='dibatalkan'=>['neutral','Pengiriman Dibatalkan'],
        $submission?->status==='perlu_perbaikan'=>['error','Perlu Revisi'],
        $entries->isNotEmpty() && $graded=>['success','ACC'],
        $entries->isNotEmpty()=>['warning','Menunggu Penilaian'],
        $deadline && now()->gt($deadline)=>['error','Waktu Habis'],
        $deadline && now()->diffInDays(\Illuminate\Support\Carbon::parse($deadline),false)<=2=>['warning','Segera!'],
        default=>['neutral','Belum Dikumpul'],
    };
    $value=$label==='ACC' && $grade->disetujui_koor && $grade->disetujui_dosen ? $ta->average($grade->skor,$stage['id']) : null;
@endphp
<span class="mp-badge {{ $tone }} sm"><span class="dot"></span>{{ $label }}@if($value!==null)<span>Nilai: {{ number_format($value,2,',','.') }}</span>@endif</span>
@if(($entries->first()['kategori']??null)==='terlambat')<span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41">Terlambat</span>@endif
