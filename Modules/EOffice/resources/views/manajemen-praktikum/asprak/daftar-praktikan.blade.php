@php
    $pageTitle = ($praktikum->nama ?? 'Praktikum') . ' / Daftar Praktikan';
@endphp

<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" :pageTitle="$pageTitle">

    @if(!$asprak)
        <div class="mp-alert warning flex-shrink-0">Status asisten praktikum Anda belum aktif. Hubungi koordinator untuk
            aktivasi.</div>
    @else

        {{-- Asprak Header Banner --}}
        @if(isset($praktikum) && $praktikum)
            <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="praktikan" />
        @endif

<x-eoffice::manajemen-praktikum.ui.practicum-schedule :praktikum="$praktikum" role="asprak" />
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

            <div class="overflow-x-auto">
                <table class="mp-table" style="min-width:800px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="mp-th text-left" style="padding:10px 20px;width:50px;">NO</th>
<th class="mp-th text-left" style="padding:10px 16px;">NAMA MAHASISWA</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
<th class="mp-th text-center"
                                style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">KELOMPOK</th>
<th class="mp-th text-center" style="padding:10px 16px;width:120px;">SHIFT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($praktikans as $idx => $dp)
                            @php
                                $pct = $absensiMap[$dp->id] ?? null;
                                $pctColor = is_null($pct) ? '#666D80' : ($pct >= 75 ? '#0B266E' : '#DF1C41');
                            @endphp
                            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                                <td style="padding:12px 20px;color:#808897;font-size:12px;">
                                    {{ $praktikans->firstItem() + $idx }}
                                </td>
                                <td style="padding:12px 16px;">
                                    <div class="flex items-center gap-3">
                                        <div class="mp-av yellow">{{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:#0D0D12;">{{ $dp->user?->name ?? '—' }}</div>
                                            <div style="font-size:11px;color:#666D80;">{{ $dp->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:12px 16px;font-size:12px;font-weight:600;color:#353849;">
                                    {{ $dp->user?->student?->student_number ?? $dp->user?->student_number ?? '-' }}
                                </td>

                                {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                                @if($kelompokRowspan[$idx] > 0)
                                    <td rowspan="{{ $kelompokRowspan[$idx] }}"
                                        style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        @if($dp->kelompok)
                                            {{ $dp->kelompok }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                                @if($shiftRowspan[$idx] > 0)
                                    <td rowspan="{{ $shiftRowspan[$idx] }}"
                                        style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        @if($dp->shift)
                                            {{ $dp->shift }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:48px;text-align:center;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8"
                                        stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
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
            @if(isset($praktikans) && $praktikans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$praktikans" :options="[10, 20, 30]" />
            @endif
        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>
