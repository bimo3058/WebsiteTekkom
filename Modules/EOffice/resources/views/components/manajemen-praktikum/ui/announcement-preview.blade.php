@props(['announcement'])
@php
    $text=\Illuminate\Support\Str::squish(strip_tags($announcement->konten));
    $truncated=\Illuminate\Support\Str::length($text)>200;
    $preview=$truncated?rtrim(\Illuminate\Support\Str::substr($text,0,200)):$text;
    $destination=route('eoffice.manprak.mahasiswa.pengumuman.index',[
        'praktikum_id'=>$announcement->praktikum_id,'pengumuman_id'=>$announcement->id,
    ]).'#pengumuman-'.$announcement->id;
@endphp
@once
    <style>
        .mp-dashboard-announcement{display:block;padding:16px 20px;border-bottom:1px solid #ECEFF3;text-decoration:none;color:inherit;overflow-wrap:anywhere;transition:background .15s}
        .mp-dashboard-announcement:hover{background:#F8F9FB}
        .mp-dashboard-announcement:focus-visible{outline:2px solid #0B266E;outline-offset:-2px;background:#F8F9FB}
    </style>
@endonce
<a href="{{ $destination }}" class="mp-dashboard-announcement" aria-label="Buka pengumuman: {{ $announcement->judul }}">
    <div style="font-size:11px;font-weight:600;color:#1E3A8A;text-transform:uppercase;margin-bottom:6px;letter-spacing:.04em">{{ $announcement->praktikum?->nama ?? 'Praktikum' }}</div>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:14px;font-weight:700;color:#353849;margin-bottom:6px;line-height:1.4">{{ $announcement->judul }}<x-eoffice::manajemen-praktikum.ui.announcement-pin-badge :announcement="$announcement" /></div>
    <div style="font-size:13px;color:#4B5563;line-height:1.6">{{ $preview }}@if($truncated)<span>... <strong style="font-weight:700;color:#0B266E">Lihat selengkapnya</strong></span>@endif</div>
    <div style="font-size:11px;color:#808897;margin-top:8px">Dipublikasikan {{ $announcement->created_at->diffForHumans() }}@if($announcement->user) · oleh <span style="font-weight:600;color:#4B5563">{{ $announcement->user->name }}</span>@endif</div>
</a>
