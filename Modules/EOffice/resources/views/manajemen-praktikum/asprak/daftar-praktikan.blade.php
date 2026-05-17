<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan — Asisten Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Praktikan</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($asprak?->praktikum) · {{ $asprak->praktikum->nama }} @endif
        </p>
    </div>
    <div class="mp-page-actions">
        <div style="text-align:right;">
            <div style="font-size:11px;color:#666D80;margin-bottom:2px;">Total Praktikan</div>
            <div style="font-size:22px;font-weight:700;color:#0D0D12;line-height:1;">{{ $praktikans->total() }}</div>
        </div>
    </div>
</div>

@if(!$asprak)
<div class="mp-alert warning flex-shrink-0">Status asisten praktikum Anda belum aktif. Hubungi koordinator untuk aktivasi.</div>
@else

{{-- Search Bar --}}
<form method="GET" action="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}"
      style="display:flex;gap:8px;flex-shrink:0;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email praktikan..."
           class="mp-input" style="flex:1;">
    <button type="submit" class="mp-btn primary md">Cari</button>
    @if($search)
    <a href="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Reset</a>
    @endif
</form>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Mahasiswa</span>
    <span class="sec-rule"></span>
    @if($search)
    <span class="mp-badge neutral sm">Hasil pencarian: "{{ $search }}"</span>
    @endif
</div>

{{-- Tabel --}}
<div class="mp-card flex-1 min-h-0">
    {{-- Header tabel --}}
    <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
        <div class="mp-th flex-1">Nama</div>
        <div class="mp-th" style="width:180px;">Email</div>
        <div class="mp-th" style="width:110px;text-align:center;">Kehadiran</div>
        <div class="mp-th" style="width:90px;text-align:center;">Status</div>
    </div>

    {{-- Rows --}}
    <div class="overflow-y-auto flex-1">
        @forelse($praktikans as $dp)
        @php
            $pct = $absensiMap[$dp->id] ?? null;
            $pctColor = is_null($pct) ? '#666D80' : ($pct >= 75 ? '#0B266E' : '#DF1C41');
        @endphp
        <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;border-bottom:1px solid #DFE1E7;"
             onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background=''">

            {{-- Avatar + Nama --}}
            <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0;padding-right:12px;">
                <div class="mp-av yellow">{{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}</div>
                <div style="min-width:0;">
                    <div style="font-size:13px;font-weight:500;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $dp->user?->name ?? '—' }}</div>
                    @if($dp->user?->student_number ?? $dp->user?->student?->student_number ?? null)
                    <div style="font-size:11px;color:#666D80;">
                        {{ $dp->user->student_number ?? $dp->user->student?->student_number }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Email --}}
            <div style="width:180px;font-size:12px;color:#666D80;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                {{ $dp->user?->email ?? '—' }}
            </div>

            {{-- Kehadiran --}}
            <div style="width:110px;text-align:center;">
                @if(is_null($pct))
                <span style="font-size:12px;color:#666D80;">—</span>
                @else
                <div style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="font-size:13px;font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</span>
                    @if($pct >= 75)
                    <span class="mp-badge success sm"><span class="dot"></span>Baik</span>
                    @else
                    <span class="mp-badge error sm"><span class="dot"></span>Kurang</span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Status --}}
            <div style="width:90px;text-align:center;">
                <span class="mp-badge success sm"><span class="dot"></span>Terdaftar</span>
            </div>
        </div>
        @empty
        <div style="padding:48px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            <div style="font-size:13px;font-weight:500;color:#666D80;">
                @if($search)
                Tidak ada praktikan yang cocok dengan pencarian "{{ $search }}".
                @else
                Belum ada praktikan yang terdaftar di praktikum ini.
                @endif
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($praktikans->hasPages())
    <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;">
        {{ $praktikans->links() }}
    </div>
    @endif
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
