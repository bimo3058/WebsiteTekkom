<x-eoffice::manajemen-ruangan.layout
    pageTitle="{{ $viewMode === 'akademik' ? 'Kelola Jadwal Akademik' : 'Kelola Event & Maintenance' }}">

    <div
        x-data="{ showModal: false, showImportModal: false, formType: '{{ $viewMode === 'akademik' ? 'rutin' : 'spesifik' }}', kategoriType: '{{ $viewMode === 'akademik' ? 'Jadwal Akademik (Kuliah)' : 'Maintenance / Perbaikan' }}' }">
        <div class="mp-page-header">
            <div>
                @if($viewMode === 'akademik')
                    <h1 class="mp-page-title">Kelola Jadwal Akademik</h1>
                    <p class="mp-page-sub">Atur dan import blocking waktu khusus untuk agenda perkuliahan rutin Fakultas.
                    </p>
                @else
                    <h1 class="mp-page-title">Kelola Event & Maintenance</h1>
                    <p class="mp-page-sub">Atur blocking waktu insidental untuk rapat dosen, acara himpunan, atau perawatan
                        ruangan.</p>
                @endif
            </div>
            <div class="mp-page-actions">
                <!-- Filter Dropdown -->
                @if($viewMode !== 'akademik')
                    <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" method="GET"
                        class="flex gap-2">
                        <select name="tipe" onchange="this.form.submit()" class="mp-input !py-2 !text-xs !bg-white">
                            <option value="semua" {{ $tipe === 'semua' ? 'selected' : '' }}>Semua Tipe Jadwal</option>
                            <option value="rutin" {{ $tipe === 'rutin' ? 'selected' : '' }}>Rutin (Mingguan)</option>
                            <option value="spesifik" {{ $tipe === 'spesifik' ? 'selected' : '' }}>Spesifik (Acara)</option>
                        </select>
                    </form>
                @endif

                @if($viewMode === 'akademik')
                    <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.reset') }}" method="POST"
                        class="inline-block"
                        onsubmit="return confirm('PERINGATAN BAHAYA!\nApakah Anda yakin ingin MENGHAPUS SELURUH jadwal akademik di sistem secara massal? Aksi ini lazimnya hanya dilakukan pada saat reset pergantian semester. Tindakan ini tidak dapat dikembalikan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="mp-btn md font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200"
                            style="gap: 6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                            Reset Semester
                        </button>
                    </form>

                    <button @click="showImportModal = true" class="mp-btn secondary md font-semibold" style="gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-green-600"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="13" x2="16" y2="13"></line>
                            <line x1="8" y1="17" x2="16" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Import Excel
                    </button>
                @endif

                <button @click="showModal = true" class="mp-btn primary md">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Manual
                </button>
            </div>
        </div> <!-- Close mp-page-header -->



        <!-- Add Modal Alpine Component -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true"
                    @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">

                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-[18px] font-bold text-gray-900 font-['Inter_Tight']" id="modal-title">Tambah
                                Jadwal Baru</h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih
                                        Ruangan</label>
                                    <select name="ruangan_id" required class="mp-input text-[14px]">
                                        <option value="" disabled selected>-- Pilih Ruangan Kelas --</option>
                                        @foreach($ruangans as $r)
                                            <option value="{{ $r->id }}">{{ $r->nama }} - Lt. {{ $r->lantai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="tipe_jadwal" value="rutin">
                                <input type="hidden" name="kategori" value="Jadwal Akademik (Kuliah)">
                                <input type="hidden" name="keterangan" value="Matkul Akademik">

                                <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                                    <h4
                                        class="text-[13px] font-bold text-indigo-900 border-b border-indigo-100 pb-2 mb-3">
                                        Informasi Mata Kuliah</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mata
                                                Kuliah</label>
                                            <input type="text" name="mata_kuliah" required class="mp-input text-[14px]"
                                                placeholder="Nama Lengkap Matkul">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kelas</label>
                                            <input type="text" name="kelas" required class="mp-input text-[14px]"
                                                placeholder="A/B/C/D">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari
                                            Pertemuan</label>
                                        <select name="hari" required class="mp-input text-[14px]">
                                            <option value="1">Senin</option>
                                            <option value="2">Selasa</option>
                                            <option value="3">Rabu</option>
                                            <option value="4">Kamis</option>
                                            <option value="5">Jumat</option>
                                            <option value="6">Sabtu</option>
                                            <option value="7">Minggu</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label
                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mulai</label>
                                            <input type="time" name="jam_mulai" required class="mp-input text-[14px]">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Akhir</label>
                                            <input type="time" name="jam_selesai" required class="mp-input text-[14px]">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4"
                                    style="padding:15px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; margin-top:10px;">
                                    <div class="col-span-2">
                                        <h4
                                            class="text-[12px] font-bold text-slate-700 border-b border-slate-200 pb-2 mb-2">
                                            Periode Semester Masa Aktif Jadwal</h4>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Dimulai
                                            Sejak</label>
                                        <input type="date" name="tgl_mulai_efektif" required
                                            class="mp-input text-[14px]">
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Berakhir
                                            Pada</label>
                                        <input type="date" name="tgl_selesai_efektif" required
                                            class="mp-input text-[14px]">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" @click="showModal = false"
                                    class="mp-btn secondary md">Batal</button>
                                <button type="submit" class="mp-btn primary md">Simpan Konfigurasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Excel Import Modal -->
        <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showImportModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" aria-hidden="true"
                    @click="showImportModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showImportModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">

                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-[18px] font-bold text-gray-900 font-['Inter_Tight']" id="modal-title">Impor
                                Jadwal Kuliah Rutin</h3>
                            <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.import-preview') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="p-4 mb-4 rounded-xl relative overflow-hidden"
                                style="background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #BBF7D0;">
                                <div class="flex gap-3 relative z-10">
                                    <div
                                        class="w-10 h-10 shrink-0 rounded-full bg-green-100/80 flex items-center justify-center border border-green-200">
                                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[13px] font-bold text-green-800 mb-1">Mekanisme Keamanan Impor
                                        </h4>
                                        <p class="text-[12.5px] leading-relaxed text-green-700/80">
                                            Data jadwal yang diunggah tidak akan langsung dimanifestasikan ke database.
                                            Sistem akan menampilkan layar Pratinjau (Sandbox) terlebih dulu.
                                        </p>

                                    </div>
                                </div>
                            </div>

                            <div class="mb-5" x-data="{ fileName: '' }">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Pilih File Ekspor
                                    SIAP (*.xlsx, *.csv)</label>
                                <div :class="fileName ? 'border-indigo-400 bg-indigo-50 text-indigo-700' : 'border-gray-300 text-gray-900'"
                                    class="relative block w-full border-2 border-dashed rounded-lg p-5 text-center hover:border-gray-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition-colors">
                                    <input type="file" name="file_excel"
                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                    <div class="flex flex-col items-center pointer-events-none">
                                        <svg x-show="!fileName" class="w-8 h-8 text-gray-400 mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <svg x-cloak x-show="fileName" class="w-8 h-8 mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-show="!fileName" class="text-sm font-medium">Klik untuk memilih file
                                            Excel / CSV</span>
                                        <span x-cloak x-show="fileName" class="text-sm font-bold" x-text="fileName"
                                            style="display: none;"></span>
                                        <span class="mt-1 text-xs text-gray-500" x-show="!fileName">Maksimum ukuran:
                                            5MB</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" @click="showImportModal = false"
                                    class="mp-btn secondary md">Batal</button>
                                <button type="submit" class="mp-btn primary md">Pratinjau Data</button>
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

        <div class="mp-card" style="margin-top: 15px;">
            <!-- NEW CARD HEADER MATCHING MOCKUP -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full"
                style="padding-bottom: 20px;">
                <h2 class="text-[16px] font-bold text-gray-800 tracking-tight">
                    {{ $viewMode === 'akademik' ? 'Jadwal Akademik Table' : 'Event & Maintenance Table' }}</h2>

                @if($viewMode === 'akademik')
                    <div class="flex items-center gap-2" x-data="{ openFilter: false, openSort: false }">
                        <form action="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}" method="GET"
                            class="flex items-center gap-2 m-0 relative">
                            <!-- Search Bar -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-[18px] w-[18px] text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z">
                                        </path>
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" onblur="this.form.submit()"
                                    placeholder="Search"
                                    class="w-full sm:w-[240px] pl-10 pr-3 py-[9px] text-[13px] bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all placeholder-gray-400">
                            </div>

                            <!-- Filter Button -->
                            <div class="relative" @click.away="openFilter = false">
                                <button type="button" @click="openFilter = !openFilter"
                                    class="inline-flex items-center gap-2 px-4 py-[9px] text-[13px] font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-100 transition-colors shadow-sm whitespace-nowrap">
                                    <svg class="w-[18px] h-[18px] text-gray-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    Filter
                                </button>
                                <!-- Filter Popover -->
                                <div x-show="openFilter" x-cloak x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 p-4"
                                    style="display:none; z-index: 50; width: 280px;">
                                    <div class="space-y-4">
                                        <div class="border-b border-gray-100 pb-2 mb-2">
                                            <h3 class="text-[13px] font-bold text-gray-800">Advanced Filters</h3>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Pilih
                                                Hari</label>
                                            <select name="hari"
                                                class="w-full px-3 py-2 text-[13px] bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-white transition-colors">
                                                <option value="">Semua Hari</option>
                                                <option value="1" {{ request('hari') == '1' ? 'selected' : '' }}>Senin
                                                </option>
                                                <option value="2" {{ request('hari') == '2' ? 'selected' : '' }}>Selasa
                                                </option>
                                                <option value="3" {{ request('hari') == '3' ? 'selected' : '' }}>Rabu</option>
                                                <option value="4" {{ request('hari') == '4' ? 'selected' : '' }}>Kamis
                                                </option>
                                                <option value="5" {{ request('hari') == '5' ? 'selected' : '' }}>Jumat
                                                </option>
                                                <option value="6" {{ request('hari') == '6' ? 'selected' : '' }}>Sabtu
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Pilih
                                                Ruangan</label>
                                            <select name="ruangan_id"
                                                class="w-full px-3 py-2 text-[13px] bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-white transition-colors">
                                                <option value="">Semua Ruangan</option>
                                                @foreach($ruangans as $r)
                                                    <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                                        {{ $r->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex gap-2 pt-3 border-t border-gray-100 mt-2">
                                            <button type="submit"
                                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-[13px] py-2 rounded-lg transition-colors text-center">Terapkan</button>
                                            @if(request('search') || request('hari') || request('ruangan_id'))
                                                <a href="{{ route('eoffice.peminjaman.admin.jadwal-akademik.index') }}"
                                                    class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold text-[13px] py-2 rounded-lg transition-colors text-center">Reset</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sort By Button -->
                            <div class="relative" @click.away="openSort = false">
                                <button type="button" @click="openSort = !openSort"
                                    class="inline-flex items-center gap-2 px-4 py-[9px] text-[13px] font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-100 transition-colors shadow-sm whitespace-nowrap">
                                    <svg class="w-[18px] h-[18px] text-gray-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                                            style="display:none;"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                    Sort by
                                </button>
                                <!-- Sort Popover -->
                                <div x-show="openSort" x-cloak x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 p-4"
                                    style="display:none; z-index: 50; width: 260px;">
                                    <div class="space-y-4">
                                        <div class="border-b border-gray-100 pb-2 mb-2">
                                            <h3 class="text-[13px] font-bold text-gray-800">Urutkan Berdasarkan</h3>
                                        </div>
                                        <div>
                                            <select name="sort" onchange="this.form.submit()"
                                                class="w-full px-3 py-3 text-[13px] bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 focus:bg-white transition-colors cursor-pointer">
                                                <option value="waktu" {{ request('sort', 'waktu') === 'waktu' ? 'selected' : '' }}>Hari & Waktu Terawal</option>
                                                <option value="matkul_asc" {{ request('sort') === 'matkul_asc' ? 'selected' : '' }}>Mata Kuliah (A-Z)</option>
                                                <option value="matkul_desc" {{ request('sort') === 'matkul_desc' ? 'selected' : '' }}>Mata Kuliah (Z-A)</option>
                                                <option value="ruangan" {{ request('sort') === 'ruangan' ? 'selected' : '' }}>Ruangan (A-Z)</option>
                                                <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Waktu Ditambahkan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="mp-card-body">
                <div class="mp-table-wrap">
                    <table class="mp-table">
                        <thead>
                            <tr>
                                <th>HARI</th>
                                <th>WAKTU</th>
                                <th>MATA KULIAH</th>
                                <th style="text-align: center;">KELAS</th>
                                <th>RUANGAN</th>
                                <th style="width: 80px; text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $j)
                                <tr class="mp-tr">
                                    <td>
                                        @php
                                            $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        @endphp
                                        <div style="font-weight: 600; color:#3730A3;">
                                            {{ $namaHari[$j->hari] ?? 'Tidak Valid' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0D0D12;">
                                            {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 700; color:#0D0D12;">
                                            {{ $j->mata_kuliah ?: '-' }}
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div
                                            style="font-size: 13px; font-weight: 700; color:#3730A3; display: inline-block; padding: 2px 8px; background: #EEF2FF; border-radius: 6px; border: 1px solid #C7D2FE;">
                                            {{ $j->kelas ?: '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0D0D12;">
                                            {{ $j->ruangan->nama ?? 'Tidak Diketahui' }}
                                            <span
                                                style="font-size: 12px; color: #666D80; margin-left: 4px; font-weight: 500;">(Lt.
                                                {{ $j->ruangan->lantai ?? '-' }})</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center;"
                                        x-data="{ showDropdown: false, showEditModal: false, formType: '{{ $j->tipe_jadwal }}', kategoriType: '{{ $j->kategori }}' }">
                                        <div class="relative inline-flex justify-center w-full relative z-[1]">
                                            <button type="button" @click="showDropdown = !showDropdown"
                                                @click.away="showDropdown = false"
                                                class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 p-1.5 rounded-md transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>

                                            <div x-show="showDropdown" style="display:none;"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="origin-top-right absolute right-6 top-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                                <div class="py-1">
                                                    <button type="button"
                                                        @click="showEditModal = true; showDropdown = false"
                                                        class="w-full text-left px-4 py-2 text-[13px] text-gray-700 hover:bg-gray-100 hover:text-indigo-600 font-semibold focus:outline-none flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                        Edit Jadwal
                                                    </button>
                                                    <form
                                                        action="{{ route('eoffice.peminjaman.admin.jadwal-internal.destroy', $j->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus blokir jadwal ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-full text-left px-4 py-2 text-[13px] text-red-600 hover:bg-red-50 font-semibold focus:outline-none flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                            Hapus Jadwal
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EDIT MODAL -->
                                        <div x-show="showEditModal" style="display: none;"
                                            class="fixed inset-0 z-[100] overflow-y-auto text-left"
                                            aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div
                                                class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                                                <div x-show="showEditModal"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"
                                                    class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md"
                                                    aria-hidden="true" @click="showEditModal = false"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                                    aria-hidden="true">&#8203;</span>

                                                <div x-show="showEditModal"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="transition ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">

                                                    <div class="flex items-center justify-between mb-5">
                                                        <h3 class="text-[18px] font-bold text-gray-900" id="modal-title">
                                                            Edit
                                                            Jadwal Internal</h3>
                                                        <button type="button" @click="showEditModal = false"
                                                            class="text-gray-400 hover:text-gray-500">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <form
                                                        action="{{ route('eoffice.peminjaman.admin.jadwal-internal.update', $j->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                                                    Ruangan</label>
                                                                <select name="ruangan_id" required
                                                                    class="mp-input text-[14px]">
                                                                    @foreach($ruangans as $r)
                                                                        <option value="{{ $r->id }}" {{ $r->id == $j->ruangan_id ? 'selected' : '' }}>{{ $r->nama }} - Lt.
                                                                            {{ $r->lantai }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <input type="hidden" name="tipe_jadwal" value="rutin">
                                                            <input type="hidden" name="kategori"
                                                                value="Jadwal Akademik (Kuliah)">
                                                            <input type="hidden" name="keterangan"
                                                                value="{{ $j->keterangan ?: 'Matkul Akademik' }}">

                                                            <div
                                                                class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                                                                <h4
                                                                    class="text-[13px] font-bold text-indigo-900 border-b border-indigo-100 pb-2 mb-3">
                                                                    Informasi Mata Kuliah</h4>
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mata
                                                                            Kuliah</label>
                                                                        <input type="text" name="mata_kuliah"
                                                                            value="{{ $j->mata_kuliah }}" required
                                                                            class="mp-input text-[14px]"
                                                                            placeholder="Nama Lengkap Matkul">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kelas</label>
                                                                        <input type="text" name="kelas"
                                                                            value="{{ $j->kelas }}" required
                                                                            class="mp-input text-[14px]"
                                                                            placeholder="A/B/C/D">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label
                                                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari
                                                                        Pertemuan</label>
                                                                    <select name="hari" required
                                                                        class="mp-input text-[14px]">
                                                                        <option value="1" {{ $j->hari == 1 ? 'selected' : '' }}>Senin</option>
                                                                        <option value="2" {{ $j->hari == 2 ? 'selected' : '' }}>Selasa</option>
                                                                        <option value="3" {{ $j->hari == 3 ? 'selected' : '' }}>Rabu</option>
                                                                        <option value="4" {{ $j->hari == 4 ? 'selected' : '' }}>Kamis</option>
                                                                        <option value="5" {{ $j->hari == 5 ? 'selected' : '' }}>Jumat</option>
                                                                        <option value="6" {{ $j->hari == 6 ? 'selected' : '' }}>Sabtu</option>
                                                                        <option value="7" {{ $j->hari == 7 ? 'selected' : '' }}>Minggu</option>
                                                                    </select>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-2">
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mulai</label>
                                                                        <input type="time" name="jam_mulai"
                                                                            value="{{ substr($j->jam_mulai, 0, 5) }}"
                                                                            required class="mp-input text-[14px]">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Akhir</label>
                                                                        <input type="time" name="jam_selesai"
                                                                            value="{{ substr($j->jam_selesai, 0, 5) }}"
                                                                            required class="mp-input text-[14px]">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-4"
                                                                style="padding:15px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; margin-top:10px;">
                                                                <div class="col-span-2">
                                                                    <h4
                                                                        class="text-[12px] font-bold text-slate-700 border-b border-slate-200 pb-2 mb-2">
                                                                        Periode Semester Masa Aktif Jadwal</h4>
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Dimulai
                                                                        Sejak</label>
                                                                    <input type="date" name="tgl_mulai_efektif"
                                                                        value="{{ $j->tgl_mulai_efektif }}" required
                                                                        class="mp-input text-[14px]">
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Berakhir
                                                                        Pada</label>
                                                                    <input type="date" name="tgl_selesai_efektif"
                                                                        value="{{ $j->tgl_selesai_efektif }}" required
                                                                        class="mp-input text-[14px]">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div
                                                            class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                                            <button type="button" @click="showEditModal = false"
                                                                class="mp-btn secondary md">Batal</button>
                                                            <button type="submit" class="mp-btn primary md">Simpan
                                                                Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 40px; color: #666D80;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E2E8F0"
                                            stroke-width="1.5" stroke-linecap="round" style="margin: 0 auto 10px auto;">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <div style="font-weight: 600; font-size:14px;">Belum Ada Jadwal Internal & Akademik
                                        </div>
                                        <div style="font-size:12px; margin-top:4px;">Gunakan tombol Tambah Jadwal di pojok
                                            kanan
                                            atas untuk mulai mengunci ruangan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding: 16px;">
                    {{ $jadwals->appends(['tipe' => $tipe])->links('pagination::tailwind') }}
                </div>
            </div>
        </div>

    </div> <!-- Close Alpine Wrapper -->
</x-eoffice::manajemen-ruangan.layout>