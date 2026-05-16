<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Admin — Manajemen Praktikum">
@php
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
    $name = auth()->user()->name;
@endphp

{{-- Welcome Banner --}}
<div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
     style="background:linear-gradient(120deg,#0B266E 0%,#1a3a8f 100%);">
    <div>
        <div class="text-[18px] font-bold tracking-tight">Selamat Datang, {{ $name }}!</div>
        <div class="text-[12px] opacity-75 mt-1">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.12);">
            <div class="text-[20px] font-bold">{{ ($totalAsprakPending??0) + ($totalKoorPending??0) }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Perlu Tindakan</div>
        </div>
        <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.12);">
            <div class="text-[20px] font-bold">{{ $totalPraktikumAktif ?? 0 }}</div>
            <div class="text-[10px] opacity-75 mt-[2px]">Praktikum Aktif</div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-4 gap-[14px] flex-shrink-0">
@php
$stats = [
    ['lbl'=>'Praktikum Aktif',   'val'=>$totalPraktikumAktif??0, 'sub'=>'semester ini',         'ibg'=>'rgba(11,38,110,0.08)','ic'=>'#0B266E','ip'=>'<path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>'],
    ['lbl'=>'Total Mahasiswa',   'val'=>$totalMahasiswa??0,       'sub'=>'terdaftar di sistem',  'ibg'=>'#D1F0F9',             'ic'=>'#106A97','ip'=>'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>'],
    ['lbl'=>'Total Dosen',       'val'=>$totalDosen??0,           'sub'=>'dari tabel lecturers', 'ibg'=>'#F0E6FA',             'ic'=>'#9B59B6','ip'=>'<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ['lbl'=>'Asprak Pending',    'val'=>$totalAsprakPending??0,   'sub'=>'perlu review',         'ibg'=>'#F9ECCB',             'ic'=>'#D39C3D','ip'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
];
@endphp
@foreach($stats as $s)
<div class="flex flex-col gap-[10px] bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
    <div class="flex items-start justify-between">
        <span class="text-[12px] font-medium text-[#666D80] leading-[1.4]">{{ $s['lbl'] }}</span>
        <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0"
             style="background:{{ $s['ibg'] }};">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $s['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $s['ip'] !!}</svg>
        </div>
    </div>
    <div class="text-[28px] font-bold text-[#0D0D12] leading-none tracking-tight">{{ $s['val'] }}</div>
    <span class="text-[11px] text-[#666D80]">{{ $s['sub'] }}</span>
</div>
@endforeach
</div>

{{-- Tabel Dosen & Mahasiswa --}}
<div class="flex gap-[14px] flex-shrink-0">

    {{-- Tabel Dosen --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7]">
            <div>
                <div class="font-bold text-[14px] text-[#0D0D12]">Dosen Terdaftar</div>
                <div class="text-[11px] text-[#666D80] mt-[1px]">{{ $totalDosen ?? 0 }} dosen dari tabel lecturers</div>
            </div>
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
        </div>
        <div class="flex px-5 py-2 bg-[#FAFBFC] border-b border-[#DFE1E7]">
            <div class="flex-1 text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama</div>
            <div class="text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:110px;">NIP / No. Pegawai</div>
            <div class="text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Praktikum</div>
        </div>
        <div class="overflow-y-auto" style="max-height:220px;">
            @forelse($dosenTerbaru ?? [] as $d)
            <div class="flex items-center px-5 py-[10px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0">
                <div class="flex-1 flex items-center gap-[8px] min-w-0 pr-2">
                    <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#7B2FBE,#9B59B6);">
                        {{ strtoupper(substr($d['name'], 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[12px] font-semibold text-[#0D0D12] truncate">{{ $d['name'] }}</div>
                        <div class="text-[10px] text-[#666D80] truncate">{{ $d['email'] }}</div>
                    </div>
                </div>
                <div class="text-[11px] text-[#666D80] font-mono" style="width:110px;">{{ $d['employee_number'] }}</div>
                <div class="text-center" style="width:80px;">
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0E6FA] text-[#9B59B6]">
                        {{ $d['jumlah_praktikum'] }} matkul
                    </span>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[12px] text-[#A4ABB8]">Belum ada dosen terdaftar.</div>
            @endforelse
        </div>
    </div>

    {{-- Tabel Mahasiswa --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7]">
            <div>
                <div class="font-bold text-[14px] text-[#0D0D12]">Mahasiswa Terdaftar</div>
                <div class="text-[11px] text-[#666D80] mt-[1px]">{{ $totalMahasiswa ?? 0 }} mahasiswa dari tabel students</div>
            </div>
        </div>
        <div class="flex px-5 py-2 bg-[#FAFBFC] border-b border-[#DFE1E7]">
            <div class="flex-1 text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama</div>
            <div class="text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:90px;">NIM</div>
            <div class="text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:60px;">Angkatan</div>
            <div class="text-[10px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Praktikum</div>
        </div>
        <div class="overflow-y-auto" style="max-height:220px;">
            @forelse($mahasiswaTerbaru ?? [] as $m)
            <div class="flex items-center px-5 py-[10px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0">
                <div class="flex-1 flex items-center gap-[8px] min-w-0 pr-2">
                    <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#106A97,#3C9DBE);">
                        {{ strtoupper(substr($m['name'], 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[12px] font-semibold text-[#0D0D12] truncate">{{ $m['name'] }}</div>
                        <div class="text-[10px] text-[#666D80] truncate">{{ $m['email'] }}</div>
                    </div>
                </div>
                <div class="text-[11px] text-[#666D80] font-mono" style="width:90px;">{{ $m['student_number'] }}</div>
                <div class="text-[11px] text-[#666D80] text-center" style="width:60px;">{{ $m['cohort_year'] }}</div>
                <div class="text-center" style="width:80px;">
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#D1F0F9] text-[#106A97]">
                        {{ $m['jumlah_praktikum'] }} ikut
                    </span>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[12px] text-[#A4ABB8]">Belum ada mahasiswa terdaftar.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Bottom: Tabel Praktikum + Pendaftaran Pending --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Tabel Praktikum --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0" style="flex:2;">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div>
                <div class="font-bold text-[15px] text-[#0D0D12]">Daftar Praktikum</div>
                <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $semesterLabel }}</div>
            </div>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
        </div>
        <div class="flex px-5 py-2 bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:90px;">Kode</div>
            <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:150px;">Dosen</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:75px;">Praktikan</div>
            <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Status</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($praktikums ?? [] as $p)
            <div class="flex items-center px-5 py-[11px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0 cursor-pointer"
                 onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.show', $p->id) }}'">
                <div class="text-[12px] font-semibold" style="width:90px; color:#0B266E;">{{ $p->kode ?? '—' }}</div>
                <div class="flex-1 text-[13px] font-medium text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap pr-3">{{ $p->nama }}</div>
                <div class="text-[12px] text-[#666D80] overflow-hidden text-ellipsis whitespace-nowrap" style="width:150px;">{{ $p->dosen?->name ?? '—' }}</div>
                <div class="text-[13px] font-semibold text-[#0D0D12]" style="width:75px;">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                <div style="width:80px;">
                    @if($p->status === 'aktif')
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#DDF2EE] text-[#174E43]">
                            <span class="w-[5px] h-[5px] rounded-full bg-[#40C4AA]"></span>Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#F0F1F4] text-[#666D80]">
                            <span class="w-[5px] h-[5px] rounded-full bg-[#A4ABB8]"></span>Nonaktif
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-[13px] text-[#666D80]">Belum ada data praktikum.</div>
            @endforelse
        </div>
    </div>

    {{-- Pendaftaran Pending --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0 flex-1">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div>
                <div class="font-bold text-[15px] text-[#0D0D12]">Pendaftaran Pending</div>
                <div class="text-[12px] text-[#666D80] mt-[2px]">Perlu persetujuan</div>
            </div>
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
               class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pendaftaranTerbaru ?? [] as $pend)
            <div class="flex items-center justify-between px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="w-[32px] h-[32px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                        {{ strtoupper(substr($pend->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $pend->user?->name ?? '—' }}</div>
                        <div class="text-[11px] text-[#666D80] truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}">
                        @csrf
                        <button type="submit" class="text-[11px] font-semibold px-2 py-[4px] rounded-[6px] bg-[#DDF2EE] text-[#174E43] border-none cursor-pointer hover:bg-[#c0e8e0]">✓</button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[11px] font-semibold px-2 py-[4px] rounded-[6px] bg-[#FADAE1] text-[#7C1028] border-none cursor-pointer hover:bg-[#f5b8c5]">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[13px] text-[#666D80]">Tidak ada pendaftaran pending.</div>
            @endforelse
        </div>
    </div>

</div>

</x-eoffice::manajemen-praktikum.layout>