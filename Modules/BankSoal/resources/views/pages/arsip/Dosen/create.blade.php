<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @include('banksoal::pages.arsip.Dosen._styles')
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Arsip Soal</a>
        <span class="text-slate-400 mx-2">/</span>
        <span class="text-slate-800 font-semibold">Tambah Arsip</span>
    @endsection


    <x-banksoal::notification.alerts />

    <x-banksoal::ui.bank-soal-page class="bs-archive-page">
    <x-slot:header>
    <x-banksoal::ui.page-header title="Tambah Arsip Soal" subtitle="Pilih metode unggah yang sesuai: format PDF standar atau import massal via CSV/Excel." >
        <x-slot:actions>
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="inline-flex items-center gap-2"><i class="fas fa-arrow-left"></i> Kembali</a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>
    </x-slot:header>

    <div class="bs-archive-upload-grid">
        
        <!-- Upload PDF Card -->
        <div class="bs-archive-form-card">
            <div class="bs-archive-card-header">
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
                            <x-banksoal::ui.alpine-select id="arsip-create-1-mk_id" label="Mata Kuliah">
                                <select id="arsip-create-1-mk_id" aria-label="Mata Kuliah" name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach($mataKuliahDosen as $mk)
                                    <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                    @endforeach
                                </select>
                            </x-banksoal::ui.alpine-select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Kategori (UTS/UAS/dsb)</label>
                            <input type="text" name="nama_arsip" placeholder="Cth: UTS Ganjil 2026" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="{{ date('Y') }}/{{ date('Y')+1 }}" placeholder="Cth: {{ date('Y') }}/{{ date('Y')+1 }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <x-banksoal::ui.alpine-select id="arsip-create-2-semester" label="Semester">
                                    <select id="arsip-create-2-semester" aria-label="Semester" name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                        <option value="Antara">Antara</option>
                                    </select>
                                </x-banksoal::ui.alpine-select>
                            </div>
                        </div>

                        <div class="p-6 mt-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-primary transition-all cursor-pointer group" onclick="document.getElementById('pdf_file').click()">
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
                        <button type="submit" class="bs-archive-primary bs-archive-submit">
                            <i class="fas fa-upload"></i> Upload PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload CSV Card -->
        <div class="bs-archive-form-card">
            <div class="bs-archive-card-header">
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
                            <x-banksoal::ui.alpine-select id="arsip-create-3-mk_id" label="Mata Kuliah">
                                <select id="arsip-create-3-mk_id" aria-label="Mata Kuliah" name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach($mataKuliahDosen as $mk)
                                    <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                    @endforeach
                                </select>
                            </x-banksoal::ui.alpine-select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Kategori (UTS/UAS/dsb)</label>
                            <input type="text" name="nama_arsip" placeholder="Cth: UTS Ganjil 2026" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="{{ date('Y') }}/{{ date('Y')+1 }}" placeholder="Cth: {{ date('Y') }}/{{ date('Y')+1 }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <x-banksoal::ui.alpine-select id="arsip-create-4-semester" label="Semester">
                                    <select id="arsip-create-4-semester" aria-label="Semester" name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                        <option value="Antara">Antara</option>
                                    </select>
                                </x-banksoal::ui.alpine-select>
                            </div>
                        </div>

                        <div class="p-6 mt-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center hover:border-primary transition-all cursor-pointer group" onclick="document.getElementById('csv_file').click()">
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
                        <button type="submit" class="bs-archive-primary bs-archive-submit">
                            <i class="fas fa-upload"></i> Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    </x-banksoal::ui.bank-soal-page>

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
            window.showLoader();
        }
    </script>
</x-banksoal::layouts.dosen-admin>
