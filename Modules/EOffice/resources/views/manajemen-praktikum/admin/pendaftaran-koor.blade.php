<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Koordinator Praktikum">

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
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Koordinator Praktikum
        </a>
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
                class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2 border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
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
                $filteredPraktikum = collect($praktikumList)->filter(function($p) {
                    return \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $p->id)->where('jenis', 'koor')->exists();
                });
                foreach($filteredPraktikum as $p) {
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
                       oninput="doLiveSearch(this.value)"
                       onkeydown="if(event.key==='Enter') event.preventDefault()">
            </div>
            @if(request()->anyFilled(['search','status','praktikum_id']))
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-koor.index') }}"
               class="mp-btn secondary md px-4" style="height:38px; text-decoration:none; flex-shrink: 0; display:flex; align-items:center; gap:6px;">
               <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
               Reset
            </a>
            @endif
        </form>
    </div>

    <div id="table_and_pagination">
    <div class="mp-card-body p-0 overflow-x-auto">
        <div style="min-width: max-content;">
        {{-- Header Tabel --}}
        <div class="grid gap-3 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 40px minmax(200px, auto) minmax(220px, auto) 130px minmax(250px, auto) 50px 160px 100px 100px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Nama Mahasiswa</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Email</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">NIM</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">IPK</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Dosen Pengampu</div>
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
             style="grid-template-columns: 40px minmax(200px, auto) minmax(220px, auto) 130px minmax(250px, auto) 50px 160px 100px 100px;">
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($pendaftaran->firstItem() ?? 1) - 1 }}
            </div>

            {{-- Mahasiswa --}}
            <div class="flex items-center gap-[10px] pr-3">
                <div class="mp-av {{ $avColor }} flex-shrink-0">{{ $initials }}</div>
                <div>
                    <div class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap">{{ $p->user?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- Email --}}
            <div class="text-[12px] text-[#666D80] whitespace-nowrap">{{ $p->user?->email ?? '—' }}</div>
            
            {{-- NIM --}}
            <div class="text-[13px] text-[#0D0D12] whitespace-nowrap" style="font-family: 'Inter', sans-serif;">
                {{ $p->user?->student?->student_number ?? $p->user?->external_id ?? '—' }}
            </div>
            
            {{-- Praktikum --}}
            <div class="text-[13px] text-[#0D0D12] whitespace-nowrap">{{ $p->praktikum?->nama ?? '—' }}</div>
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
                        <button type="submit" class="mp-btn" style="padding:6px 8px; background:rgba(16, 185, 129, 0.15); color:#10B981;" title="Terima">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.reject', $p->id) }}">
                        @csrf
                        <input type="hidden" name="alasan_penolakan" :value="alasan">
                        <button type="button" @click="let r = prompt('Alasan Penolakan:'); if(r!==null){ alasan=r; $el.closest('form').submit(); }" class="mp-btn" style="padding:6px 8px; background:rgba(223, 28, 65, 0.15); color:#DF1C41;" title="Tolak">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                @elseif($p->status === 'pending' && $p->status_dosen === 'menunggu')
                <span class="text-[11px] text-[#666D80]">Tunggu Dosen Pengampu</span>
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
            <div class="text-[13px] text-[#666D80]">Hasil Tidak Ditemukan</div>
        </div>
        @endforelse
        </div>
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '{{ request('per_page', 5) }}', options: [5, 10, 20] }" 
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
                     class="absolute top-full left-0 mt-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                    <template x-for="option in options" :key="option">
                        <label class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                               :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                            <input type="radio" :value="option" name="per_page" form="filter_pendaftaran_koor_form" x-model="selected" @change="document.getElementById('filter_pendaftaran_koor_form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
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
</div>

<script>
let searchTimer = null;
function doLiveSearch(val) {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        let url = new URL(window.location.href);
        url.searchParams.set('search', val);
        url.searchParams.set('page', 1);
        
        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let newContent = doc.getElementById('table_and_pagination');
            if (newContent) {
                document.getElementById('table_and_pagination').innerHTML = newContent.innerHTML;
            }
        });
        
        window.history.pushState({}, '', url.toString());
    }, 400);
}
</script>

</x-eoffice::manajemen-praktikum.layout>