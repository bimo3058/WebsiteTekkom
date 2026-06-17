<x-eoffice::layouts.koordinator title="Nilai Lapangan">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Nilai Lapangan</span>
    @endsection

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush

    <div x-data="nilaiLapanganApp()" x-cloak class="flex flex-col">

        <!-- Alert Banner -->
        <div x-show="showAlert" x-transition.opacity
            class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
            <div class="bg-emerald-100 p-1.5 rounded-full shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-emerald-800">Berhasil Dikonfirmasi!</p>
                <p class="text-xs text-emerald-600 mt-0.5">Nilai lapangan berhasil disimpan ke sistem.</p>
            </div>
            <button @click="showAlert = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- View: TABLE -->
        <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-slate-100">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-1">Nilai Lapangan</h2>
                    <p class="text-sm text-slate-500 font-medium">Validasi dan sinkronisasi nilai lapangan mahasiswa
                        kerja praktik.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th
                                    class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    Nama Mahasiswa</th>
                                <th
                                    class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    NIM</th>
                                <th
                                    class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    Berkas Nilai Lapangan</th>
                                <th
                                    class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-center">
                                    Nilai</th>
                                <th
                                    class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 w-32">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="mhs in mahasiswas" :key="mhs.id">
                                <tr class="hover:bg-slate-50/80 transition-colors duration-200 group">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-800" x-text="mhs.nama"></p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-medium text-slate-500 font-mono" x-text="mhs.nim"></p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <template x-if="mhs.file_nilai">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-500 shrink-0 border border-red-100">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-700 truncate max-w-[200px]"
                                                    x-text="mhs.file_nilai"></p>
                                            </div>
                                        </template>
                                        <template x-if="!mhs.file_nilai">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Belum Ada Berkas
                                            </span>
                                        </template>
                                    </td>
                                    <td class="py-4 px-6 text-center align-middle">
                                        <template x-if="mhs.nilai_validasi_koordinator !== null">
                                            <span
                                                class="inline-flex items-center justify-center px-3 py-1 rounded-md text-sm font-bold bg-amber-50 text-amber-700 border border-amber-100"
                                                x-text="mhs.nilai_validasi_koordinator"></span>
                                        </template>
                                        <template x-if="mhs.nilai_validasi_koordinator === null">
                                            <span class="text-xs text-slate-400 italic">Belum</span>
                                        </template>
                                    </td>
                                    <td class="py-4 px-6">
                                        <button @click="openDetail(mhs)" :disabled="!mhs.file_nilai"
                                            class="px-4 py-2 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 w-full"
                                            :class="mhs.file_nilai ? 'bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-100 hover:border-transparent shadow-sm hover:shadow-md hover:shadow-indigo-500/20' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                                            Lihat Berkas
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View: DETAIL -->
        <div x-show="viewMode === 'detail'" style="display: none;" class="flex-1 flex flex-col min-h-0"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0">
            <button @click="viewMode = 'table'"
                class="mb-4 flex-shrink-0 flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm w-fit">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Mahasiswa
            </button>

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col lg:flex-row min-h-[600px]">
                <!-- PDF Viewer Pane -->
                <div
                    class="w-full lg:w-[65%] bg-slate-900 relative flex flex-col items-center justify-center text-center p-8 border-b lg:border-b-0 lg:border-r border-slate-200">
                    <div x-show="loadingPdf"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-slate-800 z-10 text-white">
                        <svg class="w-10 h-10 animate-spin text-indigo-500 mb-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="font-medium animate-pulse">Memuat Dokumen PDF...</p>
                    </div>
                    <div x-show="!loadingPdf" class="text-slate-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-bold text-white mb-2" x-text="selectedStudent?.file_nilai"></p>
                        <p class="text-sm opacity-60">Dokumen PDF dirender di sini via embed.</p>
                    </div>
                </div>

                <!-- Input Form Panel -->
                <div class="w-full lg:w-[35%] bg-white shrink-0 flex flex-col">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900 tracking-tight" x-text="selectedStudent?.nama">
                        </h3>
                        <p class="text-sm font-bold text-indigo-600 font-mono mt-0.5" x-text="selectedStudent?.nim"></p>
                    </div>
                    <div class="p-6 flex-1">
                        <form
                            :action="'/eoffice/kp/koordinator/nilai-lapangan/' + selectedStudent?.dokumen_id + '/update'"
                            method="POST" id="formNilai">
                            @csrf
                            <input type="hidden" name="nilai_status" value="valid">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nilai Lapangan <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="nilai_validasi_koordinator" x-model="inputNilai" min="0"
                                        max="100" placeholder="Masukkan nilai 0-100"
                                        class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-base font-bold px-4 py-3 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                        required>
                                    <div
                                        class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mt-2 font-medium mb-6">Nilai final hasil validasi dari
                                    berkas (0 - 100).</p>
                                <button type="submit" @click.prevent="konfirmasiNilai"
                                    class="w-full mt-4 flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 group">
                                    Konfirmasi Nilai
                                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function nilaiLapanganApp() {
                return {
                    sidebarOpen: true,
                    viewMode: 'table',
                    mahasiswas: @json($mahasiswas),
                    selectedStudent: null,
                    inputNilai: '',
                    inputCatatan: '',
                    loadingPdf: false,
                    showAlert: false,

                    openDetail(mhs) {
                        if (!mhs.file_nilai) return;
                        this.selectedStudent = mhs;
                        this.inputNilai = '';
                        this.inputCatatan = '';
                        this.loadingPdf = true;
                        this.viewMode = 'detail';
                        setTimeout(() => { this.loadingPdf = false; }, 800);
                    },

                    konfirmasiNilai() {
                        if (!this.inputNilai || this.inputNilai < 0 || this.inputNilai > 100) { alert("Mohon masukkan nilai yang valid (0-100)."); return; }
                        document.getElementById('formNilai').submit();
                    }
                }
            }
        </script>
    @endpush

</x-eoffice::layouts.koordinator>