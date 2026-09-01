<x-eoffice::manajemen-ruangan.layout pageTitle="Manajemen Fasilitas">

    <div class="mp-page-header mb-6">
        <div>
            <h1 class="mp-page-title">Master Fasilitas</h1>
            <p class="mp-page-sub">Kelola data fasilitas yang nantinya akan digunakan sebagai katalog pelengkap daftar
                ruangan fisik.</p>
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

    <div class="bg-white border border-gray-200 rounded-[12px]" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);" x-data="{ 
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

        <div
            class="px-5 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 bg-white rounded-t-[12px]">

            <div class="flex flex-wrap items-center gap-4">
                <h2 class="text-[15px] font-bold text-gray-900 tracking-tight">Daftar Fasilitas</h2>

                {{-- Bulk Delete Button --}}
                <div x-show="selectedItems.length > 0" x-transition.opacity style="display: none;"
                    class="w-full md:w-auto">
                    <form action="{{ route('eoffice.peminjaman.admin.fasilitas.bulkDestroy') }}" method="POST"
                        @submit.prevent="confirmDelete($event.target, 'Kamu yakin ingin menghapus ' + selectedItems.length + ' fasilitas terpilih secara massal?')"
                        class="w-full flex">
                        @csrf
                        <input type="hidden" name="ids" x-bind:value="selectedItems.join(',')">
                        <button type="submit"
                            class="h-[38px] px-3.5 bg-red-50 text-red-600 border border-red-100 rounded-lg flex items-center gap-1.5 text-[13.5px] font-medium hover:bg-red-100 hover:text-red-700 transition-colors w-full justify-center whitespace-nowrap cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span x-text="'Hapus Terpilih (' + selectedItems.length + ')'"></span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">

                {{-- Search Bar --}}
                <form action="{{ route('eoffice.peminjaman.admin.fasilitas.index') }}" method="GET"
                    class="relative w-full md:w-56">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400"
                        placeholder="Cari fasilitas...">
                </form>

                {{-- Quick Add Button --}}
                <button type="button" @click="showAddModal = true; setTimeout(() => $refs.nama_fasilitas.focus(), 100)"
                    class="h-[38px] px-4 bg-[#0B266E] text-white rounded-lg flex items-center gap-2 text-[13px] font-medium hover:bg-[#07194A] transition-colors w-full md:w-auto justify-center cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Fasilitas
                </button>
            </div>
        </div>

        <div class="overflow-x-auto bg-white min-h-[300px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-y border-slate-200 bg-[#F9FAFB]/50">
                        <th class="py-3.5 px-5 w-12 text-center align-middle">
                            <input type="checkbox"
                                class="w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer"
                                :checked="allSelected" :indeterminate="isIndeterminate" @change="toggleAll">
                        </th>
                        <th class="py-3.5 pl-0 pr-5 text-[13px] font-bold text-slate-500 w-16">No</th>
                        <th class="py-3.5 px-5 text-[13px] font-bold text-slate-500">Nama Fasilitas</th>
                        <th class="py-3.5 px-5 text-[13px] font-bold text-slate-500 text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fasilitas as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group"
                            :class="{ 'bg-blue-50/30': selectedItems.includes('{{ $item->id }}') }">
                            <td class="py-3.5 px-5 align-middle w-12 text-center">
                                <input type="checkbox" value="{{ $item->id }}" x-model="selectedItems"
                                    class="w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer">
                            </td>
                            <td class="py-3.5 pl-0 pr-5 align-middle text-[13px] font-medium text-gray-500">
                                {{ ($fasilitas->currentPage() - 1) * $fasilitas->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-5 align-middle">
                                <div class="text-[14px] font-bold text-[#111827]">
                                    {{ $item->nama_fasilitas }}
                                </div>
                            </td>
                            <td class="py-3.5 px-5 align-middle text-right">
                                <div class="flex items-center justify-end">
                                    <form action="{{ route('eoffice.peminjaman.admin.fasilitas.destroy', $item->id) }}"
                                        method="POST"
                                        @submit.prevent="confirmDelete($event.target, 'Kamu yakin ingin menghapus fasilitas ini dari master data?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
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

        {{-- Pagination Pagination Custom Sama Kayak Arsip --}}
        <div
            class="border-t border-slate-200 bg-slate-50/50 px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-b-[12px]">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <div
                    class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-xs shadow-sm">
                    <span class="px-2.5 py-1.5 bg-slate-50 border-r border-slate-200 text-slate-500 font-medium">Per
                        halaman</span>
                    <select aria-label="Per halaman" onchange="window.location.href=this.value"
                        class="px-2.5 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat"
                        style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;">
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="text-[13px] text-slate-500 font-medium">
                    @if ($fasilitas->total() > 0)
                        <p>
                            Menampilkan <span class="font-bold text-slate-800">{{ $fasilitas->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $fasilitas->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $fasilitas->total() }}</span> fasilitas.
                        </p>
                    @else
                        <p>Belum ada data fasilitas.</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                @if ($fasilitas->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $fasilitas->previousPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                <div
                    class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                    @foreach ($fasilitas->getUrlRange(max(1, $fasilitas->currentPage() - 2), min($fasilitas->lastPage(), $fasilitas->currentPage() + 2)) as $page => $url)
                        @if ($page == $fasilitas->currentPage())
                            <span
                                class="bg-[#354371] text-white w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors cursor-pointer">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                @if ($fasilitas->hasMorePages())
                    <a href="{{ $fasilitas->nextPageUrl() }}"
                        class="text-slate-600 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- ================= QUICK ADD MODAL ================= --}}
        <div x-show="showAddModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                @click.outside="showAddModal = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 text-[15px]">Tambah Fasilitas Baru</h3>
                    <button @click="showAddModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form action="{{ route('eoffice.peminjaman.admin.fasilitas.store') }}" method="POST">
                    @csrf
                    <div x-data="{ charCount: 0 }" class="p-5">
                        <label class="block text-[13.5px] text-slate-700 mb-1.5 font-medium">Nama Fasilitas</label>
                        <div class="relative group">
                            <textarea x-ref="nama_fasilitas" name="nama_fasilitas" required rows="4" maxlength="500"
                                @input="charCount = $event.target.value.length"
                                class="w-full text-[14px] border border-gray-300 rounded-[12px] px-3.5 py-3 pb-8 outline-none focus:bg-[#EFF3F9] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-all placeholder-slate-400 text-slate-800 resize-y leading-relaxed"
                                placeholder="AC Inverter&#10;Proyektor EPSON 4K&#10;Papan Tulis Kaca"></textarea>
                            <span
                                class="absolute bottom-3 left-3.5 text-[11.5px] font-medium text-slate-400 pointer-events-none"
                                x-text="charCount + '/500'">0/500</span>
                        </div>
                        <p class="text-[12px] text-slate-500 mt-2">Ganti baris (Enter) ke bawah untuk entri banyak
                            sekaligus secara masal. Bisa *Copy-Paste* *list* dari Excel / Notes.</p>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2 text-[13px] font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-[13px] font-bold bg-[#0B266E] text-white rounded-lg hover:bg-[#07194A] transition-colors shadow-sm cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= CUSTOM DELETE CONFIRMATION MODAL ================= --}}
        <div x-show="showDeleteModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform"
                @click.outside="showDeleteModal = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <div class="p-6 text-center">
                    <div
                        class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4 border border-red-100">
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
    </div>

</x-eoffice::manajemen-ruangan.layout>