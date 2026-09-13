<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Koordinator">

    @if($praktikum)
        <x-eoffice::manajemen-praktikum.dosen-header :praktikum="$praktikum" />
    @else
        {{-- Page Header --}}
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
                <p class="mp-page-sub">Review pendaftar koordinator sesuai praktikum yang Anda ampu</p>
            </div>
        </div>
    @endif

    {{-- Content wrapper: flex column so cards flow correctly below sticky header --}}
    <div style="display: flex; flex-direction: column; gap: 19px;">

    {{-- SECTION: KOORDINATOR AKTIF --}}
    @if(isset($praktikum))
        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">

            {{-- Kiri: Koordinator Aktif --}}
            <div class="mp-card" style="flex: 1; min-width: 300px; padding: 0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Koordinator Aktif</span>
                </div>
                <div style="padding: 24px;">

                    @if($koordinator)
                        <div
                            style="font-size: 12px; font-weight: 600; color: #10B981; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; display:flex; align-items:center; gap:6px;">
                            <span class="dot" style="background:#10B981;"></span> Koordinator Aktif
                        </div>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="mp-av navy" style="width: 45px; height: 45px; font-size: 13px;">
                                {{ strtoupper(substr($koordinator->name ?? 'UN', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: #0D0D12;">{{ $koordinator->name ?? '-' }}
                                </div>
                                <div style="font-size: 12px; color: #666D80; margin-top:2px;">
                                    {{ $koordinator->student->student_number ?? '—' }} · {{ $koordinator->email ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                            <div
                                style="width: 44px; height: 44px; border-radius: 50%; background: #FFF5F5; border: 1px solid #FFEBEB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700; color: #0D0D12; margin-bottom: 4px;">Belum Ada
                                    Koordinator</div>
                                <div style="font-size: 13px; color: #666D80; line-height: 1.6;">
                                    <strong>{{ $praktikum->nama ?? '-' }}</strong> belum memiliki koordinator. Silahkan buka
                                    pendaftaran koordinator atau tunjuk koordinator via form di samping.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Kanan: Tunjuk Koordinator Langsung --}}
            <div class="mp-card" style="width: 380px; padding: 0; background: #FAFAFA;" x-data="{
             query: '', results: [], showDropdown: false, selectedUser: null, isLoading: false,
             init() { this.$watch('query', value => { if(this.selectedUser && this.selectedUser.text !== value) { this.selectedUser = null; } }); },
             async search() {
                 if(this.query.length < 3) { this.results = []; return; }
                 this.isLoading = true;
                 try {
                     const res = await fetch(`{{ route('eoffice.manprak.dosen.search-praktikan') }}?praktikum_id={{ $praktikum->id ?? '' }}&q=${encodeURIComponent(this.query)}`);
                     const data = await res.json();
                     this.results = data;
                 } catch (e) { this.results = []; }
                 this.isLoading = false;
             },
             selectUser(user) { this.selectedUser = user; this.query = user.text; this.showDropdown = false; }
         }">
                <div class="mp-card-header" style="background: #fff; border-bottom: 1px solid #DFE1E7;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M19 8v6" />
                            <path d="M16 11h6" />
                        </svg>
                        <span class="mp-card-title">Tunjuk Koordinator</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}" style="padding: 24px;">
                    @csrf
                    <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">
                    <input type="hidden" name="nim" :value="selectedUser ? selectedUser.id : ''">

                    <label
                        style="display: block; font-size: 13px; font-weight: 500; color: #666D80; margin-bottom: 6px;">Cari
                        Mahasiswa</label>
                    <div style="position: relative; margin-bottom: 16px;">
                        <input type="text" x-model="query" @input.debounce.300ms="search()"
                            placeholder="Ketik nama atau NIM..." class="mp-input text-[13px]"
                            style="width: 100%; border-radius: 8px; padding-right: 36px; background: #fff;"
                            @focus="showDropdown = true" @click.away="showDropdown = false">

                        {{-- X Delete Icon --}}
                        <div x-show="selectedUser"
                            style="display: none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                            @click="selectedUser = null; query = '';">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                class="text-red-500 hover:text-red-600 transition-colors" stroke="#DC2626" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </div>

                        {{-- Dropdown --}}
                        <div x-show="showDropdown && query.length >= 3"
                            style="position: absolute; z-index: 50; width: 100%; margin-top: 4px; background: white; border: 1px solid #DFE1E7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); max-height: 220px; overflow-y: auto;">
                            <div x-show="isLoading"
                                style="padding: 12px 16px; text-align: center; color: #666D80; font-size: 12px;">Mencari
                                data...</div>
                            <template x-if="!isLoading && results.length > 0">
                                <div>
                                    <template x-for="user in results" :key="user.id">
                                        <div @click="selectUser(user)"
                                            style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #F3F4F6;"
                                            onmouseover="this.style.background='#FAFAFA'"
                                            onmouseout="this.style.background='transparent'">
                                            <div style="font-size: 13px; font-weight: 700; color: #0D0D12;"
                                                x-text="user.text.split(' - ')[0]"></div>
                                            <div style="font-size: 11px; color: #666D80; margin-top: 2px;" x-text="user.id">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div x-show="!isLoading && results.length === 0"
                                style="padding: 12px 16px; text-align: center; color: #DF1C41; font-size: 12px;">Tidak
                                ditemukan.</div>
                        </div>
                    </div>

                    <button type="submit"
                        style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; transition: all 0.2s; font-family: inherit; display: flex; align-items: center; justify-content: center;"
                        x-bind:disabled="!selectedUser"
                        x-bind:style="{ backgroundColor: !selectedUser ? '#F3F5F9' : '#0B266E', color: !selectedUser ? '#293C79' : '#FFFFFF', cursor: !selectedUser ? 'not-allowed' : 'pointer' }">
                        Simpan
                    </button>
                </form>
            </div>

        </div>
        </div>
    @endif

    @php
        $periodeAktif = \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $praktikum?->id ?? '')
            ->where('jenis', 'koordinator')
            ->where('is_aktif', true)
            ->first();
    @endphp
    <div class="mp-card flex-shrink-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Form Pendaftaran Koordinator</span>
        </div>
        <form action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">

            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Judul</label>
                    <input type="text" name="nama" class="mp-input w-full"
                        placeholder="Contoh: Seleksi Asisten Praktikum Genap 2025"
                        value="{{ old('nama', $periodeAktif->nama ?? '') }}">
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="mp-input w-full" rows="3" maxlength="500"
                        placeholder="Tuliskan deskripsi atau link soal tes..."
                        style="resize: vertical; min-height: 80px; max-height: 200px;">{{ old('deskripsi', $periodeAktif->deskripsi ?? '') }}</textarea>
                    <p class="text-[11px] text-[#666D80] mt-1">Maksimal 500 karakter.</p>
                </div>

                <hr class="border-t border-dashed border-[#DFE1E7] my-2">

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dibuka Pada</label>
                    <div class="relative">
                        <input type="text" name="dibuka_pada"
                            class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]"
                            placeholder="Pilih tanggal dan waktu..."
                            value="{{ old('dibuka_pada', isset($periodeAktif) && $periodeAktif->dibuka_pada ? \Carbon\Carbon::parse($periodeAktif->dibuka_pada)->format('Y-m-d H:i') : '') }}"
                            style="padding-right: 32px; cursor: pointer;">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <p class="text-[11px] text-[#666D80] mt-1">Kosongkan untuk langsung dibuka saat ini.</p>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Ditutup Pada <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="ditutup_pada"
                            class="flatpickr-input mp-input w-full bg-white border border-[#DFE1E7] rounded-[8px] px-3 py-2 text-[13px] text-[#0D0D12]"
                            placeholder="Pilih tanggal dan waktu..."
                            value="{{ old('ditutup_pada', isset($periodeAktif) && $periodeAktif->ditutup_pada ? \Carbon\Carbon::parse($periodeAktif->ditutup_pada)->format('Y-m-d H:i') : '') }}"
                            required style="padding-right: 32px; cursor: pointer;">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16"
                            height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <p class="text-[11px] text-[#666D80] mt-1">Wajib diisi sebagai batas penutupan.</p>
                </div>

                <hr class="border-t border-dashed border-[#DFE1E7] my-2">

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-3">Biodata & Persyaratan (Wajib
                        diisi mahasiswa)</label>

                    <div class="flex gap-4 mb-3">
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">Nama Mahasiswa</label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">NIM</label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Terisi otomatis" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                    </div>

                    <div class="flex gap-4 mb-3">
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">IPK <span
                                    class="text-red-500">*</span></label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Diisi oleh mahasiswa" disabled
                                style="background: #F9FAFB; color: #808897;">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-1">Transkrip Nilai <span
                                    class="text-red-500">*</span></label>
                            <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)" disabled
                                style="background: #F9FAFB; color: #808897; border-style: dashed;">
                        </div>
                    </div>

                    <div x-data="{
                        berkasName: '{{ $periodeAktif->nama_berkas_tambahan ?? '' }}',
                        berkasType: '{{ $periodeAktif->jenis_berkas_tambahan ?? '' }}',
                        berkasDesc: '{{ $periodeAktif->keterangan_berkas_tambahan ?? '' }}',
                        removeBerkas() {
                            this.berkasName = '';
                            this.berkasType = '';
                            this.berkasDesc = '';
                        }
                    }" @save-berkas.window="
                        berkasName = $event.detail.name;
                        berkasType = $event.detail.type;
                        berkasDesc = $event.detail.desc;
                    ">
                        <input type="hidden" name="nama_berkas_tambahan" :value="berkasName">
                        <input type="hidden" name="jenis_berkas_tambahan" :value="berkasType">
                        <input type="hidden" name="keterangan_berkas_tambahan" :value="berkasDesc">

                        <div class="flex gap-4">
                            
                                                        <div class="flex-1">
                                <label class="block text-[11px] font-semibold text-[#353849] mb-1">Keanggotaan
                                    CERC</label>
                                <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)"
                                    disabled style="background: #F9FAFB; color: #808897; border-style: dashed;">
                            </div>
