<x-eoffice::manajemen-praktikum.layout pageTitle="Data Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Data Praktikan</h1>
            <span class="mp-badge" style="background:#D0D6E9;color:#5D6DA2;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#5D6DA2;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else

@if ($errors->any())
<div class="mp-flash mp-flash-error" style="border-radius:10px;border:1px solid #DF1C41;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div style="flex:1;">
        <div style="font-weight:700;">Gagal mengimpor:</div>
        <ul style="margin:4px 0 0;padding-left:20px;list-style-type:disc;">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

{{-- ──────────────────────────────────────────────────── --}}
{{-- SECTION 1: Import Kelompok & Shift                   --}}
{{-- ──────────────────────────────────────────────────── --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Import Kelompok & Shift</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-start;">
        {{-- Kiri: penjelasan alur --}}
        <div style="flex:1;min-width:220px;">
            <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:8px;">Cara Import Kelompok & Shift</div>
            <ol style="font-size:12px;color:#666D80;line-height:1.8;padding-left:18px;margin:0;">
                <li>Export data praktikan terdaftar ke CSV <span style="color:#0B266E;font-weight:600;">(tombol biru di bawah)</span></li>
                <li>Buka file CSV di Excel, isi kolom <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">Kelompok</code> dan <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">Shift</code></li>
                <li>Simpan kembali sebagai CSV, lalu upload di sini</li>
            </ol>
            <div style="margin-top:10px;font-size:11px;color:#A4ABB8;">
                Format kolom import: <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">nim_atau_email, kelompok, shift</code>
                &nbsp;·&nbsp;
                <a href="{{ route('eoffice.manprak.koor.praktikan.template') }}" style="color:#0B266E;font-weight:600;font-size:11px;display:inline-flex;align-items:center;gap:3px;text-decoration:none;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Template Kosong
                </a>
            </div>
        </div>
        {{-- Kanan: form upload --}}
        <div style="flex:1;min-width:240px;">
            <form method="POST" action="{{ route('eoffice.manprak.koor.praktikan.import') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File CSV (nim_atau_email, kelompok, shift)</label>
                <input type="file" name="file" accept=".csv,.txt" class="mp-input" style="margin-bottom:10px;">
                <button class="mp-btn primary md" style="width:100%;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                    Import Kelompok & Shift
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ──────────────────────────────────────────────────── --}}
{{-- SECTION 2: Tabel Praktikan Terdaftar                --}}
{{-- ──────────────────────────────────────────────────── --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikan Terdaftar</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">
            Praktikan Terdaftar
            <span class="mp-badge neutral sm" style="margin-left:6px;">{{ $praktikans->total() }}</span>
        </span>
        <div class="right" style="gap:8px;display:flex;align-items:center;flex-wrap:wrap;">
            {{-- Export CSV --}}
            <a href="{{ route('eoffice.manprak.koor.praktikan.export') }}"
               class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
            {{-- Search --}}
            <form method="GET" style="display:flex;gap:4px;">
                <input name="search" value="{{ $search }}" placeholder="Cari nama / NIM..." class="mp-input" style="width:180px;">
                <button type="submit" class="mp-btn primary sm">Cari</button>
            </form>
        </div>
    </div>

    <div style="overflow-x:auto;">
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

        <table class="mp-table" style="min-width:700px;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;width:40px;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:100px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:130px;">Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
            @forelse($praktikans as $idx => $p)
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;">
                        {{ $praktikans->firstItem() + $idx }}
                    </td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-[10px]">
                            <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '-' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                        {{ $p->user?->student?->student_number ?? '-' }}
                    </td>
                    
                    {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                    @if($kelompokRowspan[$idx] > 0)
                        <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            @if($p->kelompok)
                                {{ $p->kelompok }}
                            @else
                                <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                            @endif
                        </td>
                    @endif
                    
                    {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                    @if($shiftRowspan[$idx] > 0)
                        <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            @if($p->shift)
                                {{ $p->shift }}
                            @else
                                <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                            @endif
                        </td>
                    @endif

                    <td style="padding:12px 16px;">
                        <span class="mp-badge success sm"><span class="dot"></span>{{ $p->status ?? 'terdaftar' }}</span>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                        {{ $p->created_at?->format('d M Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan yang terdaftar.</div>
                            <div style="font-size:11px;color:#A4ABB8;margin-top:4px;">Praktikan akan muncul di sini setelah Anda menyetujui pendaftaran mereka.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($praktikans->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $praktikans->links() }}</div>
    @endif
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
