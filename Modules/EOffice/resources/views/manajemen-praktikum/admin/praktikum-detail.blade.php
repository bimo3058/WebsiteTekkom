<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Praktikum">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header flex-shrink-0">
    <div class="flex items-center gap-2.5 flex-wrap min-w-0">
        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
           class="flex items-center gap-1.5 text-[13px] font-normal text-[#666D80] hover:text-[#0B266E] transition-colors whitespace-nowrap flex-shrink-0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Daftar Praktikum
        </a>
        <span class="text-[#DFE1E7] text-[16px] font-light">/</span>
        <div class="flex items-center gap-2 min-w-0">
            <h1 class="text-[13px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $praktikum->nama }}</h1>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════
     2-COLUMN LAYOUT
════════════════════════════════════════════ --}}
<div class="flex gap-4 flex-1 min-h-0 pb-0.5" style="align-items: flex-start;">

    {{-- ══ SIDEBAR KIRI ═══════════════════════════════════════════════════ --}}
    <div class="w-[300px] flex-shrink-0 flex flex-col gap-3 pb-1 sticky top-0">

        {{-- Info Praktikum --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Info Praktikum</div>
            </div>
            <div class="p-4 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Status</span>
                    <span class="mp-badge {{ $praktikum->status === 'aktif' ? 'success' : 'neutral' }} sm"><span class="dot"></span>{{ ucfirst($praktikum->status) }}</span>
                </div>
                
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Periode</span>
                    <span class="text-[12px] font-semibold text-[#0D0D12] text-right">Tahun Ajaran {{ $praktikum->tahun_ajaran }} / Semester {{ ucfirst($praktikum->semester) }}</span>
                </div>

                @if($praktikum->matkul)
                <div class="flex items-start justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Mata Kuliah</span>
                    <div class="flex flex-col items-end gap-1 text-right min-w-0">
                        <span class="text-[12px] font-semibold text-[#0D0D12] leading-[1.3]">{{ $praktikum->matkul->nama }}</span>
                    </div>
                </div>
                @endif

                <div class="h-px bg-[#F3F4F6] -mx-4"></div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-medium text-[#666D80]">Dibuat</span>
                    <span class="text-[11px] font-medium text-[#0D0D12]">{{ $praktikum->created_at?->locale('id')->isoFormat('D MMM YYYY') }}</span>
                </div>
            </div>
        </div>

        {{-- Dosen Pengampu --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Dosen Pengampu</div>
            </div>
            <div class="p-4 flex flex-col gap-3">
                @if($praktikum->dosens->count() > 0)
                    @php $avColors = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48']; @endphp
                    @foreach($praktikum->dosens as $d)
                    @php
                        $dp = explode(' ', $d->name);
                        $di = strtoupper(substr($dp[0],0,1).substr($dp[1]??$dp[0],0,1));
                        $avColor = $avColors[crc32($d->email ?? '') % count($avColors)];
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0 text-white" style="background-color: {{ $avColor }};">{{ $di }}</div>
                        <div class="min-w-0">
                            <div class="text-[12px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $d->name }}</div>
                            <div class="text-[10px] text-[#A4ABB8] overflow-hidden text-ellipsis whitespace-nowrap">{{ $d->email }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                <span class="mp-badge error sm">Belum ada dosen pengampu</span>
                @endif
            </div>
        </div>

        {{-- Koordinator --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[16px] overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
            <div class="px-4 py-3 border-b border-[#F3F4F6] bg-[#FAFAFA]">
                <div class="text-[11px] font-bold text-[#0D0D12] uppercase tracking-[0.07em]">Koordinator Praktikum</div>
            </div>
            <div class="p-4">
                @if($praktikum->koordinator)
                @php
                    $k  = $praktikum->koordinator;
                    $kp = explode(' ', $k->name);
                    $ki = strtoupper(substr($kp[0],0,1).substr($kp[1]??$kp[0],0,1));
                    $avColors = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                    $avColorK = $avColors[crc32($k->email ?? '') % count($avColors)];
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-[12px] flex-shrink-0 text-white" style="background-color: {{ $avColorK }};">{{ $ki }}</div>
                    <div class="min-w-0">
                        <div class="text-[12px] font-semibold text-[#0D0D12] overflow-hidden text-ellipsis whitespace-nowrap">{{ $k->name }}</div>
                        <div class="text-[10px] text-[#A4ABB8] overflow-hidden text-ellipsis whitespace-nowrap">{{ $k->email }}</div>
                    </div>
                </div>
                @else
                <span class="mp-badge error sm">Belum ditunjuk</span>
                @endif
            </div>
        </div>

    </div>
    {{-- /Sidebar --}}

    {{-- ══ KONTEN KANAN: Tab Praktikan & Asisten Praktikum ═══════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col h-full min-h-0"
         x-data="{ tab: '{{ request()->has('tab') ? request('tab') : 'praktikan' }}' }">

        {{-- Tab Bar --}}
        <div class="flex items-center gap-3 mb-4 flex-shrink-0 w-fit">
            <button @click="tab='praktikan'"
                    :class="tab==='praktikan' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Praktikan
                <span :class="tab==='praktikan' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-2 py-0.5 rounded-full ml-1">{{ $praktikans->total() }}</span>
            </button>
            <button @click="tab='asprak'"
                    :class="tab==='asprak' ? 'border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]' : 'border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5'"
                    class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                Asisten Praktikum
                <span :class="tab==='asprak' ? 'bg-[#0B266E] text-white' : 'bg-[#DFE1E7] text-[#666D80]'"
                      class="text-[10px] font-extrabold px-2 py-0.5 rounded-full ml-1">{{ $aspraks->count() }}</span>
            </button>
        </div>

        {{-- ── TAB: PRAKTIKAN ───────────────────────────────────────────── --}}
        <div x-show="tab==='praktikan'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>
            
            {{-- Tabel Praktikan --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="px-4 py-3 border-b border-[#DFE1E7] bg-white flex items-center justify-between gap-3 flex-shrink-0" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <form method="GET" id="filter_praktikan_form" class="flex-1 flex gap-2 max-w-[320px]" action="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}">
                        <input type="hidden" name="tab" value="praktikan">
                        <div class="relative flex-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round"
                                 class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                   placeholder="Cari nama atau NIM..."
                                   class="mp-input w-full h-[38px] text-[13px]" style="padding-left: 34px;">
                        </div>
                        @if(!empty($search))
                        <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}" class="mp-btn secondary md px-3">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </a>
                        @endif
                    </form>
                    <div class="flex items-center gap-2">
                        <button class="mp-btn secondary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            Export
                        </button>
                        <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                                class="mp-btn primary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                            Import Praktikan
                        </button>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1">
                    <table class="w-full border-collapse min-w-[500px]">
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] border-b border-[#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Nama Praktikan</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[100px]">Angkatan</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[90px]">Status</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[70px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($praktikans as $i => $p)
                            @php
                                $np  = explode(' ', $p->user?->name ?? 'PR');
                                $ini = strtoupper(substr($np[0]??'P',0,1).substr($np[1]??$np[0]??'R',0,1));
                                $avc = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                                $av  = $avc[crc32($p->user?->email??'')%count($avc)];
                                $st  = $p->user ? \App\Models\Student::where('user_id',$p->user->id)->first() : null;
                                $nim = $st?->student_number ?? '—';
                                $ang = $st?->cohort_year ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-left">{{ $praktikans->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] flex-shrink-0 text-white" style="background-color: {{ $av }};">{{ $ini }}</div>
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-bold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis max-w-[250px]">{{ $p->user?->name ?? '—' }}</div>
                                            <div class="text-[11px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis max-w-[250px]">{{ $p->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#0D0D12]" style="font-family: 'Inter', sans-serif;">{{ $nim }}</td>
                                <td class="px-4 py-3 text-center text-[12px] font-normal text-[#0D0D12]">{{ $ang }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.destroy', $p->id) }}" onsubmit="return confirm('Hapus praktikan ini?')" class="inline-flex">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] flex items-center justify-center transition-colors" title="Hapus Praktikan">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="w-12 h-12 rounded-full bg-[#F6F8FA] flex items-center justify-center mx-auto mb-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/></svg>
                                    </div>
                                    <div class="text-[13px] font-semibold text-[#666D80]">
                                        @if(!empty($search)) Tidak ditemukan hasil untuk "{{ $search }}"
                                        @else Belum ada praktikan terdaftar @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($praktikans->hasPages() || $praktikans->total() > 0)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid #DFE1E7;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [10, 20, 30] }" 
                             class="relative"
                             @click.away="open = false">
                            <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                 :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                 @click="open = !open">
                                <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per halaman</span>
                                <div class="flex items-center gap-1 font-semibold text-[12px]">
                                    <span x-text="selected"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                         :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" 
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="open" @click.away="open = false" style="display: none;" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-0 mt-2 z-50 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                <template x-for="option in options" :key="option">
                                    <label class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                                           :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <input type="radio" :value="option" name="per_page" form="filter_praktikan_form" x-model="selected" @change="document.getElementById('filter_praktikan_form').submit()" class="hidden">
                                        <span x-text="option"></span>
                                        <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </label>
                                </template>
                            </div>
                        </div>
                        <div style="font-size:13px; color:#666D80;">Menampilkan {{ $praktikans->firstItem() ?? 0 }} sampai {{ $praktikans->lastItem() ?? 0 }} dari {{ $praktikans->total() }} data</div>
                    </div>
                    
                    <div style="display:flex; gap:4px;">
                        {{-- Tombol Prev --}}
                        @if ($praktikans->onFirstPage())
                            <span style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        @else
                            <a href="{{ $praktikans->previousPageUrl() }}&tab=praktikan" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </a>
                        @endif

                        {{-- Looping Nomor Halaman --}}
                        @php
                            $current = $praktikans->currentPage();
                            $last = $praktikans->lastPage();
                            
                            if ($current % 3 == 1) {
                                $start = $current;
                            } else {
                                $start = $current - 1;
                            }

                            if ($start + 2 > $last) {
                                $start = max(1, $last - 2);
                            }
                            
                            $end = min($start + 2, $last);
                        @endphp
                        
                        @if($start > 1)
                            <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:#666D80;">...</span>
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <span style="width:32px; height:32px; border:none; background:#0B266E; color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;">{{ $i }}</span>
                            @else
                                <a href="{{ $praktikans->url($i) }}&tab=praktikan" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:#353849; text-decoration:none;">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $last)
                            <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:#666D80;">...</span>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($praktikans->hasMorePages())
                            <a href="{{ $praktikans->nextPageUrl() }}&tab=praktikan" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @else
                            <span style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        {{-- /Tab Praktikan --}}

        {{-- ── TAB: ASISTEN PRAKTIKUM ──────────────────────────────────────────────── --}}
        <div x-show="tab==='asprak'" class="flex flex-col flex-1 min-h-0 h-full" x-cloak>
            <form method="GET" id="filter_asprak_form" class="hidden" action="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}">
                <input type="hidden" name="tab" value="asprak">
            </form>

            {{-- Tabel Asisten Praktikum --}}
            <div class="bg-white border border-[#DFE1E7] rounded-[16px] shadow-[0_1px_3px_rgba(0,0,0,0.04)] flex flex-col flex-1 min-h-0 mb-0.5">
                
                {{-- Toolbar Table --}}
                <div class="px-4 py-3 border-b border-[#DFE1E7] bg-white flex items-center justify-between gap-3 flex-shrink-0" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div>
                        <div class="text-[14px] font-bold text-[#0D0D12]">Daftar Asisten Praktikum</div>
                        <div class="text-[12px] text-[#666D80] mt-0.5">{{ $aspraks->count() }} asisten praktikum terdaftar di praktikum ini</div>
                    </div>
                    <button class="mp-btn secondary md flex items-center gap-1.5 flex-shrink-0 h-[38px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        Export
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 min-h-[400px]">
                    <table class="w-full border-collapse min-w-[500px]">
                        <thead class="sticky top-0 z-10 bg-[#FAFAFA] border-b border-[#DFE1E7]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[48px]">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Nama Asisten</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em] w-[140px]">NIM</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#666D80] uppercase tracking-[0.06em]">Modul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F3F4F6]">
                            @forelse($aspraks as $i => $asprak)
                            @php
                                $user      = $asprak->user;
                                $np        = explode(' ', $user?->name ?? 'AS');
                                $ini       = strtoupper(substr($np[0]??'A',0,1).substr($np[1]??$np[0]??'S',0,1));
                                $avc       = ['#2563EB', '#D97706', '#059669', '#7C3AED', '#E11D48'];
                                $av        = $avc[crc32($user?->email??'')%count($avc)];
                                $st        = $user ? \App\Models\Student::where('user_id',$user->id)->first() : null;
                                $nim       = $st?->student_number ?? '—';
                            @endphp
                            <tr class="hover:bg-[#F6F8FA] transition-colors">
                                <td class="px-4 py-3 text-[12px] text-[#A4ABB8] text-left">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] flex-shrink-0 text-white" style="background-color: {{ $av }};">{{ $ini }}</div>
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-bold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis max-w-[220px]">{{ $user?->name ?? '—' }}</div>
                                            <div class="text-[11px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis max-w-[220px]">{{ $user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-[13px] text-[#353849]" style="font-family: 'Inter', sans-serif;">{{ $nim }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $moduls = $asprak->modulAsprak?->map(fn($ma) => $ma->modul)->filter() ?? collect();
                                        @endphp
                                        @forelse($moduls as $m)
                                        <span class="mp-badge primary sm">{{ $m->judul ?? ('Modul ' . $m->urutan) }}</span>
                                        @empty
                                        <span class="text-[11px] text-[#A4ABB8] italic">Belum ada</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-16 text-center">
                                    <div class="w-12 h-12 rounded-full bg-[#F6F8FA] flex items-center justify-center mx-auto mb-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div class="text-[13px] font-semibold text-[#666D80]">Belum ada asisten praktikum</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($aspraks) && method_exists($aspraks, 'hasPages') && ($aspraks->hasPages() || $aspraks->total() > 0))
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid #DFE1E7;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div x-data="{ open: false, selected: '{{ request('per_page_asprak', 10) }}', options: [10, 20, 30] }" 
                             class="relative"
                             @click.away="open = false">
                            <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                 :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                 @click="open = !open">
                                <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per halaman</span>
                                <div class="flex items-center gap-1 font-semibold text-[12px]">
                                    <span x-text="selected"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                         :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" 
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="open" @click.away="open = false" style="display: none;" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-0 mt-2 z-50 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                <template x-for="option in options" :key="option">
                                    <label class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                                           :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <input type="radio" :value="option" name="per_page_asprak" form="filter_asprak_form" x-model="selected" @change="document.getElementById('filter_asprak_form').submit()" class="hidden">
                                        <span x-text="option"></span>
                                        <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </label>
                                </template>
                            </div>
                        </div>
                        <div style="font-size:13px; color:#666D80;">Menampilkan {{ $aspraks->firstItem() ?? 0 }} sampai {{ $aspraks->lastItem() ?? 0 }} dari {{ $aspraks->total() }} data</div>
                    </div>
                    
                    <div style="display:flex; gap:4px;">
                        {{-- Tombol Prev --}}
                        @if ($aspraks->onFirstPage())
                            <span style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        @else
                            <a href="{{ $aspraks->previousPageUrl() }}&tab=asprak" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </a>
                        @endif

                        {{-- Looping Nomor Halaman --}}
                        @php
                            $current = $aspraks->currentPage();
                            $last = $aspraks->lastPage();
                            
                            if ($current % 3 == 1) {
                                $start = $current;
                            } else {
                                $start = $current - 1;
                            }

                            if ($start + 2 > $last) {
                                $start = max(1, $last - 2);
                            }
                            
                            $end = min($start + 2, $last);
                        @endphp
                        
                        @if($start > 1)
                            <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:#666D80;">...</span>
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <span style="width:32px; height:32px; border:none; background:#0B266E; color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;">{{ $i }}</span>
                            @else
                                <a href="{{ $aspraks->url($i) }}&tab=asprak" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:#353849; text-decoration:none;">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($end < $last)
                            <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:#666D80;">...</span>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($aspraks->hasMorePages())
                            <a href="{{ $aspraks->nextPageUrl() }}&tab=asprak" style="width:32px; height:32px; border:1px solid #DFE1E7; background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @else
                            <span style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        {{-- /Tab Asisten Praktikum --}}
    </div>
</div>

{{-- Modal Import Praktikan --}}
<div id="modalImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 hidden">
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Import Praktikan</div>
            <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#F6F8FA] border-none bg-transparent cursor-pointer text-[#666D80] transition-colors">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.daftar-praktikan.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File Excel (.xlsx) / CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#DFE1E7] rounded-[12px] p-6 text-center hover:bg-[#F6F8FA] transition-colors cursor-pointer relative"
                     onclick="document.getElementById('fileExcel').click()">
                    <input type="file" name="file" id="fileExcel" accept=".csv,.xlsx,.xls" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    <div class="text-[13px] font-semibold text-[#0B266E]">Pilih file atau drag kemari</div>
                    <div class="text-[11px] text-[#A4ABB8] mt-1">Maksimal 2MB</div>
                </div>
                <div class="mt-3 flex items-start gap-2 bg-[#F6F8FA] p-3 rounded-[8px]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" class="mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <div class="text-[11px] text-[#353849] leading-[1.6]">
                        Format file harus berisi kolom <strong>NIM</strong> dan <strong>Nama</strong>, atau format mentah SSO Kampus. Akun mahasiswa akan dibuatkan otomatis jika belum terdaftar.
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end pt-2 border-t border-[#DFE1E7] mt-2">
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>