<div class="flex-1">
                                {{-- If no berkas --}}
                                <div x-show="!berkasName">
                                    <label class="block text-[11px] font-semibold text-transparent mb-1 select-none">&nbsp;</label>
                                    <button type="button" @click="$dispatch('open-berkas-modal', { name: berkasName, type: berkasType, desc: berkasDesc })"
                                        class="h-[38px] w-full text-[#0B266E] text-[12px] font-semibold flex justify-center items-center gap-1.5 hover:underline bg-[#F6F8FA] px-4 rounded-[8px] border border-dashed border-[#DFE1E7]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                        Tambah Pengumpulan Berkas
                                    </button>
                                </div>
                                
                                {{-- If berkas exists --}}
                                <div x-show="berkasName" style="display: none;" x-transition>
                                    <label class="block text-[11px] font-semibold text-[#353849] mb-1">
                                        <span x-text="berkasName"></span> <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" class="mp-input w-full text-[12px]"
                                            :value="berkasType === 'pdf' ? 'Upload File (.pdf)' : 'Input Link'" disabled
                                            style="background: #F9FAFB; color: #808897; border-style: dashed; padding-right: 36px;">
                                        <button type="button" @click="removeBerkas()"
                                            class="absolute right-1 top-1/2 -translate-y-1/2 text-[#DF1C41] hover:bg-[#FFF5F5] p-1.5 rounded-[6px] transition-colors"
                                            title="Hapus Berkas">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                                                <path d="M10 11v6M14 11v6" />
                                            </svg>
                                        </button>
                                    </div>
                                    <template x-if="berkasDesc">
                                        <p class="text-[11px] text-[#666D80] mt-1" x-text="berkasDesc"></p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>


                    </div>
                </div>

            <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                @if($periodeAktif && $periodeAktif->isSedangBuka())
                    <button type="submit" form="tutup-periode" class="mp-btn error sm"
                        onclick="return confirm('Tutup periode pendaftaran asisten praktikum sekarang?')"
                        style="height:36px; padding:0 16px;">Tutup Periode Sekarang</button>
                @endif
                <button type="submit" class="mp-btn primary sm"
                    style="height:36px; padding:0 16px;">{{ $periodeAktif ? 'Update Pendaftaran' : 'Buka Periode Pendaftaran' }}</button>
            </div>
        </form>
    </div>

    {{-- SECTION: DAFTAR CALON KOORDINATOR --}}
    <div class="mp-card flex-shrink-0" style="padding: 0;">
        {{-- Card Header --}}
        <div class="mp-card-header" style="display: flex; align-items: center; justify-content: space-between;">
            <span class="mp-card-title">Daftar Calon Koordinator</span>
            <span class="mp-badge navy sm">{{ $pendaftaran->total() }} pendaftar</span>
        </div>

        {{-- Filter Bar: 1 baris --}}
        <div style="padding: 12px 18px; border-bottom: 1px solid var(--c-border); background: #FAFAFA;">
            <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: nowrap;">
                {{-- Search --}}
                <div style="position: relative; flex: 1; min-width: 0;">
                    <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events:none;"
                        width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"
                        stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa..." class="mp-input"
                        style="width: 100%; padding-left: 32px; box-sizing: border-box; height: 38px;">
                </div>

                {{-- Sort Dropdown --}}
                <x-eoffice::manajemen-praktikum.ui.select name="sort" :options="[
        ['value' => 'terbaru', 'label' => 'Terbaru'],
        ['value' => 'terlama', 'label' => 'Terlama'],
        ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
        ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
    ]" :selected="request('sort', 'terbaru')"
                    placeholder="Terbaru" minWidth="130px" />

                {{-- Status Dropdown --}}
                <x-eoffice::manajemen-praktikum.ui.select name="status_dosen" :options="[
        ['value' => '', 'label' => 'Semua Status'],
        ['value' => 'menunggu', 'label' => 'Menunggu Review'],
        ['value' => 'disetujui', 'label' => 'Sudah Disetujui'],
        ['value' => 'ditolak', 'label' => 'Ditolak']
    ]" :selected="request('status_dosen', '')"
                    placeholder="Semua Status" minWidth="140px" />

                {{-- Filter Button --}}
                <button type="submit" class="mp-btn primary" style="flex-shrink: 0; padding: 0 14px; height: 38px; font-size: 13px; font-weight: 600;">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:13px; min-width: max-content;">
                <thead style="background:#F9FAFB; border-bottom:1px solid #E5E7EB;">
                    <tr>
                        <th class="mp-th text-left"
                            style="padding:14px 20px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            NO</th>
                        <th class="mp-th text-left"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            NAMA MAHASISWA</th>
                        <th class="mp-th text-left"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            NIM</th>
                        <th class="mp-th text-center"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            IPK</th>
                        <th class="mp-th text-left"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            TRANSKRIP NILAI</th>
                        
                                                <th class="mp-th text-left"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            KEANGGOTAAN CERC</th>
