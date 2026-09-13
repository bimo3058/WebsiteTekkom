<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama . ' / Anggota' : 'Anggota' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)
    <div style="padding: 48px; text-align: center; color: #808897;">Praktikum tidak ditemukan.</div>
@else

{{-- Sticky Header Wrapper --}}
    <div x-data="{ st: 0 }"
         x-init="
            const box = document.querySelector('.mp-box-body');
            if (box) {
                let ticking = false;
                box.addEventListener('scroll', () => {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            st = box.scrollTop;
                            ticking = false;
                        });
                        ticking = true;
                    }
                });
            }
         "
         class="sticky z-20 bg-white" style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border);">
         
        {{-- Banner / Cover Image Container --}}
        <div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">
            
            {{-- Cover Placeholder or Image --}}
            <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                @if($praktikum->cover_path)
                    <img src="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($praktikum->cover_path, 'eoffice') }}"
                         class="w-full h-full object-cover"
                         :style="`transform: scale(${Math.max(1, 1 + (st / 200) * 0.1)}); opacity: ${Math.max(0.3, 1 - (st / 300))}; filter: blur(${Math.min(8, st / 15)}px);`"
                         alt="Cover Praktikum">
                @else
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896" 
                         :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                @endif
            </div>

            {{-- 1. Base Gradient --}}
            <div class="absolute inset-0 pointer-events-none"
                 style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 30%, rgba(0,0,0,0) 70%);">
            </div>

            {{-- 2. Scrolled Overlay (Gelap + Blur) --}}
            <div class="absolute inset-0 pointer-events-none"
                 :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
            </div>

            {{-- Title --}}
            <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
                <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                    :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                    {{ $praktikum->nama }}
                </h1>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display: flex; gap: 8px;">
            
            <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
            <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
            <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi & Nilai</a>
            <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Anggota</a>
            <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi Koordinator</a>
        </div>
    </div>

    

<div style="display: flex; flex-direction: column; gap: 16px; padding-top: 5px;">



