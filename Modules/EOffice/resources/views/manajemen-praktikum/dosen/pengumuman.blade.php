<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Praktikum">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pengumuman Praktikum</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Pantau pengumuman yang diterbitkan pada praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

<div class="mp-alert info flex-shrink-0">
    Halaman ini bersifat <strong>read-only</strong>. Pengumuman dibuat oleh Koordinator atau Asisten Praktikum.
</div>

{{-- Pilih Praktikum --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">Anda belum mengampu praktikum aktif manapun.</div>
        @else
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:320px;"
                    onchange="this.form.submit()">
                @foreach($praktikumList as $p)
                <option value="{{ $p->id }}" {{ ($praktikum?->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->nama }}
                    @if($p->kode) [{{ $p->kode }}] @endif
                    · {{ $p->semester }} {{ $p->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>

@if($praktikum)

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pengumuman — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count() }} pengumuman</span>
</div>

@php $items = $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans : $pengumumans; @endphp

@forelse($items as $pg)
@php
    $nameParts = explode(' ', $pg->user?->name ?? 'SY');
    $initials  = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
    $avColors  = ['sky','navy','green','yellow','violet'];
    $avColor   = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
    $isSistem  = $pg->tipe_sistem !== null;
@endphp
<div class="mp-card flex-shrink-0"
     onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
     onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
    <div style="padding:20px;">
        <div style="display:flex;align-items:flex-start;gap:14px;">
            {{-- Avatar --}}
            <div class="mp-av {{ $isSistem ? 'navy' : $avColor }}" style="flex-shrink:0;margin-top:2px;">
                @if($isSistem)
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                @else
                {{ $initials }}
                @endif
            </div>

            <div style="flex:1;min-width:0;">
                {{-- Judul + Badges --}}
                <div style="display:flex;align-items:flex-start;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                    <div style="font-size:14px;font-weight:700;color:#0D0D12;flex:1;min-width:0;">{{ $pg->judul }}</div>
                    @if($isSistem)
                    <span class="mp-badge sky sm">Sistem</span>
                    @endif
                    @if(!$pg->is_published)
                    <span class="mp-badge warning sm">Draft</span>
                    @endif
                </div>

                {{-- Meta --}}
                <div style="font-size:11px;color:#808897;margin-bottom:10px;display:flex;gap:10px;flex-wrap:wrap;">
                    <span>
                        Oleh: <strong style="color:#666D80;">{{ $isSistem ? 'Sistem' : ($pg->user?->name ?? '—') }}</strong>
                    </span>
                    <span>{{ $pg->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</span>
                    @if($pg->praktikum)
                    <span>Praktikum: <strong style="color:#666D80;">{{ $pg->praktikum->nama }}</strong></span>
                    @endif
                </div>

                {{-- Konten --}}
                <div style="font-size:13px;color:#353849;line-height:1.65;white-space:pre-line;">{{ $pg->konten }}</div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman</div>
        <div style="font-size:12px;color:#666D80;">Belum ada pengumuman yang diterbitkan pada praktikum ini.</div>
    </div>
</div>
@endforelse

{{-- Pagination --}}
@if($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasPages())
<div class="flex-shrink-0" style="padding:8px 0;">{{ $pengumumans->links() }}</div>
@endif

@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>