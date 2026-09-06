@php
    $pageTitle = ($praktikum->nama ?? 'Praktikum') . ' / Daftar Praktikan';
@endphp

<x-eoffice::manajemen-praktikum.layout :pageTitle="$pageTitle">

    @if(!$asprak)
        <div class="mp-alert warning flex-shrink-0">Status asisten praktikum Anda belum aktif. Hubungi koordinator untuk
            aktivasi.</div>
    @else

        {{-- Asprak Header Banner --}}
        @if(isset($praktikum) && $praktikum)
            <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="praktikan" />
        @endif

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}"
            style="display:flex;gap:8px;flex-shrink:0;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email praktikan..."
                class="mp-input" style="flex:1;">
            <button type="submit" class="mp-btn primary md">Cari</button>
            @if($search)
                <a href="{{ route('eoffice.manprak.asprak.daftar-praktikan.index') }}" class="mp-btn secondary md"
                    style="text-decoration:none;">Reset</a>
            @endif
        </form>

        {{-- Tabel --}}
        <div class="mp-card">
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
                            <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                            <th class="mp-th text-center"
                                style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">
                                Kelompok</th>
                            <th class="mp-th text-center" style="padding:10px 16px;width:120px;">Shift</th>
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
            @if($praktikans->total() > 0)
                <div
                    style="display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;">
                    {{-- Left: Per halaman dropdown + info teks --}}
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <div x-data="{ openP: false, options: [10, 20, 30], perPage: {{ $praktikans->perPage() }} }"
                            class="relative" @click.away="openP = false">
                            <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                :class="openP ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                @click="openP = !openP">
                                <span class="text-[12px] text-[#666D80]" :class="openP ? 'text-[#0B266E]' : ''">Per
                                    halaman</span>
                                <div class="flex items-center gap-1 font-semibold text-[12px]">
                                    <span x-text="perPage"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                        :class="{'rotate-180': openP, 'text-[#0B266E]': openP, 'text-[#666D80]': !openP}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="openP" @click.away="openP = false" style="display: none;"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                <template x-for="option in options" :key="option">
                                    <a :href="`?${new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), per_page: option, page: 1}).toString()}`"
                                        class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                        :class="perPage == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <span x-text="option"></span>
                                        <svg x-show="perPage == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <div style="font-size:12px; color:#666D80;">
                            Menampilkan {{ $praktikans->firstItem() }} sampai {{ $praktikans->lastItem() }} dari
                            {{ $praktikans->total() }} data
                        </div>
                    </div>

                    {{-- Right: standard pagination buttons --}}
                    <div style="display:flex; align-items:center; gap:8px;">
                        @if ($praktikans->onFirstPage())
                            <span
                                style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#F8FAFC; border:1px solid #DFE1E7; color:#A4ABB8; cursor:not-allowed;">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $praktikans->previousPageUrl() }}"
                                style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:white; border:1px solid #DFE1E7; color:#353849; cursor:pointer; text-decoration:none;"
                                class="hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        @endif

                        <span
                            style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#0B266E; color:white; font-size:13px; font-weight:600;">
                            {{ $praktikans->currentPage() }}
                        </span>

                        @if ($praktikans->hasMorePages())
                            <a href="{{ $praktikans->nextPageUrl() }}"
                                style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:white; border:1px solid #DFE1E7; color:#353849; cursor:pointer; text-decoration:none;"
                                class="hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        @else
                            <span
                                style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#F8FAFC; border:1px solid #DFE1E7; color:#A4ABB8; cursor:not-allowed;">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>