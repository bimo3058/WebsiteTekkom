<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Koordinator — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Welcome Banner --}}
<div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#0F4C75 0%,#1a6691 100%);">
    <div>
        <div class="text-[18px] font-bold tracking-tight">Halo, {{ $name }}!</div>
        <div class="text-[12px] opacity-75 mt-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($praktikum) · Koordinator {{ $praktikum->nama }} @endif
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $totalAsprak }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Asisten Praktikum</div>
        </div>
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $asprakBelumModul }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Belum dapat Modul</div>
        </div>
    </div>
</div>

@if(!$praktikum)
<div class="bg-[#F9ECCB] border border-[#D39C3D] rounded-[12px] p-4 flex-shrink-0">
    <div class="text-[13px] font-semibold text-[#7C5309]">Anda belum ditunjuk sebagai koordinator praktikum manapun.</div>
    <div class="text-[12px] text-[#7C5309] mt-1">Hubungi dosen pengampu untuk mendapatkan penugasan.</div>
</div>
@else

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-[14px] flex-shrink-0">
@php
$stats = [
    ['lbl'=>'Total Praktikan',      'val'=>$totalPraktikan,       'sub'=>'terdaftar',           'ibg'=>'#D1F0F9','ic'=>'#106A97'],
    ['lbl'=>'Total Asprak',         'val'=>$totalAsprak,          'sub'=>'asisten aktif',       'ibg'=>'rgba(11,38,110,0.08)','ic'=>'#0B266E'],
    ['lbl'=>'Asprak Terdistribusi', 'val'=>$asprakTerdistribusi,  'sub'=>'sudah dapat modul',   'ibg'=>'#DDF2EE','ic'=>'#40C4AA'],
    ['lbl'=>'Total Modul',          'val'=>$totalModul,           'sub'=>'modul tersedia',      'ibg'=>'#F9ECCB','ic'=>'#D39C3D'],
];
@endphp
@foreach($stats as $s)
<div class="flex flex-col gap-[10px] bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
    <div class="flex items-start justify-between">
        <span class="text-[12px] font-medium text-[#666D80]">{{ $s['lbl'] }}</span>
        <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px]" style="background:{{ $s['ibg'] }};"></div>
    </div>
    <div class="text-[28px] font-bold text-[#0D0D12] leading-none tracking-tight">{{ $s['val'] }}</div>
    <span class="text-[11px] text-[#666D80]">{{ $s['sub'] }}</span>
</div>
@endforeach
</div>

{{-- Progress Distribusi Asprak --}}
@php
$pct = $totalAsprak > 0 ? round($asprakTerdistribusi / $totalAsprak * 100) : 0;
@endphp
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="font-bold text-[14px] text-[#0D0D12]">Progress Distribusi Asprak ke Modul</div>
            <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $asprakTerdistribusi }} dari {{ $totalAsprak }} asprak sudah mendapat modul</div>
        </div>
        <div class="text-[24px] font-bold" style="color:#106A97;">{{ $pct }}%</div>
    </div>
    <div class="w-full bg-[#F0F1F4] rounded-full h-[8px]">
        <div class="h-[8px] rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(90deg,#106A97,#40C4AA);"></div>
    </div>
    @if($asprakBelumModul > 0)
    <div class="mt-2 flex items-center gap-2">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        <span class="text-[11px] text-[#7C5309]">{{ $asprakBelumModul }} asprak belum mendapat modul — segera distribusikan</span>
        <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}"
           class="text-[11px] font-semibold text-[#106A97] no-underline hover:underline">Bagi Modul →</a>
    </div>
    @endif
</div>

{{-- Bottom Grid: Daftar Asprak + Pengumuman --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Daftar Asprak --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0" style="flex:2;">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Daftar Asisten Praktikum</div>
            <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Bagi Modul</a>
        </div>
        <div class="flex px-5 py-2 bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
            <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">NIM</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:120px;">Modul</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:70px;">Status</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div class="flex items-center px-5 py-[10px] border-b border-[#F8F9FB] last:border-0">
                <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                    <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1a6691,#40C4AA);">
                        {{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <span class="text-[13px] font-medium text-[#0D0D12] truncate">{{ $a->user?->name ?? '—' }}</span>
                </div>
                <div class="text-[12px] text-[#666D80]" style="width:80px;">{{ $a->user?->student_number ?? '—' }}</div>
                <div class="text-[12px] text-[#353849]" style="width:120px;">
                    {{ $a->modulAsprak->pluck('modul.nama')->join(', ') ?: '—' }}
                </div>
                <div style="width:70px;">
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Aktif</span>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Belum ada asisten praktikum.</div>
            @endforelse
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Pengumuman</div>
            <a href="{{ route('eoffice.manprak.koor.pengumuman.index') }}"
               class="text-[12px] font-medium text-white px-3 py-[6px] rounded-[7px] bg-[#106A97] no-underline hover:bg-[#0e5a80]">+ Buat</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumuman ?? [] as $peng)
            <div class="px-5 py-[12px] border-b border-[#F8F9FB] last:border-0">
                <div class="text-[13px] font-semibold text-[#0D0D12]">{{ $peng->judul }}</div>
                <div class="text-[12px] text-[#666D80] mt-1 line-clamp-2">{{ $peng->konten }}</div>
                <div class="text-[11px] text-[#A4ABB8] mt-[6px]">{{ $peng->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
