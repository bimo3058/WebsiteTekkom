<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Catat kehadiran mahasiswa per sesi modul · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <span style="font-size:12px;color:#666D80;">Sesi:</span>
        <input type="date" id="tglFilter" value="{{ date('Y-m-d') }}" class="mp-input">
    </div>
</div>

{{-- Section: Pilih Modul --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-3 flex-shrink-0 flex-wrap">
    @forelse($modulDiampu ?? [] as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="{{ request()->route('modulId') == $m->id ? 'mp-btn primary md' : 'mp-btn secondary md' }}"
       style="text-decoration:none;">
        {{ $m->nama }}
    </a>
    @empty
    <div style="font-size:13px;color:#808897;">Belum ada modul.</div>
    @endforelse
</div>

@if(isset($modul) && isset($praktikan))
{{-- Form Absensi --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">{{ $modul->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $praktikan->count() }} mahasiswa</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header" style="flex-shrink:0;">
        <div>
            <span class="mp-card-title">{{ $modul->nama }}</span>
            <div style="font-size:12px;color:#666D80;margin-top:2px;">{{ $praktikan->count() }} mahasiswa terdaftar</div>
        </div>
        <div class="right">
            <form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" id="absensiForm">
                @csrf
                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                <button type="submit" class="mp-btn primary md">Simpan Absensi</button>
            </form>
        </div>
    </div>

    <div style="display:flex;align-items:center;padding:8px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
        <div class="mp-th" style="width:50px;">No</div>
        <div class="mp-th flex-1">Nama Mahasiswa</div>
        <div class="mp-th" style="width:120px;">NIM</div>
        <div class="mp-th text-center" style="width:80px;">Hadir</div>
        <div class="mp-th text-center" style="width:80px;">Izin</div>
        <div class="mp-th text-center" style="width:80px;">Tidak Hadir</div>
        <div class="mp-th" style="width:150px;">Keterangan</div>
    </div>

    <div class="overflow-y-auto flex-1">
        @foreach($praktikan as $i => $prak)
        @php $existing = $absensiHariIni[$prak->id] ?? null; @endphp
        <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
            <div style="width:50px;font-size:12px;color:#666D80;">{{ $i + 1 }}</div>
            <div class="flex-1 flex items-center gap-[10px] pr-3 min-w-0">
                <div class="mp-av yellow">{{ strtoupper(substr($prak->user?->name ?? 'M', 0, 2)) }}</div>
                <span style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate">{{ $prak->user?->name }}</span>
            </div>
            <div style="width:120px;font-size:12px;color:#666D80;">{{ $prak->user?->student_number ?? '—' }}</div>
            @foreach(['hadir','izin','tidak_hadir'] as $status)
            <div class="flex justify-center" style="width:80px;">
                <input type="radio" form="absensiForm"
                       name="absensi[{{ $prak->id }}][status]"
                       value="{{ $status }}"
                       {{ ($existing?->status ?? 'hadir') === $status ? 'checked' : '' }}
                       class="w-4 h-4 cursor-pointer" style="accent-color:#0B266E;">
            </div>
            @endforeach
            <div style="width:150px;">
                <input type="text" form="absensiForm"
                       name="absensi[{{ $prak->id }}][keterangan]"
                       value="{{ $existing?->keterangan ?? '' }}"
                       placeholder="Opsional..."
                       class="mp-input w-full" style="font-size:12px;padding:4px 8px;">
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="mp-card flex-1" style="display:flex;align-items:center;justify-content:center;min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Pilih modul di atas untuk mulai mengisi absensi</div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
