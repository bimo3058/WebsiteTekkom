<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Asisten Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Welcome Banner --}}
<div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#0D6B55 0%,#40C4AA 100%);">
    <div>
        <div class="text-[18px] font-bold tracking-tight">Halo, {{ $name }}!</div>
        <div class="text-[12px] opacity-75 mt-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($asprak?->praktikum) · {{ $asprak->praktikum->nama }} @endif
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $tugasPendingNilai }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Tugas Perlu Dinilai</div>
        </div>
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
            <div class="text-[20px] font-bold">{{ $totalModul }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Modul Diampu</div>
        </div>
    </div>
</div>

@if(!$asprak)
<div class="bg-[#F9ECCB] border border-[#D39C3D] rounded-[12px] p-4 flex-shrink-0">
    <div class="text-[13px] font-semibold text-[#7C5309]">Status asisten praktikum Anda belum aktif.</div>
    <div class="text-[12px] text-[#7C5309] mt-1">Hubungi koordinator untuk mengaktifkan akun asprak Anda.</div>
</div>
@else

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-[14px] flex-shrink-0">
@php
$stats = [
    ['lbl'=>'Modul Diampu',         'val'=>$totalModul,          'sub'=>'modul dikelola',         'ibg'=>'#DDF2EE','ic'=>'#40C4AA'],
    ['lbl'=>'Total Materi',         'val'=>$totalMateri,         'sub'=>'materi diunggah',        'ibg'=>'#D1F0F9','ic'=>'#106A97'],
    ['lbl'=>'Tugas Perlu Dinilai',  'val'=>$tugasPendingNilai,   'sub'=>'pengumpulan masuk',      'ibg'=>'#F9ECCB','ic'=>'#D39C3D'],
    ['lbl'=>'Absensi Hari Ini',     'val'=>$absensiHariIni,      'sub'=>'sesi tercatat',          'ibg'=>'rgba(11,38,110,0.08)','ic'=>'#0B266E'],
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

{{-- Modul Cards --}}
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-center justify-between mb-4">
        <div class="font-bold text-[15px] text-[#0D0D12]">Modul yang Diampu</div>
        <a href="{{ route('eoffice.manprak.asprak.materi.index') }}"
           class="text-[12px] font-medium text-white px-3 py-[6px] rounded-[7px] bg-[#40C4AA] no-underline hover:bg-[#32a896]">+ Upload Materi</a>
    </div>
    @if($modulDiampu->isEmpty())
    <div class="py-6 text-center text-[13px] text-[#666D80]">Belum ada modul yang diampu.</div>
    @else
    <div class="grid grid-cols-3 gap-3">
        @foreach($modulDiampu as $m)
        <div class="border border-[#DFE1E7] rounded-[10px] p-4 hover:border-[#40C4AA] transition-colors">
            <div class="text-[13px] font-bold text-[#0D0D12] mb-1">{{ $m->nama ?? 'Modul' }}</div>
            <div class="text-[12px] text-[#666D80] mb-3">{{ $m->deskripsi ?? '—' }}</div>
            <div class="flex gap-2">
                <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
                   class="flex-1 text-center text-[11px] font-medium py-[5px] rounded-[6px] border border-[#DFE1E7] text-[#353849] no-underline hover:bg-[#F6F8FA]">Absensi</a>
                <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
                   class="flex-1 text-center text-[11px] font-medium py-[5px] rounded-[6px] bg-[#DDF2EE] text-[#174E43] no-underline hover:bg-[#c0e8e0]">Tugas</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Bottom: Pengumpulan Pending + Tugas Mendatang --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Pengumpulan perlu review --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0" style="flex:2;">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div>
                <div class="font-bold text-[15px] text-[#0D0D12]">Pengumpulan Tugas — Perlu Review</div>
                <div class="text-[12px] text-[#666D80] mt-[2px]">Belum diberi nilai</div>
            </div>
            <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumpulanPending ?? [] as $peng)
            <div class="flex items-center justify-between px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#0D6B55,#40C4AA);">
                        {{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[13px] font-medium text-[#0D0D12] truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                        <div class="text-[11px] text-[#666D80]">{{ $peng->tugas?->judul ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-[11px] text-[#A4ABB8]">{{ $peng->created_at?->diffForHumans() }}</span>
                    <a href="{{ route('eoffice.manprak.asprak.tugas.pengumpulan', $peng->tugas_id) }}"
                       class="text-[12px] font-semibold px-3 py-[5px] rounded-[7px] bg-[#DDF2EE] text-[#174E43] no-underline hover:bg-[#c0e8e0]">Nilai</a>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Semua pengumpulan sudah dinilai 🎉</div>
            @endforelse
        </div>
    </div>

    {{-- Tugas Mendatang --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[15px] text-[#0D0D12]">Tugas Mendatang</div>
            <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}"
               class="text-[12px] font-medium text-white px-3 py-[6px] rounded-[7px] bg-[#0B266E] no-underline hover:bg-[#0a1f5c]">+ Buat Tugas</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($tugasMendatang ?? [] as $t)
            @php
                $sisa = now()->diffInDays(\Carbon\Carbon::parse($t->deadline), false);
                $warn = $sisa <= 2;
            @endphp
            <div class="flex items-start gap-3 px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="w-[6px] h-[6px] rounded-full mt-[6px] flex-shrink-0"
                     style="background:{{ $warn ? '#DF1C41' : '#40C4AA' }};"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-medium text-[#0D0D12]">{{ $t->judul }}</div>
                    <div class="text-[11px] mt-[2px]" style="color:{{ $warn ? '#DF1C41' : '#666D80' }};">
                        Deadline: {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y, H:i') }}
                        @if($warn) ({{ $sisa }} hari lagi) @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Tidak ada tugas mendatang.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
