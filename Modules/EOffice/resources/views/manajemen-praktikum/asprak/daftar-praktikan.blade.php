<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan — Asisten Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Praktikan</h1>
        <p class="mp-page-sub">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($asprak?->praktikum) · {{ $asprak->praktikum->nama }} @endif
        </p>
    </div>
    <div style="text-align:right;">
        <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Total Praktikan</div>
        <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $praktikans->total() }}</div>
    </div>
</div>

@if(!$asprak)
<div class="mp-alert warning flex-shrink-0">Status asisten praktikum Anda belum aktif.</div>
@else

{{-- Search bar --}}
<form method="GET" action="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}" class="flex gap-2 flex-shrink-0">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email praktikan..."
           class="mp-input flex-1">
    <button type="submit" class="mp-btn success md">Cari</button>
    @if($search)
    <a href="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Reset</a>
    @endif
</form>

{{-- Tabel --}}
<div class="mp-card flex-1 min-h-0">
    {{-- Header tabel --}}
    <div style="display:flex;align-items:center;padding:10px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);flex-shrink:0;">
        <div class="mp-th flex-1">Nama</div>
        <div class="mp-th" style="width:160px;">Email</div>
        <div class="mp-th text-center" style="width:100px;">Kehadiran</div>
        <div class="mp-th text-center" style="width:80px;">Status</div>
    </div>

    {{-- Rows --}}
    <div class="overflow-y-auto flex-1">
        @forelse($praktikans as $dp)
        @php
            $pct = $absensiMap[$dp->id] ?? null;
            $pctColor = is_null($pct) ? 'var(--c-fg-muted)' : ($pct >= 75 ? '#40C4AA' : '#DF1C41');
        @endphp
        <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;border-bottom:1px solid var(--c-border-light);">
            {{-- Avatar + Nama --}}
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#0D6B55,#40C4AA);">
                    {{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $dp->user?->name ?? '—' }}</div>
                    @if($dp->user?->student_number ?? $dp->user?->student?->student_number ?? null)
                    <div style="font-size:11px;color:var(--c-fg-muted);">
                        {{ $dp->user->student_number ?? $dp->user->student?->student_number }}
                    </div>
                    @endif
                </div>
            </div>
            {{-- Email --}}
            <div style="width:160px;font-size:12px;color:var(--c-fg-muted);" class="truncate">
                {{ $dp->user?->email ?? '—' }}
            </div>
            {{-- Kehadiran --}}
            <div class="text-center" style="width:100px;">
                @if(is_null($pct))
                <span style="font-size:12px;color:var(--c-fg-muted);">—</span>
                @else
                <span style="font-size:13px;font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</span>
                @endif
            </div>
            {{-- Status --}}
            <div class="text-center" style="width:80px;">
                <span class="mp-badge success sm">Terdaftar</span>
            </div>
        </div>
        @empty
        <div style="padding:48px;text-align:center;font-size:13px;color:var(--c-fg-muted);">
            @if($search)
            Tidak ada praktikan yang cocok dengan pencarian "{{ $search }}".
            @else
            Belum ada praktikan yang terdaftar di praktikum ini.
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($praktikans->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--c-border);flex-shrink:0;">
        {{ $praktikans->links() }}
    </div>
    @endif
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
