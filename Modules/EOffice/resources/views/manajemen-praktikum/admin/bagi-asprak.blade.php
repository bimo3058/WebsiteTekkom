<x-eoffice::manajemen-praktikum.layout pageTitle="Bagi Asprak ke Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Bagi Asprak ke Modul</h1>
        <p class="mp-page-sub">Tugaskan asisten praktikum ke modul-modul yang tersedia</p>
    </div>
</div>

{{-- Pilih Praktikum --}}
<div class="flex-shrink-0">
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

@if($praktikums->isEmpty())
<div class="flex-1 flex items-center justify-center" style="font-size:14px;color:var(--c-fg-muted);">
    Tidak ada praktikum aktif saat ini.
</div>
@else

<div class="flex gap-4 flex-1 min-h-0">

    {{-- Daftar Modul --}}
    <div class="mp-card flex-1 min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">
                Modul Praktikum
                @if($selectedPraktikum) — <span style="font-weight:400;">{{ $selectedPraktikum->nama }}</span> @endif
            </span>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($moduls as $modul)
            <div style="padding:16px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-fg);">
                            {{ $modul->urutan }}. {{ $modul->nama }}
                        </div>
                        @if($modul->deskripsi)
                        <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $modul->deskripsi }}</div>
                        @endif

                        {{-- Asprak yang sudah ditugaskan --}}
                        @if($modul->modulAsprak->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($modul->modulAsprak as $ma)
                            <span class="mp-badge sky sm">{{ $ma->asprak?->user?->name ?? '—' }}</span>
                            @endforeach
                        </div>
                        @else
                        <div style="font-size:11px;color:var(--c-fg-placeholder);margin-top:4px;font-style:italic;">Belum ada asprak ditugaskan</div>
                        @endif
                    </div>

                    {{-- Form tambah asprak ke modul ini --}}
                    @if($aspraks->isNotEmpty())
                    <form method="POST" action="{{ route('eoffice.manprak.admin.bagi-asprak.store') }}"
                          class="flex gap-2 items-center flex-shrink-0">
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
            <div style="padding:56px;text-align:center;font-size:13px;color:var(--c-fg-muted);">
                Belum ada modul untuk praktikum ini.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Daftar Asprak di Praktikum ini --}}
    <div class="mp-card flex-shrink-0" style="width:260px;">
        <div class="mp-card-header">
            <span class="mp-card-title">Asprak Terdaftar</span>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($aspraks as $asprak)
            <div class="flex items-center gap-3" style="padding:12px 16px;border-bottom:1px solid var(--c-border-light);">
                <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                    {{ strtoupper(substr($asprak->user?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $asprak->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $asprak->user?->email ?? '—' }}</div>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Belum ada asprak.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
