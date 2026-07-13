<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikum">

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Modules\EOffice\Models\Praktikum[] $praktikums */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\MatkulPraktikum[] $matkulList */
    /** @var \Illuminate\Support\Collection|object[] $dosenList */
@endphp

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Praktikum</h1>
        <p class="mp-page-sub">Kelola semua praktikum di Teknik Komputer Universitas Diponegoro</p>
    </div>
</div>

{{-- Tabel Data Grid terintegrasi Filter --}}
<div id="daftar-praktikum" class="mp-card mt-2">
    {{-- Area Filter & Aksi Utama di dalam Card --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex gap-4 justify-between items-center flex-wrap">
        <form id="filter_praktikum_form" method="GET" action="{{ url()->current() }}#daftar-praktikum" class="flex flex-wrap gap-3 items-center flex-1 min-w-0">
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama praktikum..." class="w-full pl-9 pr-3 h-[38px] text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]" onchange="this.form.submit()">
            </div>
            
            {{-- Custom Dropdown Status --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="status"
                :options="[
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'aktif', 'label' => 'Aktif'],
                    ['value' => 'nonaktif', 'label' => 'Nonaktif']
                ]"
                :selected="request('status', '')"
                placeholder="Status Praktikum"
                onChange="$event.target.form.submit()"
                minWidth="170px"
            />

            {{-- Custom Dropdown Semester --}}
            <x-eoffice::manajemen-praktikum.ui.select 
                name="semester"
                :options="[
                    ['value' => '', 'label' => 'Semua Semester'],
                    ['value' => 'Ganjil', 'label' => 'Ganjil'],
                    ['value' => 'Genap', 'label' => 'Genap']
                ]"
                :selected="request('semester', '')"
                placeholder="Semua Semester"
                onChange="$event.target.form.submit()"
                minWidth="160px"
            />
            
            <input type="text" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}" placeholder="Tahun Ajaran (Contoh: 2025/2026)" class="w-56 h-[38px] px-3 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]" onchange="this.form.submit()">
            
            @if(request()->anyFilled(['search', 'status', 'semester', 'tahun_ajaran']))
                <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary md px-4 h-[38px] flex items-center justify-center">Reset</a>
            @endif
        </form>
        
        <div class="flex-shrink-0 border-l border-[#DFE1E7] pl-4 flex gap-2">
            {{-- Bulk Toggle --}}
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-bulk-modal'))" class="mp-btn secondary md" style="height:35px; background-color:#FFF1F2; border-color:#FECDD3; color:#BE123C;" onmouseover="this.style.backgroundColor='#FEE2E2'" onmouseout="this.style.backgroundColor='#FFF1F2'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                Ubah Status Keseluruhan
            </button>
            <button onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))" class="mp-btn primary md" style="height:35px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Praktikum
            </button>
        </div>
    </div>

    <div class="mp-card-body p-0 overflow-x-auto">
        <div style="min-width: 1200px;">
        {{-- Header --}}
        <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]" style="grid-template-columns: minmax(220px, 1.5fr) minmax(320px, 2fr) minmax(250px, 1.5fr) 90px 100px 90px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Dosen Pengampu</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Koordinator Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Praktikan</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Status</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Aksi</div>
        </div>

        {{-- Rows --}}
        <div class="flex flex-col">
            @forelse($praktikums ?? [] as $p)
            <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center cursor-pointer" style="grid-template-columns: minmax(220px, 1.5fr) minmax(320px, 2fr) minmax(250px, 1.5fr) 90px 100px 90px;" onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.detail', $p->id) }}'">
                <div class="min-w-0">
                    <div class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap">{{ $p->nama }}</div>
                    <div class="text-[11px] text-[#666D80] mt-1">{{ $p->tahun_ajaran }} / Semester {{ $p->semester }}</div>
                </div>
                <div class="flex flex-col gap-1.5 w-full items-start">
                    @forelse($p->dosens as $dosen)
                        @php
                            $parts   = explode(' ', $dosen->name ?? 'D');
                            $ini     = strtoupper(substr($parts[0] ?? 'D', 0, 1) . substr($parts[1] ?? $parts[0] ?? 'O', 0, 1));
                            $avColors = ['sky','navy','green','yellow','violet'];
                            $avColor = $avColors[crc32($dosen->email ?? '') % count($avColors)];
                        @endphp
                        <div class="flex items-center justify-start gap-2 pr-3 w-full">
                            <div class="mp-av {{ $avColor }} flex-shrink-0" style="min-width: 28px; min-height: 28px; max-width: 28px; max-height: 28px; font-size: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                @if($dosen->avatar_url ?? null)
                                <img src="{{ $dosen->avatar_url }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                @else
                                {{ $ini }}
                                @endif
                            </div>
                            <div class="text-[13px] font-medium text-[#353849] whitespace-nowrap" title="{{ $dosen->name }}">{{ $dosen->name }}</div>
                        </div>
                    @empty
                        <div class="text-[13px] text-[#A4ABB8] italic">Belum ada dosen</div>
                    @endforelse
                </div>
                <div class="w-full flex flex-col items-start">
                    @if($p->koordinator)
                        @php
                            $parts   = explode(' ', $p->koordinator->name ?? 'K');
                            $ini     = strtoupper(substr($parts[0] ?? 'K', 0, 1) . substr($parts[1] ?? $parts[0] ?? 'O', 0, 1));
                            $avColors = ['sky','navy','green','yellow','violet'];
                            $avColor = $avColors[crc32($p->koordinator->email ?? '') % count($avColors)];
                        @endphp
                        <div class="flex items-center justify-start gap-2 pr-3 w-full">
                            <div class="mp-av {{ $avColor }} flex-shrink-0" style="min-width: 28px; min-height: 28px; max-width: 28px; max-height: 28px; font-size: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                @if($p->koordinator->avatar_url ?? null)
                                <img src="{{ $p->koordinator->avatar_url }}" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                @else
                                {{ $ini }}
                                @endif
                            </div>
                            <div class="text-[13px] font-medium text-[#353849] whitespace-nowrap" title="{{ $p->koordinator->name }}">{{ $p->koordinator->name }}</div>
                        </div>
                    @else
                        <div class="flex justify-start w-full">
                            <span class="mp-badge error sm">Belum ditunjuk</span>
                        </div>
                    @endif
                </div>
                <div class="text-[13px] font-normal text-[#0D0D12] text-center">
                    {{ $p->daftar_praktikan_count ?? 0 }}
                </div>
                <div class="flex justify-center flex-col gap-1 items-center">
                    @if($p->is_active)
                        <span class="mp-badge success sm">Aktif</span>
                    @else
                        <span class="mp-badge neutral sm">Non-Aktif</span>
                    @endif
                </div>
                <div class="flex items-center justify-center gap-1.5" onclick="event.stopPropagation()">
                    <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $p->id) }}"
                       class="flex items-center justify-center w-8 h-8 rounded-lg border border-[#DFE1E7] bg-white text-[#666D80] hover:bg-[#F6F8FA] hover:text-[#0B266E] transition-colors" title="Detail Praktikum">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </a>
                    <button type="button" @click="$dispatch('open-edit-modal', { id: '{{ $p->id }}', nama: '{{ addslashes($p->nama) }}', matkul_id: '{{ $p->matkul_id }}', tahun_ajaran: '{{ $p->tahun_ajaran }}', semester: '{{ $p->semester }}', status: '{{ $p->is_active ? 'aktif' : 'nonaktif' }}', dosens: {{ json_encode($p->dosens->pluck('id')->isEmpty() ? [''] : $p->dosens->pluck('id')) }} })"
                       class="flex items-center justify-center w-8 h-8 rounded-lg border border-[#DFE1E7] bg-white text-[#666D80] hover:bg-[#F6F8FA] hover:text-[#0D0D12] transition-colors" title="Edit Praktikum">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.destroy', $p->id) }}"
                          onsubmit="return confirm('Hapus praktikum {{ $p->nama }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-lg border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] transition-colors">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-12">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" class="mb-4">
                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
                <div class="text-[14px] font-semibold text-[#0D0D12]">Belum ada data praktikum</div>
                <div class="text-[13px] text-[#666D80] mt-1">Tambahkan data praktikum untuk memulainya.</div>
            </div>
            @endforelse
        </div>
        </div>
    </div>
    
    {{-- Pagination Custom Fungsional --}}
    @if(isset($praktikums) && method_exists($praktikums, 'hasPages') && ($praktikums->hasPages() || $praktikums->total() > 0))
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }" 
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
                            <input type="radio" :value="option" name="per_page" form="filter_praktikum_form" x-model="selected" @change="document.getElementById('filter_praktikum_form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $praktikums->firstItem() ?? 0 }} sampai {{ $praktikums->lastItem() ?? 0 }} dari {{ $praktikums->total() }} data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            {{-- Tombol Prev --}}
            @if ($praktikums->onFirstPage())
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $praktikums->previousPageUrl() }}#daftar-praktikum" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Looping Nomor Halaman (Pola Custom) --}}
            @php
                $current = $praktikums->currentPage();
                $last = $praktikums->lastPage();
                
                if ($current % 3 == 1) {
                    $start = $current;
                } else {
                    $start = $current - 1;
                }

                // Jika sudah mentok di halaman terakhir
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
                    <a href="{{ $praktikums->url($i) }}#daftar-praktikum" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:var(--c-fg); text-decoration:none;">{{ $i }}</a>
                @endif
            @endfor

            @if($end < $last)
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            @endif

            {{-- Tombol Next --}}
            @if ($praktikums->hasMorePages())
                <a href="{{ $praktikums->nextPageUrl() }}#daftar-praktikum" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
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