<th class="mp-th text-center"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            STATUS</th>
                        <th class="mp-th text-left"
                            style="padding:14px 16px; color:#8CA3BA; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                            AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $index => $p)
                        @php
                            $namaParts = explode(' ', $p->user?->name ?? 'UN');
                            $initials = strtoupper(substr($namaParts[0] ?? 'U', 0, 1) . substr($namaParts[1] ?? $namaParts[0] ?? 'N', 0, 1));
                            $avColors = ['sky', 'navy', 'green', 'yellow', 'violet', 'red'];
                            $avColor = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                            $ipkOk = ($p->ipk ?? 0) >= 3.0;

                            $emailStr = $p->user?->email ?? '';
                            $nim = explode('@', $emailStr)[0];
                            if (empty($nim))
                                $nim = '—';
                        @endphp
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:14px 20px; color:#0B266E; font-weight:600; font-size: 13px;">
                                {{ $pendaftaran->firstItem() + $index }}
                            </td>

                            <td style="padding:14px 16px;">
                                <div class="flex items-center gap-3">
                                    <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#0D0D12; font-size: 13px;">
                                            {{ $p->user?->name ?? '—' }}</div>
                                        <div style="font-size:11px;color:#666D80;margin-top:2px;">{{ $p->user?->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td style="padding:14px 16px; color:#4B5563; font-size: 13px; font-weight: 400;">
                                {{ $nim }}
                            </td>

                            <td style="padding:14px 16px; text-align:center;">
                                <span
                                    style="font-weight:400; color:#353849; font-size: 13px;">{{ number_format($p->ipk ?? 0, 2) }}</span>
                            </td>

                            <td style="padding:14px 16px;">
                                @if($p->transkrip_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice') }}"
                                        target="_blank"
                                        style="font-size:13px; font-weight:600; color:#0B266E; text-decoration:none;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px; color:#808897;">—</span>
                                @endif
                            </td>

                            

                                                        <td style="padding:14px 16px;">
                                @if($p->berkas_cerc_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, 'eoffice') }}"
                                        target="_blank"
                                        style="display:inline-flex; align-items:center; gap:6px; color:#0B266E; font-size:12px; font-weight:600; text-decoration:none;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="color:#808897; font-size:12px; font-style:italic;">Tidak ada</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                                    @if($p->status_dosen === 'disetujui')
                                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                                    @elseif($p->status_dosen === 'ditolak')
                                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                                    @else
                                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                                    @endif

                                    @if($p->status_dosen !== 'menunggu')
                                        @if($p->status === 'pending')
                                            <span style="font-size:11px; color:#808897;">Menunggu Admin</span>
                                        @elseif($p->status === 'approved')
                                            <span style="font-size:11px; color:#059669; font-weight:500;">Disetujui Admin</span>
                                        @elseif($p->status === 'rejected')
                                            <span style="font-size:11px; color:#DC2626; font-weight:500;">Ditolak Admin</span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <td style="padding:14px 16px;">
                                <div class="flex items-center gap-2">
                                    @if($p->status_dosen === 'menunggu')
                                        <form action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="mp-btn ghost sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="mp-btn destructive sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size:13px;color:#808897;">Sudah diproses</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center" style="padding:32px 16px; color:#666D80; font-size:13px;">
                                Belum ada pendaftar koordinator untuk praktikum ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Custom Fungsional --}}
        @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }"
                        class="relative" @click.away="open = false">
                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                            :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                            @click="open = !open">
                            <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per
                                halaman</span>
                            <div class="flex items-center gap-1 font-semibold text-[12px]">
                                <span x-text="selected"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div x-show="open" @click.away="open = false" style="display: none;"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full left-0 mt-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                            <template x-for="option in options" :key="option">
                                <a :href="'?per_page=' + option + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}#daftar-pendaftaran'"
                                    class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                    :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <span x-text="option"></span>
                                    <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>
                    <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $pendaftaran->firstItem() ?? 0 }}
                        sampai {{ $pendaftaran->lastItem() ?? 0 }} dari {{ $pendaftaran->total() }} data</div>
                </div>

                <div style="display:flex; gap:4px;">
                    @if ($pendaftaran->onFirstPage())
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $pendaftaran->previousPageUrl() }}#daftar-pendaftaran"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $pendaftaran->currentPage();
                        $last = $pendaftaran->lastPage();
                        $start = max(1, $current - 1);
                        $end = min($start + 2, $last);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <span
                                style="width:32px; height:32px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $pendaftaran->url($i) }}#daftar-pendaftaran"
                                style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($pendaftaran->hasMorePages())
                        <a href="{{ $pendaftaran->nextPageUrl() }}#daftar-pendaftaran"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </a>
                    @else
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Styling Flatpickr untuk mengikuti tema desain */
        .flatpickr-calendar {
            box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12) !important;
            border: 1px solid #DFE1E7 !important;
            border-radius: 12px !important;
            font-family: inherit !important;
            padding: 8px !important;
        }

        .flatpickr-calendar.hasTime .flatpickr-time {
            border-top: 1px solid #DFE1E7 !important;
            margin-top: 8px;
        }

        .flatpickr-months {
            margin-bottom: 8px !important;
        }

        .flatpickr-current-month {
            font-size: 12px !important;
            padding-top: 0px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            display: none !important;
        }

        .flatpickr-current-month .cur-month {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #1a1a2e !important;
            padding-top: 0px !important;
            cursor: pointer;
        }

        .flatpickr-current-month .cur-month:hover {
            color: #0B266E !important;
        }


        /* Month dropdown arrow animation */
        .flatpickr-months .flatpickr-month {
            position: relative;
        }

        .custom-month-chevron {
            display: inline-block;
            margin-left: 4px;
            transition: transform 0.25s ease;
            vertical-align: middle;
            font-size: 10px;
            color: #666D80;
        }

        .custom-month-chevron.open {
            transform: rotate(180deg);
        }

        .custom-month-dropdown {
            position: absolute;
            background: #fff;
            border: 1px solid #DFE1E7;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
            z-index: 99999;
            display: none;
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease;
            width: 140px;
            max-height: 220px;
            overflow-y: auto;
            padding: 6px;
            font-family: inherit;
            visibility: hidden;
            opacity: 0;
            transform: translateY(-8px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .custom-month-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .custom-month-dropdown::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-month-dropdown::-webkit-scrollbar-thumb {
            background: #DFE1E7;
            border-radius: 4px;
        }

        .custom-month-dropdown.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .custom-month-arrow svg {
            transition: transform 0.2s ease;
        }

        .custom-month-arrow.open svg {
            transform: rotate(180deg);
        }

        .custom-month-item {
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #353849;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .custom-month-item:hover {
            background: #F6F8FA;
            color: #0B266E;
        }

        .custom-month-dropdown.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .custom-month-item.active {
            background: #0B266E !important;
            color: #fff !important;
        }

        .flatpickr-current-month .numInputWrapper {
            width: 65px !important;
            border-radius: 6px !important;
            margin-left: 4px;
        }

        .flatpickr-current-month input.cur-year {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #1a1a2e !important;
            padding: 6px 8px !important;
            border: 1px solid transparent !important;
            border-radius: 6px !important;
            height: 32px !important;
            box-sizing: border-box !important;
            background: transparent !important;
            transition: all 0.2s;
        }

        .flatpickr-current-month .numInputWrapper:hover input.cur-year {
            border: 1px solid #DFE1E7 !important;
            background: #F9FAFB !important;
        }

        /* Customizing the arrows */
        .flatpickr-current-month .numInputWrapper span.arrowUp,
        .flatpickr-current-month .numInputWrapper span.arrowDown {
            border: none !important;
            border-left: 1px solid transparent !important;
            right: 1px;
        }

        .flatpickr-current-month .numInputWrapper:hover span.arrowUp,
        .flatpickr-current-month .numInputWrapper:hover span.arrowDown {
            border-left: 1px solid #DFE1E7 !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp {
            border-top-right-radius: 5px;
            top: 1px;
        }

        .flatpickr-current-month .numInputWrapper span.arrowDown {
            border-bottom-right-radius: 5px;
            bottom: 1px;
        }

        .flatpickr-current-month .numInputWrapper span:hover {
            background: #F0F2F5 !important;
        }

        /* Color of the arrow triangles */
        .flatpickr-current-month .numInputWrapper span.arrowUp:after {
            border-bottom-color: #666D80 !important;
            top: 35% !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-top-color: #666D80 !important;
            top: 40% !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp:hover:after {
            border-bottom-color: #0B266E !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowDown:hover:after {
            border-top-color: #0B266E !important;
        }

        .flatpickr-day {
            border-radius: 8px !important;
            font-size: 13px !important;
            color: #353849 !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:focus,
        .flatpickr-day.selected:hover {
            background: #0B266E !important;
            border-color: #0B266E !important;
            color: #fff !important;
            font-weight: 600;
        }

        .flatpickr-day:hover,
        .flatpickr-day.prevMonthDay:hover,
        .flatpickr-day.nextMonthDay:hover {
            background: #F6F8FA !important;
            border-color: #DFE1E7 !important;
        }

        .flatpickr-time input {
            color: #353849 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .flatpickr-time .flatpickr-time-separator {
            color: #808897 !important;
        }

        .flatpickr-time .numInputWrapper:hover {
            background: #F6F8FA !important;
        }
    </style>
    <script>
        function initFlatpickr() {
            if (typeof flatpickr === 'undefined') {
                setTimeout(initFlatpickr, 100);
                return;
            }

            var commonConfig = {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                allowInput: true,
                monthSelectorType: "static",
                appendTo: document.body,
                static: false,
                onReady: function (selectedDates, dateStr, instance) {
                    // Buat custom dropdown untuk ganti bulan secara estetik
                    const monthDropdown = document.createElement('div');
                    monthDropdown.className = 'custom-month-dropdown';

                    const monthsStr = instance.l10n.months.longhand;
                    monthsStr.forEach((month, index) => {
                        const div = document.createElement('div');
                        div.className = 'custom-month-item';
                        div.textContent = month;
                        div.addEventListener('click', (e) => {
                            e.stopPropagation();
                            instance.changeMonth(index, false); // set bulan
                            monthDropdown.classList.remove('show');
                            if (instance.monthNav.querySelector('.custom-month-arrow')) {
                                instance.monthNav.querySelector('.custom-month-arrow').classList.remove('open');
                            }
                        });
                        monthDropdown.appendChild(div);
                    });

                    instance.calendarContainer.appendChild(monthDropdown);

                    const monthElement = instance.monthNav.querySelector('.cur-month');
                    if (monthElement) {
                        // Add simple arrow to element parent to avoid reset issue
                        const arrow = document.createElement('span');
                        arrow.className = 'custom-month-arrow';
                        arrow.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;
                        arrow.style.marginLeft = '4px';
                        arrow.style.cursor = 'pointer';
                        arrow.style.display = 'inline-flex';
                        arrow.style.alignItems = 'center';
                        monthElement.parentNode.insertBefore(arrow, monthElement.nextSibling);

                        // Wrapper trigger
                        const toggleDropdown = (e) => {
                            e.stopPropagation();
                            monthDropdown.classList.toggle('show');

                            if (monthDropdown.classList.contains('show')) {
                                arrow.classList.add('open');
                            } else {
                                arrow.classList.remove('open');
                            }

                            const monthRect = monthElement.getBoundingClientRect();
                            const calRect = instance.calendarContainer.getBoundingClientRect();

                            monthDropdown.style.top = (monthRect.bottom - calRect.top + 3) + 'px';
                            monthDropdown.style.left = (monthRect.left - calRect.left - 6) + 'px';

                            const items = monthDropdown.querySelectorAll('.custom-month-item');
                            items.forEach((item, idx) => {
                                item.classList.toggle('active', idx === instance.currentMonth);
                            });

                            // Scroll into view safely
                            const activeItem = monthDropdown.querySelector('.custom-month-item.active');
                            if (activeItem && monthDropdown.classList.contains('show')) {
                                monthDropdown.scrollTop = activeItem.offsetTop - 10;
                            }
                        };

                        monthElement.addEventListener('click', toggleDropdown);
                        arrow.addEventListener('click', toggleDropdown);
                    }

                    // Hide if clicked out
                    document.addEventListener('click', (e) => {
                        if (monthDropdown.classList.contains('show') && !instance.monthNav.contains(e.target) && !monthDropdown.contains(e.target)) {
                            monthDropdown.classList.remove('show');
                            if (monthElement.nextSibling && monthElement.nextSibling.classList) {
                                monthElement.nextSibling.classList.remove('open');
                            }
                        }
                    });
                }
            };

        // Init untuk semua input tanggal di halaman ini
        document.querySelectorAll('input[name="dibuka_pada"], input[name="ditutup_pada"]').forEach(function (el) {
            flatpickr(el, commonConfig);
        });
    }
        initFlatpickr();
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quizBuilder', () => ({
                showQuizModal: false,
                questions: [],
                init() {
                    try {
                        // For live interaction preview
                        let existing = `{!! isset($periodeAktif) && $periodeAktif?->konfigurasi_kuis ? json_encode($periodeAktif->konfigurasi_kuis) : '[]' !!}`;
                        this.questions = JSON.parse(existing);
                    } catch (e) {
                        this.questions = [];
                    }
                },
                addQuestion(type) {
                    this.questions.push({
                        id: Date.now(),
                        tipe: type,
                        pertanyaan: '',
                        poin: 10,
                        opsi: type === 'pilihan_ganda' ? [
                            { text: 'Opsi Jawaban A', is_correct: true },
                            { text: 'Opsi Jawaban B', is_correct: false }
                        ] : []
                    });
                    setTimeout(() => {
                        const modalBody = document.querySelector('.overflow-y-auto');
                        if (modalBody) modalBody.scrollTop = modalBody.scrollHeight;
                    }, 50);
                },
                removeQuestion(index) {
                    if (confirm('Yakin ingin menghapus form soal ini?')) {
                        this.questions.splice(index, 1);
                    }
                },
                addOption(qIndex) {
                    this.questions[qIndex].opsi.push({ text: '', is_correct: false });
                },
                removeOption(qIndex, optIndex) {
                    this.questions[qIndex].opsi.splice(optIndex, 1);
                },
                setCorrectOption(qIndex, optIndex) {
                    this.questions[qIndex].opsi.forEach((o, i) => o.is_correct = (i === optIndex));
                }
            }));
        });
    </script>
