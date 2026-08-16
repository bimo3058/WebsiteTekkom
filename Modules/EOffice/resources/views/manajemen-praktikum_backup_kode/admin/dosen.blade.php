<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Dosen">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Dosen</h1>
        <p class="mp-page-sub">Berikut daftar dosen di Teknik Komputer Universitas Diponegoro</p>
    </div>
</div>

{{-- ════════════════════════════════════════════
     CARD TABEL TERINTEGRASI
════════════════════════════════════════════ --}}
<div id="daftar-dosen" class="mp-card mt-2">

    {{-- Toolbar: Search + Filter --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex flex-wrap gap-3 items-center justify-between">
        <form id="filter-dosen-form" method="GET" action="{{ url()->current() }}#daftar-dosen" class="flex flex-wrap gap-3 items-center flex-1">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama / email / NIP..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>

            @if(request()->filled('search'))
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}"
               class="mp-btn secondary md px-4" style="height:35px;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Kolom Header Tabel --}}
    <div class="mp-card-body p-0">
        <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 50px 2fr 1.5fr 150px 130px 100px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Dosen</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Email</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">NIP</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Praktikum Diampu</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Bergabung</div>
        </div>

        {{-- Rows --}}
        @forelse($dosens as $lecturer)
        @php $user = $lecturer->user; @endphp
        <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 50px 2fr 1.5fr 150px 130px 100px;">
            
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($dosens->firstItem() ?? 1) - 1 }}
            </div>

            {{-- Nama --}}
            <div class="flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av violet flex-shrink-0">
                    {{ strtoupper(substr($user?->name ?? 'D', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $user?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Email --}}
            <div class="text-[12px] text-[#666D80] truncate">{{ $user?->email ?? '—' }}</div>
            
            {{-- NIP --}}
            <div class="text-[12px] font-medium text-[#353849]">{{ $lecturer->employee_number }}</div>
            
            {{-- Praktikum Diampu --}}
            <div class="text-center">
                @php $jumlah = \Modules\EOffice\Models\Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $lecturer->user_id))->count(); @endphp
                <span class="text-[13px] font-medium text-[#353849]">{{ $jumlah }}</span>
                <span class="text-[12px] font-medium text-[#353849]"> praktikum</span>
            </div>
            
            {{-- Bergabung --}}
            <div class="text-[12px] text-[#666D80]">
                {{ $lecturer->created_at?->locale('id')->translatedFormat('F Y') ?? '—' }}
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 gap-4">
            <div class="w-14 h-14 rounded-full bg-[#F6F8FA] flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-[14px] font-semibold text-[#353849] mb-1">Belum ada dosen terdaftar</div>
                <div class="text-[12px] text-[#A4ABB8]">
                    @if(request()->filled('search'))
                        Tidak ada hasil untuk pencarian "{{ request('search') }}"
                    @else
                        Belum ada data dosen di dalam tabel lecturers.
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($dosens) && method_exists($dosens, 'hasPages') && ($dosens->hasPages() || $dosens->total() > 0))
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }" 
                 class="relative flex items-center gap-2 border border-[#DFE1E7] rounded-[8px] px-2 py-1 cursor-pointer bg-white hover:bg-[#F6F8FA] transition-colors"
                 @click="open = !open">
                <span class="text-[12px] text-[#666D80]">Per halaman</span>
                <div class="flex items-center gap-1 font-semibold text-[12px] text-[#0D0D12]">
                    <span x-text="selected"></span>
                    <svg class="w-3 h-3 text-[#666D80] transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div x-show="open" @click.away="open = false" style="display: none;" 
                     class="absolute bottom-full left-0 mb-1 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-lg shadow-[0_4px_12px_rgba(0,0,0,0.08)] py-1.5 overflow-hidden">
                    <template x-for="option in options" :key="option">
                        <label class="flex items-center justify-between px-3 py-2 cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                               :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                            <input type="radio" :value="option" name="per_page" form="filter-dosen-form" x-model="selected" @change="document.getElementById('filter-dosen-form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $dosens->firstItem() ?? 0 }} sampai {{ $dosens->lastItem() ?? 0 }} dari {{ $dosens->total() }} data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            {{-- Tombol Prev --}}
            @if ($dosens->onFirstPage())
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $dosens->previousPageUrl() }}#daftar-dosen" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Looping Nomor Halaman (Pola Custom) --}}
            @php
                $current = $dosens->currentPage();
                $last = $dosens->lastPage();
                
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
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <span style="width:32px; height:32px; border:none; background:var(--c-primary); color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;">{{ $i }}</span>
                @else
                    <a href="{{ $dosens->url($i) }}#daftar-dosen" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:var(--c-fg); text-decoration:none;">{{ $i }}</a>
                @endif
            @endfor

            @if($end < $last)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            {{-- Tombol Next --}}
            @if ($dosens->hasMorePages())
                <a href="{{ $dosens->nextPageUrl() }}#daftar-dosen" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
