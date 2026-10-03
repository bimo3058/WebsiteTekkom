<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @include('banksoal::pages.arsip.Dosen._styles')
    @section('breadcrumbs')
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-slate-500 hover:text-primary transition-colors">Arsip Soal</a>
        <span class="text-slate-400 mx-2">/</span>
        <span class="text-slate-800 font-semibold">Upload PDF</span>
    @endsection

    <link rel="stylesheet" href="{{ asset('css/banksoal-ui.css') }}">

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.bank-soal-page class="bs-archive-page">
    <x-slot:header>
    <x-banksoal::ui.page-header title="Upload PDF Arsip" subtitle="Dokumen akan diarsipkan sebagai format PDF standar.">
        <x-slot:actions>
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 font-bold text-slate-700 transition-all hover:bg-slate-50 active:scale-95">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>
    </x-slot:header>

    <div class="bs-archive-form-width">
        <div class="bs-archive-form-card">
            <div class="bs-archive-card-header">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                    <i class="fas fa-file-pdf text-xl"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Informasi Arsip</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Lengkapi data untuk mengunggah file PDF.</p>
                </div>
            </div>
            
            <div class="p-6">
                <form action="{{ route('banksoal.arsip.dosen.upload-pdf') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Mata Kuliah</label>
                            <x-banksoal::ui.alpine-select id="arsip-create-pdf-1-mk_id" label="Mata Kuliah">
                                <select id="arsip-create-pdf-1-mk_id" aria-label="Mata Kuliah" name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach($mataKuliahDosen as $mk)
                                    <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                                    @endforeach
                                </select>
                            </x-banksoal::ui.alpine-select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Kategori Ujian</label>
                                <x-banksoal::ui.alpine-select id="arsip-create-pdf-2-tipe_ujian" label="Kategori Ujian">
                                    <select id="arsip-create-pdf-2-tipe_ujian" aria-label="Kategori Ujian" name="tipe_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="uts">UTS (Ujian Tengah Semester)</option>
                                        <option value="uas">UAS (Ujian Akhir Semester)</option>
                                        <option value="kuis">Kuis</option>
                                        <option value="tugas">Tugas</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </x-banksoal::ui.alpine-select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Nama / Keterangan Arsip</label>
                                <input type="text" name="nama_arsip" placeholder="Cth: Susulan Kelas A" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="{{ date('Y') }}/{{ date('Y')+1 }}" placeholder="Cth: {{ date('Y') }}/{{ date('Y')+1 }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <x-banksoal::ui.alpine-select id="arsip-create-pdf-3-semester" label="Semester">
                                    <select id="arsip-create-pdf-3-semester" aria-label="Semester" name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all" required>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                        <option value="Antara">Antara</option>
                                    </select>
                                </x-banksoal::ui.alpine-select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Metode Ujian</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="online" checked class="w-4 h-4 text-primary border-slate-300 focus:ring-primary">
                                    <span class="text-sm text-slate-600">Terkomputerisasi (Online)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="offline" class="w-4 h-4 text-primary border-slate-300 focus:ring-primary">
                                    <span class="text-sm text-slate-600">Cetak Kertas (Offline)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Ujian <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="tanggal_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                        </div>

                        <x-banksoal::ui.upload-zone
                            name="pdf_file"
                            inputId="pdf_file"
                            accept="application/pdf,.pdf"
                            maxLabel="PDF (Maks. 5MB)"
                            :required="true"
                        />
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <button type="submit" class="bs-archive-primary bs-archive-submit">
                            <i class="fas fa-upload"></i> Upload PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </x-banksoal::ui.bank-soal-page>

    <script>
        function showLoader() {
            window.showLoader();
        }
    </script>
</x-banksoal::layouts.dosen-admin>