{{-- Modal Create --}}
<div x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }" 
     @open-create-modal.window="open = true"
     id="modalCreate" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
     x-show="open" style="display: none;" x-cloak>
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="open = false" x-show="open"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Praktikum Baru</div>
        </div>
        <div class="overflow-y-auto flex-1">
        <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.store') }}" class="p-6">
            @csrf
            @if($errors->any())
            <div class="mb-4 p-3 bg-[#FFF5F5] border border-[#FCA5A5] rounded-[9px]">
                <div class="text-[#EF4444] text-[12px] font-bold mb-1">Gagal menyimpan data:</div>
                <ul class="list-disc pl-5 text-[11px] text-[#EF4444] m-0">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Praktikum <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Praktikum Algoritma dan Pemrograman"
                           value="{{ old('nama') }}" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Mata Kuliah Praktikum <span class="text-red-500">*</span></label>
                    <div x-data="{ 
                        open: false, 
                        selected: '{{ old('matkul_id') }}', 
                        options: [
                            @foreach(($matkulList ?? collect())->groupBy('semester') as $sem => $items)
                                @foreach($items as $mk)
                                { value: '{{ $mk->id }}', label: '[{{ $mk->kode }}] {{ addslashes($mk->nama) }}', group: 'SEMESTER {{ $sem }}' },
                                @endforeach
                            @endforeach
                        ],
                        search: '',
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }" class="relative w-full text-left" @click.away="open = false">
                        <input type="hidden" name="matkul_id" x-model="selected" required>
                        <button type="button" @click="open = !open" 
                                class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                            <span x-text="selected ? options.find(o => o.value == selected)?.label : '— Pilih Mata Kuliah —'" class="truncate pr-2" :class="!selected ? 'text-[#94A3B8]' : ''"></span>
                            <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="open" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-60 flex flex-col">
                            <div class="mb-1.5 flex-shrink-0">
                                <input type="text" x-model="search" placeholder="Cari mata kuliah..." class="w-full text-[13px] px-2.5 py-1.5 border border-[#DFE1E7] rounded-[6px] focus:outline-none focus:border-[#0B266E] transition-colors bg-[#FAFAFA]" @click.stop @keydown.enter.prevent>
                            </div>
                            <div class="overflow-y-auto min-h-0 flex flex-col">
                                <template x-for="(option, idx) in filteredOptions" :key="option.value">
                                    <div>
                                        <template x-if="idx === 0 || option.group !== filteredOptions[idx-1].group">
                                            <div class="px-3 py-1.5 mt-1 text-[11px] font-bold text-[#666D80] bg-[#F6F8FA] uppercase tracking-wider" x-text="option.group"></div>
                                        </template>
                                        <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="selected == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                            <input type="radio" :value="option.value" x-model="selected" @change="open = false" class="hidden">
                                            <span x-text="option.label" class="truncate"></span>
                                            <svg x-show="selected == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </label>
                                    </div>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-[#94A3B8] text-[12px]">Tidak ada mata kuliah yang cocok.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun_ajaran" required placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajaran') }}"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#666D80] mb-1.5">Semester <span class="text-red-500">*</span></label>
                        <div x-data="{ dropdownOpen: false, options: [{value:'Ganjil', label:'Ganjil'}, {value:'Genap', label:'Genap'}] }" class="relative w-full text-left" @click.away="dropdownOpen = false">
                            <input type="hidden" name="semester" x-model="selected" required>
                            <button type="button" @click="dropdownOpen = !dropdownOpen"
                                    class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                    :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                                <span x-text="selected ? options.find(o => o.value == selected)?.label : '— Pilih Semester —'" class="truncate pr-2" :class="!selected ? 'text-[#94A3B8]' : ''"></span>
                                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" 
                                     :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div x-show="dropdownOpen" style="display:none;"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-[250px] overflow-y-auto">
                                <template x-for="option in options" :key="option.value">
                                    <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0"
                                           :class="selected == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <input type="radio" :value="option.value" x-model="selected" @change="dropdownOpen = false" class="hidden">
                                        <span x-text="option.label" class="truncate"></span>
                                        <svg x-show="selected == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $oldDosenIds = old('dosen_ids', ['']);
                    if (!is_array($oldDosenIds) || empty($oldDosenIds)) {
                        $oldDosenIds = [''];
                    }
                @endphp
                <div x-data="{ 
                    dosens: {{ json_encode($oldDosenIds) }},
                    dosenOptions: [
                        @foreach($dosenList ?? [] as $d)
                        { value: '{{ $d->id }}', label: '{{ addslashes($d->name) }}' },
                        @endforeach
                    ]
                }">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dosen Pengampu <span class="text-red-500">*</span></label>
                    <template x-for="(dosen, index) in dosens" :key="index">
                        <div class="flex gap-2 mb-2">
                            
                            {{-- Custom Select Dosen --}}
                            <div x-data="{ 
                                open: false,
                                search: '',
                                get filteredOptions() {
                                    if (!this.search) return dosenOptions;
                                    return dosenOptions.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                                }
                            }" class="relative w-full">
                                <input type="hidden" :name="'dosen_ids[' + index + ']'" x-model="dosens[index]" required>
                                <button type="button" @click="open = !open" 
                                        class="mp-input w-full flex items-center justify-between bg-white text-left text-[13px] h-[38px] px-3 border rounded-[8px] focus:outline-none transition-colors"
                                        :class="open ? 'border-[#0B266E] shadow-[0_0_0_3px_rgba(11,38,110,0.1)]' : 'border-[#DFE1E7]'">
                                    <span x-text="dosens[index] ? dosenOptions.find(o => o.value == dosens[index])?.label : '— Pilih Dosen —'" class="truncate pr-2" :class="dosens[index] ? 'text-[#0D0D12]' : 'text-[#94A3B8]'"></span>
                                    <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" style="display: none;" 
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-60 flex flex-col">
                                    <div class="mb-1.5 flex-shrink-0">
                                        <input type="text" x-model="search" placeholder="Cari dosen..." class="w-full text-[13px] px-2.5 py-1.5 border border-[#DFE1E7] rounded-[6px] focus:outline-none focus:border-[#0B266E] transition-colors bg-[#FAFAFA]" @click.stop @keydown.enter.prevent>
                                    </div>
                                    <div class="overflow-y-auto min-h-0 flex flex-col">
                                        <template x-for="option in filteredOptions" :key="option.value">
                                            <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="dosens[index] == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                                <input type="radio" :value="option.value" x-model="dosens[index]" @change="open = false" class="hidden">
                                                <span x-text="option.label" class="truncate"></span>
                                                <svg x-show="dosens[index] == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </label>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-[#94A3B8] text-[12px]">Tidak ada dosen yang cocok.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Hapus (Tampil jika > 1) --}}
                            <button type="button" x-show="dosens.length > 1" @click="dosens.splice(index, 1)" 
                                    class="flex-shrink-0 w-[38px] h-[38px] flex items-center justify-center rounded-[8px] border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] transition-colors" title="Hapus Dosen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" x-show="dosens.length < 3" @click="dosens.push('')" 
                            class="w-full flex items-center justify-center gap-1.5 h-[38px] mt-1 border border-dashed border-[#DFE1E7] rounded-[8px] text-[12px] font-semibold text-[#0B266E] bg-[#FAFAFA] hover:bg-[#F6F8FA] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Dosen Pengampu
                    </button>
                    <p class="text-[11px] text-[#666D80] mt-1.5 text-right">Maksimal 3 dosen pengampu</p>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Status <span class="text-red-500">*</span></label>
                    <div x-data="{ dropdownOpen: false, selected: '{{ old('status', 'aktif') }}', options: [{value:'aktif', label:'Aktif'}, {value:'nonaktif', label:'Non-Aktif'}] }" class="relative w-full text-left" @click.away="dropdownOpen = false">
                        <input type="hidden" name="status" x-model="selected" required>
                        <button type="button" @click="dropdownOpen = !dropdownOpen" 
                                class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                            <span x-text="selected ? options.find(o => o.value == selected)?.label : '— Pilih Status —'" class="truncate pr-2" :class="!selected ? 'text-[#94A3B8]' : ''"></span>
                            <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="dropdownOpen" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-[calc(100%+6px)] z-50 w-full bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_-8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-[250px] overflow-y-auto">
                            <template x-for="option in options" :key="option.value">
                                <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="selected == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <input type="radio" :value="option.value" x-model="selected" @change="dropdownOpen = false" class="hidden">
                                    <span x-text="option.label" class="truncate"></span>
                                    <svg x-show="selected == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="mp-btn secondary md px-5">Batal</button>
                <button type="submit" class="mp-btn primary md px-5">Simpan</button>
            </div>
        </form>
        </div>{{-- /overflow-y-auto --}}
    </div>{{-- /white card --}}
