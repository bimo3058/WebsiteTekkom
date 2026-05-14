<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Dosen — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Welcome Banner --}}
<div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#6B21A8 0%,#9B59B6 100%);">
    <div>
        <div class="text-[18px] font-bold tracking-tight">Halo, {{ $name }}!</div>
        <div class="text-[12px] opacity-75 mt-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel ?? 'Semester Genap 2025/2026' }}
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $totalPraktikumDiampu ?? 0 }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Praktikum Diampu</div>
        </div>
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $nilaiPending ?? 0 }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Nilai Belum Dinput</div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-[14px] flex-shrink-0">
@php
$stats = [
    ['lbl'=>'Praktikum Aktif',  'val'=>$totalPraktikumAktif??0,  'sub'=>'diampu semester ini',    'ibg'=>'#F0E6FA','ic'=>'#9B59B6'],
    ['lbl'=>'Total Mahasiswa',  'val'=>$totalMahasiswa??0,        'sub'=>'semua praktikum',        'ibg'=>'#D1F0F9','ic'=>'#106A97'],
    ['lbl'=>'Total Modul',      'val'=>$totalModul??0,            'sub'=>'modul tersedia',         'ibg'=>'#DDF2EE','ic'=>'#40C4AA'],
    ['lbl'=>'Nilai Pending',    'val'=>$nilaiPending??0,          'sub'=>'perlu diinput',          'ibg'=>'#F9ECCB','ic'=>'#D39C3D'],
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

{{-- Daftar Praktikum yang Diampu --}}
<div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7]">
        <div>
            <div class="font-bold text-[15px] text-[#0D0D12]">Praktikum yang Diampu</div>
            <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $semesterLabel ?? 'Semester Genap 2025/2026' }}</div>
        </div>
    </div>

    @if($praktikums->isEmpty())
    <div class="py-12 text-center text-[13px] text-[#666D80]">Belum ada praktikum yang diampu.</div>
    @else
    <div class="grid grid-cols-2 gap-4 p-5">
        @foreach($praktikums as $p)
        <div class="border border-[#DFE1E7] rounded-[12px] p-4 hover:border-[#C1C7CF] transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="text-[13px] font-bold text-[#0D0D12]">{{ $p->nama }}</div>
                    <div class="text-[11px] text-[#666D80] mt-[2px]">Kode: <span class="font-semibold text-[#9B59B6]">{{ $p->kode ?? '—' }}</span></div>
                </div>
                @if($p->status === 'aktif')
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Aktif</span>
                @else
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0F1F4] text-[#666D80]">Nonaktif</span>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="text-center p-2 bg-[#F6F8FA] rounded-[8px]">
                    <div class="text-[16px] font-bold text-[#0D0D12]">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                    <div class="text-[10px] text-[#666D80]">Mahasiswa</div>
                </div>
                <div class="text-center p-2 bg-[#F6F8FA] rounded-[8px]">
                    <div class="text-[16px] font-bold text-[#0D0D12]">{{ $p->modul_count ?? 0 }}</div>
                    <div class="text-[10px] text-[#666D80]">Modul</div>
                </div>
                <div class="text-center p-2 bg-[#F6F8FA] rounded-[8px]">
                    <div class="text-[16px] font-bold text-[#0D0D12]">{{ $p->asisten_praktikum_count ?? 0 }}</div>
                    <div class="text-[10px] text-[#666D80]">Asprak</div>
                </div>
            </div>

            {{-- Koordinator status --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="text-[11px] text-[#666D80]">Koordinator:</span>
                @if($p->koordinator)
                <span class="text-[11px] font-semibold text-[#0D0D12]">{{ $p->koordinator->name }}</span>
                @else
                <span class="text-[11px] font-semibold text-[#DF1C41]">Belum ditunjuk</span>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
                   class="flex-1 text-center text-[12px] font-medium py-[7px] rounded-[8px] border border-[#DFE1E7] text-[#353849] no-underline hover:bg-[#F6F8FA]">Lihat Modul</a>
                <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
                   class="flex-1 text-center text-[12px] font-medium py-[7px] rounded-[8px] bg-[#9B59B6] text-white no-underline hover:bg-[#8e44ad]">Input Nilai</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Perlu Tindakan: Tunjuk Koordinator --}}
@if($praktikumTanpaKoor->isNotEmpty())
<div class="bg-[#FADAE1] border border-[#DF1C41] rounded-[12px] p-4 flex-shrink-0">
    <div class="flex items-center gap-2 mb-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span class="text-[13px] font-bold text-[#7C1028]">Praktikum Belum Punya Koordinator</span>
    </div>
    <div class="flex flex-col gap-2">
        @foreach($praktikumTanpaKoor as $p)
        <div class="flex items-center justify-between bg-white rounded-[8px] px-3 py-2">
            <span class="text-[13px] font-medium text-[#0D0D12]">{{ $p->nama }}</span>
            <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                <input type="text" name="nim" placeholder="NIM Koordinator"
                       class="text-[12px] border border-[#DFE1E7] rounded-[6px] px-2 py-[5px] w-[150px] focus:outline-none focus:border-[#9B59B6]">
                <button type="submit"
                        class="text-[12px] font-semibold px-3 py-[5px] rounded-[6px] bg-[#9B59B6] text-white border-none cursor-pointer hover:bg-[#8e44ad]">Tunjuk</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
