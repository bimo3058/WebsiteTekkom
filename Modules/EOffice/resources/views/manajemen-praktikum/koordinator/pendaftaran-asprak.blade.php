<x-eoffice::manajemen-praktikum.layout
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Seleksi Asisten">

    @if($praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif

    {{-- ── BUKA PERIODE BARU ────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <div class="mp-card flex-shrink-0 ">
        <div class="mp-card-header">
            <span class="mp-card-title">Form Pendaftaran Asisten</span>
        </div>

        <form action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.store') }}" method="POST" class="p-6">
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


                        <div class="mt-5">
                            <label class="block text-[11px] font-semibold text-[#353849] mb-2">Jadwal <span
                                    class="text-[#808897] font-normal">(Pilih hari luang)</span></label>
                            <div class="flex gap-6 flex-wrap">
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled checked
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Senin</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Selasa</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Rabu</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Kamis</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Jumat</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Sabtu</span>
                                </label>
                                <label class="flex items-center gap-2" style="cursor:not-allowed;">
                                    <input type="radio" disabled
                                        class="w-4 h-4 text-[#0B266E] border-gray-300 focus:ring-[#0B266E] disabled:bg-[#F9FAFB] disabled:opacity-75">
                                    <span class="text-[12px] text-[#666D80]">Minggu</span>
                                </label>
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
        @if($periodeAktif && $periodeAktif->isSedangBuka())
            <form id="tutup-periode" method="POST"
                action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $periodeAktif->id) }}"
                style="display:none;">
                @csrf
            </form>
        @endif
    </div>

    {{-- CARD 3: PENDAFTAR ASPRAK (TABEL) --}}
    <div class="mp-card flex-shrink-0">
        <div class="mp-card-header"
            style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px;">
            <span class="mp-card-title">Daftar Calon Asisten</span>
            <span class="mp-badge navy sm" style="font-size: 11px;">{{ $pendaftaran->total() }} pendaftar</span>
        </div>
        <div style="padding: 16px 20px; border-bottom: 1px solid #DFE1E7; background: #fff;">
            <form method="GET" style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <input type="hidden" name="praktikum_id" value="{{ request('praktikum_id', $praktikum?->id) }}">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa..." class="mp-input w-full"
                        style="height:36px; font-size:13px; width:250px; padding-left: 36px; padding-right: 12px;">
                </div>

                <x-eoffice::manajemen-praktikum.ui.select name="sort" :options="[
        ['value' => 'terbaru', 'label' => 'Terbaru'],
        ['value' => 'terlama', 'label' => 'Terlama'],
        ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
        ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
    ]" :selected="request('sort', 'terbaru')"
                    placeholder="Urutkan..." onChange="$event.target.form.submit()" minWidth="140px" />

                <x-eoffice::manajemen-praktikum.ui.select name="status" :options="[
        ['value' => '', 'label' => 'Semua Status'],
        ['value' => 'pending', 'label' => 'Menunggu Review'],
        ['value' => 'approved', 'label' => 'Sudah Disetujui'],
        ['value' => 'rejected', 'label' => 'Ditolak']
    ]" :selected="request('status', '')"
                    placeholder="Semua Status" onChange="$event.target.form.submit()" minWidth="160px" />

                <button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">
                    Filter
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" style="font-size:13px;">
                <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                    <tr>
                        <th class="mp-th text-left" style="padding:10px 20px; width:40px;">No</th>
                        <th class="mp-th text-left" style="padding:10px 16px; padding-left: 0;">NAMA MAHASISWA</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">NIM</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">IPK</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">TRANSKRIP NILAI</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">KEANGGOTAAN CERC</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Jadwal</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $p)
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:12px 20px;color:#808897;font-size:12px;">
                                {{ $loop->iteration }}
                            </td>
                            <td style="padding:12px 16px; padding-left: 0;">
                                <div class="flex items-center gap-[10px]">
                                    <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '--' }}</div>
                                        <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @php
                                $emailStr_c = $p->user?->email ?? '';
                                $nim_c = explode('@', $emailStr_c)[0];
                                if (empty($nim_c))
                                    $nim_c = '—';
                            @endphp
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{ $nim_c }}
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:400; font-size:13px; color:#666D80;">
                                    {{ number_format($p->ipk ?? 0, 2) }}
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->transkrip_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice') }}"
                                        target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->berkas_cerc_path)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, 'eoffice') }}"
                                        target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;"
                                        class="hover:underline">Lihat Berkas</a>
                                @else
                                    <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:#666D80;">
                                {{ collect($p->jadwal ?? [])->join(', ') ?: '—' }}
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->status_koor === 'disetujui')
                                    <div>
                                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui Koor</span>
                                        <div style="font-size:13px;color:#666D80;margin-top:4px;">Menunggu Admin</div>
                                    </div>
                                @elseif($p->status_koor === 'ditolak' || $p->status === 'rejected')
                                    <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                                @else
                                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;">
                                @if($p->status_koor === 'menunggu')
                                    <div class="flex gap-2" x-data="{ catatan: '' }">
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.approve', $p->id) }}">
                                            @csrf
                                            <input type="hidden" name="catatan_koor" value="">
                                            <button type="submit" class="mp-btn ghost sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.reject', $p->id) }}"
                                            x-data="{ alasan: '' }">
                                            @csrf
                                            <input type="hidden" name="alasan_penolakan" :value="alasan">
                                            <button type="button"
                                                @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) .closest('form').submit()"
                                                class="mp-btn destructive sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size:13px;color:#808897;">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:48px;text-align:center;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                    style="margin:0 auto 12px;display:block;">
                                    <path
                                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5" />
                                </svg>
                                <div style="font-size:13px;color:#666D80;">Belum ada pendaftar asprak.</div>
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
                            class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                            <template x-for="option in options" :key="option">
                                <a :href="'?per_page=' + option + '&' + decodeURIComponent(new URLSearchParams(Object.fromEntries(Object.entries(Object.fromEntries(new URLSearchParams(window.location.search))).filter(([k,v])=>k!=='per_page'))).toString())"
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
                        <a href="{{ $pendaftaran->previousPageUrl() }}"
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
                            <a href="{{ $pendaftaran->url($i) }}"
                                style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($pendaftaran->hasMorePages())
                        <a href="{{ $pendaftaran->nextPageUrl() }}"
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

