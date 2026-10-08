<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Ruangan">
    <div x-data="ruanganManager()">
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Manajemen Ruangan</h1>
                <p class="mp-page-sub">Kelola data ruangan fisik yang tersedia untuk dipinjam.</p>
            </div>
            <div class="mp-page-actions">
                <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}" class="mp-btn primary md cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Ruangan
                </a>
            </div>
        </div>



        <div class="mp-card" style="margin-top: 15px;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full" style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">Daftar Ruangan</h2>

                <form action="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" method="GET"
                    class="flex flex-wrap items-center gap-2.5">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-auto" x-data="{ searchQuery: '{{ addslashes(request('search')) }}' }">
                        <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer text-gray-400 hover:text-[#0B266E] transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                            </svg>
                        </button>
                        <input type="text" name="search" x-model="searchQuery" 
                            @input.debounce.500ms="$el.form.submit()"
                            @scroll.window.capture="$el.blur()"
                            @touchmove.window.capture="$el.blur()"
                            placeholder="Search..."
                            class="w-full sm:w-56 h-[38px] pl-9 pr-8 text-[13px] bg-white border border-gray-200 rounded-lg focus:border-[#0B266E] focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder-gray-400">
                        
                        <!-- Clear Search (X) -->
                        <button type="button" x-show="searchQuery.length > 0" x-cloak
                            @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit())"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center cursor-pointer text-gray-400 hover:text-gray-700 transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Wrapper for Filter & Sort to sit side-by-side on mobile -->
                    <div class="flex flex-row items-stretch gap-2 w-full md:w-auto" x-data="{ openFilter: false, openSort: false }">
                        <!-- Filter Button -->
                        <div class="relative flex-1 md:flex-none" @click.away="openFilter = false">
                            <button type="button" @click="openFilter = !openFilter; openSort = false"
                                class="relative w-full inline-flex justify-center items-center gap-2 px-3 h-[38px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-[13px] font-semibold text-slate-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[#0B266E] whitespace-nowrap cursor-pointer">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                                Filter
                                @if(request('status') || request('lokasi'))
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#0B266E] border-2 border-white"></span>
                                    </span>
                                @endif
                            </button>
                            <!-- Filter Popover -->
                            <div x-show="openFilter" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 md:left-auto md:right-0 z-50 mt-2 w-52 origin-top-left md:origin-top-right rounded-xl bg-white shadow-md border border-gray-200 focus:outline-none overflow-hidden max-h-[320px] overflow-y-auto"
                                style="display:none;">
                                
                                <input type="hidden" name="status" x-ref="statusInput" value="{{ request('status') }}">
                                <input type="hidden" name="lokasi" x-ref="lokasiInput" value="{{ request('lokasi') }}">

                                <div class="py-1 px-1">
                                    <!-- Section: PILIH STATUS -->
                                    <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        STATUS RUANGAN</div>
                                    <button type="button"
                                        @click="$refs.statusInput.value=''; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('status') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Semua Status
                                        @if(!request('status'))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </button>
                                    @foreach(['aktif' => 'Aktif', 'nonaktif' => 'Non-aktif'] as $val => $label)
                                        <button type="button"
                                            @click="$refs.statusInput.value='{{ $val }}'; $el.closest('form').submit();"
                                            class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('status') == $val ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            {{ $label }}
                                            @if(request('status') == $val)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                    @endforeach

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <!-- Section: PILIH LOKASI -->
                                    <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        PILIH GEDUNG</div>
                                    <button type="button"
                                        @click="$refs.lokasiInput.value=''; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('lokasi') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Semua Gedung
                                        @if(!request('lokasi'))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </button>
                                    @foreach($lokasis as $lok)
                                        <button type="button"
                                            @click="$refs.lokasiInput.value='{{ $lok }}'; $el.closest('form').submit();"
                                            class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('lokasi') == $lok ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            {{ $lok }}
                                            @if(request('lokasi') == $lok)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Sort By Button -->
                        <div class="relative flex-1 md:flex-none" @click.away="openSort = false">
                            <button type="button" @click="openSort = !openSort; openFilter = false"
                                class="w-full inline-flex justify-center items-center gap-2 px-3 h-[38px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-[13px] font-semibold text-slate-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[#0B266E] whitespace-nowrap cursor-pointer">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <line x1="4" y1="6" x2="20" y2="6"></line>
                                    <line x1="4" y1="12" x2="14" y2="12"></line>
                                    <line x1="4" y1="18" x2="8" y2="18"></line>
                                    <polyline points="14 15 17 18 20 15"></polyline>
                                    <line x1="17" y1="18" x2="17" y2="10"></line>
                                </svg>
                                Sort
                            </button>
                            <!-- Sort Popover -->
                            <div x-show="openSort" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-52 origin-top-right rounded-xl bg-white shadow-md border border-gray-200 focus:outline-none overflow-hidden max-h-[320px] overflow-y-auto"
                                style="display:none;">
                                
                                <input type="hidden" name="sort" x-ref="sortInput" value="{{ request('sort', 'terbaru') }}">

                                <div class="py-1 px-1">
                                    <!-- Section: URUTAN -->
                                    <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        URUTKAN BERDASARKAN</div>
                                    @php
                                        $sorts = [
                                            'terbaru' => 'Terbaru',
                                            'terlama' => 'Terlama',
                                            'kapasitas_desc' => 'Kapasitas Terbesar',
                                            'kapasitas_asc' => 'Kapasitas Terkecil',
                                            'nama_asc' => 'Nama Ruangan (A-Z)',
                                            'nama_desc' => 'Nama Ruangan (Z-A)'
                                        ];
                                        $currentSort = request('sort', 'terbaru');
                                    @endphp
                                    @foreach($sorts as $val => $label)
                                        <button type="button"
                                            @click="$refs.sortInput.value='{{ $val }}'; $el.closest('form').submit();"
                                            class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ $currentSort == $val ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            {{ $label }}
                                            @if($currentSort == $val)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mp-table-wrap" @scroll.passive="$dispatch('close-action-dropdowns')">
                <table class="mp-table" style="table-layout: auto; width: 100%;">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                NAMA RUANG</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                GEDUNG</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                KAPASITAS</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                FASILITAS</th>
                            <th
                                style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                STATUS</th>
                            <th
                                style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ruangans as $r)
                            <tr class="mp-tr">
                                <td style="font-weight: 600;">{{ $r->nama }}</td>
                                <td>{{ $r->lokasi }} <br><span style="font-size:11px; color:#A4ABB8;">Lt.
                                        {{ $r->lantai ?? '-' }}</span></td>
                                <td>{{ $r->kapasitas }} Orang</td>
                                <td>
                                    @if(is_array($r->fasilitas) && count($r->fasilitas) > 0)
                                        @php $fstr = implode(', ', $r->fasilitas); @endphp
                                        <div style="max-width: 140px;">
                                            <div class="truncate" style="font-size: 12px; color: #666D80;" title="{{ $fstr }}">
                                                {{ $fstr }}
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size: 12px; color: #A4ABB8;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->is_active)
                                        <span class="mp-badge success sm">Aktif</span>
                                    @else
                                        <span class="mp-badge sm" style="background:#FADAE1; color:#710E21;">Non-aktif</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div class="relative inline-flex flex-col items-center justify-center w-full"
                                        x-data="{ 
                                            open: false, 
                                            dropdownStyles: '',
                                            initialX: 0,
                                            initialY: 0,
                                            checkScroll() {
                                                if (!this.open) return;
                                                const rect = this.$el.getBoundingClientRect();
                                                const diffX = Math.abs(rect.left - this.initialX);
                                                const diffY = Math.abs(rect.top - this.initialY);
                                                if (diffX > 30 || diffY > 30) {
                                                    this.open = false;
                                                }
                                            }
                                        }">
                                        <button type="button" 
                                            @click="
                                                const rect = $el.getBoundingClientRect();
                                                initialX = rect.left;
                                                initialY = rect.top;
                                                
                                                const popUp = (window.innerHeight - rect.bottom) < 150;
                                                let calcLeft = Math.max(10, rect.right - 140);
                                                
                                                let style = `position: fixed; left: ${calcLeft}px; z-index: 99999; width: 140px; `;
                                                if (popUp) {
                                                    style += `bottom: ${window.innerHeight - rect.top + 8}px; transform-origin: bottom right;`;
                                                } else {
                                                    style += `top: ${rect.bottom + 8}px; transform-origin: top right;`;
                                                }
                                                dropdownStyles = style;
                                                open = !open;
                                            "
                                            @click.away="open = false"
                                            class="inline-flex items-center justify-center w-[32px] h-[32px] rounded-lg border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle></svg>
                                        </button>

                                        <div x-show="open" style="display:none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5"
                                            :style="dropdownStyles"
                                            @scroll.window.capture="checkScroll()"
                                            @close-action-dropdowns.window="checkScroll()">

                                            <a href="{{ route('eoffice.peminjaman.admin.ruangan.edit', $r->id) }}"
                                                class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors no-underline cursor-pointer">
                                                <svg class="w-[14px] h-[14px] text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit Info
                                            </a>

                                            <form id="delete-form-{{ $r->id }}" method="POST"
                                                action="{{ route('eoffice.peminjaman.admin.ruangan.destroy', $r->id) }}"
                                                style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmDelete('delete-form-{{ $r->id }}', '{{ addslashes($r->nama) }}')"
                                                    class="w-full text-left px-2.5 py-1.5 mt-0.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                                    <svg class="w-[14px] h-[14px] text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus Ruangan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 30px; color: #666D80;">
                                    Belum ada data ruangan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Arsip & Rekap Style) --}}
            <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
                <div class="flex flex-col md:flex-row items-center gap-4 text-[13px] text-slate-500 w-full md:w-auto">
                    <div class="flex items-center justify-center gap-2 w-full md:w-auto">
                        <span class="font-medium">Per halaman</span>
                        <div x-data="{ 
                            open: false, 
                            selectedVal: '{{ request('per_page', 10) }}',
                            selectItem(val, url) {
                                this.selectedVal = val;
                                this.open = false;
                                window.location.href = url;
                            }
                        }" class="relative" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-between px-3 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm gap-2 min-w-[64px] transition-colors">
                                <span x-text="selectedVal"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                    :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
    
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 bottom-full mb-1 w-full bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden"
                                style="display: none;">
                                <div class="p-1">
                                    <button type="button"
                                        @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                    <button type="button"
                                        @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                    <button type="button"
                                        @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="hidden md:block w-px h-4 bg-slate-200"></div>
    
                    <p class="hidden md:block font-medium text-slate-500">
                        Menampilkan <span class="font-bold text-slate-800">{{ $ruangans->firstItem() ?? 0 }}</span>
                        sampai <span class="font-bold text-slate-800">{{ $ruangans->lastItem() ?? 0 }}</span>
                        dari <span class="font-bold text-slate-800">{{ $ruangans->total() }}</span> entri
                    </p>
                </div>
    
                <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
                    @if ($ruangans->onFirstPage())
                        <button disabled
                            class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ $ruangans->previousPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif
    
                    @php
                        $startPage = max(1, $ruangans->currentPage() - 1);
                        $endPage = min($ruangans->lastPage(), $ruangans->currentPage() + 1);
    
                        if ($endPage - $startPage < 2) {
                            if ($startPage == 1) {
                                $endPage = min($ruangans->lastPage(), 3);
                            } elseif ($endPage == $ruangans->lastPage()) {
                                $startPage = max(1, $ruangans->lastPage() - 2);
                            }
                        }
                    @endphp
    
                    @if($startPage > 1)
                        <a href="{{ $ruangans->url(1) }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                        @if($startPage > 2)
                            <span
                                class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                        @endif
                    @endif
    
                    @foreach ($ruangans->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $ruangans->currentPage())
                            <span
                                class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
    
                    @if($endPage < $ruangans->lastPage())
                        @if($endPage < $ruangans->lastPage() - 1)
                            <span
                                class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                        @endif
                        <a href="{{ $ruangans->url($ruangans->lastPage()) }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $ruangans->lastPage() }}</a>
                    @endif
    
                    @if ($ruangans->hasMorePages())
                        <a href="{{ $ruangans->nextPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <button disabled
                            class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Delete Confirmation --}}
        <div x-show="showDeleteModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal"
            role="dialog" aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="showDeleteModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity"
                @click="showDeleteModal = false"></div>

            <!-- Modal Content -->
            <div x-show="showDeleteModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[400px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-6 overflow-hidden z-10 text-center">

                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4 border border-red-100">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-[16px] mb-2 tracking-tight">Konfirmasi Hapus</h3>
                <p class="text-[13.5px] text-gray-500 mb-6 leading-relaxed px-2">
                    Apakah Anda yakin ingin menghapus <strong class="text-red-600" x-text="deleteRoomName"></strong>? Sistem akan menonaktifkan ruangan ini secara permanen agar tidak lagi bisa dipinjam oleh pengguna.
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="px-5 py-2.5 text-[13px] font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-colors focus:ring-2 focus:ring-gray-200 outline-none w-1/2 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeDelete()"
                        class="px-5 py-2.5 text-[13px] font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors shadow-sm focus:ring-2 focus:ring-red-500 focus:ring-offset-1 outline-none w-1/2 cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div> <!-- Close Alpine Wrapper -->

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('ruanganManager', () => ({
                showDeleteModal: false,
                pendingDeleteFormId: null,
                deleteRoomName: '',

                confirmDelete(formId, roomName) {
                    this.pendingDeleteFormId = formId;
                    this.deleteRoomName = roomName;
                    this.showDeleteModal = true;
                },

                executeDelete() {
                    if (this.pendingDeleteFormId) {
                        document.getElementById(this.pendingDeleteFormId).submit();
                    }
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>