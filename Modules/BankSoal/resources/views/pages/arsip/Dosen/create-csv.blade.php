<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-navy transition-colors">Arsip Soal</a>
        <span class="text-slate-400 mx-2">/</span>
        <span class="text-slate-800 font-semibold">Import CSV/Excel</span>
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

    </style>

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header title="Import Massal CSV/Excel" subtitle="Gunakan template resmi untuk format data.">
        <x-slot:actions>
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 font-bold text-slate-700 transition-all hover:bg-slate-50 active:scale-95">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                        <i class="fas fa-file-excel text-xl"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Informasi Arsip</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Lengkapi data untuk mengimpor file csv.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <form id="importCsvForm" action="{{ route('banksoal.arsip.dosen.upload-csv') }}" method="POST" enctype="multipart/form-data" onsubmit="return handleImportSubmit(event)">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Kategori Ujian</label>
                                <select name="tipe_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="uts">UTS (Ujian Tengah Semester)</option>
                                    <option value="uas">UAS (Ujian Akhir Semester)</option>
                                    <option value="kuis">Kuis</option>
                                    <option value="tugas">Tugas</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Nama / Keterangan Arsip</label>
                                <input type="text" name="nama_arsip" placeholder="Cth: Susulan Kelas A" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                            </div>
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
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Metode Ujian</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="online" checked class="w-4 h-4 text-navy border-slate-300 focus:ring-navy">
                                    <span class="text-sm text-slate-600">Terkomputerisasi (Online)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="offline" class="w-4 h-4 text-navy border-slate-300 focus:ring-navy">
                                    <span class="text-sm text-slate-600">Cetak Kertas (Offline)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Ujian <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="tanggal_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                        </div>

                        <x-banksoal::ui.upload-zone
                            name="csv_file"
                            inputId="csv_file"
                            accept=".csv,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                            maxLabel="CSV/XLSX (Maks. 1MB)"
                            :required="true"
                        />
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <button type="submit" id="importSubmitBtn" class="w-full py-3 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i> Import CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function handleImportSubmit(event) {
            const form = event.target;
            const submitButton = document.getElementById('importSubmitBtn');

            if (form.dataset.submitting === '1') {
                event.preventDefault();
                return false;
            }

            form.dataset.submitting = '1';
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses...';
            }

            window.showLoader();
            return true;
        }
    </script>
</x-banksoal::layouts.dosen-admin>
