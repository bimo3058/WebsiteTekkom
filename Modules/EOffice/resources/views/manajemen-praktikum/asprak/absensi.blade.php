<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Absensi Praktikum</h1>
        <p class="mp-page-sub">Catat kehadiran mahasiswa per sesi modul</p>
    </div>
    <div class="mp-page-actions">
        <span style="font-size:12px;color:var(--c-fg-muted);">Sesi:</span>
        <input type="date" id="tglFilter" value="{{ date('Y-m-d') }}" class="mp-input">
    </div>
</div>

{{-- Pilih Modul --}}
<div class="flex gap-3 flex-shrink-0 flex-wrap">
    @forelse($modulDiampu ?? [] as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="{{ request()->route('modulId') == $m->id ? 'mp-btn success md' : 'mp-btn secondary md' }}"
       style="text-decoration:none;">
        {{ $m->nama }}
    </a>
    @empty
    <div style="font-size:13px;color:var(--c-fg-placeholder);">Belum ada modul.</div>
    @endforelse
</div>

@if(isset($modul) && isset($praktikan))
{{-- Form Absensi --}}
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header" style="flex-shrink:0;">
        <div>
            <span class="mp-card-title">{{ $modul->nama }}</span>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $praktikan->count() }} mahasiswa terdaftar</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" id="absensiForm">
            @csrf
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
            <button type="submit" class="mp-btn success md">Simpan Absensi</button>
        </form>
    </div>

    <div class="mp-card-body" style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
            <div class="mp-th" style="width:50px;">No</div>
            <div class="mp-th flex-1">Nama Mahasiswa</div>
            <div class="mp-th" style="width:120px;">NIM</div>
            <div class="mp-th text-center" style="width:80px;">Hadir</div>
            <div class="mp-th text-center" style="width:80px;">Izin</div>
            <div class="mp-th text-center" style="width:80px;">Tidak Hadir</div>
            <div class="mp-th" style="width:150px;">Keterangan</div>
        </div>
    </div>

    <div class="overflow-y-auto flex-1">
        @foreach($praktikan as $i => $prak)
        @php $existing = $absensiHariIni[$prak->id] ?? null; @endphp
        <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
            <div style="width:50px;font-size:12px;color:var(--c-fg-muted);">{{ $i + 1 }}</div>
            <div class="flex-1 flex items-center gap-[10px] pr-3 min-w-0">
                <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#0D6B55,#40C4AA);">
                    {{ strtoupper(substr($prak->user?->name ?? 'M', 0, 2)) }}
                </div>
                <span style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $prak->user?->name }}</span>
            </div>
            <div style="width:120px;font-size:12px;color:var(--c-fg-muted);">{{ $prak->user?->student_number ?? '—' }}</div>
            @foreach(['hadir','izin','tidak_hadir'] as $status)
            <div class="flex justify-center" style="width:80px;">
                <input type="radio" form="absensiForm"
                       name="absensi[{{ $prak->id }}][status]"
                       value="{{ $status }}"
                       {{ ($existing?->status ?? 'hadir') === $status ? 'checked' : '' }}
                       class="w-4 h-4 cursor-pointer accent-[#40C4AA]">
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
<div class="mp-card flex-1 flex items-center justify-center">
    <div style="text-align:center;color:var(--c-fg-placeholder);">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
        <div style="font-size:14px;font-weight:500;">Pilih modul di atas untuk mulai mengisi absensi</div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