</div>{{-- /modalCreate --}}

{{-- Modal Edit --}}
<div x-data="{ 
    open: false, 
    editData: { id: '', nama: '', matkul_id: '', tahun_ajaran: '', semester: 'Ganjil', status: 'aktif', dosens: [''] },
    updateActionUrl: '' 
}" 
@open-edit-modal.window="
    editData = $event.detail; 
    updateActionUrl = '{{ route('eoffice.manprak.admin.praktikum.index') }}/' + editData.id;
    open = true;
"
id="modalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
x-show="open" style="display: none;" x-cloak>
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="open = false" x-show="open"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[16px] text-[#0D0D12]">Edit Praktikum</div>
        </div>
        <div class="overflow-y-auto flex-1">
        <form method="POST" :action="updateActionUrl" class="p-6 overflow-visible">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Praktikum <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Praktikum Algoritma dan Pemrograman"
                           x-model="editData.nama" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Mata Kuliah Praktikum <span class="text-red-500">*</span></label>
                    <div x-data="{ 
                        dropdownOpen: false, 
                        options: [
                            @foreach(($matkulList ?? collect())->groupBy('semester') as $sem => $items)
                                @foreach($items as $mk)
                                { value: '{{ $mk->id }}', label: '[{{ $mk->kode }}] {{ addslashes($mk->nama) }}', group: 'SEMESTER {{ $sem }}' },
                                @endforeach
                            @endforeach
                        ],
                        search: '',
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                        }
                    }" class="relative w-full text-left" @click.away="dropdownOpen = false">
                        <input type="hidden" name="matkul_id" x-model="editData.matkul_id" required>
                        <button type="button" @click="dropdownOpen = !dropdownOpen" 
                                class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                            <span x-text="editData.matkul_id ? options.find(o => o.value == editData.matkul_id)?.label : '— Pilih Mata Kuliah —'" class="truncate pr-2" :class="!editData.matkul_id ? 'text-[#94A3B8]' : ''"></span>
                            <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="dropdownOpen" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-60 flex flex-col">
                            <div class="mb-1.5 flex-shrink-0">
                                <input type="text" x-model="search" placeholder="Cari mata kuliah..." class="w-full text-[13px] px-2.5 py-1.5 border border-[#DFE1E7] rounded-[6px] focus:outline-none focus:border-[#0B266E] transition-colors bg-[#FAFAFA]" @click.stop @keydown.enter.prevent>
                            </div>
                            <div class="overflow-y-auto min-h-0 flex flex-col">
                                <template x-for="(option, idx) in filteredOptions" :key="option.value">
                                    <div>
                                        <template x-if="idx === 0 || option.group !== filteredOptions[idx-1].group">
                                            <div class="px-3 py-1.5 mt-1 text-[11px] font-bold text-[#666D80] bg-[#F6F8FA] uppercase tracking-wider" x-text="option.group"></div>
                                        </template>
                                        <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="editData.matkul_id == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                            <input type="radio" :value="option.value" x-model="editData.matkul_id" @change="dropdownOpen = false" class="hidden">
                                            <span x-text="option.label" class="truncate"></span>
                                            <svg x-show="editData.matkul_id == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </label>
                                    </div>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-[#94A3B8] text-[12px]">Tidak ada mata kuliah yang cocok.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun_ajaran" required placeholder="Contoh: 2025/2026" x-model="editData.tahun_ajaran"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#666D80] mb-1.5">Semester <span class="text-red-500">*</span></label>
                        <div x-data="{ dropdownOpen: false, options: [{value:'Ganjil', label:'Ganjil'}, {value:'Genap', label:'Genap'}] }" class="relative w-full text-left" @click.away="dropdownOpen = false">
                            <input type="hidden" name="semester" x-model="editData.semester" required>
                            <button type="button" @click="dropdownOpen = !dropdownOpen"
                                    class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                    :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                                <span x-text="editData.semester ? options.find(o => o.value == editData.semester)?.label : '— Pilih Semester —'" class="truncate pr-2" :class="!editData.semester ? 'text-[#94A3B8]' : ''"></span>
                                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" 
                                     :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div x-show="dropdownOpen" style="display:none;"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-[250px] overflow-y-auto">
                                <template x-for="option in options" :key="option.value">
                                    <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0"
                                           :class="editData.semester == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <input type="radio" :value="option.value" x-model="editData.semester" @change="dropdownOpen = false" class="hidden">
                                        <span x-text="option.label" class="truncate"></span>
                                        <svg x-show="editData.semester == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-data="{ 
                    dosenOptions: [
                        @foreach($dosenList ?? [] as $d)
                        { value: '{{ $d->id }}', label: '{{ addslashes($d->name) }}' },
                        @endforeach
                    ]
                }">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dosen Pengampu <span class="text-red-500">*</span></label>
                    <template x-for="(dosen, index) in editData.dosens" :key="index">
                        <div class="flex gap-2 mb-2">
                            <div x-data="{ 
                                dropdownOpen: false,
                                search: '',
                                get filteredOptions() {
                                    if (!this.search) return dosenOptions;
                                    return dosenOptions.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                                }
                            }" class="relative w-full">
                                <input type="hidden" :name="'dosen_ids[' + index + ']'" x-model="editData.dosens[index]" required>
                                <button type="button" @click="dropdownOpen = !dropdownOpen" 
                                        class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                        :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                                    <span x-text="editData.dosens[index] ? dosenOptions.find(o => o.value == editData.dosens[index])?.label : '— Pilih Dosen —'" class="truncate pr-2" :class="editData.dosens[index] ? 'text-[#0D0D12]' : 'text-[#94A3B8]'"></span>
                                    <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div x-show="dropdownOpen" @click.away="dropdownOpen = false" style="display: none;" 
                                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-60 flex flex-col">
                                    <div class="mb-1.5 flex-shrink-0">
                                        <input type="text" x-model="search" placeholder="Cari dosen..." class="w-full text-[13px] px-2.5 py-1.5 border border-[#DFE1E7] rounded-[6px] focus:outline-none focus:border-[#0B266E] transition-colors bg-[#FAFAFA]" @click.stop @keydown.enter.prevent>
                                    </div>
                                    <div class="overflow-y-auto min-h-0 flex flex-col">
                                        <template x-for="option in filteredOptions" :key="option.value">
                                            <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="editData.dosens[index] == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                                <input type="radio" :value="option.value" x-model="editData.dosens[index]" @change="dropdownOpen = false" class="hidden">
                                                <span x-text="option.label" class="truncate"></span>
                                                <svg x-show="editData.dosens[index] == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </label>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-[#94A3B8] text-[12px]">Tidak ada dosen yang cocok.</div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" x-show="editData.dosens.length > 1" @click="editData.dosens.splice(index, 1)" 
                                    class="flex-shrink-0 w-[38px] h-[38px] flex items-center justify-center rounded-[8px] border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] transition-colors" title="Hapus Dosen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" x-show="editData.dosens.length < 3" @click="editData.dosens.push('')" 
                            class="w-full flex items-center justify-center gap-1.5 h-[38px] mt-1 border border-dashed border-[#DFE1E7] rounded-[8px] text-[12px] font-semibold text-[#0B266E] bg-[#FAFAFA] hover:bg-[#F6F8FA] transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Dosen Pengampu
                    </button>
                    <p class="text-[11px] text-[#666D80] mt-1.5 text-right">Maksimal 3 dosen pengampu</p>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Status <span class="text-red-500">*</span></label>
                    <div x-data="{ dropdownOpen: false, options: [{value:'aktif', label:'Aktif'}, {value:'nonaktif', label:'Non-Aktif'}] }" class="relative w-full text-left" @click.away="dropdownOpen = false">
                        <input type="hidden" name="status" x-model="editData.status" required>
                        <button type="button" @click="dropdownOpen = !dropdownOpen" 
                                class="w-full py-2 pl-3 pr-3 text-left text-[13px] border focus:outline-none font-medium flex items-center justify-between transition-colors rounded-[8px]"
                                :class="dropdownOpen ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'">
                            <span x-text="editData.status ? options.find(o => o.value == editData.status)?.label : '— Pilih Status —'" class="truncate pr-2" :class="!editData.status ? 'text-[#94A3B8]' : ''"></span>
                            <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen, 'text-[#0B266E]': dropdownOpen, 'text-[#666D80]': !dropdownOpen}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="dropdownOpen" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-[calc(100%+6px)] z-50 w-full bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_-8px_30px_rgb(0,0,0,0.12)] p-1.5 max-h-[250px] overflow-y-auto">
                            <template x-for="option in options" :key="option.value">
                                <label class="flex items-center justify-between px-3 py-2.5 rounded-[6px] cursor-pointer text-[13px] transition-colors mb-0.5 last:mb-0" :class="editData.status == option.value ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <input type="radio" :value="option.value" x-model="editData.status" @change="dropdownOpen = false" class="hidden">
                                    <span x-text="option.label" class="truncate"></span>
                                    <svg x-show="editData.status == option.value" class="w-4 h-4 flex-shrink-0 text-[#0B266E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                <button type="button" @click="open = false"
                        class="mp-btn secondary md px-5">Batal</button>
                <button type="submit" class="mp-btn primary md px-5">Simpan Perubahan</button>
            </div>
        </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('open-create-modal'));
    });
