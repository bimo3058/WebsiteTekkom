<x-eoffice::manajemen-praktikum.layout pageTitle="Bagi Asprak ke Modul">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Bagi Asprak ke Modul</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Tugaskan asisten praktikum ke modul-modul yang tersedia · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select">
                @foreach($praktikums as $prak)
                <option value="{{ $prak->id }}" {{ $prak->id == $praktikumId ? 'selected' : '' }}>
                    {{ $prak->nama }} ({{ $prak->semester }} {{ $prak->tahun_ajaran }})
                </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Penugasan Modul</span>
    <span class="sec-rule"></span>
    @if($selectedPraktikum)
    <span style="font-size:12px;color:#666D80;">{{ $selectedPraktikum->nama }}</span>
    @endif
</div>

@if($praktikums->isEmpty())
<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada praktikum aktif saat ini.</div>
    </div>
</div>
@else

<div class="flex gap-4 flex-1 min-h-0">

    {{-- Daftar Modul --}}
<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">
                Modul Praktikum
                @if($selectedPraktikum) &mdash; <span style="font-weight:400;color:var(--c-fg-muted, #666D80);">{{ $selectedPraktikum->nama }}</span> @endif
            </h2>
        </div>
        <div style="overflow-y:auto; flex:1;">
            @forelse($moduls as $modul)
            <div style="padding:14px 16px; border-bottom:1px solid #F3F4F6; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--c-fg, #0D0D12);margin-bottom:2px;">
                            {{ $modul->urutan }}. {{ $modul->nama }}
                        </div>
                        @if($modul->deskripsi)
                        <div style="font-size:12px;color:var(--c-fg-muted, #666D80);margin-bottom:6px;">{{ $modul->deskripsi }}</div>
                        @endif

                        {{-- Asprak yang sudah ditugaskan --}}
                        @if($modul->modulAsprak->isNotEmpty())
                        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
                            @foreach($modul->modulAsprak as $ma)
                            <span class="mp-badge green sm"><span class="dot"></span>{{ $ma->asprak?->user?->name ?? '—' }}</span>
                            @endforeach
                        </div>
                        @else
                        <div style="font-size:11px;color:var(--c-fg-muted, #808897);margin-top:4px;font-style:italic;">Belum ada asprak ditugaskan</div>
                        @endif
                    </div>

                    {{-- Form tambah asprak ke modul ini --}}
                    @if($aspraks->isNotEmpty())
                    <form method="POST" action="{{ route('eoffice.manprak.admin.bagi-asprak.store') }}"
                          style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
                        @csrf
                        <input type="hidden" name="modul_id" value="{{ $modul->id }}">
                        <select name="asprak_id" class="mp-input mp-select" style="font-size:12px;">
                            @foreach($aspraks as $asprak)
                            <option value="{{ $asprak->id }}">{{ $asprak->user?->name ?? '—' }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="mp-btn primary sm">Tugaskan</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <div style="font-size:13px;font-weight:500;color:var(--c-fg-muted, #666D80);">Belum ada modul untuk praktikum ini.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Daftar Asprak di Praktikum ini --}}
    <div class="mp-card flex-shrink-0" style="width:260px;">
        <div class="mp-card-header">
            <span class="mp-card-title">Asprak Terdaftar</span>
            <span class="mp-badge neutral sm">{{ $aspraks->count() }}</span>
        </div>
        <div style="overflow-y:auto;flex:1;">
            @forelse($aspraks as $asprak)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #DFE1E7;"
                 onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                <div class="mp-av green">{{ strtoupper(substr($asprak->user?->name ?? 'A', 0, 1)) }}{{ strtoupper(substr($asprak->user?->name ?? 'A', strpos(($asprak->user?->name ?? 'A').' ', ' ')+1, 1)) }}</div>
                <div style="min-width:0;flex:1;">
                    <div style="font-size:12px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $asprak->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#666D80;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $asprak->user?->email ?? '—' }}</div>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 8px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                <div style="font-size:12px;color:#808897;">Belum ada asprak.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
