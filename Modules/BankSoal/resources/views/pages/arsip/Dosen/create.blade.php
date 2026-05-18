<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-navy transition-colors">Arsip Soal</a>
        <span class="text-slate-400 mx-2">/</span>
        <span class="text-slate-800 font-semibold">Tambah Arsip</span>
    @endsection

    <style>
        :root {
            --navy: #0B266E;
            --navy-light: rgba(11, 38, 110, 0.1);
        }
        .bg-navy { background-color: var(--navy); }
        .text-navy { color: var(--navy); }
        .border-navy { border-color: var(--navy); }
        .focus\:border-navy:focus { border-color: var(--navy); }
        .focus\:ring-navy:focus { --tw-ring-color: rgba(11, 38, 110, 0.5); }
        .shadow-navy { --tw-shadow-color: rgba(11, 38, 110, 0.2); }
        
        @keyframes popup {
            0% { opacity: 0; transform: scale(0.95) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-popup {
            animation: popup 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Loading Spinner */
        #global-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--navy-light);
            border-top: 4px solid var(--navy);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Global Loader Overlay -->
    <div id="global-loader">
        <div class="flex flex-col items-center gap-3">
            <div class="spinner"></div>
            <p class="text-sm font-bold text-navy">Memproses data...</p>
        </div>
    </div>

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header title="Tambah Arsip Soal" subtitle="Pilih metode unggah yang sesuai: format PDF standar atau import massal via CSV/Excel." />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Upload PDF Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                    <i class="fas fa-file-pdf text-xl"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Unggah PDF Arsip</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Dokumen akan diarsipkan sebagai format PDF standar.</p>
                </div>
            </div>
            
            <div class="p-6 flex-1">
                <form action="{{ route('banksoal.arsip.dosen.upload-pdf') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Mata Kuliah</label>
                            <select name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Kategori (UTS/UAS/dsb)</label>
                            <input type="text" name="nama_arsip" placeholder="Cth: UTS Ganjil 2026" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="{{ date('Y') }}/{{ date('Y')+1 }}" placeholder="Cth: {{ date('Y') }}/{{ date('Y')+1 }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <select name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                    <option value="Antara">Antara</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-6 mt-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-navy transition-all cursor-pointer group" onclick="document.getElementById('pdf_file').click()">
                            <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-pdf text-2xl text-rose-500"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Klik atau Tarik File PDF</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest">Maksimal 5MB</p>
                            <input type="file" id="pdf_file" name="pdf_file" class="hidden" accept="application/pdf" onchange="updateFileName(this, 'pdf_name_display')" required>
                        </div>
                        <div id="pdf_name_display" class="hidden animate-popup mt-2">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold">
                                <i class="fas fa-check-circle"></i>
                                <span class="file-name truncate"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-3 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i> Upload PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload CSV Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                        <i class="fas fa-file-excel text-xl"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Import Massal CSV/Excel</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Gunakan template resmi untuk format data.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 flex-1">
                <form action="{{ route('banksoal.arsip.dosen.upload-csv') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                    @csrf
                    
                    <div class="flex items-center justify-between p-4 mb-5 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                <i class="fas fa-download text-xs"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-amber-900">Unduh Template</p>
                            </div>
                        </div>
                        <a href="{{ route('banksoal.soal.dosen.export-csv') }}" class="px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-amber-700 text-[11px] font-bold hover:bg-amber-100 transition-all shadow-sm">
                            Download .xlsx
                        </a>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Mata Kuliah</label>
                            <select name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Kategori (UTS/UAS/dsb)</label>
                            <input type="text" name="nama_arsip" placeholder="Cth: UTS Ganjil 2026" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="{{ date('Y') }}/{{ date('Y')+1 }}" placeholder="Cth: {{ date('Y') }}/{{ date('Y')+1 }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <select name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                    <option value="Antara">Antara</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-6 mt-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-navy transition-all cursor-pointer group" onclick="document.getElementById('csv_file').click()">
                            <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-excel text-2xl text-emerald-500"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Klik atau Tarik File Spreadsheet</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest">Format: CSV/XLSX • Maks 1MB</p>
                            <input type="file" id="csv_file" name="csv_file" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="updateFileName(this, 'csv_name_display')" required>
                        </div>
                        <div id="csv_name_display" class="hidden animate-popup mt-2">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold">
                                <i class="fas fa-check-circle"></i>
                                <span class="file-name truncate"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-3 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i> Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function updateFileName(input, displayId) {
            const display = document.getElementById(displayId);
            const nameSpan = display.querySelector('.file-name');
            if (input.files.length > 0) {
                nameSpan.textContent = input.files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        }
        function showLoader() {
            document.getElementById('global-loader').style.display = 'flex';
        }
    </script>
</x-banksoal::layouts.dosen-admin>
