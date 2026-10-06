<x-eoffice::manajemen-ruangan.layout
    pageTitle="{{ $viewMode === 'akademik' ? 'Jadwal Akademik' : 'Kelola Blokir Ruangan' }}">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />

    <div x-data="{ 
        showModal: false, 
        showImportModal: false, 
        formType: '{{ $viewMode === 'akademik' ? 'rutin' : 'spesifik' }}', 
        kategoriType: '{{ $viewMode === 'akademik' ? 'Jadwal Akademik (Kuliah)' : 'Maintenance / Perbaikan' }}',
        ruanganId: '',
        currentDay: '1',
        jamMulai: '',
        jamSelesai: '',
        conflictError: false,
        isCheckingOut: false,
        checkTimeout: null,
        
        triggerCheck() {
            if(this.checkTimeout) clearTimeout(this.checkTimeout);
            this.isCheckingOut = true;
            this.checkTimeout = setTimeout(() => {
                this.executeCheck();
            }, 500);
        },
        
        akademikConflicts: [],
        peminjamanConflicts: [],
        
        async executeCheck() {
            if (!this.ruanganId || !this.jamMulai || !this.jamSelesai) {
                this.isCheckingOut = false;
                this.conflictError = false;
                this.akademikConflicts = [];
                this.peminjamanConflicts = [];
                return;
            }
            
            try {
                let url = `{{ route('eoffice.peminjaman.admin.jadwal-internal.check-collision') }}?ruangan_id=${this.ruanganId}&tipe_jadwal=${this.formType}&kategori=${this.kategoriType}&hari=${this.currentDay}&jam_mulai=${this.jamMulai}&jam_selesai=${this.jamSelesai}`;
                let res = await fetch(url);
                let data = await res.json();
                
                if (data.conflict) {
                    this.conflictError = true;
                    this.akademikConflicts = data.akademik_details || [];
                    this.peminjamanConflicts = data.peminjaman_details || [];
                } else {
                    this.conflictError = false;
                    this.akademikConflicts = [];
                    this.peminjamanConflicts = [];
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isCheckingOut = false;
            }
        },

        resetForm() {
            this.ruanganId = '';
            this.currentDay = '1';
            this.jamMulai = '';
            this.jamSelesai = '';
            this.conflictError = false;
            this.akademikConflicts = [];
            this.peminjamanConflicts = [];
        }
    }">
        <div class="mp-page-header flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                @if($viewMode === 'akademik')
                    <h1 class="mp-page-title">Kelola Jadwal Akademik</h1>
                    <p class="mp-page-sub">Atur dan import blocking waktu khusus untuk agenda perkuliahan rutin Fakultas.
                    </p>
                @else
                    <h1 class="mp-page-title">Kelola Blokir Ruangan</h1>
                    <p class="mp-page-sub">Atur blocking waktu insidental untuk rapat dosen, acara himpunan, atau perawatan
                        ruangan.</p>
                @endif
            </div>
            <div class="mp-page-actions flex flex-wrap items-center gap-2 w-full md:w-auto">
                <!-- Filter Dropdown -->
                @if($viewMode !== 'akademik')
                    <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" method="GET"
                        class="flex gap-2">
                        <div x-data="{ 
                                            open: false, 
                                            selectedVal: '{{ $tipe }}', 
                                            get selectedName() {
                                                const map = {
                                                    'semua': 'Semua Tipe Jadwal',
                                                    'rutin': 'Rutin (Mingguan)',
                                                    'spesifik': 'Spesifik (Acara)'
                                                };
                                                return map[this.selectedVal] || 'Semua Tipe Jadwal';
                                            },
                                            selectItem(val) { 
                                                this.selectedVal = val; 
                                                $refs.tipeInput.value = val;
                                                $refs.tipeInput.form.submit();
                                            } 
                                        }" class="relative w-48" @click.away="open = false">

                            <input type="hidden" name="tipe" x-ref="tipeInput" :value="selectedVal">

                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between mp-input !py-2 !text-xs !bg-white focus:outline-none transition-colors">
                                <span x-text="selectedName" class="truncate pr-2"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0"
                                    :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50 overflow-hidden"
                                style="display: none;">
                                <div class="p-1">
                                    <button type="button" @click="selectItem('semua')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'semua', 'text-gray-700 hover:bg-gray-50': selectedVal != 'semua'}">Semua
                                        Tipe Jadwal</button>
                                    <button type="button" @click="selectItem('rutin')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors mt-0.5"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'rutin', 'text-gray-700 hover:bg-gray-50': selectedVal != 'rutin'}">Rutin
                                        (Mingguan)</button>
                                    <button type="button" @click="selectItem('spesifik')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors mt-0.5"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'spesifik', 'text-gray-700 hover:bg-gray-50': selectedVal != 'spesifik'}">Spesifik
                                        (Acara)</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if($viewMode === 'akademik')
                    <div class="inline-block" x-data="{ showDeleteAllModal: false }">
                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.reset') }}" method="POST"
                            class="m-0">
                            @csrf
                            @method('DELETE')
                            @php
                                $isJadwalKosong = \Modules\EOffice\Models\MrJadwalInternal::where('kategori', 'Jadwal Akademik (Kuliah)')->count() === 0;
                            @endphp

                            @if($isJadwalKosong)
                                <button type="button" disabled
                                    title="Tidak ada jadwal akademik yang bisa dihapus (data kosong)."
                                    class="mp-btn md bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed shadow-none opacity-80"
                                    style="pointer-events: auto;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Hapus
                                </button>
                            @else
                                <button type="button" @click="showDeleteAllModal = true"
                                    class="mp-btn md bg-white border border-red-200 text-red-600 hover:bg-red-50 transition-colors shadow-sm cursor-pointer">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Hapus
                                </button>
                            @endif

                            {{-- Decision Modal --}}
                            <div x-show="showDeleteAllModal" x-cloak style="display: none;"
                                class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal">
                                <div x-show="showDeleteAllModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 bg-slate-900/60 transition-opacity"
                                    @click="showDeleteAllModal = false"></div>

                                <div x-show="showDeleteAllModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                    class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[500px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-5 sm:p-6 overflow-hidden z-10 text-left">
                                    <div class="flex flex-col flex-1 min-h-0">
                        <div class="-mx-5 sm:-mx-6 -mt-5 sm:-mt-6 mb-5 px-6 py-4 border-b border-red-600/10 flex items-center justify-between bg-red-600/[0.06] rounded-t-[24px] sm:rounded-t-[20px]">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-red-600 flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Hapus Seluruh Jadwal</h3>
                                    <p class="text-[10px] text-red-600 font-medium">Peringatan bahaya! Tindakan ini permanen</p>
                                </div>
                            </div>
                            <button type="button" @click="showDeleteAllModal = false" class="text-red-600 hover:text-red-700 hover:bg-red-600/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                <span class="material-symbols-outlined" style="font-size:20px">close</span>
                            </button>
                        </div>

                                        <div class="overflow-y-auto pr-1 -mr-1 pb-2">
                                            <div class="mb-5">
                                                <p class="text-[13px] leading-relaxed text-gray-600 m-0">
                                                    Apakah Anda yakin ingin <strong class="text-gray-800">MENGHAPUS SELURUH</strong> jadwal akademik di sistem secara massal? Aksi ini lazimnya hanya dilakukan pada saat reset pergantian semester. Tindakan ini tidak dapat dikembalikan.
                                                </p>
                                            </div>

                                            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-gray-100">
                                                <button type="button" @click="showDeleteAllModal = false"
                                                    class="w-full sm:flex-1 flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all cursor-pointer">Batal</button>
                                                <button type="submit" class="w-full sm:flex-1 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all gap-1.5 cursor-pointer border-0">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <button @click="showImportModal = true" 
                        class="mp-btn md bg-white border border-gray-200 text-[#0B266E] hover:bg-gray-50 transition-colors shadow-sm cursor-pointer">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Import Excel
                    </button>
                @endif

                <button @click="showModal = true" class="mp-btn primary md">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24">
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
                                <span class="material-symbols-outlined text-white" style="font-size:20px">event_note</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Tambah Jadwal Baru</h3>
                                <p class="text-[10px] text-[#0B266E] font-medium">Buat plotting jadwal kelas baru</p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                        </button>
                    </div>

                    <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.store') }}" method="POST"
                        @submit="if(isCheckingOut || conflictError) { $event.preventDefault(); return false; }"
                        class="flex flex-col flex-1 min-h-0">
                        @csrf
                        <div
                            class="space-y-4 overflow-y-auto overflow-x-hidden pl-1 -ml-1 pr-2 -mr-2 pb-2 flex-1 whitespace-normal">
                            <div class="space-y-1.5">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih
                                    Ruangan</label>
                                <div x-data="{ 
                                            open: false,
                                            search: '',
                                            rooms: [
                                                @foreach($ruangans as $r)
                                                    {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }} - Lt. {{ $r->lantai }}'},
                                                @endforeach
                                            ],
                                            get filteredRooms() {
                                                if (this.search === '') return this.rooms;
                                                const currentRoom = this.rooms.find(r => r.id == ruanganId);
                                                if (currentRoom && this.search === currentRoom.name) return this.rooms;
                                                return this.rooms.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                                            },
                                            selectItem(id) { 
                                                ruanganId = id;
                                                this.open = false; 
                                                if (id) {
                                                    const room = this.rooms.find(r => r.id == id);
                                                    this.search = room ? room.name : '';
                                                } else {
                                                    this.search = '';
                                                }
                                                triggerCheck();
                                            },
                                            init() {
                                                this.$watch('search', (val) => {
                                                    if (val === '') {
                                                        ruanganId = '';
                                                    } else {
                                                        const currentRoom = this.rooms.find(r => r.id == ruanganId);
                                                        if (currentRoom && val !== currentRoom.name) {
                                                            ruanganId = '';
                                                        }
                                                    }
                                                });
                                                this.$watch('ruanganId', (val) => {
                                                    if (!val) {
                                                        this.search = '';
                                                    } else {
                                                        const room = this.rooms.find(r => r.id == val);
                                                        if(room) this.search = room.name;
                                                    }
                                                });
                                            }
                                        }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}"
                                    @click.away="open = false; if(!ruanganId) search = ''; else search = rooms.find(r => r.id == ruanganId)?.name || ''">

                                    <input type="hidden" name="ruangan_id" :value="ruanganId" required>

                                    <div class="relative flex items-center">
                                        <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                            placeholder="-- Pilih atau Ketik Ruangan --" autocomplete="off"
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px] text-gray-800">
                                        <div class="absolute right-3 pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4 transition-transform duration-200"
                                                :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-60 flex flex-col overflow-hidden"
                                        style="display: none;">
                                        
                                        <div class="p-1 flex flex-col overflow-y-auto overflow-x-hidden flex-1 min-h-0">
                                            <button type="button" @click="selectItem('')" x-show="search === ''"
                                                class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">--
                                                Pilih Ruangan Kelas --</button>

                                            <template x-for="r in filteredRooms" :key="r.id">
                                                <button type="button" @click="selectItem(r.id)"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == r.id, 'text-gray-700 hover:bg-gray-50': ruanganId != r.id}"
                                                    x-text="r.name">
                                                </button>
                                            </template>

                                            <div x-show="filteredRooms.length === 0" class="px-3 py-4 text-xs text-center text-gray-500">
                                                Ruangan tidak ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="tipe_jadwal" value="rutin">
                            <input type="hidden" name="kategori" value="Jadwal Akademik (Kuliah)">
                            <input type="hidden" name="keterangan" value="Matkul Akademik">

                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Informasi Mata Kuliah</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata
                                            Kuliah</label>
                                        <input type="text" name="mata_kuliah" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            placeholder="Nama Lengkap Matkul">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kelas</label>
                                        <input type="text" name="kelas" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            placeholder="A/B/C/D">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Waktu & Tanggal</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Hari
                                            Pertemuan</label>
                                    <div x-data="{ 
                                                open: false,
                                                get selectedName() {
                                                    const map = {
                                                        '1': 'Senin', '2': 'Selasa', '3': 'Rabu',
                                                        '4': 'Kamis', '5': 'Jumat', '6': 'Sabtu', '7': 'Minggu'
                                                    };
                                                    return map[currentDay] || 'Pilih Hari...';
                                                },
                                                selectItem(val) { 
                                                    currentDay = val;
                                                    this.open = false; 
                                                    triggerCheck();
                                                } 
                                            }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}"
                                        @click.away="open = false">

                                        <input type="hidden" name="hari" :value="currentDay" required>

                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                            <span x-text="selectedName" class="truncate text-gray-800"></span>
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
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto overflow-x-hidden"
                                            style="display: none;">
                                            <div class="p-1 flex flex-col">
                                                <button type="button" @click="selectItem('1')"
                                                    class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '1', 'text-gray-700 hover:bg-gray-50': currentDay != '1'}">Senin</button>
                                                <button type="button" @click="selectItem('2')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '2', 'text-gray-700 hover:bg-gray-50': currentDay != '2'}">Selasa</button>
                                                <button type="button" @click="selectItem('3')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '3', 'text-gray-700 hover:bg-gray-50': currentDay != '3'}">Rabu</button>
                                                <button type="button" @click="selectItem('4')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '4', 'text-gray-700 hover:bg-gray-50': currentDay != '4'}">Kamis</button>
                                                <button type="button" @click="selectItem('5')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '5', 'text-gray-700 hover:bg-gray-50': currentDay != '5'}">Jumat</button>
                                                <button type="button" @click="selectItem('6')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '6', 'text-gray-700 hover:bg-gray-50': currentDay != '6'}">Sabtu</button>
                                                <button type="button" @click="selectItem('7')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '7', 'text-gray-700 hover:bg-gray-50': currentDay != '7'}">Minggu</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                                        <input type="time" name="jam_mulai" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            x-model="jamMulai" @input="triggerCheck()">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Akhir</label>
                                        <input type="time" name="jam_selesai" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            x-model="jamSelesai" @input="triggerCheck()">
                                    </div>
                                </div>
                            </div>
                            </div>

                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Periode Semester Masa Aktif Jadwal</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dimulai
                                            Sejak</label>
                                        <input type="date" name="tgl_mulai_efektif" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Berakhir
                                            Pada</label>
                                        <input type="date" name="tgl_selesai_efektif" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                                    </div>
                                </div>
                            </div>


                            <div x-show="conflictError" x-cloak x-transition
                                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex flex-col gap-2 text-red-700 text-[13px]">
                                <div class="font-bold">
                                    <span>Gagal Menyimpan! Terdapat Jadwal Beririsan:</span>
                                </div>
                                <div class="flex flex-col gap-1.5 mt-1">
                                    <template x-for="c in akademikConflicts" :key="c.nama">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <strong x-text="c.nama"></strong>
                                            <span class="text-red-600/80" x-text="'('+c.waktu+')'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Mobile Footer (Scrolls with content) -->
                            <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="button" @click="showModal = false; resetForm()"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" :disabled="conflictError || isCheckingOut"
                                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span>Simpan Jadwal</span>
                                </button>
                            </div>
                        </div>

                        <!-- Desktop Sticky Footer -->
                        <div class="hidden sm:flex mt-4 -mx-6 -mb-6 px-6 py-4 border-t border-slate-100 bg-slate-50/50 items-center justify-end gap-3 shrink-0 rounded-b-[20px]">
                            <button type="button" @click="showModal = false; resetForm()"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="conflictError || isCheckingOut"
                                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <span>Simpan Jadwal</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Excel Import Modal -->
        <div x-show="showImportModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <div x-show="showImportModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                @click="showImportModal = false"></div>

            <div x-show="showImportModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border border-gray-100 shadow-2xl w-full max-w-[500px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-5 sm:p-6">

                    <div class="flex flex-col flex-1 min-h-0">
                        <div class="-mx-5 sm:-mx-6 -mt-5 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-t-[24px] sm:rounded-t-[20px]">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Impor Jadwal Kuliah</h3>
                                    <p class="text-[10px] text-[#0B266E] font-medium">Upload file jadwal dari sistem akademik (SIAP)</p>
                                </div>
                            </div>
                            <button type="button" @click="showImportModal = false" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                <span class="material-symbols-outlined" style="font-size:20px">close</span>
                            </button>
                        </div>

                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.import-preview') }}"
                            method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                            @csrf
                            
                            <div class="overflow-y-auto px-1 -mx-1 pb-2">
                                <div class="mb-5 px-1" x-data="{ fileName: '' }">
                                    <label class="block mb-2 text-[13px] font-medium text-gray-700">Pilih File CSV / Excel</label>
                                    <div class="relative flex items-center w-full border border-gray-200 rounded-xl overflow-hidden focus-within:border-[#0B266E] focus-within:ring-1 focus-within:ring-[#0B266E] transition-all bg-white shadow-sm">
                                        <input type="file" name="file_excel"
                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                            required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        
                                        <div class="px-4 py-2.5 bg-blue-50 text-[#0B266E] text-[13px] font-bold border-r border-gray-200">
                                            Choose File
                                        </div>
                                        <div class="px-3 py-2.5 text-[13px] text-gray-500 flex-1 truncate" x-text="fileName || 'No file chosen'">
                                        </div>
                                    </div>
                                    <div class="mt-2 text-[12px] text-gray-500">Format: .csv atau .xlsx (Maks. 5MB)</div>
                                </div>

                                <div class="p-4 mb-4 rounded-[14px] border border-[#bbf7d0]" style="background-color: #ecfdf5;">
                                    <div>
                                        <h4 class="text-[13px] font-bold text-[#065f46] mb-1.5">Mekanisme Keamanan Impor</h4>
                                        <p class="text-[12px] leading-relaxed text-[#065f46]/90 m-0">
                                            Seluruh data jadwal yang diunggah tidak langsung diproses ke dalam basis data (database). Sistem akan menampilkan halaman Pratinjau agar pengguna dapat melakukan pengecekan data sebelum konfirmasi akhir.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="showImportModal = false"
                                        class="w-full sm:flex-1 flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all cursor-pointer">Batal</button>
                                    <button type="submit" class="w-full sm:flex-1 flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all gap-1.5 cursor-pointer">
                                        <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Mulai Impor
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>



        <div class="mp-card" style="margin-top: 15px;"
            x-data="{ 
                selectedIds: [], 
                openFilter: false, 
                openSort: false,
                showBulkDeleteModal: false,
                get allSelected() { return this.selectedIds.length === {{ count($jadwals) }} && {{ count($jadwals) }} > 0; }
            }">
            <!-- NEW CARD HEADER MATCHING MOCKUP -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full"
                style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">
                    {{ $viewMode === 'akademik' ? 'Jadwal Akademik' : 'Blokir Ruangan' }}
                </h2>

                @if($viewMode === 'akademik')
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto mt-3 md:mt-0">
                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}" method="GET"
                            class="flex flex-col md:flex-row items-stretch md:items-center gap-2 m-0 relative w-full">
                            <!-- Search Bar -->
                            <div class="relative w-full sm:w-auto" x-data="{ searchQuery: '{{ addslashes(request('search')) }}' }">
                                <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer text-gray-400 hover:text-[#0B266E] transition-colors bg-transparent border-0 outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z">
                                        </path>
                                    </svg>
                                </button>
                                <input type="text" name="search" x-model="searchQuery" 
                                    @input.debounce.500ms="$el.form.submit()"
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
                            <div class="flex flex-row items-stretch gap-2 w-full md:w-auto">
                                <!-- Filter Button -->
                                <div class="relative flex-1 md:flex-none" @click.away="openFilter = false">
                                    <button type="button" @click="openFilter = !openFilter"
                                        class="relative w-full inline-flex justify-center items-center gap-2 px-3 h-[38px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-[13px] font-semibold text-slate-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[#0B266E] whitespace-nowrap cursor-pointer">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                        </svg>
                                        Filter
                                        @if(request('hari') || request('ruangan_id'))
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
                                        
                                        <input type="hidden" name="hari" x-ref="hariInput" value="{{ request('hari') }}">
                                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" value="{{ request('ruangan_id') }}">

                                        <div class="py-1 px-1">
                                            <!-- Section: PILIH HARI -->
                                            <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                PILIH HARI</div>
                                            <button type="button"
                                                @click="$refs.hariInput.value=''; $el.closest('form').submit();"
                                                class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('hari') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                                Semua Hari
                                                @if(!request('hari'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                            @php
                                                $days = [
                                                    '1' => 'Senin', '2' => 'Selasa', '3' => 'Rabu',
                                                    '4' => 'Kamis', '5' => 'Jumat', '6' => 'Sabtu', '7' => 'Minggu'
                                                ];
                                            @endphp
                                            @foreach($days as $val => $label)
                                                <button type="button"
                                                    @click="$refs.hariInput.value='{{ $val }}'; $el.closest('form').submit();"
                                                    class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ request('hari') == $val ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                                    {{ $label }}
                                                    @if(request('hari') == $val)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach

                                            <div class="my-1 border-t border-gray-100"></div>

                                            <!-- Section: PILIH RUANGAN -->
                                            <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                PILIH RUANGAN</div>
                                            <button type="button"
                                                @click="$refs.ruanganInput.value=''; $el.closest('form').submit();"
                                                class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ !request('ruangan_id') ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                                Semua Ruangan
                                                @if(!request('ruangan_id'))
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
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
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Sort By Button -->
                                <div class="relative flex-1 md:flex-none" @click.away="openSort = false">
                                    <button type="button" @click="openSort = !openSort"
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
                                        
                                        <input type="hidden" name="sort" x-ref="sortInput" value="{{ request('sort', 'waktu') }}">

                                        <div class="py-1 px-1">
                                            <!-- Section: URUTAN -->
                                            <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                URUTKAN BERDASARKAN</div>
                                            @php
                                                $sorts = [
                                                    'waktu' => 'Hari & Waktu Terawal',
                                                    'matkul_asc' => 'Mata Kuliah (A-Z)',
                                                    'matkul_desc' => 'Mata Kuliah (Z-A)',
                                                    'ruangan' => 'Ruangan (A-Z)',
                                                    'terbaru' => 'Waktu Ditambahkan'
                                                ];
                                                $currentSort = request('sort', 'waktu');
                                            @endphp
                                            @foreach($sorts as $val => $label)
                                                <button type="button"
                                                    @click="$refs.sortInput.value='{{ $val }}'; $el.closest('form').submit();"
                                                    class="w-full text-left px-3 py-1.5 text-[13px] rounded-md transition-colors flex items-center justify-between {{ $currentSort == $val ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                                                    {{ $label }}
                                                    @if($currentSort == $val)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="mp-card-body">
                <form x-ref="bulkForm" action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.bulk-destroy') }}" method="POST">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <div x-show="selectedIds.length > 0" style="display: none;" x-transition class="bg-red-50/80 px-4 py-2.5 border-b border-red-100 flex items-center justify-between">
                        <span class="text-red-700 text-[13px] font-bold"><span x-text="selectedIds.length"></span> Jadwal Terpilih</span>
                        <button type="button" @click="showBulkDeleteModal = true" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-[12px] font-bold shadow-sm transition-colors cursor-pointer">
                            Hapus Terpilih
                        </button>
                    </div>

                    <!-- BULK DELETE MODAL -->
                    <div x-show="showBulkDeleteModal" x-cloak style="display: none;"
                        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal">
                        
                        <!-- Backdrop -->
                        <div x-show="showBulkDeleteModal" 
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" 
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" 
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-slate-900/60 transition-opacity"
                            @click="showBulkDeleteModal = false"></div>

                        <!-- Modal Content -->
                        <div x-show="showBulkDeleteModal" 
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                            class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[500px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-5 sm:p-6 overflow-hidden z-10 text-left">
                            
                            <div class="flex flex-col flex-1 min-h-0">
                                <!-- Header Modal -->
                                <div class="-mx-5 sm:-mx-6 -mt-5 sm:-mt-6 mb-5 px-6 py-4 border-b border-red-600/10 flex items-center justify-between bg-red-600/[0.06] rounded-t-[24px] sm:rounded-t-[20px]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-red-600 flex items-center justify-center shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Hapus Jadwal Terpilih</h3>
                                            <p class="text-[10px] text-red-600 font-medium">Tindakan ini tidak dapat dikembalikan</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="showBulkDeleteModal = false" class="text-red-600 hover:text-red-700 hover:bg-red-600/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                        <span class="material-symbols-outlined" style="font-size:20px">close</span>
                                    </button>
                                </div>

                                <!-- Body Modal -->
                                <div class="overflow-y-auto pb-1">
                                    <div class="mb-5">
                                        <p class="text-[13px] leading-relaxed text-gray-600 m-0">
                                            Apakah Anda yakin ingin menghapus permanen <strong class="text-red-600" x-text="selectedIds.length + ' jadwal'"></strong> yang telah dipilih? Tindakan ini akan menghapus semua jadwal tersebut secara permanen.
                                        </p>
                                    </div>

                                    <!-- Footer Modal -->
                                    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-gray-100">
                                        <button type="button" @click="showBulkDeleteModal = false"
                                            class="w-full sm:flex-1 flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all cursor-pointer">Batal</button>
                                        <button type="button" @click="$refs.bulkForm.submit()" 
                                            class="w-full sm:flex-1 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all gap-1.5 cursor-pointer border-0">
                                            Hapus Terpilih
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mp-table-wrap" @scroll.passive="$dispatch('close-action-dropdowns')">
                    <table class="mp-table">
                        <thead>
                            <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                                <th style="padding:11px 16px; text-align:center; width: 40px;">
                                    <input type="checkbox"
                                        class="form-checkbox w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer"
                                        :checked="allSelected"
                                        @change="if($event.target.checked) { selectedIds = {{ $jadwals->pluck('id')->map(fn($id) => (string) $id)->toJson() }} } else { selectedIds = [] }">
                                </th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Hari</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Waktu</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Mata Kuliah</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Kelas</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Ruangan</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">
                                    Periode</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $j)
                                <tr class="mp-table-row transition-colors"
                                    :class="{ 'bg-blue-50/30': selectedIds.includes('{{ $j->id }}') }">
                                    <td style="padding:11px 16px; text-align:center;">
                                        <input type="checkbox"
                                            class="form-checkbox w-[18px] h-[18px] rounded-[6px] text-[#0B266E] border-gray-300 focus:ring-[#0B266E] focus:ring-offset-0 cursor-pointer"
                                            value="{{ $j->id }}" x-model="selectedIds">
                                    </td>
                                    <td>
                                        @php
                                            $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        @endphp
                                        <div style="font-weight: 600; color:#3730A3;">
                                            {{ $namaHari[$j->hari] ?? 'Tidak Valid' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; color: #0D0D12;">
                                            {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>
                                    <td style="max-width: 200px;">
                                        <div style="font-size: 13px; font-weight: 500; color:#0D0D12; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                            title="{{ $j->mata_kuliah ?: '-' }}">
                                            {{ $j->mata_kuliah ?: '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div
                                            style="font-size: 13px; font-weight: 700; color:#3730A3; display: inline-block; padding: 2px 8px; background: #EBEDF6; border-radius: 6px; border: 1px solid #C7D2FE;">
                                            {{ $j->kelas ?: '-' }}
                                        </div>
                                    </td>
                                    <td style="max-width: 180px;">
                                        <div style="font-weight: 500; color: #0D0D12; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                            title="{{ $j->ruangan->nama ?? 'Tidak Diketahui' }} (Lt. {{ $j->ruangan->lantai ?? '-' }})">
                                            {{ $j->ruangan->nama ?? 'Tidak Diketahui' }}
                                            <span style="font-size: 12px; font-weight: 500; color: #666D80; margin-left: 4px;">(Lt.
                                                {{ $j->ruangan->lantai ?? '-' }})</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 12px; font-weight: 600; color: #4B5563;">
                                            @if($j->tgl_mulai_efektif && $j->tgl_selesai_efektif)
                                                {{ \Carbon\Carbon::parse($j->tgl_mulai_efektif)->format('d/m/Y') }}<br>
                                                <span style="color: #9CA3AF;">s.d</span>
                                                {{ \Carbon\Carbon::parse($j->tgl_selesai_efektif)->format('d/m/Y') }}
                                            @else
                                                <span style="color: #9CA3AF; font-style: italic;">Sepanjang Waktu</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;" x-data="{ 
                                                            showDropdown: false, 
                                                            showEditModal: false, 
                                                            showDeleteModal: false,
                                                            formType: '{{ $j->tipe_jadwal }}', 
                                                            kategoriType: '{{ $j->kategori }}',
                                                            ruanganId: '{{ $j->ruangan_id }}',
                                                            currentDay: '{{ $j->hari }}',
                                                            jamMulai: '{{ substr($j->jam_mulai, 0, 5) }}',
                                                            jamSelesai: '{{ substr($j->jam_selesai, 0, 5) }}',
                                                            conflictError: false,
                                                            isCheckingOut: false,
                                                            checkTimeout: null,

                                                            akademikConflicts: [],
                                                            peminjamanConflicts: [],

                                                            triggerCheck() {
                                                                if(this.checkTimeout) clearTimeout(this.checkTimeout);
                                                                this.isCheckingOut = true;
                                                                this.checkTimeout = setTimeout(() => {
                                                                    this.executeCheck();
                                                                }, 500);
                                                            },

                                                            async executeCheck() {
                                                                if (!this.ruanganId || !this.jamMulai || !this.jamSelesai) {
                                                                    this.isCheckingOut = false;
                                                                    this.conflictError = false;
                                                                    this.akademikConflicts = [];
                                                                    this.peminjamanConflicts = [];
                                                                    return;
                                                                }

                                                                try {
                                                                    let url = `{{ route('eoffice.peminjaman.admin.jadwal-internal.check-collision') }}?ruangan_id=${this.ruanganId}&tipe_jadwal=${this.formType}&kategori=${this.kategoriType}&hari=${this.currentDay}&jam_mulai=${this.jamMulai}&jam_selesai=${this.jamSelesai}&exclude_id={{ $j->id }}`;
                                                                    let res = await fetch(url);
                                                                    let data = await res.json();

                                                                    if (data.conflict) {
                                                                        this.conflictError = true;
                                                                        this.akademikConflicts = data.akademik_details || [];
                                                                        this.peminjamanConflicts = data.peminjaman_details || [];
                                                                    } else {
                                                                        this.conflictError = false;
                                                                        this.akademikConflicts = [];
                                                                        this.peminjamanConflicts = [];
                                                                    }
                                                                } catch (e) {
                                                                    console.error(e);
                                                                } finally {
                                                                    this.isCheckingOut = false;
                                                                }
                                                            },

                                                            resetEditForm() {
                                                                this.ruanganId = '{{ $j->ruangan_id }}';
                                                                this.currentDay = '{{ $j->hari }}';
                                                                this.jamMulai = '{{ substr($j->jam_mulai, 0, 5) }}';
                                                                this.jamSelesai = '{{ substr($j->jam_selesai, 0, 5) }}';
                                                                this.conflictError = false;
                                                                this.akademikConflicts = [];
                                                                this.peminjamanConflicts = [];
                                                            }
                                                        }">
                                        <div class="relative inline-flex flex-col items-center justify-center w-full"
                                            x-data="{ 
                                                showDropdown: false, 
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
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle></svg>
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
                                        <div x-show="showDeleteModal" x-cloak style="display: none;"
                                            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal">
                                            
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
                                                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[500px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-5 sm:p-6 overflow-hidden z-10 text-left">
                                                
                                                <div class="flex flex-col flex-1 min-h-0">
                                                    <!-- Header Modal -->
                                                    <div class="-mx-5 sm:-mx-6 -mt-5 sm:-mt-6 mb-5 px-6 py-4 border-b border-red-600/10 flex items-center justify-between bg-red-600/[0.06] rounded-t-[24px] sm:rounded-t-[20px]">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 rounded-xl bg-red-600 flex items-center justify-center shadow-sm">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Hapus Jadwal?</h3>
                                                                <p class="text-[10px] text-red-600 font-medium">Tindakan ini tidak dapat dikembalikan</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="showDeleteModal = false" class="text-red-600 hover:text-red-700 hover:bg-red-600/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                                                        </button>
                                                    </div>

                                                    <!-- Body Modal -->
                                                    <div class="overflow-y-auto pb-1">
                                                        <div class="mb-5">
                                                            <p class="text-[13px] leading-relaxed text-gray-600 m-0">
                                                                Apakah Anda yakin ingin menghapus jadwal <strong class="text-gray-800">{{ $j->mata_kuliah ?: 'Tanpa Mata Kuliah' }} (Kelas {{ $j->kelas ?: '-' }})</strong> di ruangan <strong class="text-gray-800">{{ $j->ruangan->nama ?? 'Tidak Diketahui' }}</strong>?
                                                            </p>
                                                        </div>

                                                        <!-- Footer Modal -->
                                                        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-gray-100">
                                                            <button type="button" @click="showDeleteModal = false"
                                                                class="w-full sm:flex-1 flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all cursor-pointer">Batal</button>
                                                            <button type="button" @click="document.getElementById('deleteForm-{{ $j->id }}').submit()" 
                                                                class="w-full sm:flex-1 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white text-[13px] font-semibold px-4 py-2.5 rounded-[12px] transition-all gap-1.5 cursor-pointer border-0">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- EDIT MODAL -->
                                        <div x-show="showEditModal" x-cloak style="display: none;"
                                            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal"
                                            aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                            <!-- 1. BACKDROP OVERLAY -->
                                            <div x-show="showEditModal"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 bg-slate-900/60 transition-opacity"
                                                @click="showEditModal = false; resetEditForm()"></div>

                                            <!-- 2. KONTEN MODAL -->
                                            <div x-show="showEditModal"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                                                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[600px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-5 sm:p-6 overflow-hidden z-10 text-left">
                                                <div class="flex flex-col flex-1 min-h-0">
                                                    <div class="-mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Edit Jadwal Akademik</h3>
                                                                <p class="text-[10px] text-[#0B266E] font-medium">Ubah detail baris jadwal akademik</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="showEditModal = false; resetEditForm()" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                                                        </button>
                                                    </div>

                                                    <form
                                                        action="{{ route('eoffice.peminjaman.admin.jadwal-internal.update', $j->id) }}"
                                                        method="POST" class="flex flex-col flex-1 min-h-0"
                                                        @submit="if(isCheckingOut || conflictError) { $event.preventDefault(); return false; }">
                                                        @csrf
                                                        @method('PUT')
                                                        <div
                                                            class="space-y-4 overflow-y-auto overflow-x-hidden pr-2 -mr-2 pb-2 flex-1 whitespace-normal">
                                                            <div class="space-y-1.5 px-1">
                                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Ruangan</label>
                                                                <div x-data="{ 
                                                                            open: false,
                                                                            search: '',
                                                                            rooms: [
                                                                                @foreach($ruangans as $r)
                                                                                    {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }} - Lt. {{ $r->lantai }}'},
                                                                                @endforeach
                                                                            ],
                                                                            get filteredRooms() {
                                                                                if (this.search === '') return this.rooms;
                                                                                const currentRoom = this.rooms.find(r => r.id == ruanganId);
                                                                                if (currentRoom && this.search === currentRoom.name) return this.rooms;
                                                                                return this.rooms.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()));
                                                                            },
                                                                            selectItem(id) { 
                                                                                ruanganId = id;
                                                                                this.open = false; 
                                                                                if (id) {
                                                                                    const room = this.rooms.find(r => r.id == id);
                                                                                    this.search = room ? room.name : '';
                                                                                } else {
                                                                                    this.search = '';
                                                                                }
                                                                                triggerCheck();
                                                                            },
                                                                            init() {
                                                                                if(ruanganId) {
                                                                                    const room = this.rooms.find(r => r.id == ruanganId);
                                                                                    if(room) this.search = room.name;
                                                                                }
                                                                                this.$watch('search', (val) => {
                                                                                    if (val === '') {
                                                                                        ruanganId = '';
                                                                                    } else {
                                                                                        const currentRoom = this.rooms.find(r => r.id == ruanganId);
                                                                                        if (currentRoom && val !== currentRoom.name) {
                                                                                            ruanganId = '';
                                                                                        }
                                                                                    }
                                                                                });
                                                                                this.$watch('ruanganId', (val) => {
                                                                                    if (!val) {
                                                                                        this.search = '';
                                                                                    } else {
                                                                                        const room = this.rooms.find(r => r.id == val);
                                                                                        if(room) this.search = room.name;
                                                                                    }
                                                                                });
                                                                            }
                                                                        }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}"
                                                                    @click.away="open = false; if(!ruanganId) search = ''; else search = rooms.find(r => r.id == ruanganId)?.name || ''">
                                
                                                                    <input type="hidden" name="ruangan_id" :value="ruanganId" required>
                                
                                                                    <div class="relative flex items-center">
                                                                        <input type="text" x-model="search" @click="open = true" @focus="open = true"
                                                                            placeholder="-- Pilih atau Ketik Ruangan --" autocomplete="off"
                                                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px] text-gray-800">
                                                                        <div class="absolute right-3 pointer-events-none text-gray-400">
                                                                            <svg class="w-4 h-4 transition-transform duration-200"
                                                                                :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M19 9l-7 7-7-7" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                
                                                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                                                        x-transition:enter-start="opacity-0 scale-95"
                                                                        x-transition:enter-end="opacity-100 scale-100"
                                                                        x-transition:leave="transition ease-in duration-75"
                                                                        x-transition:leave-start="opacity-100 scale-100"
                                                                        x-transition:leave-end="opacity-0 scale-95"
                                                                        class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-60 flex flex-col overflow-hidden"
                                                                        style="display: none;">
                                                                        
                                                                        <div class="p-1 flex flex-col overflow-y-auto overflow-x-hidden flex-1 min-h-0">
                                                                            <button type="button" @click="selectItem('')" x-show="search === ''"
                                                                                class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                                                :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">--
                                                                                Pilih Ruangan Kelas --</button>
                                
                                                                            <template x-for="r in filteredRooms" :key="r.id">
                                                                                <button type="button" @click="selectItem(r.id)"
                                                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words"
                                                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == r.id, 'text-gray-700 hover:bg-gray-50': ruanganId != r.id}"
                                                                                    x-text="r.name">
                                                                                </button>
                                                                            </template>
                                
                                                                            <div x-show="filteredRooms.length === 0" class="px-3 py-4 text-xs text-center text-gray-500">
                                                                                Ruangan tidak ditemukan
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="tipe_jadwal" value="rutin">
                                                            <input type="hidden" name="kategori"
                                                                value="Jadwal Akademik (Kuliah)">
                                                            <input type="hidden" name="keterangan"
                                                                value="{{ $j->keterangan ?: 'Matkul Akademik' }}">
                                
                                                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl mx-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Informasi Mata Kuliah</span>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                    <div class="space-y-1.5">
                                                                        <label
                                                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata
                                                                            Kuliah</label>
                                                                        <input type="text" name="mata_kuliah"
                                                                            value="{{ $j->mata_kuliah }}" required
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                                                            placeholder="Nama Lengkap Matkul">
                                                                    </div>
                                                                    <div class="space-y-1.5">
                                                                        <label
                                                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kelas</label>
                                                                        <input type="text" name="kelas"
                                                                            value="{{ $j->kelas }}" required
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                                                            placeholder="A/B/C/D">
                                                                    </div>
                                                                </div>
                                                            </div>
                                
                                                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl mx-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Waktu & Tanggal</span>
                                                                </div>
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                    <div class="space-y-1.5 md:col-span-2">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Hari Pertemuan</label>
                                                                        <div x-data="{ 
                                                                                    open: false,
                                                                                    get selectedName() {
                                                                                        const map = {
                                                                                            '1': 'Senin', '2': 'Selasa', '3': 'Rabu',
                                                                                            '4': 'Kamis', '5': 'Jumat', '6': 'Sabtu', '7': 'Minggu'
                                                                                        };
                                                                                        return map[currentDay] || 'Pilih Hari...';
                                                                                    },
                                                                                    selectItem(val) { 
                                                                                        currentDay = val;
                                                                                        this.open = false; 
                                                                                        triggerCheck();
                                                                                    } 
                                                                                }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}"
                                                                            @click.away="open = false">
                                    
                                                                            <input type="hidden" name="hari" :value="currentDay" required>
                                    
                                                                            <button type="button" @click="open = !open"
                                                                                class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                                <span x-text="selectedName" class="truncate text-gray-800"></span>
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
                                                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto overflow-x-hidden"
                                                                                style="display: none;">
                                                                                <div class="p-1 flex flex-col">
                                                                                    <button type="button" @click="selectItem('1')"
                                                                                        class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '1', 'text-gray-700 hover:bg-gray-50': currentDay != '1'}">Senin</button>
                                                                                    <button type="button" @click="selectItem('2')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '2', 'text-gray-700 hover:bg-gray-50': currentDay != '2'}">Selasa</button>
                                                                                    <button type="button" @click="selectItem('3')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '3', 'text-gray-700 hover:bg-gray-50': currentDay != '3'}">Rabu</button>
                                                                                    <button type="button" @click="selectItem('4')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '4', 'text-gray-700 hover:bg-gray-50': currentDay != '4'}">Kamis</button>
                                                                                    <button type="button" @click="selectItem('5')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '5', 'text-gray-700 hover:bg-gray-50': currentDay != '5'}">Jumat</button>
                                                                                    <button type="button" @click="selectItem('6')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '6', 'text-gray-700 hover:bg-gray-50': currentDay != '6'}">Sabtu</button>
                                                                                    <button type="button" @click="selectItem('7')"
                                                                                        class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentDay == '7', 'text-gray-700 hover:bg-gray-50': currentDay != '7'}">Minggu</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                                                                        <input type="time" name="jam_mulai" x-model="jamMulai" @input="triggerCheck()" required 
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                    </div>
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Akhir</label>
                                                                        <input type="time" name="jam_selesai" x-model="jamSelesai" @input="triggerCheck()" required 
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl mx-1 mt-3">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Periode Semester (Masa Aktif)</span>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dimulai Sejak</label>
                                                                        <input type="date" name="tgl_mulai_efektif" value="{{ $j->tgl_mulai_efektif }}" required
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                                                                    </div>
                                                                    <div class="space-y-1.5">
                                                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Berakhir Pada</label>
                                                                        <input type="date" name="tgl_selesai_efektif" value="{{ $j->tgl_selesai_efektif }}" required
                                                                            class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div x-show="conflictError" x-cloak x-transition
                                                                class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex flex-col gap-2 text-red-700 text-[13px]">
                                                                <div class="font-bold">
                                                                    <span>Gagal Menyimpan! Terdapat Jadwal Beririsan:</span>
                                                                </div>
                                                                <div class="flex flex-col gap-1.5 mt-1">
                                                                    <template x-for="c in akademikConflicts" :key="c.nama">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                                            <strong x-text="c.nama"></strong>
                                                                            <span class="text-red-600/80" x-text="'('+c.waktu+')'"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <!-- Mobile Footer (Inside scrollable area so it doesn't follow keyboard) -->
                                                            <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                                                <button type="button" @click="showEditModal = false; resetEditForm()"
                                                                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                                                    Batal
                                                                </button>
                                                                <button type="submit" :disabled="conflictError || isCheckingOut"
                                                                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                                                    <span>Simpan Perubahan</span>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Desktop Sticky Footer -->
                                                        <div class="hidden sm:flex -mx-5 sm:-mx-6 -mb-5 sm:-mb-6 px-5 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/50 mt-4 flex-row items-center justify-end gap-3 shrink-0 rounded-b-none sm:rounded-b-[20px]">
                                                            <button type="button" @click="showEditModal = false; resetEditForm()"
                                                                class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                                                Batal
                                                            </button>
                                                            <button type="submit" :disabled="conflictError || isCheckingOut"
                                                                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
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
                                    <td colspan="8" style="text-align:center; padding: 40px; color: #666D80;">
                                        <div style="font-weight: 600; font-size:14px;">Belum Ada Jadwal</div>
                                        <div style="font-size:12px; margin-top:4px;">Gunakan tombol Tambah di pojok kanan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div
                    class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 mt-2">
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
        </div>

    </div> <!-- Close Alpine Wrapper -->
</x-eoffice::manajemen-ruangan.layout>