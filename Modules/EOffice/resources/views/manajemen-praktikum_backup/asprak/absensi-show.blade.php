<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi &amp; Nilai">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi &amp; Nilai — {{ $modul->nama }}</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">{{ $modul->praktikum?->nama }} · {{ $praktikans->count() }} praktikan</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.absensi.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Pilih Modul --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-2 flex-wrap flex-shrink-0">
    @foreach($moduls as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="{{ $m->id === $modul->id ? 'mp-btn primary md' : 'mp-btn secondary md' }}"
       style="text-decoration:none;">{{ $m->nama }}</a>
    @endforeach
</div>

@php
    $shiftRowspan = [];
    $kelompokRowspan = [];
    
    $items = $praktikans->all();
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

{{-- Flash Messages --}}
@if(session('success'))
<div class="mp-flash mp-flash-success flex-shrink-0" style="margin-top:24px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="mp-card flex-shrink-0" style="margin-top:24px;margin-bottom:24px;padding:14px 20px;background:#EFF6FF;border-left:3px solid #3B82F6;">
    <p style="font-size:12px;color:#1E3A8A;margin:0;line-height:1.6;">
        <strong>Informasi:</strong> Kehadiran dan Nilai kini digabung dalam satu tabel untuk mempermudah monitoring. 
        Kolom Nilai (Tugas Pendahuluan, Praktikum, Laporan, Responsi) akan otomatis aktif jika mahasiswa ditandai <strong>Hadir</strong>. 
        Nilai yang diinput di sini akan tersinkronisasi dua arah dengan form tugas di menu <em>Lihat Pengumpulan</em>.
    </p>
</div>

{{-- Single Form for Absensi & Nilai --}}
<form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" class="mp-card flex-shrink-0">
    @csrf
    <div class="mp-card-header" style="position:sticky;left:0;">
        <div style="display:flex;align-items:center;gap:12px;">
            <label style="font-size:12px;font-weight:600;color:#353849;">Tanggal Praktikum</label>
            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="mp-input">
        </div>
        <div class="right">
            <button class="mp-btn primary md">Simpan Semua (Absensi &amp; Nilai)</button>
        </div>
    </div>

    <div style="overflow-x:auto; padding-bottom: 24px;">
        <table class="mp-table" style="min-width: 1400px;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;width:40px;position:sticky;left:0;background:#F9FAFB;z-index:2;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;min-width:200px;position:sticky;left:40px;background:#F9FAFB;z-index:2;border-right:1px solid #DFE1E7;">Mahasiswa</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:90px;">Klp</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:70px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;">Hadir</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;">Izin</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;border-right:1px solid #DFE1E7;">Alpha</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#EEF2FF;">Pendahuluan</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#F0FDF4;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#FEF9C3;">Laporan</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#FFF7ED;">Responsi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;min-width:180px;border-left:1px solid #DFE1E7;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($praktikans as $idx => $p)
                @php
                    $row      = $absensi[$p->id] ?? null;
                    $status   = $row?->status ?? 'hadir';
                    
                    $njMap    = $nilaiJenis[$p->id] ?? collect();
                    $nilaiTP  = $njMap['tugas_pendahuluan']->nilai ?? null;
                    $nilaiPrak = $njMap['praktikum']->nilai ?? null;
                    $nilaiLap = $njMap['laporan']->nilai ?? null;
                    $nilaiResp = $njMap['responsi']->nilai ?? null;
                @endphp
                
                {{-- Alpine scope to handle disabled states reactively --}}
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;" x-data="{ status: '{{ $status }}' }">
                    <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;position:sticky;left:0;background:#FFF;z-index:1;">
                        {{ $idx + 1 }}
                    </td>
                    <td style="padding:12px 16px;position:sticky;left:40px;background:#FFF;z-index:1;border-right:1px solid #DFE1E7;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="mp-av yellow" style="flex-shrink:0;">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                            <div style="min-width:0;">
                                <div style="font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->user?->name ?? '-' }}</div>
                                <div style="font-size:11px;color:#666D80;font-family:monospace;letter-spacing:0.02em;">{{ $p->user?->student?->student_number ?? '-' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                    @if($kelompokRowspan[$idx] > 0)
                        <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            @if($p->kelompok) {{ $p->kelompok }} @else <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span> @endif
                        </td>
                    @endif
                    
                    {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                    @if($shiftRowspan[$idx] > 0)
                        <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            @if($p->shift) {{ $p->shift }} @else <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span> @endif
                        </td>
                    @endif

                    {{-- Kehadiran Radios --}}
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
                        <input type="radio" name="absensi[{{ $p->id }}][status]" value="hadir" x-model="status" style="accent-color:#10B981;cursor:pointer;width:18px;height:18px;">
                    </td>
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
                        <input type="radio" name="absensi[{{ $p->id }}][status]" value="izin" x-model="status" style="accent-color:#3B82F6;cursor:pointer;width:18px;height:18px;">
                    </td>
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;">
                        <input type="radio" name="absensi[{{ $p->id }}][status]" value="tidak_hadir" x-model="status" style="accent-color:#EF4444;cursor:pointer;width:18px;height:18px;">
                    </td>

                    {{-- Tugas Pendahuluan --}}
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#EEF2FF;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[{{ $p->id }}][tugas_pendahuluan]"
                               value="{{ $nilaiTP !== null ? $nilaiTP : '' }}"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#4338CA;border-color:#C7D2FE;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>

                    {{-- Praktikum --}}
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#F0FDF4;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[{{ $p->id }}][praktikum]"
                               value="{{ $nilaiPrak !== null ? $nilaiPrak : '' }}"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#15803D;border-color:#BBF7D0;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>
                    
                    {{-- Laporan --}}
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#FEF9C3;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[{{ $p->id }}][laporan]"
                               value="{{ $nilaiLap !== null ? $nilaiLap : '' }}"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#A16207;border-color:#FEF08A;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>

                    {{-- Responsi --}}
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#FFF7ED;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[{{ $p->id }}][responsi]"
                               value="{{ $nilaiResp !== null ? $nilaiResp : '' }}"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#C2410C;border-color:#FED7AA;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>
                    
                    {{-- Keterangan --}}
                    <td style="padding:12px 16px;vertical-align:middle;border-left:1px solid #DFE1E7;">
                        <input name="absensi[{{ $p->id }}][keterangan]" value="{{ $row?->keterangan }}"
                               class="mp-input w-full" style="font-size:12px;padding:6px 8px;min-width:140px;" placeholder="Opsional (Sakit, dsb)">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>
                            </svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</form>

</x-eoffice::manajemen-praktikum.layout>
