<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan — Asisten Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Praktikan</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
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
        @php
            $shiftRowspan = [];
            $kelompokRowspan = [];
            
            $items = $praktikans->items();
            $totalItems = count($items);

            // Calculate rowspan for shift
            $i = 0;
            while ($i < $totalItems) {
                $val = $items[$i]->shift;
                if (empty($val)) {
                    $shiftRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                    $count++;
                }
                $shiftRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $shiftRowspan[$i + $j] = 0;
                }
                $i += $count;
            }

            // Calculate rowspan for kelompok (must match same kelompok AND same shift)
            $i = 0;
            while ($i < $totalItems) {
                $valK = $items[$i]->kelompok;
                $valS = $items[$i]->shift;
                if (empty($valK)) {
                    $kelompokRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while (
                    $i + $count < $totalItems && 
                    $items[$i + $count]->kelompok === $valK && 
                    $items[$i + $count]->shift === $valS
                ) {
                    $count++;
                }
                $kelompokRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $kelompokRowspan[$i + $j] = 0;
                }
                $i += $count;
            }
        @endphp

        <div class="overflow-x-auto">
            <table class="mp-table" style="min-width:800px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 20px;width:40px;">#</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-right:1px solid #DFE1E7;">Shift</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:120px;">Kehadiran</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($praktikans as $idx => $dp)
                    @php
                        $pct = $absensiMap[$dp->id] ?? null;
                        $pctColor = is_null($pct) ? '#666D80' : ($pct >= 75 ? '#0B266E' : '#DF1C41');
                    @endphp
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                        <td style="padding:12px 20px;color:#808897;font-size:12px;">{{ $praktikans->firstItem() + $idx }}</td>
                        <td style="padding:12px 16px;">
                            <div class="flex items-center gap-3">
                                <div class="mp-av yellow">{{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}</div>
                                <div>
                                    <div style="font-weight:600;color:#0D0D12;">{{ $dp->user?->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:#666D80;">{{ $dp->user?->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                            {{ $dp->user?->student?->student_number ?? $dp->user?->student_number ?? '-' }}
                        </td>

                        {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                        @if($kelompokRowspan[$idx] > 0)
                            <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                @if($dp->kelompok)
                                    {{ $dp->kelompok }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif
                        
                        {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                        @if($shiftRowspan[$idx] > 0)
                            <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                @if($dp->shift)
                                    {{ $dp->shift }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                @endif
                            </td>
                        @endif

                        {{-- Kehadiran --}}
                        <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
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
                        </td>

                        <td style="padding:12px 16px;">
                            <span class="mp-badge success sm"><span class="dot"></span>Terdaftar</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:48px;text-align:center;">
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
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @if($praktikans->hasPages())
    <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;">
        {{ $praktikans->links() }}
    </div>
    @endif
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
