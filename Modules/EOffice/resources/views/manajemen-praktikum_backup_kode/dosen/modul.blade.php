<x-eoffice::manajemen-praktikum.layout pageTitle="Modul Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)

{{-- Section: Pilih Praktikum --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
            <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Pilih Praktikum</div>
        <div style="font-size:13px;color:#666D80;margin-bottom:24px;">Pilih praktikum yang ingin dilihat modulnya.</div>

        @if($praktikums->isEmpty())
        <div style="font-size:13px;color:#808897;">Kamu belum mengampu praktikum apapun.</div>
        @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
               class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid #DFE1E7;">
                <span style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</span>
                <span style="font-size:11px;color:#666D80;">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

@else

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Modul</h1>
        <p class="mp-page-sub">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.dosen.modul.index', '0') }}" class="mp-btn secondary md" style="text-decoration:none;">Ganti Praktikum</a>
    </div>
</div>

{{-- Section: Daftar Modul --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Modul Tersedia</span>
    <span class="sec-rule"></span>
    @if(!$modulList->isEmpty())
    <span class="mp-badge navy sm">{{ $modulList->count() }} modul</span>
    @endif
</div>

@if($modulList->isEmpty())
<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
            <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div style="font-size:15px;font-weight:500;color:#0D0D12;margin-bottom:4px;">Belum ada modul</div>
        <div style="font-size:13px;color:#666D80;">Modul dibuat oleh koordinator atau asisten praktikum.</div>
    </div>
</div>
@else
<div class="flex flex-col gap-3">
    @foreach($modulList as $item)
    @php $modul = $item['modul']; @endphp
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">

                {{-- Judul & badge --}}
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="mp-badge primary sm">Modul {{ $modul->urutan }}</span>
                    <div style="font-size:14px;font-weight:600;color:#0D0D12;" class="truncate">{{ $modul->nama }}</div>
                </div>

                @if($modul->deskripsi)
                <div style="font-size:12px;color:#666D80;margin-bottom:8px;" class="line-clamp-2">{{ $modul->deskripsi }}</div>
                @endif

                {{-- Asisten Praktikum --}}
                @if($item['asprak']->isNotEmpty())
                <div class="flex items-center gap-1 flex-wrap">
                    <span style="font-size:10px;color:#808897;">Asisten Praktikum:</span>
                    @foreach($item['asprak'] as $nama)
                    <span class="mp-badge success sm">{{ $nama }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Stat counts --}}
            <div class="flex gap-3 flex-shrink-0">
                <div style="text-align:center;padding:10px 14px;background:#F9FAFB;border:1px solid #DFE1E7;border-radius:10px;">
                    <div style="font-size:18px;font-weight:700;color:#0B266E;line-height:1;">{{ $item['jumlah_materi'] }}</div>
                    <div style="font-size:10px;color:#808897;margin-top:3px;">Materi</div>
                </div>
                <div style="text-align:center;padding:10px 14px;background:#F9FAFB;border:1px solid #DFE1E7;border-radius:10px;">
                    <div style="font-size:18px;font-weight:700;color:#0B266E;line-height:1;">{{ $item['jumlah_tugas'] }}</div>
                    <div style="font-size:10px;color:#808897;margin-top:3px;">Tugas</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endif

</x-eoffice::manajemen-praktikum.layout>
