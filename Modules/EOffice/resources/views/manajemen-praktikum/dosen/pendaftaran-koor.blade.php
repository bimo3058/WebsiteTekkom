<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Koordinator">

    @if($praktikum)
        {{-- Sticky Header Wrapper --}}
        <div x-data="{ st: 0 }" x-init="
                const box = document.querySelector('.mp-box-body');
                if (box) {
                    let ticking = false;
                    box.addEventListener('scroll', () => {
                        if (!ticking) {
                            window.requestAnimationFrame(() => {
                                st = box.scrollTop;
                                ticking = false;
                            });
                            ticking = true;
                        }
                    });
                }
             " class="sticky z-20 bg-white"
            style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border);">

            {{-- Banner / Cover Image Container --}}
            <div
                style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">

                {{-- Cover Placeholder or Image --}}
                <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896"
                        :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                </div>

                {{-- 1. Base Gradient (Tampil penuh, tidak berubah) --}}
                <div class="absolute inset-0 pointer-events-none"
                    style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);">
                </div>

                {{-- 2. Scrolled Overlay (Gelap + Blur) - Transparansi memudar seiring scroll --}}
                <div class="absolute inset-0 pointer-events-none"
                    :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
                </div>

                {{-- Title --}}
                <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
                    <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                        :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                        {{ $praktikum->nama }}
                    </h1>
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
                <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
                <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
                <a href="{{ route('eoffice.manprak.dosen.nilai.index', $praktikum->id) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Absensi
                    dan Nilai</a>
                <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}"
                    style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
                <a href="#"
                    style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Seleksi
                    Koordinator</a>
            </div>
        </div>



    @else
        {-- Page Header --}
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
                <p class="mp-page-sub">Review pendaftar koordinator sesuai praktikum yang Anda ampu</p>
            </div>
        </div>
    @endif

    {{-- SECTION: KOORDINATOR AKTIF --}}
    @if(isset($praktikum))
        <div
            style="margin-bottom: 19px; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">

            {{-- Kiri: Koordinator Aktif --}}
            <div class="mp-card" style="flex: 1; min-width: 300px; padding: 0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Koordinator Aktif</span>
                </div>
                <div style="padding: 24px;">

                    @if($koordinator)
                        <div
                            style="font-size: 13px; font-weight: 700; color: #10B981; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; display:flex; align-items:center; gap:6px;">
                            <span class="dot" style="background:#10B981;"></span> Koordinator Aktif
                        </div>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="mp-av navy" style="width: 52px; height: 52px; font-size: 16px;">
                                {{ strtoupper(substr($koordinator->name ?? 'UN', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size: 16px; font-weight: 700; color: #0D0D12;">{{ $koordinator->name ?? '-' }}
                                </div>
                                <div style="font-size: 13px; color: #666D80; margin-top:2px;">
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
    @endif

    @php
        $periodeAktif = \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $praktikum?->id ?? '')
            ->where('jenis', 'koordinator')
            ->where('is_aktif', true)
            ->first();
    @endphp
    <div class="mp-card flex-shrink-0" style="margin-bottom: 19px;">
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
                        isEditing: false,
                        tempName: '',
                        addBerkas() {
                            if(this.tempName.trim() !== '') {
                                this.berkasName = this.tempName.trim();
                                this.isEditing = false;
                                this.tempName = '';
                            }
                        },
                        removeBerkas() {
                            this.berkasName = '';
                            this.tempName = '';
                        }
                    }">
                        <input type="hidden" name="nama_berkas_tambahan" :value="berkasName">

                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-[11px] font-semibold text-[#353849] mb-1">Keanggotaan
                                    CERC</label>
                                <input type="text" class="mp-input w-full text-[12px]" value="Upload File (.pdf)"
                                    disabled style="background: #F9FAFB; color: #808897; border-style: dashed;">
                            </div>
                            <div class="flex-1">
                                {{-- If no berkas and not editing --}}
                                <div x-show="!berkasName && !isEditing" class="h-full flex items-end pb-1.5">
                                    <button type="button"
                                        @click="isEditing = true; setTimeout(() => $refs.berkasInput.focus(), 50)"
                                        class="text-[#0B266E] text-[12px] font-semibold flex items-center gap-1.5 hover:underline bg-[#F6F8FA] px-3 py-1.5 rounded-[8px] border border-dashed border-[#DFE1E7]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                        Tambah Berkas Tambahan
                                    </button>
                                </div>

                                {{-- If editing --}}
                                <div x-show="isEditing" style="display: none;" @click.away="isEditing = false">
                                    <label class="block text-[11px] font-semibold text-[#353849] mb-1">Nama Berkas <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" x-ref="berkasInput" x-model="tempName"
                                            @keydown.enter.prevent="addBerkas()"
                                            @keydown.escape.prevent="isEditing = false"
                                            class="mp-input w-full text-[12px] border-[#0B266E] shadow-sm"
                                            placeholder="Contoh: Portofolio">
                                    </div>
                                </div>

                                {{-- If berkas exists --}}
                                <div x-show="berkasName" style="display: none;" x-transition>
                                    <label class="block text-[11px] font-semibold text-[#353849] mb-1">
                                        <span x-text="berkasName"></span> <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" class="mp-input w-full text-[12px]"
                                            value="Upload File (.pdf)" disabled
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
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 border border-[#DFE1E7] rounded-[10px] overflow-hidden" x-data="quizBuilder()">
                            <input type="hidden" name="konfigurasi_kuis" :value="JSON.stringify(questions)">

                            <div class="bg-[#F0F4FF] p-4 border-b border-[#DFE1E7] flex justify-between items-center">
                                <div>
                                    <h4 class="text-[13px] font-bold text-[#0B266E]">Modul Uji Tertulis & Tes Kelayakan
                                    </h4>
                                    <p class="text-[11px] text-[#666D80] mt-0.5">Berikan kuesioner dinamis pada calon
                                        asisten</p>
                                </div>
                                <button type="button" @click="showQuizModal = true" class="mp-btn primary sm font-bold"
                                    style="padding: 0 16px; height: 34px; box-shadow: 0 2px 4px rgba(11,38,110,0.1);">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="margin-right:6px;">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Rancang Formulir
                                </button>
                            </div>

                            <!-- Live Preview Data -->
                            <div class="p-4 bg-white flex flex-col items-center justify-center min-h-[80px]">
                                <template x-if="questions.length === 0">
                                    <p class="text-[12px] text-[#A4ABB8] text-center w-full">Belum ada tes algoritma
                                        atau esai yang ditambahkan ke formulir ini.</p>
                                </template>
                                <template x-if="questions.length > 0">
                                    <div class="w-full">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                                            <span class="text-[12px] font-bold text-[#10B981]"><span
                                                    x-text="questions.length"></span> Soal Valid Dikonfigurasi</span>
                                        </div>
                                        <div class="flex overflow-x-auto gap-2 pb-2">
                                            <template x-for="(q, idx) in questions" :key="q.id">
                                                <div
                                                    class="bg-[#F8FAFC] border border-[#DFE1E7] rounded px-3 py-2 flex-shrink-0 min-w-[200px] max-w-[250px]">
                                                    <div class="text-[10px] font-bold text-[#0B266E] mb-1"
                                                        x-text="q.tipe === 'pilihan_ganda' ? 'PILIHAN GANDA' : 'ESAI'">
                                                    </div>
                                                    <div class="text-[11px] text-[#353849] truncate"
                                                        x-text="q.pertanyaan"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Modal -->
                            <div x-show="showQuizModal" style="display:none;" x-transition.opacity
                                class="fixed inset-0 z-[99999] flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-[#0D0D12] bg-opacity-60 backdrop-blur-sm"
                                    @click="showQuizModal = false"></div>

                                <div class="bg-white rounded-[14px] shadow-2xl w-full max-w-3xl flex flex-col relative"
                                    style="max-height: 85vh;">
                                    <div class="flex items-center justify-between p-5 border-b border-[#DFE1E7]">
                                        <div>
                                            <h3 class="font-bold text-[#0D0D12] text-[18px]">Form Builder Studio</h3>
                                            <p class="text-[12px] text-[#666D80] mt-0.5">Bangun lembar form ujian
                                                seleksi pendaftaran</p>
                                        </div>
                                        <button type="button" @click="showQuizModal = false"
                                            class="text-[#666D80] hover:text-[#DF1C41] p-2 bg-[#F6F8FA] hover:bg-[#FFF5F5] rounded-full transition-colors">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex-1 overflow-y-auto p-6 bg-[#F8FAFC]">

                                        <template x-for="(q, qIndex) in questions" :key="q.id">
                                            <div
                                                class="bg-white border-2 border-[#DFE1E7] rounded-[12px] p-5 mb-5 relative hover:border-[#0B266E] transition-colors group">
                                                <button type="button" @click="removeQuestion(qIndex)"
                                                    class="absolute top-4 right-4 text-[#A4ABB8] hover:text-[#DF1C41] hover:bg-[#FFF5F5] p-2 rounded-md transition-colors"
                                                    title="Hapus Blok">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                                                    </svg>
                                                </button>

                                                <div class="flex items-center gap-2 mb-4">
                                                    <span
                                                        class="bg-[#E6F0FF] text-[#0B266E] text-[10px] font-extrabold tracking-wider px-2.5 py-1 rounded-full border border-[#BFD6FF]"
                                                        x-text="q.tipe === 'pilihan_ganda' ? 'SOAL PILIHAN GANDA' : 'SOAL ESAI PENDEK/PANJANG'"></span>
                                                </div>

                                                <label class="block text-[13px] font-bold text-[#353849] mb-1.5">Judul
                                                    Pertanyaan / Syarat</label>
                                                <textarea x-model="q.pertanyaan"
                                                    class="mp-input w-full text-[14px] mb-4 p-3 bg-[#F9FAFB]" rows="2"
                                                    placeholder="Cth: Mengapa kamu mendaftar asisten? Atau uji logika if-else..."></textarea>

                                                <div class="w-1/3 mb-4">
                                                    <label
                                                        class="block text-[11px] font-semibold text-[#666D80] mb-1.5">Bobot
                                                        Nilai (Poin jika terjawab benar/maksimal)</label>
                                                    <div class="relative">
                                                        <input type="number" x-model="q.poin"
                                                            class="mp-input w-full pl-8 font-bold text-[#0B266E]"
                                                            min="1" max="100">
                                                        <span
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-[#A4ABB8] font-bold">Pt.</span>
                                                    </div>
                                                </div>

                                                <template x-if="q.tipe === 'pilihan_ganda'">
                                                    <div
                                                        class="mt-2 pl-4 py-2 border-l-4 border-[#0B266E] bg-[#FAFBFF] rounded-r-[8px]">
                                                        <label
                                                            class="block text-[11px] font-bold text-[#353849] mb-3 uppercase tracking-wider text-opacity-80">Konfigurasi
                                                            Jawaban <span class="font-normal normal-case">(Pilih Radio
                                                                Button Sebagai Kunci)</span></label>
                                                        <template x-for="(opt, optIndex) in q.opsi" :key="optIndex">
                                                            <div class="flex items-center gap-3 mb-2 group/opt">
                                                                <label class="flex items-center gap-2 cursor-pointer"
                                                                    title="Jadikan Kunci Jawaban Benar">
                                                                    <input type="radio" :name="'kunci_' + q.id"
                                                                        :checked="opt.is_correct"
                                                                        @change="setCorrectOption(qIndex, optIndex)"
                                                                        class="w-5 h-5 accent-[#0B266E]">
                                                                </label>
                                                                <input type="text" x-model="opt.text"
                                                                    class="mp-input w-full text-[13px]"
                                                                    :class="opt.is_correct ? 'border-[#0B266E] bg-white font-semibold' : 'bg-transparent'"
                                                                    :placeholder="'Opsi ' + (String.fromCharCode(65 + optIndex))">
                                                                <button type="button"
                                                                    @click="removeOption(qIndex, optIndex)"
                                                                    class="text-[#DFE1E7] hover:text-[#DF1C41] p-1.5 rounded hover:bg-[#FFF5F5] transition-colors"><svg
                                                                        width="18" height="18" viewBox="0 0 24 24"
                                                                        fill="none" stroke="currentColor"
                                                                        stroke-width="2.5" stroke-linecap="round">
                                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                                    </svg></button>
                                                            </div>
                                                        </template>
                                                        <button type="button" @click="addOption(qIndex)"
                                                            class="text-[#0B266E] text-[12px] font-bold mt-2 ml-8 flex items-center gap-1.5 hover:underline">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.5">
                                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                            </svg>
                                                            Tambah Jawaban Lain
                                                        </button>
                                                    </div>
                                                </template>

                                                <template x-if="q.tipe === 'esai'">
                                                    <div
                                                        class="mt-2 p-4 border border-dashed border-[#DFE1E7] bg-white rounded text-center opacity-60">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="#A4ABB8" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" class="mx-auto mb-2">
                                                            <path
                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                            </path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                                            <polyline points="10 9 9 9 8 9"></polyline>
                                                        </svg>
                                                        <span class="text-[11px] font-semibold text-[#666D80]">Area ini
                                                            nantinya akan berupa TextInput besar bagi Pendaftar. Tipe
                                                            soal ini membutuhkan review manual (Dosen menginput skor
                                                            sendiri nantinya).</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Add Block Buttons -->
                                        <div class="flex gap-4 justify-center mt-8 pb-4">
                                            <button type="button" @click="addQuestion('pilihan_ganda')"
                                                class="mp-btn bg-white border border-[#DFE1E7] shadow-sm text-[#0B266E] hover:bg-[#F6F8FA] hover:border-[#0B266E] flex items-center gap-2"
                                                style="border-radius: 20px; padding: 0 20px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <span class="font-bold">Tambah Soal Pilgan</span>
                                            </button>
                                            <button type="button" @click="addQuestion('esai')"
                                                class="mp-btn bg-white border border-[#DFE1E7] shadow-sm text-[#0B266E] hover:bg-[#F6F8FA] hover:border-[#0B266E] flex items-center gap-2"
                                                style="border-radius: 20px; padding: 0 20px;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                    <line x1="17" y1="10" x2="3" y2="10"></line>
                                                    <line x1="21" y1="6" x2="3" y2="6"></line>
                                                    <line x1="21" y1="14" x2="3" y2="14"></line>
                                                    <line x1="17" y1="18" x2="3" y2="18"></line>
                                                </svg>
                                                <span class="font-bold">Tambah Kuisioner Esai</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        class="p-5 border-t border-[#DFE1E7] bg-white flex justify-end gap-3 rounded-b-[14px]">
                                        <button type="button" @click="showQuizModal = false"
                                            class="mp-btn outline sm font-semibold" style="height:42px;">Tutup & Simpan
                                            Otomatis</button>
                                    </div>
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
    <div class="mp-card" style="padding: 0;">
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
                                        style="font-size:13px; font-weight:600; color:#0B266E; text-decoration:none;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px; color:#808897;">—</span>
                                @endif
                            </td>

                            <td style="padding:14px 16px; text-align:center;">
                                @if($p->status_dosen === 'disetujui')
                                    <span
                                        style="display:inline-flex; align-items:center; border:1px solid #c3e6cb; color:#155724; background:#d4edda; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500;">Disetujui</span>
                                @elseif($p->status_dosen === 'ditolak')
                                    <span
                                        style="display:inline-flex; align-items:center; border:1px solid #f5c6cb; color:#721c24; background:#f8d7da; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500;">Ditolak</span>
                                @else
                                    <span
                                        style="display:inline-flex; align-items:center; border:1px solid #F5D565; color:#B47D00; background:#FFFdf2; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500;">Menunggu
                                        Review</span>
                                @endif
                            </td>

                            <td style="padding:14px 16px;">
                                <div class="flex items-center gap-2">
                                    @if($p->status_dosen === 'menunggu')
                                        <form action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id) }}"
                                            method="POST">
                                            @csrf

                                            <button type="submit" class="mp-btn sm"
                                                style="background:#F3F4F6;color:#0B266E;font-weight:600;display:flex;gap:4px;border:none;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="mp-btn sm"
                                                style="background:#DF1C41;color:white;font-weight:600;display:flex;gap:4px;border:none;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg> Tolak
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size:12px;color:#808897;font-style:italic;">Diproses</span>
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