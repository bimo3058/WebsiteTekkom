<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Fasilitas">

    <div x-data="{ 
            showAddModal: false,
            selectedItems: [],
            get allSelected() {
                return this.selectedItems.length === {{ $fasilitas->count() }} && {{ $fasilitas->count() }} > 0;
            },
            get isIndeterminate() {
                return this.selectedItems.length > 0 && this.selectedItems.length < {{ $fasilitas->count() }};
            },
            toggleAll() {
                if (this.allSelected) {
                    this.selectedItems = [];
                } else {
                    this.selectedItems = [{!! $fasilitas->pluck('id')->map(fn($id) => "'{$id}'")->join(',') !!}];
                }
            },
            showDeleteModal: false,
            pendingDeleteForm: null,
            deleteMessage: '',
            confirmDelete(form, message) {
                this.pendingDeleteForm = form;
                this.deleteMessage = message;
                this.showDeleteModal = true;
            },
            executeDelete() {
                if (this.pendingDeleteForm) {
                    this.pendingDeleteForm.submit();
                }
            }
        }">

        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Master Fasilitas</h1>
                <p class="mp-page-sub">Kelola data fasilitas yang nantinya akan digunakan sebagai katalog pelengkap daftar
                    ruangan fisik.</p>
            </div>
            <div class="mp-page-actions flex flex-wrap items-center gap-2">
                <button type="button" @click="showAddModal = true; setTimeout(() => $refs.nama_fasilitas.focus(), 100)"
                    class="mp-btn primary md">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Fasilitas
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-[10px] mb-6 flex items-center gap-3">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <div class="text-[13px] font-medium">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mp-card" style="margin-top: 15px;">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full" style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">Daftar Fasilitas</h2>

            <form action="{{ route('eoffice.peminjaman.admin.fasilitas.index') }}" method="GET"
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
                        placeholder="Cari fasilitas..."
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
            </form>
        </div>

        <form x-ref="bulkForm" action="{{ route('eoffice.peminjaman.admin.fasilitas.bulkDestroy') }}" method="POST">
            @csrf
            <input type="hidden" name="ids" x-bind:value="selectedItems.join(',')">
            <div x-show="selectedItems.length > 0" style="display: none;" x-transition class="bg-red-50/80 px-4 py-2.5 border-b border-red-100 flex items-center justify-between">
                <span class="text-red-700 text-[13px] font-bold"><span x-text="selectedItems.length"></span> Fasilitas Terpilih</span>
                <button type="button" @click="confirmDelete($refs.bulkForm, 'Kamu yakin ingin menghapus ' + selectedItems.length + ' fasilitas terpilih secara massal?')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-[12px] font-bold shadow-sm transition-colors cursor-pointer border-0">
                    Hapus Terpilih
                </button>
            </div>
        </form>

        <div class="mp-table-wrap">
            <table class="mp-table" style="table-layout: auto; width: 100%;">
                <thead>
                    <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 48px;">
                            <input type="checkbox"
                                class="w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer"
                                :checked="allSelected" :indeterminate="isIndeterminate" @change="toggleAll">
                        </th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 64px;">NO</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">NAMA FASILITAS</th>
                        <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 128px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fasilitas as $item)
                        <tr class="mp-tr group" :class="{ 'bg-blue-50/30': selectedItems.includes('{{ $item->id }}') }">
                            <td style="text-align:center;">
                                <input type="checkbox" value="{{ $item->id }}" x-model="selectedItems"
                                    class="w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer">
                            </td>
                            <td>
                                {{ ($fasilitas->currentPage() - 1) * $fasilitas->perPage() + $loop->iteration }}
                            </td>
                            <td style="font-weight: 600;">
                                {{ $item->nama_fasilitas }}
                            </td>
                            <td style="text-align:right;">
                                <div class="flex items-center justify-end">
                                    <form action="{{ route('eoffice.peminjaman.admin.fasilitas.destroy', $item->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            @click="confirmDelete($el.closest('form'), 'Kamu yakin ingin menghapus fasilitas ini dari master data?')"
                                            class="w-8 h-8 rounded-md flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500 text-[13px]">Belum ada item fasilitas
                                yang terdaftar atau ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Custom --}}
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
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                <button type="button"
                                    @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                <button type="button"
                                    @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block w-px h-4 bg-slate-200"></div>

                <div class="hidden md:block font-medium text-slate-500">
                    @if ($fasilitas->total() > 0)
                        <p class="m-0">
                            Menampilkan <span class="font-bold text-slate-800">{{ $fasilitas->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $fasilitas->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $fasilitas->total() }}</span> entri
                        </p>
                    @else
                        <p class="m-0">Belum ada data fasilitas.</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
                @if ($fasilitas->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $fasilitas->previousPageUrl() }}"
                        class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                @php
                    $startPage = max(1, $fasilitas->currentPage() - 1);
                    $endPage = min($fasilitas->lastPage(), $fasilitas->currentPage() + 1);

                    if ($endPage - $startPage < 2) {
                        if ($startPage == 1) {
                            $endPage = min($fasilitas->lastPage(), 3);
                        } elseif ($endPage == $fasilitas->lastPage()) {
                            $startPage = max(1, $fasilitas->lastPage() - 2);
                        }
                    }
                @endphp

                @if($startPage > 1)
                    <a href="{{ $fasilitas->url(1) }}"
                        class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                    @if($startPage > 2)
                        <span
                            class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                    @endif
                @endif

                @foreach ($fasilitas->getUrlRange($startPage, $endPage) as $page => $url)
                    @if ($page == $fasilitas->currentPage())
                        <span
                            class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                    @endif
                @endforeach

                @if($endPage < $fasilitas->lastPage())
                    @if($endPage < $fasilitas->lastPage() - 1)
                        <span
                            class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                    @endif
                    <a href="{{ $fasilitas->url($fasilitas->lastPage()) }}"
                        class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $fasilitas->lastPage() }}</a>
                @endif

                @if ($fasilitas->hasMorePages())
                    <a href="{{ $fasilitas->nextPageUrl() }}"
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

        {{-- ================= QUICK ADD MODAL ================= --}}
        <div x-show="showAddModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal"
            role="dialog" aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="showAddModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity"
                @click="showAddModal = false"></div>

            <!-- Modal Content -->
            <div x-show="showAddModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-lg max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-visible z-10 text-left">
                
                <div class="p-4 sm:p-6 flex flex-col flex-1 min-h-0">
                    <!-- Modal Header -->
                    <div class="-mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Tambah Fasilitas Baru</h3>
                                <p class="text-[10px] text-[#0B266E] font-medium">Entri master data fasilitas ruangan</p>
                            </div>
                        </div>
                        <button type="button" @click="showAddModal = false" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('eoffice.peminjaman.admin.fasilitas.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
                        @csrf
                        
                        <!-- Modal Body -->
                        <div class="space-y-4 overflow-y-auto overflow-x-hidden pr-2 -mr-2 pb-2 flex-1 whitespace-normal">
                            <div x-data="{ charCount: 0 }">
                                <label class="block text-[13.5px] text-slate-700 mb-1.5 font-medium">Nama Fasilitas</label>
                                <div class="relative group">
                                    <textarea x-ref="nama_fasilitas" name="nama_fasilitas" required rows="4" maxlength="500"
                                        @input="charCount = $event.target.value.length"
                                        class="w-full text-[14px] border border-gray-300 rounded-[12px] px-3.5 py-3 pb-8 outline-none focus:bg-[#EFF3F9] focus:border-[#0B266E] focus:ring-1 focus:ring-inset focus:ring-[#0B266E] transition-all placeholder-slate-400 text-slate-800 resize-y leading-relaxed"
                                        placeholder="AC Inverter&#10;Proyektor EPSON 4K&#10;Papan Tulis Kaca"></textarea>
                                    <span
                                        class="absolute bottom-3 left-3.5 text-[11.5px] font-medium text-slate-400 pointer-events-none"
                                        x-text="charCount + '/500'">0/500</span>
                                </div>
                                <p class="text-[12px] text-slate-500 mt-2">Gunakan baris baru (Enter) untuk memasukkan beberapa fasilitas sekaligus.</p>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="mt-5 sm:mt-6 pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                            <button type="button" @click="showAddModal = false"
                                class="w-full sm:w-auto flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto flex items-center justify-center bg-[#0B266E] hover:bg-[#07194A] text-white text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all shadow-sm cursor-pointer border-0">
                                Simpan Fasilitas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= CUSTOM DELETE CONFIRMATION MODAL ================= --}}
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
                <p class="text-[13.5px] text-gray-500 mb-6 leading-relaxed px-2" x-text="deleteMessage"></p>

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
    </div>

</x-eoffice::manajemen-ruangan.layout>