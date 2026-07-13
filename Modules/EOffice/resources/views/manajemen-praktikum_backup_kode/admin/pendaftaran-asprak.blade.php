<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asisten Praktikum">
@php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Praktikum[] $praktikumList */
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Modules\EOffice\Models\PendaftaranAsprak[] $pendaftarans */
@endphp
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
                class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2 border-[#DFE1E7] text-[#666D80] hover:text-[#353849] hover:bg-black/5" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            Koordinator Praktikum
        </a>
        <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
                class="px-4 py-2 rounded-[10px] border-[1.5px] cursor-pointer text-[13px] font-bold transition-all flex items-center justify-center gap-2 border-[#0B266E] bg-[#0B266E]/5 text-[#0B266E]" style="text-decoration: none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Asisten Praktikum
        </a>
    </div>
</div>

<div id="daftar-pendaftaran" class="mp-card">
    {{-- Toolbar: Filter & Search --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex items-center justify-between">
        <form id="filter_pendaftaran_asprak_form" method="GET" action="{{ url()->current() }}#daftar-pendaftaran" class="flex w-full gap-3 items-center">
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

            {{-- Status Koordinator Filter --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status_koor"
                :options="[
                    ['value' => '', 'label' => 'Status Koordinator'],
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak']
                ]"
                :selected="request('status_koor', '')"
                placeholder="Status Koordinator"
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
                       placeholder="Cari nama / NIM..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>
            @if(request()->hasAny(['search','status_koor','status','praktikum_id']))
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
               class="mp-btn secondary md px-4" style="height:35px; text-decoration:none; flex-shrink: 0;">Reset</a>
            @endif
        </form>
    </div>

    <div class="mp-card-body p-0">
        {{-- Header Tabel --}}
        <div class="grid gap-3 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 40px 1.5fr 1fr 1.5fr 40px 60px 1.5fr 100px 100px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Mahasiswa</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">NIM</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">IPK</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Motivasi</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Koordinator Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Status Admin</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Aksi</div>
        </div>

        {{-- Baris Tabel --}}
        @forelse($pendaftarans ?? [] as $pend)
        @php
            $nameParts = explode(' ', $pend->user?->name ?? 'AS');
            $initials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
            $avColors = ['sky','navy','green','yellow','violet'];
            $avColor = $avColors[crc32($pend->user?->email ?? '') % count($avColors)];
        @endphp
        <div class="grid gap-3 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 40px 1.5fr 1fr 1.5fr 40px 60px 1.5fr 100px 100px;">
            {{-- No --}}
            <div class="text-[12px] font-semibold text-[#666D80]">
                {{ $loop->iteration + ($pendaftarans->firstItem() ?? 1) - 1 }}
            </div>

            {{-- Mahasiswa --}}
            <div class="flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av {{ $avColor }} flex-shrink-0">{{ $initials }}</div>
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $pend->user?->name ?? '—' }}</div>
                </div>
            </div>

            {{-- NIM --}}
            <div class="text-[12px] text-[#666D80] truncate">{{ $pend->user?->student_number ?? '—' }}</div>

            {{-- Praktikum --}}
            <div class="text-[13px] text-[#0D0D12] truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
            <div class="text-center">
                <span class="text-[13px] text-[#0D0D12]">
                    {{ number_format($pend->ipk ?? 0, 2) }}
                </span>
            </div>
            <div class="text-center">
                @if($pend->motivasi)
                <span class="mp-badge success sm"><span class="dot"></span>Ada</span>
                @else
                <span class="mp-badge neutral sm">—</span>
                @endif
            </div>
            <div class="text-center">
                @if($pend->status_koor === 'disetujui')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($pend->status_koor === 'ditolak')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center">
                @if($pend->status === 'approved')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                @elseif($pend->status === 'rejected')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                @else
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                @endif
            </div>
            <div class="text-center flex justify-center gap-2">
                @if($pend->status === 'pending' && $pend->status_koor === 'disetujui')
                <div class="flex gap-2 justify-center">
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}">
                        @csrf
                        <button type="submit" class="h-8 w-8 flex items-center justify-center p-0 rounded-md transition-colors text-[#10B981] bg-[#10B981]/10 hover:bg-[#10B981]/20" title="Terima">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}" onsubmit="return confirm('Tolak pendaftaran ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="h-8 w-8 flex items-center justify-center p-0 rounded-md transition-colors text-[#EF4444] bg-[#EF4444]/10 hover:bg-[#EF4444]/20" title="Tolak">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                @elseif($pend->status === 'pending' && $pend->status_koor === 'menunggu')
                <span class="text-[11px] text-[#666D80]">Tunggu Koor</span>
                @else
                <span class="text-[11px] text-[#666D80]">Selesai</span>
                @endif
            </div>
        </div>
        @empty
        <div class="py-12 flex flex-col items-center justify-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mb-3">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
            </svg>
            <div class="text-[13px] text-[#666D80]">Tidak ada data pendaftaran asisten praktikum.</div>
        </div>
        @endforelse
    </div>

    {{-- Pagination Custom Fungsional --}}
    @if(isset($pendaftarans) && method_exists($pendaftarans, 'hasPages') && ($pendaftarans->hasPages() || $pendaftarans->total() > 0))
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
                        <label class="flex items-center justify-between px-3 py-2 cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                               :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                            <input type="radio" :value="option" name="per_page" form="filter_pendaftaran_asprak_form" x-model="selected" @change="document.getElementById('filter_pendaftaran_asprak_form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $pendaftarans->firstItem() ?? 0 }} sampai {{ $pendaftarans->lastItem() ?? 0 }} dari {{ $pendaftarans->total() }} data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            {{-- Tombol Prev --}}
            @if ($pendaftarans->onFirstPage())
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $pendaftarans->previousPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Looping Nomor Halaman --}}
            @php
                $current = $pendaftarans->currentPage();
                $last = $pendaftarans->lastPage();
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
                    <a href="{{ $pendaftarans->url($i) }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            @if($end < $last)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            {{-- Tombol Next --}}
            @if ($pendaftarans->hasMorePages())
                <a href="{{ $pendaftarans->nextPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
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