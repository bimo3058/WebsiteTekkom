@props(['announcement'])
@php
    $parts=explode(' ',$announcement->user?->name??'Sistem');
    $initials=strtoupper(substr($parts[0],0,1).substr($parts[1]??'',0,1));
@endphp
<div style="display:flex;align-items:center;gap:12px;min-width:0">
    <div style="width:40px;height:40px;border-radius:50%;background:#D1D5DB;display:flex;align-items:center;justify-content:center;flex-shrink:0"><span style="font-size:14px;font-weight:700;color:#6B7280">{{ $initials }}</span></div>
    <div style="min-width:0;overflow-wrap:anywhere">
        <div style="font-weight:700;font-size:14px;color:#111827">{{ $announcement->user?->name??'Sistem' }}</div>
        <div style="font-size:12px;color:#6B7280">{{ $announcement->created_at?->format('d M Y, H:i')??'-' }}@if($announcement->updated_at && $announcement->updated_at->gt($announcement->created_at))<span style="font-style:italic;margin-left:4px">(Diedit {{ $announcement->updated_at->format('d M Y, H:i') }})</span>@endif</div>
    </div>
</div>
