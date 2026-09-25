<x-eoffice::manajemen-ruangan.layout
    pageTitle="{{ $viewMode === 'akademik' ? 'Kelola Jadwal Akademik' : 'Kelola Event dan Maintenance' }}">

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
            <div class="mp-page-actions flex items-center gap-3">
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
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

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
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih
                                            Ruangan</label>
                                        <div x-data="{ 
                                                open: false,
                                                ruanganId: '',
                                                get selectedName() {
                                                    const room = [
                                                        @foreach($ruangans as $r)
                                                            {id: '{{ $r->id }}', name: '{{ addslashes($r->nama) }} - Lt. {{ $r->lantai }}'},
                                                        @endforeach
                                                    ].find(r => r.id == this.ruanganId);
                                                    return room ? room.name : '-- Pilih Ruangan --';
                                                },
                                                selectItem(id) { 
                                                    this.ruanganId = id;
                                                    this.open = false; 
                                                } 
                                            }" class="relative w-full" @click.away="open = false">
                                            
                                            <input type="hidden" name="ruangan_id" :value="ruanganId" required>

                                            <button type="button" @click="open = !open" 
                                                class="w-full flex items-center justify-between mp-input bg-white focus:outline-none transition-colors h-[42px] px-3">
                                                <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !ruanganId, 'text-gray-800': ruanganId}"></span>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            
                                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                                <div class="p-1">
                                                    <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">-- Pilih Ruangan --</button>
                                                    
                                                    @foreach($ruangans as $r)
                                                        <button type="button" @click="selectItem('{{ $r->id }}')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '{{ $r->id }}', 'text-gray-700 hover:bg-gray-50': ruanganId != '{{ $r->id }}'}">
                                                            {{ $r->nama }} - Lt. {{ $r->lantai }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
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
                                            }" class="relative w-full" @click.away="open = false">
                                            
                                            <input type="hidden" name="kategori" :value="kategoriType" required>

                                            <button type="button" @click="open = !open" 
                                                class="w-full flex items-center justify-between mp-input bg-white focus:outline-none transition-colors h-[42px] px-3">
                                                <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !kategoriType, 'text-gray-800': kategoriType}"></span>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            
                                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                                <div class="p-1">
                                                    <button type="button" @click="selectItem('Pindah Kelas')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Pindah Kelas', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Pindah Kelas'}">Pindah / Pengganti Kelas</button>
                                                    <button type="button" @click="selectItem('Maintenance / Perbaikan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Maintenance / Perbaikan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Maintenance / Perbaikan'}">Maintenance / Perbaikan Ruangan</button>
                                                    <button type="button" @click="selectItem('Sterilisasi Ruangan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Sterilisasi Ruangan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Sterilisasi Ruangan'}">Sterilisasi / Persiapan Ruangan</button>
                                                    <button type="button" @click="selectItem('Penutupan Khusus')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Penutupan Khusus', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Penutupan Khusus'}">Penutupan Khusus / Libur Nasional</button>
                                                    <button type="button" @click="selectItem('Ujian / Evaluasi')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Ujian / Evaluasi', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Ujian / Evaluasi'}">Ujian / Evaluasi (UTS/UAS)</button>
                                                    <button type="button" @click="selectItem('Lainnya')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Lainnya', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Lainnya'}">Lainnya...</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2 text-left">
                                    <label
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama
                                        Acara</label>
                                    <input type="text" name="keterangan" required class="mp-input text-[14px] w-full"
                                        placeholder="Misal: Rapat Evaluasi Kurikulum...">
                                </div>

                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                    <h4
                                        class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">
                                        Pengaturan Waktu Pelaksanaan</h4>

                                    <input type="hidden" name="tipe_jadwal" x-model="formType">

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Tampil Jika Tipe Spesifik -->
                                        <template x-if="formType === 'spesifik'">
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tanggal
                                                    Spesifik</label>
                                                <input type="date" name="tanggal_spesifik" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                        </template>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mulai</label>
                                                <input type="time" name="jam_mulai" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Akhir</label>
                                                <input type="time" name="jam_selesai" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showModal = false" class="mp-btn secondary md">Batal</button>
                        <button type="submit" class="mp-btn primary md">Simpan Konfigurasi</button>
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

    <div class="bg-white border border-gray-200 rounded-[12px] mt-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <div
            class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white rounded-t-[12px]">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Jadwal</h2>

            <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" method="GET"
                class="flex flex-wrap items-center gap-2.5">
                {{-- Search --}}
                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full sm:w-56 h-[38px] pl-9 pr-3 text-[13px] bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:bg-slate-50 focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] outline-none transition-all placeholder-gray-400"
                        placeholder="Cari acara..." x-on:input.debounce.700ms="$el.form.submit()">
                </div>

                <div
                    class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-xs shadow-sm">
                    <div x-data="{ 
                            open: false, 
                            selectedVal: '{{ request('kategori') }}', 
                            get selectedName() {
                                const map = {
                                    'Pindah Kelas': 'Pindah / Pengganti Kelas',
                                    'Maintenance / Perbaikan': 'Maintenance / Perbaikan Ruangan',
                                    'Sterilisasi Ruangan': 'Sterilisasi / Persiapan Ruangan',
                                    'Penutupan Khusus': 'Penutupan Khusus / Libur Nasional',
                                    'Ujian / Evaluasi': 'Ujian / Evaluasi (UTS/UAS)',
                                    'Lainnya': 'Lainnya...'
                                };
                                return map[this.selectedVal] || 'Semua Kategori';
                            },
                            selectItem(val) { 
                                this.selectedVal = val; 
                                $refs.kategoriInput.value = val;
                                $refs.kategoriInput.form.submit();
                            } 
                        }" class="relative w-full max-w-[140px] sm:max-w-[180px]" @click.away="open = false">
                        
                        <input type="hidden" name="kategori" x-ref="kategoriInput" :value="selectedVal">

                        <button type="button" @click="open = !open" 
                            class="w-full flex items-center justify-between px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 transition-colors h-[34px] rounded-md border-none">
                            <span x-text="selectedName" class="truncate pr-2"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                            class="absolute right-0 top-full mt-1 w-[220px] bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden" style="display: none;">
                            <div class="p-1">
                                <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == '', 'text-slate-700 hover:bg-slate-50': selectedVal != ''}">Semua Kategori</button>
                                <button type="button" @click="selectItem('Pindah Kelas')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Pindah Kelas', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Pindah Kelas'}">Pindah / Pengganti Kelas</button>
                                <button type="button" @click="selectItem('Maintenance / Perbaikan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Maintenance / Perbaikan', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Maintenance / Perbaikan'}">Maintenance / Perbaikan Ruangan</button>
                                <button type="button" @click="selectItem('Sterilisasi Ruangan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Sterilisasi Ruangan', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Sterilisasi Ruangan'}">Sterilisasi / Persiapan Ruangan</button>
                                <button type="button" @click="selectItem('Penutupan Khusus')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Penutupan Khusus', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Penutupan Khusus'}">Penutupan Khusus / Libur Nasional</button>
                                <button type="button" @click="selectItem('Ujian / Evaluasi')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Ujian / Evaluasi', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Ujian / Evaluasi'}">Ujian / Evaluasi (UTS/UAS)</button>
                                <button type="button" @click="selectItem('Lainnya')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'Lainnya', 'text-slate-700 hover:bg-slate-50': selectedVal != 'Lainnya'}">Lainnya...</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mp-card-body p-0 mt-4 rounded-b-[12px] overflow-hidden">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Tanggal</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Waktu</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Nama Acara</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Ruangan</th>
                            <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $j)
                            <tr class="mp-tr">
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
                                    <div class="relative inline-flex flex-col items-center justify-center w-full" :class="{'z-50': showDropdown, 'z-[1]': !showDropdown}">
                                        <button type="button" @click="showDropdown = !showDropdown"
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
                                            class="origin-top-right absolute right-5 top-0 mt-8 bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 z-20 w-[140px]">

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
                                                method="POST"
                                                id="deleteForm-{{ $j->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="showDeleteModal = true; showDropdown = false"
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
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showDeleteModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md"
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
                                                
                                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 mb-4">
                                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </div>
                                                <h3 class="text-base font-bold text-slate-800 mb-2">Hapus Jadwal?</h3>
                                                <p class="text-xs text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin ingin menghapus blokir jadwal ini? Tindakan ini tidak dapat dikembalikan.</p>
                                                
                                                <div class="flex justify-center gap-3">
                                                    <button type="button" @click="showDeleteModal = false" class="mp-btn secondary px-4 py-2">Batal</button>
                                                    <button type="button" @click="document.getElementById('deleteForm-{{ $j->id }}').submit()" class="mp-btn px-4 py-2 bg-red-600 hover:bg-red-700 text-white border-transparent" style="border:none;">Hapus Jadwal</button>
                                                </div>
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
                                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
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
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                                                    Ruangan</label>
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
                                                                    }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}" @click.away="open = false">
                                                                    
                                                                    <input type="hidden" name="ruangan_id" :value="ruanganId" required>

                                                                    <button type="button" @click="open = !open" 
                                                                        class="w-full flex items-center justify-between mp-input bg-white focus:outline-none transition-colors h-[42px] px-3">
                                                                        <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !ruanganId, 'text-gray-800': ruanganId}"></span>
                                                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                        </svg>
                                                                    </button>
                                                                    
                                                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                                        class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[9999] max-h-48 overflow-y-auto overflow-x-hidden" style="display: none;">
                                                                        <div class="p-1 flex flex-col">
                                                                            <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '', 'text-gray-700 hover:bg-gray-50': ruanganId != ''}">-- Pilih Ruangan --</button>
                                                                            @foreach($ruangans as $r)
                                                                                <button type="button" @click="selectItem('{{ $r->id }}')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors whitespace-normal break-words" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': ruanganId == '{{ $r->id }}', 'text-gray-700 hover:bg-gray-50': ruanganId != '{{ $r->id }}'}">
                                                                                    {{ $r->nama }} - Lt. {{ $r->lantai }}
                                                                                </button>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
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
                                                                    }" class="relative w-full" :class="{'z-50': open, 'z-10': !open}" @click.away="open = false">
                                                                    
                                                                    <input type="hidden" name="kategori" :value="kategoriType" required>

                                                                    <button type="button" @click="open = !open" 
                                                                        class="w-full flex items-center justify-between mp-input bg-white focus:outline-none transition-colors h-[42px] px-3">
                                                                        <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !kategoriType, 'text-gray-800': kategoriType}"></span>
                                                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                                        </svg>
                                                                    </button>
                                                                    
                                                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                                        class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[9999] max-h-48 overflow-y-auto" style="display: none;">
                                                                        <div class="p-1 flex flex-col">
                                                                            <button type="button" @click="selectItem('Pindah Kelas')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Pindah Kelas', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Pindah Kelas'}">Pindah / Pengganti Kelas</button>
                                                                            <button type="button" @click="selectItem('Maintenance / Perbaikan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Maintenance / Perbaikan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Maintenance / Perbaikan'}">Maintenance / Perbaikan Ruangan</button>
                                                                            <button type="button" @click="selectItem('Sterilisasi Ruangan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Sterilisasi Ruangan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Sterilisasi Ruangan'}">Sterilisasi / Persiapan Ruangan</button>
                                                                            <button type="button" @click="selectItem('Penutupan Khusus')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Penutupan Khusus', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Penutupan Khusus'}">Penutupan Khusus / Libur Nasional</button>
                                                                            <button type="button" @click="selectItem('Ujian / Evaluasi')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Ujian / Evaluasi', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Ujian / Evaluasi'}">Ujian / Evaluasi (UTS/UAS)</button>
                                                                            <button type="button" @click="selectItem('Lainnya')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Lainnya', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Lainnya'}">Lainnya...</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2 text-left">
                                                            <label
                                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama
                                                                Acara</label>
                                                            <input type="text" name="keterangan"
                                                                value="{{ $j->keterangan }}" required
                                                                class="mp-input text-[14px] w-full"
                                                                placeholder="Misal: Rapat Evaluasi Kurikulum...">
                                                        </div>

                                                        <div
                                                            class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                                            <h4
                                                                class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">
                                                                Pengaturan Waktu Pelaksanaan</h4>

                                                            <input type="hidden" name="tipe_jadwal"
                                                                value="{{ $j->tipe_jadwal }}">

                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label
                                                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tanggal
                                                                        Spesifik</label>
                                                                    <input type="date" name="tanggal_spesifik"
                                                                        value="{{ $j->tanggal_spesifik }}" required
                                                                        class="mp-input text-[14px]">
                                                                </div>
                                                                </template>

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
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
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
                                    <div class="text-[12px] mt-1 text-gray-400">Gunakan tombol Tambah Jadwal di pojok kanan
                                        atas
                                        untuk mulai mengunci ruangan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 rounded-b-[12px]">
            <div class="flex items-center gap-4 text-[13px] text-slate-500">
                <div class="flex items-center gap-2">
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
                                    @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                <button type="button"
                                    @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                <button type="button"
                                    @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-px h-4 bg-slate-200"></div>

                <p class="font-medium text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $jadwals->firstItem() ?? 0 }}</span>
                    sampai <span class="font-bold text-slate-800">{{ $jadwals->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-slate-800">{{ $jadwals->total() }}</span> entri
                </p>
            </div>

            <div class="flex items-center gap-1.5">
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
</x-eoffice::manajemen-ruangan.layout>