<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Asisten</span>
    </div>
    
    @if($aspraks->isEmpty())
    <div class="flex items-center justify-center" style="min-height:200px; padding:48px; text-align:center;">
        <div>
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                 stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/>
                <path d="M23 11l-3.5 3.5-1.5-1.5"/>
            </svg>
            <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Asisten Praktikum</div>
            <div style="font-size:12px;color:#666D80;">Belum ada asisten praktikum yang bertugas pada praktikum ini.</div>
        </div>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;width:40px;">NO</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">NAMA MAHASISWA</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:150px;">Peran</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aspraks as $i => $a)
                @php
                    $nameParts  = explode(' ', $a->user?->name ?? 'AS');
                    $initials   = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
                    $avColors   = ['sky','navy','green','yellow','violet'];
                    $avColor    = $avColors[crc32($a->user?->email ?? '') % count($avColors)];
                    $allModulAsprak = $a->merged_modul_asprak ?? $a->modulAsprak;
                    $modulDiampu = $allModulAsprak->map(fn($ma) => $ma->modul)->filter()->sortBy('urutan')->unique('id');
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;color:#808897;font-size:12px;">{{ $aspraks->firstItem() + $i }}</td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $a->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $a->user?->email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;font-weight:600;color:#353849;">
                        {{ $a->user?->student?->student_number ?? '-' }}
                    </td>
                    <td style="padding:12px 16px;">
                        @if($a->role === 'koor' || $a->role === 'koordinator')
                            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;font-weight:600;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
                        @else
                            <span class="mp-badge neutral sm" style="font-weight:500;">Asisten</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($modulDiampu->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($modulDiampu as $m)
                                <span class="mp-badge sm" style="background:#F3F4F6;color:#353849;border:1px solid #E5E7EB;">
                                    @if($m->urutan) Modul {{ $m->urutan }} @endif{{ $m->nama }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span style="font-size:12px;color:#A4ABB8;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($aspraks instanceof \Illuminate\Pagination\LengthAwarePaginator && $aspraks->total() > 0)
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div x-data="{ openA: false, options: [5, 10, 15], perPage: {{ $aspraks->perPage() }} }"
                    class="relative" @click.away="openA = false">
                    <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors"
                        :class="openA ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                        @click="openA = !openA">
                        <span class="text-[12px] text-[#666D80]" :class="openA ? 'text-[#0B266E]' : ''">Per halaman</span>
                        <div class="flex items-center gap-1 font-semibold text-[12px]">
                            <span x-text="perPage"></span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                :class="{'rotate-180': openA, 'text-[#0B266E]': openA, 'text-[#666D80]': !openA}"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div x-show="openA" style="display:none;"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                        <template x-for="option in options" :key="option">
                            <a :href="`?${new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), per_page_asisten: option, page_asisten: 1}).toString()}`"
                                class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                :class="perPage == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                <span x-text="option"></span>
                                <svg x-show="perPage == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>
                <div style="font-size:12px; color:#666D80;">
                    Menampilkan {{ $aspraks->firstItem() }} sampai {{ $aspraks->lastItem() }} dari {{ $aspraks->total() }} data
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                @if($aspraks->onFirstPage())
                    <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#F8FAFC;border:1px solid #DFE1E7;color:#A4ABB8;cursor:not-allowed;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </span>
                @else
                    <a href="{{ $aspraks->previousPageUrl() }}" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:white;border:1px solid #DFE1E7;color:#353849;text-decoration:none;" class="hover:bg-gray-50">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                @endif
                <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#0B266E;color:white;font-size:13px;font-weight:600;">
                    {{ $aspraks->currentPage() }}
                </span>
                @if($aspraks->hasMorePages())
                    <a href="{{ $aspraks->nextPageUrl() }}" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:white;border:1px solid #DFE1E7;color:#353849;text-decoration:none;" class="hover:bg-gray-50">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                @else
                    <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#F8FAFC;border:1px solid #DFE1E7;color:#A4ABB8;cursor:not-allowed;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                @endif
            </div>
        </div>
    @endif
    @endif
</div>

{{-- Tabel --}}
<div class="mp-card flex-shrink-0" style="display:flex;flex-direction:column;">
    <div class="mp-card-header" style="flex-shrink:0;">
        <span class="mp-card-title">Daftar Praktikan</span>
    </div>
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

        <div style="overflow-x:auto;">
            <table class="mp-table" style="min-width:700px; margin:0;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 20px;width:40px;">NO</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">NAMA MAHASISWA</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-right:1px solid #DFE1E7;">Shift</th>
                    
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


                    
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:48px;text-align:center;">
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
        @if($praktikans instanceof \Illuminate\Pagination\LengthAwarePaginator && $praktikans->total() > 0)
            <div style="display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;">
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <div x-data="{ openP: false, options: [5, 10, 15], perPage: {{ $praktikans->perPage() }} }"
                        class="relative" @click.away="openP = false">
                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors"
                            :class="openP ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                            @click="openP = !openP">
                            <span class="text-[12px] text-[#666D80]" :class="openP ? 'text-[#0B266E]' : ''">Per halaman</span>
                            <div class="flex items-center gap-1 font-semibold text-[12px]">
                                <span x-text="perPage"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="{'rotate-180': openP, 'text-[#0B266E]': openP, 'text-[#666D80]': !openP}"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div x-show="openP" style="display:none;"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                            <template x-for="option in options" :key="option">
                                <a :href="`?${new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), per_page_praktikan: option, page_praktikan: 1}).toString()}`"
                                    class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                    :class="perPage == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <span x-text="option"></span>
                                    <svg x-show="perPage == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>
                    <div style="font-size:12px; color:#666D80;">
                        Menampilkan {{ $praktikans->firstItem() }} sampai {{ $praktikans->lastItem() }} dari {{ $praktikans->total() }} data
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    @if($praktikans->onFirstPage())
                        <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#F8FAFC;border:1px solid #DFE1E7;color:#A4ABB8;cursor:not-allowed;">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $praktikans->previousPageUrl() }}" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:white;border:1px solid #DFE1E7;color:#353849;text-decoration:none;" class="hover:bg-gray-50">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif
                    <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#0B266E;color:white;font-size:13px;font-weight:600;">
                        {{ $praktikans->currentPage() }}
                    </span>
                    @if($praktikans->hasMorePages())
                        <a href="{{ $praktikans->nextPageUrl() }}" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:white;border:1px solid #DFE1E7;color:#353849;text-decoration:none;" class="hover:bg-gray-50">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    @else
                        <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#F8FAFC;border:1px solid #DFE1E7;color:#A4ABB8;cursor:not-allowed;">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
</div>


</div>
@endif {{-- end if praktikum --}}

</x-eoffice::manajemen-praktikum.layout>