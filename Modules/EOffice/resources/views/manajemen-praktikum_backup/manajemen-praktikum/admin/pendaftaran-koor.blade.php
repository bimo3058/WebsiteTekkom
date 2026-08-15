<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Koordinator">

<div class="flex flex-col gap-5 mb-2">
    {{-- Page Header --}}
    <div class="mp-page-header" style="margin-bottom: 0;">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pendaftaran</h1>
            </div>
            <p class="mp-page-sub">Kelola seleksi pendaftaran Koordinator dan Asisten Praktikum · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>

    {{-- Tab Bar --}}
    <div class="flex items-center gap-3 flex-shrink-0 w-fit">
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-koor.index') }}"
                class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2 border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Koordinator Praktikum
        </a>
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
                class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2 border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Asisten Praktikum
        </a>
    </div>
</div>

<div id="daftar-pendaftaran" class="mp-card">
    {{-- Toolbar: Filter & Search --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex items-center justify-between">
        <form id="filter_pendaftaran_koor_form" method="GET" action="{{ url()->current() }}#daftar-pendaftaran" class="flex w-full gap-3 items-center">
            @php
                $praktikumOptions = [['value' => '', 'label' => 'Semua Praktikum']];
                foreach($praktikumList as $p) {
                    $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $p->nama];
                }
            @endphp
            {{-- Praktikum Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="request('praktikum_id', '')"
                placeholder="Semua Praktikum"
                onChange="$event.target.form.submit()"
                minWidth="160px"
            />

            {{-- Status Dosen Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status_dosen"
                :options="[
                    ['value' => '', 'label' => 'Status Dosen'],
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak']
                ]"
                :selected="request('status_dosen', '')"
                placeholder="Status Dosen"
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />

            {{-- Status Admin Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status"
                :options="[
                    ['value' => '', 'label' => 'Status Admin'],
                    ['value' => 'pending', 'label' => 'Menunggu'],
                    ['value' => 'approved', 'label' => 'Disetujui'],
                    ['value' => 'rejected', 'label' => 'Ditolak']
                ]"
                :selected="request('status', '')"
                placeholder="Status Admin"
                onChange="$event.target.form.submit()"
                minWidth="140px"
            />
            <div class="relative flex-1 min-w-[150px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari mahasiswa..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>
            @if(request()->hasAny(['search','status_dosen','status','praktikum_id']))
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-koor.index') }}"
               class="mp-btn secondary md px-4" style="height:35px; text-decoration:none; flex-shrink: 0;">Reset</a>
            @endif
        </form>
    </div>

    <div class="mp-card-body p-0">
        {{-- Header Tabel --}}
        <div class="grid gap-3 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 40px 1.5fr 1.5fr 1.5fr 50px 100px 100px 100px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Mahasiswa</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Email</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">IPK</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Status Dosen</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Status Admin</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Aksi</div>
        </div>

        {{-- Baris Tabel --}}
        @forelse($pendaftaran as $p)
        @php
            $nameParts = explode(' ', $p->user?->name ?? 'KR');
            $initials = strtoupper(substr($nameParts[0] ?? 'K', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'R', 0, 1));
            $avColors = ['sky','navy','green','yellow','violet'];
            $avColor = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
        @endphp
        <div class="grid gap-3 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 40px 1.5fr 1.5fr 1.5fr 50px 100px 100px 100px;">
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($pendaftaran->firstItem() ?? 1) - 1 }}
            </div>
            
            {{-- Mahasiswa --}}
            <div class="flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av {{ $avColor }} flex-shrink-0">{{ $initials }}</div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $p->user?->name ?? '—' }}</div>
                </div>
            </div>
            
            {{-- Email --}}
            <div class="text-[12px] text-[#666D80] truncate">{{ $p->user?->email }}</div>
            
            {{-- Praktikum --}}
            <div class="text-[13px] text-[#0D0D12] truncate">{{ $p->praktikum?->nama ?? '—' }}</div>
            <div class="text-center">
                <span class="text-[13px] text-[#0D0D12]">
                    {{ number_format($p->ipk ?? 0, 2) }}
                </span>
            </div>
            <div class="text-center">
                @if($p->status_dosen === 'disetujui')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($p->status_dosen === 'ditolak')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center">
                @if($p->status === 'approved')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($p->status === 'rejected')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center flex justify-center gap-2">
                @if($p->status === 'pending' && $p->status_dosen === 'disetujui')
                <div class="flex gap-2 justify-center" x-data="{ alasan: '' }">
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.approve', $p->id) }}">
                        @csrf
                        <button type="submit" class="h-8 w-8 flex items-center justify-center p-0 rounded-md transition-colors text-[#10B981] bg-[#10B981]/10 hover:bg-[#10B981]/20" title="Terima">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.reject', $p->id) }}">
                        @csrf
                        <input type="hidden" name="alasan_penolakan" :value="alasan">
                        <button type="button" @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()" class="h-8 w-8 flex items-center justify-center p-0 rounded-md transition-colors text-[#EF4444] bg-[#EF4444]/10 hover:bg-[#EF4444]/20" title="Tolak">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                @elseif($p->status === 'pending' && $p->status_dosen === 'menunggu')
                <span class="text-[11px] text-[#666D80]">Tunggu dosen</span>
                @else
                <span class="text-[11px] text-[#666D80]">Selesai</span>
                @endif
            </div>
        </div>
        @empty
        <div class="py-12 flex flex-col items-center justify-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mb-3">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            <div class="text-[13px] text-[#666D80]">Tidak ada data pendaftaran koordinator.</div>
        </div>
        @endforelse
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '{{ request('per_page', 5) }}', options: [5, 10, 20] }" 
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
                        <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-[#F6F8FA] cursor-pointer text-[12px] font-medium text-[#353849]">
                            <input type="radio" :value="option" name="per_page" form="filter_pendaftaran_koor_form" x-model="selected" @change="document.getElementById('filter_pendaftaran_koor_form').submit()" class="hidden">
                            <div class="w-3 h-3 rounded-full border flex items-center justify-center transition-colors flex-shrink-0" :class="selected == option ? 'border-[#0B266E]' : 'border-[#DFE1E7]'">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0B266E]" x-show="selected == option" style="display: none;"></div>
                            </div>
                            <span x-text="option"></span>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari {{ $pendaftaran->total() }} data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            {{-- Tombol Prev --}}
            @if ($pendaftaran->onFirstPage())
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $pendaftaran->previousPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Looping Nomor Halaman --}}
            @php
                $current = $pendaftaran->currentPage();
                $last = $pendaftaran->lastPage();
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
                    <span style="width:32px; height:32px; background:var(--c-primary); color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $pendaftaran->url($i) }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if($end < $last)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            {{-- Tombol Next --}}
            @if ($pendaftaran->hasMorePages())
                <a href="{{ $pendaftaran->nextPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
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