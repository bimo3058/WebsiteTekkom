<x-eoffice::manajemen-praktikum.layout pageTitle="Asisten Praktikum">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Asisten Praktikum</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Daftar asisten praktikum aktif pada praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
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

{{-- Tabel Asprak --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Asprak — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm">{{ $aspraks->count() }} asprak</span>
</div>

@if($aspraks->isEmpty())
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/>
            <path d="M23 11l-3.5 3.5-1.5-1.5"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Asisten Praktikum</div>
        <div style="font-size:12px;color:#666D80;">Belum ada asprak yang bertugas pada praktikum ini.</div>
    </div>
</div>
@else
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Asisten Praktikum</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Nama</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Email</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Modul Diampu</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aspraks as $i => $a)
                @php
                    $nameParts  = explode(' ', $a->user?->name ?? 'AS');
                    $initials   = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
                    $avColors   = ['sky','navy','green','yellow','violet'];
                    $avColor    = $avColors[crc32($a->user?->email ?? '') % count($avColors)];
                    $modulDiampu = $a->modulAsprak->map(fn($ma) => $ma->modul)->filter()->sortBy('urutan');
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;color:#808897;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $a->user?->name ?? '—' }}</div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#666D80;font-size:12px;">{{ $a->user?->email ?? '—' }}</td>
                    <td style="padding:12px 16px;">
                        @if($modulDiampu->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($modulDiampu as $m)
                                <span class="mp-badge sky sm" style="font-size:11px;">
                                    @if($m->urutan) M{{ $m->urutan }} · @endif{{ $m->nama }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size:12px;color:#A4ABB8;">Belum ada modul</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:12px;color:#666D80;">
                        {{ $a->created_at?->locale('id')->isoFormat('D MMM YYYY') ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>
