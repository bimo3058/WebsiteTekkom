<x-eoffice::manajemen-praktikum.layout pageTitle="Modul Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)
<div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">📚</div>
    <div style="font-size:15px;font-weight:600;color:var(--c-fg);margin-bottom:4px;">Pilih Praktikum</div>
    <div style="font-size:13px;color:var(--c-fg-muted);margin-bottom:20px;">Pilih praktikum yang ingin dilihat modulnya.</div>
    @if($praktikums->isEmpty())
        <div style="font-size:13px;color:var(--c-fg-placeholder);">Kamu belum mengampu praktikum apapun.</div>
    @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
               class="mp-tr flex items-center justify-between px-4 py-3 rounded-[10px]" style="text-decoration:none;border:1px solid var(--c-border);">
                <span style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $p->nama }}</span>
                <span style="font-size:11px;color:var(--c-fg-muted);">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
    @endif
</div>
@else

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Modul</h1>
        <p class="mp-page-sub">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.dosen.modul.index', '0') }}" class="mp-btn secondary md" style="text-decoration:none;">Ganti Praktikum</a>
    </div>
</div>

@if($modulList->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:48px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">📭</div>
    <div style="font-size:15px;font-weight:600;color:var(--c-fg);">Belum ada modul</div>
    <div style="font-size:13px;color:var(--c-fg-muted);margin-top:4px;">Modul dibuat oleh koordinator atau asisten praktikum.</div>
</div>
@else
<div class="flex flex-col gap-3">
    @foreach($modulList as $item)
    @php $modul = $item['modul']; @endphp
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="mp-badge primary sm">Modul {{ $modul->urutan }}</span>
                    <div style="font-size:14px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $modul->nama }}</div>
                </div>
                @if($modul->deskripsi)
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:4px;" class="line-clamp-2">{{ $modul->deskripsi }}</div>
                @endif
                @if($item['asprak']->isNotEmpty())
                <div class="flex items-center gap-1 mt-2 flex-wrap">
                    <span style="font-size:10px;color:var(--c-fg-placeholder);">Asprak:</span>
                    @foreach($item['asprak'] as $nama)
                    <span class="mp-badge success sm">{{ $nama }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex gap-3 flex-shrink-0">
                <div style="text-align:center;">
                    <div style="font-size:16px;font-weight:700;color:var(--c-primary);">{{ $item['jumlah_materi'] }}</div>
                    <div style="font-size:10px;color:var(--c-fg-placeholder);">Materi</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:16px;font-weight:700;color:var(--c-primary);">{{ $item['jumlah_tugas'] }}</div>
                    <div style="font-size:10px;color:var(--c-fg-placeholder);">Tugas</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endif

</x-eoffice::manajemen-praktikum.layout>