</x-eoffice::manajemen-praktikum.layout>

<!-- Modal Tambah Berkas (Global) -->
<div x-data="{
    show: false,
    tempName: '',
    tempType: '',
    tempDesc: '',
    dropdownOpen: false,
    options: [
        { value: 'pdf', label: 'Berkas PDF (Maks. 5MB)' },
        { value: 'link', label: 'Link Tautan' }
    ],
    get selectedLabel() {
        let opt = this.options.find(o => o.value === this.tempType);
        return opt ? opt.label : 'Pilih Jenis Berkas';
    },
    init() {
        this.$watch('show', value => {
            if (value) { $store.modal.open(); }
            else { $store.modal.close(); }
        });
    }
}" 
@open-berkas-modal.window="
    tempName = $event.detail.name;
    tempType = $event.detail.type || '';
    tempDesc = $event.detail.desc;
    show = true;
    dropdownOpen = false;
">
    <template x-teleport="body">
        <div x-show="show" style="display:none;" x-cloak
        class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 p-4">
        <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]"
            @click.away="show = false"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Pengumpulan Berkas</div>
            </div>
            <div class="overflow-y-visible flex-1">
                <div class="p-6 flex flex-col gap-5">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Berkas Pengumpulan <span class="text-red-500">*</span></label>
                        <input type="text" x-model="tempName"
                            class="mp-input w-full text-[13px] bg-white border-[#DFE1E7]"
                            placeholder="Contoh: Portofolio">
                    </div>
                    
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jenis Berkas <span class="text-red-500">*</span></label>
                        <div class="relative" @click.away="dropdownOpen = false">
                            <button type="button" @click="dropdownOpen = !dropdownOpen"
                                class="w-full text-left bg-white border border-[#DFE1E7] rounded-[8px] px-4 py-2.5 text-[13px] text-[#353849] flex justify-between items-center transition-colors hover:bg-[#F8FAFC] focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E]"
                                :class="{'border-[#0B266E] ring-1 ring-[#0B266E]': dropdownOpen}">
                                <span x-text="selectedLabel" :class="{'text-[#808897]': !tempType}"></span>
                                <svg class="w-4 h-4 text-[#808897] transition-transform duration-200" :class="{'rotate-180': dropdownOpen}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div x-show="dropdownOpen" x-transition.opacity.duration.200ms
                                class="absolute z-20 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-[12px] shadow-lg py-1.5"
                                style="display:none; top: 100%;">
                                <template x-for="option in options" :key="option.value">
                                    <button type="button" @click="tempType = option.value; dropdownOpen = false"
                                        class="w-full text-left px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-[#F8FAFC] flex justify-between items-center"
                                        :class="tempType === option.value ? 'text-[#0B266E]' : 'text-[#353849]'">
                                        <span x-text="option.label"></span>
                                        <svg x-show="tempType === option.value" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[12px] font-semibold text-[#353849] mb-1">Keterangan
                            (Opsional)</label>
                        <input type="text" x-model="tempDesc"
                            class="mp-input w-full text-[13px] bg-white border-[#DFE1E7]"
                            placeholder="Tambahkan petunjuk untuk asisten...">
                    </div>
                    
                    <div
                        class="flex gap-3 justify-end mt-4 pt-5 border-t border-[#DFE1E7]">
                        <button type="button" @click="show = false"
                            class="mp-btn secondary md px-5">Batal</button>
                        <button type="button"
                            @click="if(tempName && tempType) { $dispatch('save-berkas', { name: tempName, type: tempType, desc: tempDesc }); show = false; }"
                            class="mp-btn primary md px-5"
                            :disabled="!tempName || !tempType"
                            :class="{'opacity-50 cursor-not-allowed': !tempName || !tempType}">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
    </div>{{-- end content wrapper --}}
</div>