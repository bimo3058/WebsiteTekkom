<x-eoffice::manajemen-praktikum.layout pageTitle="Pembagian Modul ke Asisten">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pembagian Modul ke Asisten Praktikum</h1>
        <p class="mp-page-sub">Tentukan asisten mana yang mengelola modul mana</p>
    </div>
</div>

<div class="flex gap-[14px] flex-1 min-h-0">

    {{-- Panel Kiri: Daftar Asprak --}}
    <div class="mp-card flex-shrink-0" style="width:280px;">
        <div class="mp-card-header">
            <div>
                <span class="mp-card-title">Asisten Praktikum</span>
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $asistenList->count() }} asprak terdaftar</div>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div style="padding:10px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-center gap-[10px]">
                    <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1a6691,#40C4AA);">
                        {{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $a->user?->name ?? '—' }}</div>
                        <div style="font-size:10px;color:var(--c-fg-muted);">{{ $a->modulAsprak->count() }} modul</div>
                    </div>
                </div>
                @if($a->modulAsprak->isNotEmpty())
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($a->modulAsprak as $ma)
                    <span class="mp-badge sky sm">{{ $ma->modul?->nama }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Belum ada asprak.</div>
            @endforelse
        </div>
    </div>

    {{-- Panel Kanan --}}
    <div class="flex flex-col gap-[14px] flex-1 min-w-0 overflow-y-auto">

        {{-- Form Assign --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div style="font-weight:700;font-size:14px;color:var(--c-fg);margin-bottom:16px;">Assign Asprak ke Modul</div>
            <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <select name="asprak_id" required class="mp-input mp-select w-full">
                            <option value="">— Pilih Asprak —</option>
                            @foreach($asistenList ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->user?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <select name="modul_id" required class="mp-input mp-select w-full">
                            <option value="">— Pilih Modul —</option>
                            @foreach($modulList ?? [] as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="mp-btn primary md">Assign Modul</button>
            </form>
        </div>

        {{-- Tabel Distribusi Saat Ini --}}
        <div class="mp-card flex-1 min-h-0">
            <div class="mp-card-header" style="flex-shrink:0;">
                <span class="mp-card-title">Distribusi Saat Ini</span>
            </div>
            <div class="mp-card-body" style="flex-shrink:0;">
                <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                    <div class="mp-th flex-1">Modul</div>
                    <div class="mp-th" style="width:180px;">Asisten Praktikum</div>
                    <div class="mp-th" style="width:80px;">Aksi</div>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($distribusiList ?? [] as $d)
                <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
                    <div class="flex-1">
                        <div style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $d->modul?->nama ?? '—' }}</div>
                    </div>
                    <div style="width:180px;font-size:12px;color:var(--c-fg-sub);">{{ $d->asprak?->user?->name ?? '—' }}</div>
                    <div style="width:80px;">
                        <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.destroy', $d->id) }}"
                              onsubmit="return confirm('Hapus distribusi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mp-btn destructive sm">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada distribusi modul.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
