<x-eoffice::manajemen-praktikum.layout pageTitle="Pembagian Modul ke Asisten">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pembagian Modul ke Asisten Praktikum</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Tentukan asisten mana yang mengelola modul mana · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Distribusi Asisten & Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-[14px] flex-1 min-h-0">

    {{-- Panel Kiri: Daftar Asprak --}}
    <div class="mp-card flex-shrink-0" style="width:280px;">
        <div class="mp-card-header">
            <div>
                <span class="mp-card-title">Asisten Praktikum</span>
                <div style="font-size:12px;color:#666D80;margin-top:2px;">{{ $asistenList->count() }} asprak terdaftar</div>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div style="padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                <div class="flex items-center gap-[10px]">
                    <div class="mp-av green">{{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}</div>
                    <div class="min-w-0">
                        <div style="font-size:12px;font-weight:600;color:#0D0D12;" class="truncate">{{ $a->user?->name ?? '—' }}</div>
                        <div style="font-size:10px;color:#666D80;">{{ $a->modulAsprak->count() }} modul</div>
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
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asprak.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Panel Kanan --}}
    <div class="flex flex-col gap-[14px] flex-1 min-w-0 overflow-y-auto">

        {{-- Form Assign --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Assign Asprak ke Modul</div>
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
                <div class="right">
                    <span class="mp-badge neutral sm">{{ count($distribusiList ?? []) }} entri</span>
                </div>
            </div>
            <div style="flex-shrink:0;">
                <table class="mp-table" style="width:100%;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:200px;">Asisten Praktikum</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distribusiList ?? [] as $d)
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:12px 16px;">
                                <div style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $d->modul?->nama ?? '—' }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div class="flex items-center gap-2">
                                    <div class="mp-av green" style="width:24px;height:24px;font-size:9px;">{{ strtoupper(substr($d->asprak?->user?->name ?? 'A', 0, 2)) }}</div>
                                    <span style="font-size:12px;color:#353849;">{{ $d->asprak?->user?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.destroy', $d->id) }}"
                                      onsubmit="return confirm('Hapus distribusi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mp-btn destructive sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div style="padding:48px;text-align:center;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada distribusi modul.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
