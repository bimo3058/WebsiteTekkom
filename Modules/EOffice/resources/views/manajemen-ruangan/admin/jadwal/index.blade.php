<x-eoffice::manajemen-ruangan.layout pageTitle="Kelola Jadwal Internal">

    <div class="mp-page-header" x-data="{ showModal: false, formType: 'spesifik' }">
        <div>
            <h1 class="mp-page-title">Kelola Jadwal Internal</h1>
            <p class="mp-page-sub">Atur blocking waktu untuk agenda akademik, rapat dosen, atau perawatan ruangan kelas.
            </p>
        </div>
        <div class="mp-page-actions">
            <!-- Filter Dropdown -->
            <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.index') }}" method="GET"
                class="flex gap-2">
                <select name="tipe" onchange="this.form.submit()" class="mp-input !py-2 !text-xs !bg-white">
                    <option value="semua" {{ $tipe === 'semua' ? 'selected' : '' }}>Semua Tipe Jadwal</option>
                    <option value="rutin" {{ $tipe === 'rutin' ? 'selected' : '' }}>Rutin (Mingguan)</option>
                    <option value="spesifik" {{ $tipe === 'spesifik' ? 'selected' : '' }}>Spesifik (Acara)</option>
                </select>
            </form>
            <button @click="showModal = true" class="mp-btn primary md">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Jadwal
            </button>
        </div>

        <!-- Add Modal Alpine Component -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm"
                     aria-hidden="true" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" 
                     x-transition:enter="transition ease-out duration-300"
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
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                        Ruangan</label>
                                    <select name="ruangan_id" required class="mp-input text-[14px]">
                                        <option value="" disabled selected>-- Pilih Ruangan Kelas --</option>
                                        @foreach($ruangans as $r)
                                            <option value="{{ $r->id }}">{{ $r->nama }} - Lt. {{ $r->lantai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                        Agenda / Bentuk Jadwal</label>
                                    <div class="flex gap-4 p-1 bg-gray-100 rounded-lg">
                                        <label
                                            class="flex-1 text-center font-semibold text-[13px] py-1.5 rounded-md cursor-pointer transition-colors"
                                            :class="formType === 'spesifik' ? 'bg-white shadow relative text-[#0B266E]' : 'text-gray-500 hover:text-gray-700'">
                                            <input type="radio" name="tipe_jadwal" value="spesifik" x-model="formType"
                                                class="hidden">
                                            Kegiatan Spesifik (Satu Hari)
                                        </label>
                                        <label
                                            class="flex-1 text-center font-semibold text-[13px] py-1.5 rounded-md cursor-pointer transition-colors"
                                            :class="formType === 'rutin' ? 'bg-white shadow relative text-[#0B266E]' : 'text-gray-500 hover:text-gray-700'">
                                            <input type="radio" name="tipe_jadwal" value="rutin" x-model="formType"
                                                class="hidden">
                                            Kuliah Rutin (Mingguan)
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori
                                            Acara</label>
                                        <select name="kategori" required class="mp-input">
                                            <option value="Jadwal Akademik (Kuliah)">Jadwal Akademik (Kuliah)</option>
                                            <option value="Sidang / Ujian Akademik">Sidang / Ujian Akademik</option>
                                            <option value="Rapat Internal Jurusan">Rapat Internal Jurusan</option>
                                            <option value="Bimbingan Mahasiswa">Bimbingan Mahasiswa</option>
                                            <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                            <option value="Acara Kemahasiswaan">Acara Kemahasiswaan Khusus</option>
                                        </select>
                                    </div>
                                    <div x-show="formType === 'spesifik'">
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih
                                            Tanggal</label>
                                        <input type="date" name="tanggal_spesifik" :required="formType === 'spesifik'"
                                            class="mp-input text-[14px]">
                                    </div>
                                    <div x-show="formType === 'rutin'" style="display: none;">
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari
                                            Pertemuan</label>
                                        <select name="hari" :required="formType === 'rutin'"
                                            class="mp-input text-[14px]">
                                            <option value="1">Senin</option>
                                            <option value="2">Selasa</option>
                                            <option value="3">Rabu</option>
                                            <option value="4">Kamis</option>
                                            <option value="5">Jumat</option>
                                            <option value="6">Sabtu</option>
                                            <option value="7">Minggu</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Waktu
                                            Mulai</label>
                                        <input type="time" name="jam_mulai" required class="mp-input text-[14px]">
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Waktu
                                            Berakhir</label>
                                        <input type="time" name="jam_selesai" required class="mp-input text-[14px]">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Keterangan
                                        Publik / Nama Mata Kuliah</label>
                                    <input type="text" name="keterangan" required
                                        placeholder="Contoh: Matkul Pemrograman Web (Bpk. Budi) atau Perbaikan AC Rusak"
                                        class="mp-input text-[14px]">
                                    <p class="mt-1 text-[11px] text-gray-500">Teks ini akan dimunculkan ke Mahasiswa
                                        sebagai alasan Auto-Block ruangan.</p>
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
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>NAMA RUANGAN</th>
                            <th>TIPE</th>
                            <th>WAKTU & AGENDA</th>
                            <th>KATEGORI</th>
                            <th style="width: 80px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $j)
                            <tr class="mp-tr">
                                <td>
                                    <div style="font-weight: 700; color: #0D0D12;">
                                        {{ $j->ruangan->nama ?? 'Tidak Diketahui' }}</div>
                                    <div style="font-size: 11px; color: #666D80; margin-top: 2px;">Lt.
                                        {{ $j->ruangan->lantai ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($j->tipe_jadwal === 'rutin')
                                        <span class="mp-badge sm" style="background:#E0E7FF; color:#3730A3;">Rutin
                                            Mingguan</span>
                                    @else
                                        <span class="mp-badge sm" style="background:#FEF3C7; color:#92400E;">Acara
                                            Spesifik</span>
                                    @endif
                                </td>
                                <td>
                                    @if($j->tipe_jadwal === 'rutin')
                                        @php
                                            $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        @endphp
                                        <div style="font-weight: 600; color:#3730A3;">Setiap Hari
                                            {{ $namaHari[$j->hari] ?? 'Tidak Valid' }}</div>
                                    @else
                                        <div style="font-weight: 600; color:#92400E;">Tanggal:
                                            {{ \Carbon\Carbon::parse($j->tanggal_spesifik)->translatedFormat('d F Y') }}</div>
                                    @endif
                                    <div style="font-size: 12px; color: #475569; margin-top: 2px; font-weight: 500;">
                                        {{ substr($j->jam_mulai, 0, 5) }} WIB - {{ substr($j->jam_selesai, 0, 5) }} WIB
                                    </div>
                                    <div style="font-size: 12px; color: #666D80; margin-top: 2px;">
                                        <span class="font-bold text-gray-700">Agenda:</span> {{ $j->keterangan }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12px; font-weight: 600; color:#475569;">{{ $j->kategori }}</div>
                                </td>
                                <td style="text-align: center;" x-data="{ showDropdown: false, showEditModal: false, formType: '{{ $j->tipe_jadwal }}' }">
                                    <div class="relative inline-block text-left relative z-[1]">
                                        <button type="button" @click="showDropdown = !showDropdown" @click.away="showDropdown = false" class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-200 p-1.5 rounded-md transition-colors mr-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" /></svg>
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
                                                <button type="button" @click="showEditModal = true; showDropdown = false" class="w-full text-left px-4 py-2 text-[13px] text-gray-700 hover:bg-gray-100 hover:text-indigo-600 font-semibold focus:outline-none flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Edit Jadwal
                                                </button>
                                                <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus blokir jadwal ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-[13px] text-red-600 hover:bg-red-50 font-semibold focus:outline-none flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Hapus Jadwal
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- EDIT MODAL -->
                                    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            
                                            <div x-show="showEditModal" 
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md"
                                                 aria-hidden="true" @click="showEditModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                
                                            <div x-show="showEditModal" 
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">
                                                 
                                                <div class="flex items-center justify-between mb-5">
                                                    <h3 class="text-[18px] font-bold text-gray-900" id="modal-title">Edit Jadwal Internal</h3>
                                                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                
                                                <form action="{{ route('eoffice.peminjaman.admin.jadwal-internal.update', $j->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe Ruangan</label>
                                                            <select name="ruangan_id" required class="mp-input text-[14px]">
                                                                @foreach($ruangans as $r)
                                                                    <option value="{{ $r->id }}" {{ $r->id == $j->ruangan_id ? 'selected' : '' }}>{{ $r->nama }} - Lt. {{ $r->lantai }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe Agenda / Bentuk Jadwal</label>
                                                            <div class="flex gap-4 p-1 bg-gray-100 rounded-lg">
                                                                <label class="flex-1 text-center font-semibold text-[13px] py-1.5 rounded-md cursor-pointer transition-colors" 
                                                                    :class="formType === 'spesifik' ? 'bg-white shadow relative text-[#0B266E]' : 'text-gray-500 hover:text-gray-700'">
                                                                    <input type="radio" name="tipe_jadwal" value="spesifik" x-model="formType" class="hidden">
                                                                    Spesifik (Satu Hari)
                                                                </label>
                                                                <label class="flex-1 text-center font-semibold text-[13px] py-1.5 rounded-md cursor-pointer transition-colors" 
                                                                    :class="formType === 'rutin' ? 'bg-white shadow relative text-[#0B266E]' : 'text-gray-500 hover:text-gray-700'">
                                                                    <input type="radio" name="tipe_jadwal" value="rutin" x-model="formType" class="hidden">
                                                                    Kuliah (Mingguan)
                                                                </label>
                                                            </div>
                                                        </div>
                                
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori Acara</label>
                                                                <select name="kategori" required class="mp-input text-[14px]">
                                                                    <option value="Jadwal Akademik (Kuliah)" {{ $j->kategori == 'Jadwal Akademik (Kuliah)' ? 'selected' : '' }}>Jadwal Akademik (Kuliah)</option>
                                                                    <option value="Sidang / Ujian Akademik" {{ $j->kategori == 'Sidang / Ujian Akademik' ? 'selected' : '' }}>Sidang / Ujian Akademik</option>
                                                                    <option value="Rapat Internal Jurusan" {{ $j->kategori == 'Rapat Internal Jurusan' ? 'selected' : '' }}>Rapat Internal Jurusan</option>
                                                                    <option value="Bimbingan Mahasiswa" {{ $j->kategori == 'Bimbingan Mahasiswa' ? 'selected' : '' }}>Bimbingan Mahasiswa</option>
                                                                    <option value="Maintenance / Perbaikan" {{ $j->kategori == 'Maintenance / Perbaikan' ? 'selected' : '' }}>Maintenance / Perbaikan</option>
                                                                    <option value="Acara Kemahasiswaan" {{ $j->kategori == 'Acara Kemahasiswaan' ? 'selected' : '' }}>Acara Kemahasiswaan Khusus</option>
                                                                </select>
                                                            </div>
                                                            <div x-show="formType === 'spesifik'" :style="formType !== 'spesifik' ? 'display:none;' : ''">
                                                                <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih Tanggal</label>
                                                                <input type="date" name="tanggal_spesifik" value="{{ $j->tanggal_spesifik }}" :required="formType === 'spesifik'" class="mp-input text-[14px]">
                                                            </div>
                                                            <div x-show="formType === 'rutin'" :style="formType !== 'rutin' ? 'display:none;' : ''">
                                                                <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari Pertemuan</label>
                                                                <select name="hari" :required="formType === 'rutin'" class="mp-input text-[14px]">
                                                                    <option value="1" {{ $j->hari == 1 ? 'selected' : '' }}>Senin</option>
                                                                    <option value="2" {{ $j->hari == 2 ? 'selected' : '' }}>Selasa</option>
                                                                    <option value="3" {{ $j->hari == 3 ? 'selected' : '' }}>Rabu</option>
                                                                    <option value="4" {{ $j->hari == 4 ? 'selected' : '' }}>Kamis</option>
                                                                    <option value="5" {{ $j->hari == 5 ? 'selected' : '' }}>Jumat</option>
                                                                    <option value="6" {{ $j->hari == 6 ? 'selected' : '' }}>Sabtu</option>
                                                                    <option value="7" {{ $j->hari == 7 ? 'selected' : '' }}>Minggu</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Waktu Mulai</label>
                                                                <input type="time" name="jam_mulai" value="{{ substr($j->jam_mulai, 0, 5) }}" required class="mp-input text-[14px]">
                                                            </div>
                                                            <div>
                                                                <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Waktu Berakhir</label>
                                                                <input type="time" name="jam_selesai" value="{{ substr($j->jam_selesai, 0, 5) }}" required class="mp-input text-[14px]">
                                                            </div>
                                                        </div>
                                
                                                        <div>
                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Keterangan / Nama Matkul</label>
                                                            <input type="text" name="keterangan" value="{{ $j->keterangan }}" required class="mp-input text-[14px]">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                                        <button type="button" @click="showEditModal = false" class="mp-btn secondary md">Batal</button>
                                                        <button type="submit" class="mp-btn primary md">Simpan Perubahan</button>
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
                                    <div style="font-size:12px; margin-top:4px;">Gunakan tombol Tambah Jadwal di pojok kanan
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

</x-eoffice::manajemen-ruangan.layout>