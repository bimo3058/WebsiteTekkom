<x-eoffice::manajemen-ruangan.layout
    pageTitle="{{ $viewMode === 'akademik' ? 'Jadwal Akademik' : 'Kelola Blokir Ruangan' }}">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

    <div
        x-data="{ showModal: false, showImportModal: false, formType: '{{ $viewMode === 'akademik' ? 'rutin' : 'spesifik' }}', kategoriType: '{{ $viewMode === 'akademik' ? 'Jadwal Akademik (Kuliah)' : 'Maintenance / Perbaikan' }}' }">
        <div class="mp-page-header flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                @if($viewMode === 'akademik')
                    <h1 class="mp-page-title">Kelola Jadwal Akademik</h1>
                    <p class="mp-page-sub">Atur dan import blocking waktu khusus untuk agenda perkuliahan rutin Fakultas.
                    </p>
                @else
                    <h1 class="mp-page-title">Kelola Blokir Ruangan</h1>
                    <p class="mp-page-sub">Kelola penutupan akses ruangan untuk memprioritaskan jadwal insidental dan
                        perawatan fisik.</p>
                @endif
            </div>
            <div class="mp-page-actions flex flex-col md:flex-row md:items-center gap-3">
                <button @click="showModal = true" class="mp-btn primary md">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah
                </button>
            </div>
        </div> <!-- Close mp-page-header -->

        <!-- Add Modal Alpine Component -->
        <div x-show="showModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY (Latar Belakang Gelap) -->
            <div x-show="showModal" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                aria-hidden="true" @click="showModal = false"></div>

            <!-- 2. MODAL PANEL (Kotak Utama Modal) -->
            <div x-show="showModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-xl max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-visible">
                
                <div class="p-4 sm:p-6 flex flex-col flex-1 min-h-0">

                    <div class="-mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">event_busy</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Blokir Ruangan Baru</h3>
                                <p class="text-[10px] text-[#0B266E] font-medium">Buat jadwal penutupan ruangan</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                        </button>
                    </div>

                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.store') }}" method="POST"
                            class="flex flex-col flex-1 min-h-0"
                            x-data="{ 
                                isMultiday: false,
                                selectedRooms: [],
                                selectedKategori: 'Maintenance / Perbaikan',
                                tglSpesifik: '',
                                tglMulai: '',
                                tglSelesai: '',
                                jamMulai: '',
                                jamSelesai: '',
                                namaAcara: '',
                                warningData: null,
                                isChecking: false,
                                
                                checkCollision() {
                                    if (this.selectedRooms.length === 0 || !this.jamMulai || !this.jamSelesai) {
                                        this.warningData = null;
                                        return;
                                    }
                                    if (!this.isMultiday && !this.tglSpesifik) return;
                                    if (this.isMultiday && (!this.tglMulai || !this.tglSelesai)) return;
                                    
                                    this.isChecking = true;
                                    
                                    const params = new URLSearchParams({
                                        tipe_jadwal: 'spesifik',
                                        kategori: this.selectedKategori,
                                        jam_mulai: this.jamMulai,
                                        jam_selesai: this.jamSelesai,
                                        is_multiday: this.isMultiday ? 1 : 0,
                                        tanggal_spesifik: this.tglSpesifik || '',
                                        tanggal_mulai: this.tglMulai || '',
                                        tanggal_selesai: this.tglSelesai || ''
                                    });
                                    this.selectedRooms.forEach(id => params.append('ruangan_ids[]', id));
                                    
                                    fetch(`{{ route('eoffice.peminjaman.admin.jadwal-internal.check-collision') }}?${params.toString()}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.conflict) {
                                            this.warningData = data;
                                        } else {
                                            this.warningData = null;
                                        }
                                    })
                                    .finally(() => {
                                        this.isChecking = false;
                                    });
                                }
                            }" @change="checkCollision" @input.debounce.500ms="checkCollision">
                            @csrf
                            <input type="hidden" name="tipe_jadwal" value="spesifik">

                            <div class="space-y-4 overflow-y-auto overflow-x-hidden pl-1 -ml-1 pr-2 -mr-2 pb-2 flex-1 whitespace-normal">
                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Informasi Kegiatan</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Bagian Kategori Kegiatan -->
                                        <div x-data="{ open: false, options: ['Maintenance / Perbaikan', 'Sterilisasi Ruangan', 'Penutupan Khusus', 'Ujian / Evaluasi', 'Pindah Kelas', 'Lainnya'] }"
                                            class="relative space-y-1.5" :class="{'z-50': open, 'z-10': !open}">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori
                                                Kegiatan</label>
                                            <input type="hidden" name="kategori" :value="selectedKategori">
                                            <button type="button" @click="open = !open" @click.away="open = false"
                                                class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                <span x-text="selectedKategori"
                                                    class="truncate text-gray-800"></span>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
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
                                                class="absolute left-0 mt-1 w-full bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden"
                                                style="display: none;">
                                                <div class="p-1">
                                                    <template x-for="opt in options" :key="opt">
                                                        <button type="button"
                                                            @click="selectedKategori = opt; open = false; checkCollision()"
                                                            class="w-full text-left px-3 py-1.5 text-[13px] text-gray-700 font-medium hover:bg-[#EFF6FF] hover:text-[#0B266E] rounded-md transition-colors"
                                                            x-text="opt"></button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bagian Nama Acara -->
                                        <div class="space-y-1.5">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama
                                                Kegiatan</label>
                                            <input type="text" name="keterangan" x-model="namaAcara" required autocomplete="off"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]" placeholder="Misal: UTS Ganjil 2026">
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian Pilih Ruangan (Accordion Checkbox) -->
                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center gap-2 mb-1 justify-between">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Pemilihan Ruangan (Bisa > 1)</span>
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#EFF6FF] text-[#0B266E]"
                                            x-text="selectedRooms.length + ' Dipilih'"></span>
                                    </div>

                                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                                        @php
                                            $groupedRuangans = $ruangans->groupBy('kategori')->sortKeys();
                                        @endphp
                                        @foreach($groupedRuangans as $kategori => $rooms)
                                            <div x-data="{ expanded: false }"
                                                class="border-b border-slate-100 last:border-0">
                                                <button type="button" @click="expanded = !expanded"
                                                    class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-slate-50 transition-colors">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                                            :class="{'rotate-90': expanded}" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                        <span
                                                            class="font-bold text-[13px] text-slate-800">{{ $kategori ?: 'Lainnya' }}</span>
                                                    </div>
                                                    <span
                                                        class="text-[11px] font-medium text-slate-500">{{ $rooms->count() }}
                                                        Ruang</span>
                                                </button>
                                                <div x-show="expanded" style="display: none;"
                                                    class="px-4 py-3 grid grid-cols-2 gap-3 bg-slate-50/50 border-t border-slate-100">
                                                    @foreach($rooms->sortBy('nama') as $r)
                                                        <label class="flex items-center gap-2.5 cursor-pointer group">
                                                            <input type="checkbox" name="ruangan_ids[]" value="{{ $r->id }}"
                                                                x-model="selectedRooms"
                                                                class="rounded border-slate-300 text-[#0B266E] focus:ring-[#0B266E]">
                                                            <span
                                                                class="text-[13px] font-bold text-slate-700 group-hover:text-[#0B266E] transition-colors leading-tight">{{ $r->nama }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Bagian Pengaturan Waktu & Tanggal -->
                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Waktu & Tanggal</span>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer bg-white px-2.5 py-1 rounded-md border border-slate-200 shadow-sm">
                                            <input type="hidden" name="is_multiday" value="0">
                                            <input type="checkbox" name="is_multiday" value="1" x-model="isMultiday"
                                                class="rounded border-slate-300 text-[#0B266E] focus:ring-[#0B266E]">
                                            <span class="text-[11px] font-bold text-slate-700">Rentang Waktu
                                                (Multi-Hari)</span>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Tanggal Mode 1 Hari -->
                                        <div x-show="!isMultiday" class="col-span-2 space-y-1.5">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal
                                                Pelaksanaan</label>
                                            <input type="date" name="tanggal_spesifik" x-model="tglSpesifik"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]" :required="!isMultiday">
                                        </div>

                                        <!-- Tanggal Mode Multi-Hari -->
                                        <div x-show="isMultiday" class="col-span-1 space-y-1.5" style="display: none;">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari
                                                Tanggal</label>
                                            <input type="date" name="tanggal_mulai" x-model="tglMulai"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]" :required="isMultiday">
                                        </div>
                                        <div x-show="isMultiday" class="col-span-1 space-y-1.5" style="display: none;">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai
                                                Tanggal</label>
                                            <input type="date" name="tanggal_selesai" x-model="tglSelesai"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]" :required="isMultiday">
                                        </div>

                                        <!-- Jam -->
                                        <div class="col-span-1 space-y-1.5">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam
                                                Mulai</label>
                                            <input type="time" name="jam_mulai" x-model="jamMulai" required
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                        </div>
                                        <div class="col-span-1 space-y-1.5">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam
                                                Selesai</label>
                                            <input type="time" name="jam_selesai" x-model="jamSelesai" required
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                        </div>
                                        <div class="col-span-2 text-[10px] text-slate-500 mt-0.5 ml-1">
                                            <p class="leading-relaxed"><b>Info:</b> Jam operasional peminjaman reguler adalah <b>07:00 - 17:00</b>. Sebagai Admin, Anda bebas melewati batas ini untuk pemblokiran 24 jam jika diperlukan.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- SUMMARIZATION WARNING ALERT (REALTIME) -->
                                <template x-if="warningData">
                                    <div class="p-4 bg-red-50/80 border border-red-200 rounded-xl relative overflow-hidden"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                                        <div class="flex items-start gap-3 pl-2">
                                            <div class="mt-0.5 shrink-0 bg-white p-1.5 rounded-full shadow-sm">
                                                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h5 class="text-[13px] font-bold text-red-800">Peringatan Super
                                                    Override!</h5>
                                                <p class="text-[12px] font-medium text-red-700 mt-1 leading-relaxed"
                                                    x-text="warningData.message"></p>
                                                <p
                                                    class="text-[10px] font-bold text-red-600 mt-2.5 uppercase tracking-wider">
                                                    Menekan Simpan = Membatalkan Semua Jadwal Tersebut</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <!-- Mobile Footer (Scrolls with content) -->
                                <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                    <button type="button" @click="showModal = false"
                                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer text-center">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        :disabled="isChecking"
                                        class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0B266E] focus:ring-offset-2 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer text-center justify-center">
                                        Simpan Konfigurasi
                                    </button>
                                </div>
                            </div>

                            <!-- Desktop Sticky Footer -->
                            <div class="hidden sm:flex px-2 pt-4 pb-2 border-t border-gray-100 items-center justify-end gap-2 bg-white mt-auto shrink-0">
                                <button type="button" @click="showModal = false"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit"
                                    :disabled="isChecking"
                                    class="px-5 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0B266E] focus:ring-offset-2 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                    Simpan Konfigurasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Error Block -->
        @if ($errors->any())
            <div
                class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative text-[13px] font-medium shadow-sm">
                <strong class="font-bold mr-1">Terdapat Kesalahan Input!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mp-card" style="margin-top: 0px;"
            x-data="{ openFilter: false, openSort: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full"
                style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">Daftar Jadwal</h2>

                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto mt-3 md:mt-0">
                    <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" method="GET"
                        class="flex flex-col md:flex-row items-stretch md:items-center gap-2 m-0 relative w-full">
                        <!-- Search Bar -->
                        <div class="relative w-full sm:w-auto" x-data="{ searchQuery: '{{ addslashes(request('search')) }}' }">
                            <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer text-gray-400 hover:text-[#0B266E] transition-colors bg-transparent border-0 outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                                </svg>
                            </button>
                            <input type="text" name="search" x-model="searchQuery" 
                                @input="if(searchQuery.trim() === '') $el.form.submit()"
                                @scroll.window.capture="$el.blur()"
                                @touchmove.window.capture="$el.blur()"
                                placeholder="Cari acara..."
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

                        <!-- Hidden Inputs untuk mempertahankan state -->
                        <input type="hidden" name="status_waktu" x-ref="statusWaktuInput" value="{{ request('status_waktu') }}">
                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" value="{{ request('ruangan_id') }}">
                        <input type="hidden" name="kategori" x-ref="kategoriInput" value="{{ request('kategori') }}">
                        <input type="hidden" name="sort" x-ref="sortInput" value="{{ request('sort', 'terbaru') }}">

                        <!-- Wrapper for Filter & Sort to sit side-by-side on mobile -->
                        <div class="flex flex-row items-stretch gap-2 w-full md:w-auto">
                            <!-- Filter Button -->
                            <div class="relative flex-1 md:flex-none" @click.away="openFilter = false">
                                <button type="button" @click="openFilter = !openFilter"
                                    class="relative w-full inline-flex justify-center items-center gap-2 px-3 h-[38px] bg-white border rounded-lg text-[13px] font-semibold transition-colors whitespace-nowrap cursor-pointer"
                                    :class="openFilter ? 'border-[#0B266E] text-[#0B266E]' : 'border-gray-300 text-slate-700 hover:bg-gray-50'">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                    </svg>
                                    Filter
                                @if(request('status_waktu') || request('ruangan_id') || request('kategori'))
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#0B266E] border-2 border-white"></span>
                                    </span>
                                @endif
                            </button>

                            <!-- Dropdown Menu Filter -->
                            <div x-show="openFilter" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 sm:left-auto sm:right-0 z-50 mt-2 w-52 origin-top-left sm:origin-top-right rounded-xl bg-white shadow-md border border-gray-200 focus:outline-none overflow-hidden max-h-[320px] overflow-y-auto"
                                style="display: none;">
                                <div class="py-1 px-1">
                                    <!-- Section: STATUS WAKTU -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        STATUS WAKTU</div>
                                    <button type="button"
                                        @click="$refs.statusWaktuInput.value=''; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('status_waktu') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Semua Waktu
                                        @if(!request('status_waktu'))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button"
                                        @click="$refs.statusWaktuInput.value='mendatang'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('status_waktu') === 'mendatang' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Akan Datang & Hari Ini
                                        @if(request('status_waktu') === 'mendatang')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button"
                                        @click="$refs.statusWaktuInput.value='lewat'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('status_waktu') === 'lewat' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Sudah Lewat (Expired)
                                        @if(request('status_waktu') === 'lewat')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <!-- Section: RUANGAN -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        PILIH RUANGAN</div>
                                    <button type="button"
                                        @click="$refs.ruanganInput.value=''; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('ruangan_id') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        Semua Ruangan
                                        @if(!request('ruangan_id'))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    @foreach($ruangans as $ruang)
                                        <button type="button"
                                            @click="$refs.ruanganInput.value='{{ $ruang->id }}'; $el.closest('form').submit();"
                                            class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('ruangan_id') == $ruang->id ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            {{ $ruang->nama }}
                                            @if(request('ruangan_id') == $ruang->id)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    @endforeach

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <!-- Section: KATEGORI -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        KATEGORI BLOKIR</div>
                                    @php
                                        $kategories = [
                                            '' => 'Semua Kategori',
                                            'Maintenance / Perbaikan' => 'Maintenance / Perbaikan',
                                            'Ujian / Evaluasi' => 'Ujian / Evaluasi',
                                            'Penutupan Khusus' => 'Penutupan Khusus',
                                            'Pindah Kelas' => 'Pindah / Pengganti Kelas',
                                            'Lainnya' => 'Lainnya...'
                                        ];
                                    @endphp
                                    @foreach($kategories as $val => $label)
                                        <button type="button"
                                            @click="$refs.kategoriInput.value='{{ $val }}'; $el.closest('form').submit();"
                                            class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('kategori') == $val ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            {{ $label }}
                                            @if(request('kategori') == $val)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Sort Dropdown --}}
                        <div x-data="{ openSort: false }" class="relative inline-block text-left flex-1 sm:flex-none">
                            <!-- Tombol Sort -->
                            <button type="button" @click="openSort = !openSort" @click.away="openSort = false"
                                class="flex items-center justify-center w-full relative gap-2 px-3 h-[38px] bg-white border rounded-lg text-[13px] font-semibold transition-colors cursor-pointer"
                                :class="openSort ? 'border-[#0B266E] text-[#0B266E]' : 'border-gray-300 text-slate-700 hover:bg-gray-50'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="6" x2="20" y2="6"></line>
                                    <line x1="4" y1="12" x2="14" y2="12"></line>
                                    <line x1="4" y1="18" x2="8" y2="18"></line>
                                    <polyline points="14 15 17 18 20 15"></polyline>
                                    <line x1="17" y1="18" x2="17" y2="10"></line>
                                </svg>
                                Sort
                                @if(request('sort') && request('sort') !== 'terbaru')
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#0B266E] border-2 border-white"></span>
                                    </span>
                                @endif
                            </button>

                            <!-- Dropdown Menu Sort -->
                            <div x-show="openSort" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-xl bg-white shadow-md border border-gray-200 focus:outline-none"
                                style="display: none;">
                                <div class="py-1 px-1">
                                    <!-- Section: PELAKSANAAN -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        PELAKSANAAN</div>
                                    <button type="button"
                                        @click="$refs.sortInput.value='pelaksanaan_asc'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('sort') === 'pelaksanaan_asc' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <line x1="4" y1="6" x2="20" y2="6" />
                                                <line x1="4" y1="12" x2="14" y2="12" />
                                                <line x1="4" y1="18" x2="8" y2="18" />
                                                <polyline points="14 15 17 18 20 15" />
                                                <line x1="17" y1="18" x2="17" y2="10" />
                                            </svg>
                                            Terdekat
                                        </div>
                                        @if(request('sort') === 'pelaksanaan_asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button"
                                        @click="$refs.sortInput.value='pelaksanaan_desc'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('sort') === 'pelaksanaan_desc' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <line x1="4" y1="6" x2="20" y2="6" />
                                                <line x1="4" y1="12" x2="14" y2="12" />
                                                <line x1="4" y1="18" x2="8" y2="18" />
                                                <polyline points="14 15 17 18 20 15" />
                                                <line x1="17" y1="18" x2="17" y2="10" />
                                            </svg>
                                            Terlama
                                        </div>
                                        @if(request('sort') === 'pelaksanaan_desc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <!-- Section: RUANGAN -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        RUANGAN</div>
                                    <button type="button"
                                        @click="$refs.sortInput.value='ruangan_asc'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('sort') === 'ruangan_asc' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <line x1="4" y1="6" x2="20" y2="6" />
                                                <line x1="4" y1="12" x2="14" y2="12" />
                                                <line x1="4" y1="18" x2="8" y2="18" />
                                                <polyline points="14 15 17 18 20 15" />
                                                <line x1="17" y1="18" x2="17" y2="10" />
                                            </svg>
                                            Ruangan A-Z
                                        </div>
                                        @if(request('sort') === 'ruangan_asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <button type="button"
                                        @click="$refs.sortInput.value='ruangan_desc'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('sort') === 'ruangan_desc' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <line x1="4" y1="6" x2="20" y2="6" />
                                                <line x1="4" y1="12" x2="14" y2="12" />
                                                <line x1="4" y1="18" x2="8" y2="18" />
                                                <polyline points="14 15 17 18 20 15" />
                                                <line x1="17" y1="18" x2="17" y2="10" />
                                            </svg>
                                            Ruangan Z-A
                                        </div>
                                        @if(request('sort') === 'ruangan_desc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>

                                    <div class="my-1 border-t border-gray-100"></div>

                                    <!-- Section: DIBUAT -->
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        INPUT DATA</div>
                                    <button type="button"
                                        @click="$refs.sortInput.value='terbaru'; $el.closest('form').submit();"
                                        class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('sort', 'terbaru') === 'terbaru' ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <line x1="4" y1="6" x2="20" y2="6" />
                                                <line x1="4" y1="12" x2="14" y2="12" />
                                                <line x1="4" y1="18" x2="8" y2="18" />
                                                <polyline points="14 15 17 18 20 15" />
                                                <line x1="17" y1="18" x2="17" y2="10" />
                                            </svg>
                                            Baru Dibuat
                                        </div>
                                        @if(request('sort', 'terbaru') === 'terbaru')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
            <div class="mp-card-body" x-data="{ 
                selectedIds: [],
                toggleAll() {
                    let checkboxes = document.querySelectorAll('.bulk-checkbox');
                    if (this.selectedIds.length === checkboxes.length) {
                        this.selectedIds = [];
                    } else {
                        this.selectedIds = Array.from(checkboxes).map(cb => cb.value);
                    }
                },
                get allSelected() {
                    let checkboxes = document.querySelectorAll('.bulk-checkbox');
                    return checkboxes.length > 0 && this.selectedIds.length === checkboxes.length;
                },
                submitBulkDelete() {
                    if (confirm('Yakin ingin menghapus permanen ' + this.selectedIds.length + ' jadwal terpilih?')) {
                        $refs.bulkForm.submit();
                    }
                }
             }">

                <form x-ref="bulkForm" action="{{ route('eoffice.peminjaman.admin.jadwal-internal.bulk-destroy') }}"
                    method="POST">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <div x-show="selectedIds.length > 0" style="display: none;" x-transition
                        class="bg-red-50/80 px-4 py-2.5 border-b border-red-100 flex items-center justify-between">
                        <span class="text-red-700 text-[13px] font-bold"><span x-text="selectedIds.length"></span>
                            Jadwal Terpilih</span>
                        <button type="button" @click="submitBulkDelete"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-[12px] font-bold shadow-sm transition-colors cursor-pointer">
                            Hapus Terpilih
                        </button>
                    </div>
                </form>
                <div class="mp-table-wrap" @scroll.passive="$dispatch('close-action-dropdowns')">
                    <table class="mp-table">
                        <thead>
                            <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                                <th style="padding:11px 16px; text-align:center; width: 40px;">
                                    <input type="checkbox" @click="toggleAll" :checked="allSelected"
                                        class="rounded border-slate-300 text-red-600 focus:ring-red-600 cursor-pointer w-4 h-4">
                                </th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Tanggal</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Waktu</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Kegiatan</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Ruangan</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $j)
                                <tr class="mp-tr" :class="{'bg-red-50/30': selectedIds.includes('{{ $j->id }}')}">
                                    <td style="text-align: center;">
                                        <input type="checkbox" name="ids[]" value="{{ $j->id }}" x-model="selectedIds"
                                            class="bulk-checkbox rounded border-slate-300 text-red-600 focus:ring-red-600 cursor-pointer w-4 h-4">
                                    </td>
                                    <td>
                                        @if($j->tipe_jadwal === 'rutin')
                                            @php
                                                $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                            @endphp
                                            <div style="font-weight: 600; color:#3730A3;">
                                                {{ $namaHari[$j->hari] ?? 'Tidak Valid' }}
                                            </div>
                                        @else
                                            <div style="font-weight: 600; color:#3730A3;">
                                                {{ \Carbon\Carbon::parse($j->tanggal_spesifik)->translatedFormat('d M Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; color: #0D0D12;">
                                            {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            style="font-size: 13px; font-weight: 500; color:#0D0D12; white-space: normal; word-wrap: break-word; max-width: 300px;">
                                            {{ $j->keterangan ?: '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; color: #0D0D12;">
                                            {{ $j->ruangan->nama ?? 'Tidak Diketahui' }}
                                            <span
                                                style="font-size: 12px; color: #666D80; margin-left: 4px; font-weight: 500;">(Lt.
                                                {{ $j->ruangan->lantai ?? '-' }})</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;"
                                        x-data="{ showDropdown: false, showEditModal: false, showDeleteModal: false, formType: '{{ $j->tipe_jadwal }}', kategoriType: '{{ $j->kategori }}', ruanganId: '{{ $j->ruangan_id }}' }">
                                        <div class="relative inline-flex flex-col items-center justify-center w-full"
                                            x-data="{ 
                                                dropdownStyles: '',
                                                initialX: 0,
                                                initialY: 0,
                                                checkScroll() {
                                                    if (!this.showDropdown) return;
                                                    const rect = this.$el.getBoundingClientRect();
                                                    const diffX = Math.abs(rect.left - this.initialX);
                                                    const diffY = Math.abs(rect.top - this.initialY);
                                                    if (diffX > 30 || diffY > 30) {
                                                        this.showDropdown = false;
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
                                                    showDropdown = !showDropdown;
                                                "
                                                @click.away="showDropdown = false"
                                                class="inline-flex items-center justify-center w-[32px] h-[32px] rounded-lg border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                                    <circle cx="5" cy="12" r="1.5"></circle>
                                                    <circle cx="12" cy="12" r="1.5"></circle>
                                                    <circle cx="19" cy="12" r="1.5"></circle>
                                                </svg>
                                            </button>

                                            <div x-show="showDropdown" style="display:none;"
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

                                                <button type="button" @click="showEditModal = true; showDropdown = false"
                                                    class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors">
                                                    <svg class="w-[14px] h-[14px] text-gray-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Edit Jadwal
                                                </button>

                                                <form
                                                    action="{{ route('eoffice.peminjaman.admin.jadwal-internal.destroy', $j->id) }}"
                                                    method="POST" id="deleteForm-{{ $j->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        @click="showDeleteModal = true; showDropdown = false"
                                                        class="w-full text-left px-2.5 py-1.5 mt-0.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors">
                                                        <svg class="w-[14px] h-[14px] text-red-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Hapus Jadwal
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- DELETE MODAL -->
                                        <div x-show="showDeleteModal" style="display: none;"
                                            class="fixed inset-0 z-[100] overflow-y-auto text-left whitespace-normal"
                                            aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="showDeleteModal"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"
                                                    class="fixed inset-0 transition-opacity bg-slate-900/60 "
                                                    aria-hidden="true" @click="showDeleteModal = false"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                                    aria-hidden="true">&#8203;</span>

                                                <div x-show="showDeleteModal"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-center align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6 relative">

                                                    <div
                                                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4">
                                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-base font-bold text-slate-800 mb-2">Hapus Jadwal?</h3>
                                                    <p class="text-xs text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin
                                                        ingin menghapus blokir jadwal ini? Tindakan ini tidak dapat
                                                        dikembalikan.</p>

                                                    <div class="flex justify-center gap-3">
                                                        <button type="button" @click="showDeleteModal = false"
                                                            class="mp-btn secondary px-4 py-2">Batal</button>
                                                        <button type="button"
                                                            @click="document.getElementById('deleteForm-{{ $j->id }}').submit()"
                                                            class="mp-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white border-transparent"
                                                            style="border:none;">Hapus Jadwal</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EDIT MODAL -->
                                        <!-- EDIT MODAL -->
                                        <div x-show="showEditModal" style="display: none;"
                                            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
                                            aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                            <div x-show="showEditModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 transition-opacity bg-slate-900/60"
                                                aria-hidden="true" @click="showEditModal = false"></div>

                                            <!-- 2. MODAL PANEL (Kotak Utama Modal) -->
                                            <div x-show="showEditModal"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[600px] max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden z-10 text-left">
                                                
                                                <div class="p-4 sm:p-6 flex flex-col flex-1 min-h-0">
                                                    <div class="-mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center shadow-sm">
                                                                <span class="material-symbols-outlined text-white" style="font-size:20px">edit</span>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Edit Blokir Ruangan</h3>
                                                                <p class="text-[10px] text-[#0B266E] font-medium">Perbarui informasi blokir</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="showEditModal = false"
                                                            class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                                                        </button>
                                                    </div>

                                                    <form
                                                        action="{{ route('eoffice.peminjaman.admin.jadwal-internal.update', $j->id) }}"
                                                        method="POST" class="flex flex-col flex-1 min-h-0">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="space-y-4 overflow-y-auto overflow-x-hidden pr-2 -mr-2 pb-2 flex-1 relative h-full min-h-0 whitespace-normal">
                                                            
                                                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl mx-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Informasi Kegiatan</span>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                    <!-- Tipe Ruangan -->
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tipe Ruangan</label>
                                                                        <div x-data="{ 
                                                                                        open: false,
                                                                                        get selectedName() {
                                                                                            const room = [
                                                                                                @foreach($ruangans as $r)
                                                                                                    {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }} - Lt. {{ $r->lantai }}'},
                                                                                                @endforeach
                                                                                            ].find(r => r.id == ruanganId);
                                                                                            return room ? room.name : '-- Pilih Ruangan --';
                                                                                        },
                                                                                        selectItem(id) { 
                                                                                            ruanganId = id;
                                                                                            this.open = false; 
                                                                                        } 
                                                                                    }" class="relative w-full"
                                                                            :class="{'z-50': open, 'z-10': !open}"
                                                                            @click.away="open = false">

                                                                            <input type="hidden" name="ruangan_id"
                                                                                :value="ruanganId" required>

                                                                            <button type="button" @click="open = !open"
                                                                                class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                                <span x-text="selectedName" class="truncate"
                                                                                    :class="{'text-gray-400': !ruanganId, 'text-gray-800': ruanganId}"></span>
                                                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                                                                                    :class="{'rotate-180': open}" fill="none"
                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 9l-7 7-7-7" />
                                                                                </svg>
                                                                            </button>

                                                                            <div x-show="open" x-cloak
                                                                                x-transition:enter="transition ease-out duration-100"
                                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                                x-transition:leave="transition ease-in duration-75"
                                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                                x-transition:leave-end="opacity-0 scale-95"
                                                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[9999] max-h-48 overflow-y-auto overflow-x-hidden"
                                                                                style="display: none;">
                                                                                <div class="p-1 flex flex-col">
                                                                                    <button type="button"
                                                                                        @click="selectItem('')"
                                                                                        class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">--
                                                                                        Pilih Ruangan --</button>
                                                                                    @foreach($ruangans as $r)
                                                                                        <button type="button"
                                                                                            @click="selectItem('{{ $r->id }}')"
                                                                                            class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '{{ $r->id }}', 'text-gray-700 hover:bg-gray-50': ruanganId != '{{ $r->id }}'}">
                                                                                            {{ $r->nama }} - Lt. {{ $r->lantai }}
                                                                                        </button>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Kategori -->
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori Kegiatan</label>
                                                                        <div x-data="{ 
                                                                                        open: false,
                                                                                        get selectedName() {
                                                                                            const map = {
                                                                                                'Pindah Kelas': 'Pindah / Pengganti Kelas',
                                                                                                'Maintenance / Perbaikan': 'Maintenance / Perbaikan Ruangan',
                                                                                                'Sterilisasi Ruangan': 'Sterilisasi / Persiapan Ruangan',
                                                                                                'Penutupan Khusus': 'Penutupan Khusus / Libur Nasional',
                                                                                                'Ujian / Evaluasi': 'Ujian / Evaluasi (UTS/UAS)',
                                                                                                'Lainnya': 'Lainnya...'
                                                                                            };
                                                                                            return map[kategoriType] || 'Pilih Kategori...';
                                                                                        },
                                                                                        selectItem(val) { 
                                                                                            kategoriType = val;
                                                                                            this.open = false; 
                                                                                        } 
                                                                                    }" class="relative w-full"
                                                                            :class="{'z-50': open, 'z-10': !open}"
                                                                            @click.away="open = false">

                                                                            <input type="hidden" name="kategori"
                                                                                :value="kategoriType" required>

                                                                            <button type="button" @click="open = !open"
                                                                                class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                                <span x-text="selectedName" class="truncate"
                                                                                    :class="{'text-gray-400': !kategoriType, 'text-gray-800': kategoriType}"></span>
                                                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                                                                                    :class="{'rotate-180': open}" fill="none"
                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 9l-7 7-7-7" />
                                                                                </svg>
                                                                            </button>

                                                                            <div x-show="open" x-cloak
                                                                                x-transition:enter="transition ease-out duration-100"
                                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                                x-transition:enter-end="opacity-100 scale-100"
                                                                                x-transition:leave="transition ease-in duration-75"
                                                                                x-transition:leave-start="opacity-100 scale-100"
                                                                                x-transition:leave-end="opacity-0 scale-95"
                                                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[9999] max-h-48 overflow-y-auto"
                                                                                style="display: none;">
                                                                                <div class="p-1 flex flex-col">
                                                                                    <button type="button"
                                                                                        @click="selectItem('Pindah Kelas')"
                                                                                        class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Pindah Kelas', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Pindah Kelas'}">Pindah / Pengganti Kelas</button>
                                                                                    <button type="button"
                                                                                        @click="selectItem('Maintenance / Perbaikan')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Maintenance / Perbaikan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Maintenance / Perbaikan'}">Maintenance / Perbaikan Ruangan</button>
                                                                                    <button type="button"
                                                                                        @click="selectItem('Sterilisasi Ruangan')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Sterilisasi Ruangan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Sterilisasi Ruangan'}">Sterilisasi / Persiapan Ruangan</button>
                                                                                    <button type="button"
                                                                                        @click="selectItem('Penutupan Khusus')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Penutupan Khusus', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Penutupan Khusus'}">Penutupan Khusus / Libur Nasional</button>
                                                                                    <button type="button"
                                                                                        @click="selectItem('Ujian / Evaluasi')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Ujian / Evaluasi', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Ujian / Evaluasi'}">Ujian / Evaluasi (UTS/UAS)</button>
                                                                                    <button type="button"
                                                                                        @click="selectItem('Lainnya')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Lainnya', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Lainnya'}">Lainnya...</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="space-y-1.5 sm:col-span-2">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kegiatan</label>
                                                                        <input type="text" name="keterangan"
                                                                            value="{{ $j->keterangan }}" required autocomplete="off"
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]"
                                                                            placeholder="Misal: Rapat Evaluasi Kurikulum...">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl mx-1 mt-3">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Waktu & Tanggal</span>
                                                                </div>

                                                                <input type="hidden" name="tipe_jadwal"
                                                                    value="{{ $j->tipe_jadwal }}">

                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div class="col-span-2 space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Spesifik</label>
                                                                        <input type="date" name="tanggal_spesifik"
                                                                            value="{{ $j->tanggal_spesifik }}" required
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                    </div>

                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                                                                        <input type="time" name="jam_mulai"
                                                                            value="{{ substr($j->jam_mulai, 0, 5) }}" required 
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                    </div>
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Akhir</label>
                                                                        <input type="time" name="jam_selesai"
                                                                            value="{{ substr($j->jam_selesai, 0, 5) }}" required 
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                    </div>
                                                                    <div class="col-span-2 text-[10px] text-slate-500 mt-0.5 ml-1">
                                                                        <p class="leading-relaxed"><b>Info:</b> Jam operasional peminjaman reguler adalah <b>07:00 - 17:00</b>. Sebagai Admin, Anda bebas melewati batas ini untuk pemblokiran 24 jam jika diperlukan.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Mobile Footer (Inside scrollable area so it doesn't follow keyboard) -->
                                                            <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                                                <button type="button" @click="showEditModal = false"
                                                                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                                                    Batal
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer">
                                                                    <span>Simpan Perubahan</span>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Desktop Sticky Footer -->
                                                        <div class="hidden sm:flex -mx-4 sm:-mx-6 -mb-4 sm:-mb-6 px-4 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/50 mt-4 flex-row items-center justify-end gap-3 shrink-0 rounded-b-none sm:rounded-b-[20px]">
                                                            <button type="button" @click="showEditModal = false"
                                                                class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                                                Batal
                                                            </button>
                                                            <button type="submit"
                                                                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer">
                                                                <span>Simpan Perubahan</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 text-[13px]">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E2E8F0"
                                            stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-3">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <div class="font-bold text-[14px]">Belum Ada Jadwal Internal & Akademik
                                        </div>
                                        <div class="text-[12px] mt-1 text-gray-400">Gunakan tombol Tambah Jadwal di pojok
                                            kanan
                                            atas
                                            untuk mulai mengunci ruangan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 mt-2 rounded-b-[12px]">
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
                            Menampilkan <span class="font-bold text-slate-800">{{ $jadwals->firstItem() ?? 0 }}</span>
                            sampai <span class="font-bold text-slate-800">{{ $jadwals->lastItem() ?? 0 }}</span>
                            dari <span class="font-bold text-slate-800">{{ $jadwals->total() }}</span> entri
                        </p>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
                        @if ($jadwals->onFirstPage())
                            <button disabled
                                class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $jadwals->previousPageUrl() }}"
                                class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        @php
                            $startPage = max(1, $jadwals->currentPage() - 1);
                            $endPage = min($jadwals->lastPage(), $jadwals->currentPage() + 1);

                            // Adjust start and end to always show at least 3 pages if possible
                            if ($endPage - $startPage < 2) {
                                if ($startPage == 1) {
                                    $endPage = min($jadwals->lastPage(), 3);
                                } elseif ($endPage == $jadwals->lastPage()) {
                                    $startPage = max(1, $jadwals->lastPage() - 2);
                                }
                            }
                        @endphp

                        @if($startPage > 1)
                            <a href="{{ $jadwals->url(1) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                            @if($startPage > 2)
                                <span
                                    class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                        @endif

                        @foreach ($jadwals->getUrlRange($startPage, $endPage) as $page => $url)
                            @if ($page == $jadwals->currentPage())
                                <span
                                    class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($endPage < $jadwals->lastPage())
                            @if($endPage < $jadwals->lastPage() - 1)
                                <span
                                    class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                            @endif
                            <a href="{{ $jadwals->url($jadwals->lastPage()) }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $jadwals->lastPage() }}</a>
                        @endif

                        @if ($jadwals->hasMorePages())
                            <a href="{{ $jadwals->nextPageUrl() }}"
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

        </div> <!-- Close Alpine Wrapper -->
    </div>
</x-eoffice::manajemen-ruangan.layout>