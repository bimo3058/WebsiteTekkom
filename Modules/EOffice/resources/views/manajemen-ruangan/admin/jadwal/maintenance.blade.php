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
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih
                                            Ruangan</label>
                                        <select name="ruangan_id" required class="mp-input text-[14px]">
                                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                                            @foreach($ruangans as $r)
                                                <option value="{{ $r->id }}">{{ $r->nama }} - Lt. {{ $r->lantai }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
                                        <select name="kategori" x-model="kategoriType" required
                                            class="mp-input text-[14px]">
                                            <option value="Event / Kegiatan">Event / Kegiatan Mahasiswa</option>
                                            <option value="Rapat Internal">Rapat Internal Dosen</option>
                                            <option value="Ujian / Evaluasi">Ujian / Evaluasi (UTS/UAS)</option>
                                            <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                            <option value="Lainnya">Lainnya...</option>
                                        </select>
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
                    <select name="kategori"
                        class="px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat w-full max-w-[140px] sm:max-w-[180px] truncate"
                        style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;"
                        onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <option value="Event / Kegiatan" {{ request('kategori') == 'Event / Kegiatan' ? 'selected' : '' }}>Event / Kegiatan Mahasiswa</option>
                        <option value="Rapat Internal" {{ request('kategori') == 'Rapat Internal' ? 'selected' : '' }}>
                            Rapat Internal Dosen</option>
                        <option value="Ujian / Evaluasi" {{ request('kategori') == 'Ujian / Evaluasi' ? 'selected' : '' }}>Ujian / Evaluasi (UTS/UAS)</option>
                        <option value="Maintenance / Perbaikan" {{ request('kategori') == 'Maintenance / Perbaikan' ? 'selected' : '' }}>Maintenance / Perbaikan</option>
                        <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya...
                        </option>
                    </select>
                </div>
            </form>
        </div>
        <div class="mp-card-body p-0 mt-4 rounded-b-[12px] overflow-hidden">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>WAKTU</th>
                            <th>NAMA ACARA</th>
                            <th>RUANGAN</th>
                            <th style="width: 80px; text-align: center;">AKSI</th>
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
                                    x-data="{ showDropdown: false, showEditModal: false, formType: '{{ $j->tipe_jadwal }}', kategoriType: '{{ $j->kategori }}' }">
                                    <div class="relative inline-flex flex-col items-center justify-center w-full z-[1]">
                                        <button type="button" @click="showDropdown = !showDropdown"
                                            @click.away="showDropdown = false"
                                            class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 p-1.5 rounded-md transition-colors cursor-pointer">
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
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus blokir jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
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
                                                                <select name="ruangan_id" required
                                                                    class="mp-input text-[14px]">
                                                                    @foreach($ruangans as $r)
                                                                        <option value="{{ $r->id }}" {{ $r->id == $j->ruangan_id ? 'selected' : '' }}>
                                                                            {{ $r->nama }} - Lt.
                                                                            {{ $r->lantai }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
                                                                <select name="kategori" x-model="kategoriType" required
                                                                    class="mp-input text-[14px]">
                                                                    <option value="Event / Kegiatan" {{ $j->kategori == 'Event / Kegiatan' ? 'selected' : '' }}>Event / Kegiatan
                                                                        Mahasiswa</option>
                                                                    <option value="Rapat Internal" {{ $j->kategori == 'Rapat Internal' ? 'selected' : '' }}>Rapat Internal Dosen
                                                                    </option>
                                                                    <option value="Ujian / Evaluasi" {{ $j->kategori == 'Ujian / Evaluasi' ? 'selected' : '' }}>Ujian / Evaluasi
                                                                        (UTS/UAS)</option>
                                                                    <option value="Maintenance / Perbaikan" {{ $j->kategori == 'Maintenance / Perbaikan' ? 'selected' : '' }}>Maintenance / Perbaikan
                                                                    </option>
                                                                    <option value="Lainnya" {{ $j->kategori == 'Lainnya' ? 'selected' : '' }}>Lainnya...</option>
                                                                </select>
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

            <div style="padding: 16px;">
                {{ $jadwals->appends(['tipe' => $tipe])->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    </div> <!-- Close Alpine Wrapper -->
</x-eoffice::manajemen-ruangan.layout>