</script>
@endif

{{-- Modal Bulk Toggle --}}
<div x-data="{ open: false, selectAll: false, action: 'deactivate', toggleAll() { document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = this.selectAll); } }"
     @open-bulk-modal.window="open = true"
     @close-bulk-modal.window="open = false"
     id="modalBulkToggle" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
     x-show="open" style="display: none;" x-cloak>
    
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="open = false" x-show="open"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex justify-between items-center flex-shrink-0">
            <div>
                <div class="font-bold text-[16px] text-[#0D0D12]">Ubah Status Keseluruhan</div>
                <div class="text-[12px] text-[#666D80] mt-0.5">Pilih praktikum yang akan diubah statusnya</div>
            </div>
            <button type="button" @click="open = false" class="text-[#666D80] hover:text-[#0D0D12] transition-colors p-1.5 hover:bg-[#F6F8FA] rounded-[8px]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.bulk-toggle-active') }}" class="p-6">
                @csrf

                <div class="mb-5">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-2">Aksi yang diinginkan <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-[13px] text-[#353849] cursor-pointer">
                            <input type="radio" name="action" value="deactivate" x-model="action" class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E]">
                            Non-Aktifkan Praktikum
                        </label>
                        <label class="flex items-center gap-2 text-[13px] text-[#353849] cursor-pointer">
                            <input type="radio" name="action" value="activate" x-model="action" class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E]">
                            Aktifkan Praktikum
                        </label>
                    </div>
                </div>

                <div class="mb-3 flex justify-between items-center border-b border-[#DFE1E7] pb-3">
                    <label class="flex items-center gap-2 text-[13px] font-semibold text-[#353849] cursor-pointer">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 rounded text-[#0B266E] border-gray-300 focus:ring-[#0B266E]">
                        Pilih Semua
                    </label>
                </div>

                <div class="flex flex-col border border-[#DFE1E7] rounded-[8px] overflow-hidden">
                    @forelse($praktikums ?? [] as $p)
                    <label class="flex items-start gap-3 p-3 border-b border-[#F6F8FA] last:border-0 cursor-pointer hover:bg-[#F6F8FA] transition-colors">
                        <input type="checkbox" name="praktikum_ids[]" value="{{ $p->id }}" class="bulk-checkbox w-4 h-4 mt-0.5 rounded text-[#0B266E] border-gray-300 focus:ring-[#0B266E]">
                        <div>
                            <div class="text-[13px] font-medium text-[#0D0D12] leading-tight mb-1">{{ $p->nama }}</div>
                            <div class="text-[11px] text-[#666D80]">
                                Sem. {{ $p->semester }} {{ $p->tahun_ajaran }} &bull; Status:
                                <span class="{{ $p->is_active ? 'text-[#0D9488]' : 'text-[#DF1C41]' }} font-semibold">{{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                            </div>
                        </div>
                    </label>
                    @empty
                    <div class="text-[13px] text-center text-[#666D80] py-6 bg-[#FAFAFA]">Belum ada praktikum yang tersedia.</div>
                    @endforelse
                </div>

                <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                    <button type="button" @click="open = false" class="mp-btn secondary md px-5">Batal</button>
                    <button type="submit" class="mp-btn primary md px-5"
                            :class="action === 'deactivate' ? 'bg-[#DF1C41] border-[#DF1C41] hover:bg-[#C9193A]' : ''">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Legacy functions redirect to Alpine events for safety
function openBulkModal() { window.dispatchEvent(new CustomEvent('open-bulk-modal')); }
function closeBulkModal() { window.dispatchEvent(new CustomEvent('close-bulk-modal')); }
</script>

</x-eoffice::manajemen-praktikum.layout>