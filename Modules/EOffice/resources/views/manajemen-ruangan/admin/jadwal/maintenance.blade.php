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
                            style="gap: 6px; padding: 0 16px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                            Reset Semester
                        </button>
                    </form>

                    <button @click="showImportModal = true" class="mp-btn secondary md font-semibold"
                        style="gap: 6px; padding: 0 16px;">
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
                                            <option value="Event / Kegiatan">Event / Kegiatan Himatikan/BEM</option>
                                            <option value="Rapat Internal">Rapat Internal Dosen</option>
                                            <option value="Ujian / Evaluasi">Ujian / Evaluasi (UTS/UAS)</option>
                                            <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                            <option value="Lainnya">Lainnya...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                                    <h4 class="text-[13px] font-bold text-indigo-900 border-b border-indigo-100 pb-2 mb-3">Informasi Acara</h4>
                                    <div>
                                        <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama Acara</label>
                                        <textarea name="keterangan" required rows="2" class="mp-input text-[14px]" placeholder="Misal: Rapat Evaluasi Kurikulum..."></textarea>
                                    </div>
                                </div>

                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                    <h4
                                        class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">
                                        Pengaturan Waktu Pelaksanaan</h4>

                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                            Jadwal</label>
                                        <select name="tipe_jadwal" x-model="formType" required
                                            class="mp-input text-[14px]">
                                            <option value="spesifik">Jadwal Spesifik (Pilih Tanggal)</option>
                                            <option value="rutin">Jadwal Rutin (Pilih Hari dalam Seminggu)</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Tampil Jika Tipe Rutin -->
                                        <template x-if="formType === 'rutin'">
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
                                        </template>

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

                                    <!-- Tanggal Efektif (Hanya Untuk Rutin) -->
                                    <template x-if="formType === 'rutin'">
                                        <div class="grid grid-cols-2 gap-4 mt-3"
                                            style="padding:15px; background:white; border:1px solid #E2E8F0; border-radius:12px;">
                                            <div class="col-span-2">
                                                <h4
                                                    class="text-[12px] font-bold text-slate-700 border-b border-slate-200 pb-2 mb-2">
                                                    Periode Semester Masa Aktif Jadwal Rutin</h4>
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
                                    </template>
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
                                <th>KATEGORI & TIPE</th>
                                <th>HARI / TANGGAL</th>
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
                                        <div style="font-weight: 700; color:#3730A3;">
                                            {{ $j->kategori ?? '-' }}
                                        </div>
                                        <div style="font-size: 11px; margin-top:2px;">
                                            <span class="mp-badge {{ $j->tipe_jadwal === 'rutin' ? 'success' : 'primary' }} sm">
                                                {{ ucfirst($j->tipe_jadwal) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($j->tipe_jadwal === 'rutin')
                                            @php
                                                $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                            @endphp
                                            <div style="font-weight: 600; color:#0D0D12;">
                                                {{ $namaHari[$j->hari] ?? 'Tidak Valid' }}
                                            </div>
                                        @else
                                            <div style="font-weight: 600; color:#0D0D12;">
                                                {{ \Carbon\Carbon::parse($j->tanggal_spesifik)->translatedFormat('d F Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0D0D12;">
                                            {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color:#4B5563; max-width:250px; overflow-wrap:break-word;">
                                            {{ $j->keterangan ?: '-' }}
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
                                        <div class="relative inline-block text-left relative z-[1]">
                                            <button type="button" @click="showDropdown = !showDropdown"
                                                @click.away="showDropdown = false"
                                                class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-200 p-1.5 rounded-md transition-colors mr-2">
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
                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe Ruangan</label>
                                                                    <select name="ruangan_id" required class="mp-input text-[14px]">
                                                                        @foreach($ruangans as $r)
                                                                            <option value="{{ $r->id }}" {{ $r->id == $j->ruangan_id ? 'selected' : '' }}>{{ $r->nama }} - Lt. {{ $r->lantai }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
                                                                    <select name="kategori" x-model="kategoriType" required class="mp-input text-[14px]">
                                                                        <option value="Event / Kegiatan" {{ $j->kategori == 'Event / Kegiatan' ? 'selected' : '' }}>Event / Kegiatan Himatikan/BEM</option>
                                                                        <option value="Rapat Internal" {{ $j->kategori == 'Rapat Internal' ? 'selected' : '' }}>Rapat Internal Dosen</option>
                                                                        <option value="Ujian / Evaluasi" {{ $j->kategori == 'Ujian / Evaluasi' ? 'selected' : '' }}>Ujian / Evaluasi (UTS/UAS)</option>
                                                                        <option value="Maintenance / Perbaikan" {{ $j->kategori == 'Maintenance / Perbaikan' ? 'selected' : '' }}>Maintenance / Perbaikan</option>
                                                                        <option value="Lainnya" {{ $j->kategori == 'Lainnya' ? 'selected' : '' }}>Lainnya...</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                                                                <h4 class="text-[13px] font-bold text-indigo-900 border-b border-indigo-100 pb-2 mb-3">Informasi Acara</h4>
                                                                <div>
                                                                    <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama Acara</label>
                                                                    <textarea name="keterangan" required rows="2" class="mp-input text-[14px]">{{ $j->keterangan }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                                                <h4 class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">Pengaturan Waktu Pelaksanaan</h4>
                                                                
                                                                <div>
                                                                    <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe Jadwal</label>
                                                                    <select name="tipe_jadwal" x-model="formType" required class="mp-input text-[14px]">
                                                                        <option value="spesifik" {{ $j->tipe_jadwal === 'spesifik' ? 'selected' : '' }}>Jadwal Spesifik (Pilih Tanggal)</option>
                                                                        <option value="rutin" {{ $j->tipe_jadwal === 'rutin' ? 'selected' : '' }}>Jadwal Rutin (Pilih Hari dalam Seminggu)</option>
                                                                    </select>
                                                                </div>

                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <template x-if="formType === 'rutin'">
                                                                        <div>
                                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Hari Pertemuan</label>
                                                                            <select name="hari" required class="mp-input text-[14px]">
                                                                                <option value="1" {{ $j->hari == 1 ? 'selected' : '' }}>Senin</option>
                                                                                <option value="2" {{ $j->hari == 2 ? 'selected' : '' }}>Selasa</option>
                                                                                <option value="3" {{ $j->hari == 3 ? 'selected' : '' }}>Rabu</option>
                                                                                <option value="4" {{ $j->hari == 4 ? 'selected' : '' }}>Kamis</option>
                                                                                <option value="5" {{ $j->hari == 5 ? 'selected' : '' }}>Jumat</option>
                                                                                <option value="6" {{ $j->hari == 6 ? 'selected' : '' }}>Sabtu</option>
                                                                                <option value="7" {{ $j->hari == 7 ? 'selected' : '' }}>Minggu</option>
                                                                            </select>
                                                                        </div>
                                                                    </template>

                                                                    <template x-if="formType === 'spesifik'">
                                                                        <div>
                                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tanggal Spesifik</label>
                                                                            <input type="date" name="tanggal_spesifik" value="{{ $j->tanggal_spesifik }}" required class="mp-input text-[14px]">
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

                                                                <template x-if="formType === 'rutin'">
                                                                    <div class="grid grid-cols-2 gap-4 mt-3" style="padding:15px; background:white; border:1px solid #E2E8F0; border-radius:12px;">
                                                                        <div class="col-span-2">
                                                                            <h4 class="text-[12px] font-bold text-slate-700 border-b border-slate-200 pb-2 mb-2">Periode Semester Masa Aktif Jadwal Rutin</h4>
                                                                        </div>
                                                                        <div>
                                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Dimulai Sejak</label>
                                                                            <input type="date" name="tgl_mulai_efektif" value="{{ $j->tgl_mulai_efektif }}" required class="mp-input text-[14px]">
                                                                        </div>
                                                                        <div>
                                                                            <label class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Berakhir Pada</label>
                                                                            <input type="date" name="tgl_selesai_efektif" value="{{ $j->tgl_selesai_efektif }}" required class="mp-input text-[14px]">
                                                                        </div>
                                                                    </div>
                                                                </template>
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