</x-eoffice::manajemen-praktikum.layout>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
    /* Styling Flatpickr untuk mengikuti tema desain */
    .flatpickr-calendar {
        width: 340px !important;
        padding-right: 12px !important;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12) !important;
        border: 1px solid #DFE1E7 !important;
        border-radius: 12px !important;
        font-family: inherit !important;
        padding-left: 8px !important;
        padding-bottom: 8px !important;
    }

    .flatpickr-days {
        width: 320px !important;
    }

    .dayContainer {
        width: 320px !important;
        min-width: 320px !important;
        max-width: 320px !important;
    }

    .flatpickr-calendar.hasTime .flatpickr-time {
        border-top: 1px solid #DFE1E7 !important;
        margin-top: 8px;
    }

    .flatpickr-months {
        margin-bottom: 8px !important;
    }

    .flatpickr-current-month {
        font-size: 14px !important;
        padding-top: 0px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        display: none !important;
    }

    .flatpickr-current-month .cur-month {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
        padding-top: 0px !important;
        cursor: pointer;
    }

    .flatpickr-current-month .cur-month:hover {
        color: #0B266E !important;
    }

    .custom-month-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
        z-index: 99999;
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
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
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
        max-width: 42px !important;
        height: 40px !important;
        line-height: 40px !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:focus,
    .flatpickr-day.selected:hover {
        background: #0B266E !important;
        border-color: #0B266E !important;
        color: #fff !important;
        font-weight: 600;
    }

    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #C1C7D0 !important;
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
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.flatpickr-input', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true,
            monthSelectorType: "static",
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
        });
    });
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