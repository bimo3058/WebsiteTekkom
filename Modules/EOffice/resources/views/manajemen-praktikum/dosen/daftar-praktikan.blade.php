<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Daftar Praktikan">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Daftar Praktikan</h1>
            <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Lihat daftar mahasiswa peserta praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Info --}}
<div class="mp-alert info flex-shrink-0">
    Halaman ini bersifat <strong>read-only</strong>. Penambahan data praktikan dilakukan oleh Admin.
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
            @php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        $label .= " · {$p->semester} {$p->tahun_ajaran}";
                        $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $label];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="240px"
            />
        </form>
        @endif
    </div>
</div>

@if($praktikum)

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikan — {{ $praktikum->nama }}</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $praktikans->total() }} praktikan</span>
</div>

<x-eoffice::manajemen-praktikum.ui.practicum-schedule :praktikum="$praktikum" role="dosen" />
<div class="mp-card" style="overflow:visible">
    <x-eoffice::manajemen-praktikum.ui.participants-header :search="$search ?? ''" :praktikum="$praktikum" />
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

        <div style="flex:1; overflow:auto; min-height:0;">
            <table class="mp-table" style="min-width:700px; margin:0;">
            <thead>
                <tr style="background:#F9FAFB;">
<th class="mp-th text-left" style="padding:10px 20px;width:40px;">NO</th>
<th class="mp-th text-left" style="padding:10px 16px;">NAMA MAHASISWA</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
<th class="mp-th text-center" style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">KELOMPOK</th>
<th class="mp-th text-center" style="padding:10px 16px;width:120px;border-right:1px solid #DFE1E7;">SHIFT</th>
<th class="mp-th text-left" style="padding:10px 16px;width:100px;">STATUS</th>
<th class="mp-th text-left" style="padding:10px 16px;width:130px;">TANGGAL MASUK</th>
                </tr>
            </thead>
            <tbody>
                @forelse($praktikans as $idx => $p)
                @php
                    $nameParts = explode(' ', $p->user?->name ?? 'PR');
                    $initials  = strtoupper(substr($nameParts[0] ?? 'P', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'R', 0, 1));
                    $avColors  = ['sky','navy','green','yellow','violet'];
                    $avColor   = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;color:#808897;font-size:12px;">{{ $praktikans->firstItem() + $idx }}</td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;font-weight:600;color:#353849;">
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
                        <span class="mp-badge success sm"><span class="dot"></span>{{ ucfirst($p->status ?? 'aktif') }}</span>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                        {{ $p->created_at?->locale('id')->isoFormat('D MMM YYYY') ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                             stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/>
                        </svg>
                        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Tidak Ada Praktikan</div>
                        <div style="font-size:12px;color:#666D80;">
                            @if($search)
                            Praktikan dengan kata kunci "{{ $search }}" tidak ditemukan.
                            @else
                            Belum ada praktikan terdaftar pada praktikum ini.
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    @if(isset($praktikans) && $praktikans instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$praktikans" />
    @endif
</div>

@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>
