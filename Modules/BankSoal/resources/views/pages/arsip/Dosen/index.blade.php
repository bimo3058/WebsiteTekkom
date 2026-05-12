<x-banksoal::layouts.dosen-admin>

<x-banksoal::notification.alerts />

<x-banksoal::ui.page-header title="Arsip Soal" subtitle="Kelola arsip final dan riwayat penarikan soal dosen.">
    <x-slot:actions>
        <div class="flex flex-wrap items-center gap-2">

            <div class="relative">
                <button id="arsipUploadTrigger" type="button" onclick="toggleArsipUploadMenu()" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 font-medium text-white shadow-sm transition-colors hover:bg-primary/90">
                    <i class="fas fa-upload w-4"></i> Upload Arsip <i class="fas fa-chevron-down text-[10px] text-slate-300"></i>
                </button>

                <div id="arsipUploadMenu" class="pointer-events-none absolute right-0 top-[110%] z-50 w-52 translate-y-1 scale-95 overflow-hidden rounded-xl border border-slate-100 bg-white opacity-0 shadow-lg transition-all duration-200">
                    <button type="button" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-slate-700 transition-colors hover:bg-primary/10 hover:text-primary" onclick="openArsipUploadModal('uploadPdfModal')">
                        <i class="fas fa-file-pdf w-4 text-center text-rose-500"></i> Upload PDF
                    </button>
                    <button type="button" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-slate-700 transition-colors hover:bg-emerald-50 hover:text-emerald-700" onclick="openArsipUploadModal('uploadCsvModal')">
                        <i class="fas fa-file-csv w-4 text-center text-emerald-500"></i> Upload CSV
                    </button>
                </div>
            </div>
        </div>
    </x-slot:actions>
</x-banksoal::ui.page-header>

<div id="arsip-dosen-page-data" data-initial-modal="{{ $errors->has('pdf_file') ? 'uploadPdfModal' : ($errors->has('csv_file') ? 'uploadCsvModal' : '') }}" hidden></div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-banksoal::ui.stat-card label="Total Arsip" :value="$stats['total_arsip'] ?? 0" icon="fa-archive" tone="blue" />
    <x-banksoal::ui.stat-card label="Riwayat Penarikan" :value="$stats['total_penarikan'] ?? 0" icon="fa-clock-rotate-left" tone="amber" />
    <x-banksoal::ui.stat-card label="Mata Kuliah" :value="$stats['mata_kuliah'] ?? 0" icon="fa-book-open" tone="green" />
    <x-banksoal::ui.stat-card label="Semester Aktif" :value="max(1, ($arsipGrouped ?? collect())->count())" icon="fa-calendar-alt" tone="slate" />
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Tab Diarsipkan -->
    <x-ui.card class="overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-border bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Diarsipkan</h3>
            <span class="text-xs font-medium text-muted-foreground">{{ max(1, ($arsipGrouped ?? collect())->count()) }} Semester</span>
        </div>
        <div id="arsipTab" class="p-5 space-y-6">
            @forelse(($arsipGrouped ?? collect()) as $semesterKey => $mkGroups)
                @php([$tahun, $semester] = explode('|', $semesterKey . '|'))
                <x-ui.card class="overflow-hidden shadow-none border-border">
                    <div class="px-5 py-3 bg-slate-50/50 border-b border-border flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $semester ?? '-' }}</p>
                            <p class="text-xs text-muted-foreground">Tahun Akademik {{ $tahun ?? '-' }}</p>
                        </div>
                        <x-ui.badge variant="secondary">{{ $mkGroups->flatten()->count() }} arsip</x-ui.badge>
                    </div>
                    <div class="divide-y divide-border">
                        @foreach($mkGroups as $mkId => $arsips)
                            @php($first = $arsips->first())
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                            <i class="fas fa-book text-sm"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $first->mataKuliah->nama ?? 'Mata Kuliah' }}</p>
                                        <p class="text-sm text-muted-foreground">{{ $first->mataKuliah->kode ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-6 flex-1">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($arsips->take(3) as $arsip)
                                            <x-ui.badge variant="sky" class="font-medium text-[10px]">
                                                {{ Str::limit($arsip->nama_arsip, 12) }}
                                            </x-ui.badge>
                                        @endforeach
                                        @if($arsips->count() > 3)
                                            <x-ui.badge variant="secondary" class="font-medium text-[10px]">
                                                +{{ $arsips->count() - 3 }}
                                            </x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex -space-x-2">
                                            @foreach($arsips->unique('user_id')->take(3) as $a)
                                                <x-ui.avatar size="sm" fallback="{{ substr($a->user->name ?? 'D', 0, 2) }}" class="border-2 border-white" />
                                            @endforeach
                                        </div>
                                        <x-ui.button as="a" href="{{ route('banksoal.arsip.dosen.show', $first->id) }}" variant="outline" size="sm" class="text-xs">
                                            Buka
                                        </x-ui.button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @empty
                <div class="rounded-xl border border-dashed border-border bg-slate-50 p-8 text-center text-muted-foreground text-sm">
                    Belum ada arsip soal.
                </div>
            @endforelse
        </div>
    </x-ui.card>

    <!-- Tab Riwayat Penarikan -->
    <x-ui.card class="overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-border bg-slate-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900">Riwayat Penarikan</h3>
            <span class="text-xs font-medium text-muted-foreground">{{ count($penarikanPending ?? []) }} Pending</span>
        </div>
        <div class="p-5 space-y-4">
            @forelse($penarikanPending as $penarikan)
                <x-ui.card class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white hover:bg-slate-50/50 transition-colors border-border shadow-none">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 mt-1 sm:mt-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-50 text-warning-500">
                                <i class="fas fa-clock-rotate-left text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-slate-900">{{ $penarikan->nama_ekstraksi }}</p>
                                <x-ui.badge variant="warning" class="text-[10px] py-0">Pending</x-ui.badge>
                                @if(($penarikan->metode_ujian ?? '') === 'offline')
                                    <span class="inline-flex items-center rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Offline Cetak</span>
                                @endif
                            </div>
                            <p class="text-sm text-muted-foreground mt-1">{{ $penarikan->mataKuliah->nama ?? '-' }} · {{ $penarikan->semester }} · {{ $penarikan->tahun_akademik }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 mt-3 sm:mt-0 w-full sm:w-auto">
                        <a href="{{ route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 text-xs font-medium transition-colors">
                            <i class="fas fa-archive"></i> Arsipkan
                        </a>
                        <form action="{{ route('banksoal.arsip.dosen.penarikan.destroy', $penarikan->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat penarikan ini?')" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="outline" size="sm" class="w-full sm:w-auto text-xs">
                                <i class="fas fa-trash text-muted-foreground mr-1.5"></i> Discard
                            </x-ui.button>
                        </form>
                    </div>
                </x-ui.card>
            @empty
                <div class="rounded-xl border border-dashed border-border bg-slate-50 p-8 text-center text-muted-foreground text-sm">
                    Belum ada riwayat penarikan.
                </div>
            @endforelse
        </div>
    </x-ui.card>
</div>

<div id="uploadPdfModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[1px]" data-modal-overlay="uploadPdfModal"></div>
    <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
        <form action="{{ route('banksoal.arsip.dosen.upload-pdf') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Upload PDF Arsip</h2>
                    <p class="text-xs text-slate-500">Seret file PDF ke area upload atau pilih file dari perangkat.</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="uploadPdfModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600">File PDF <span class="text-rose-500">*</span></label>
                    <div id="pdfUploadZone" class="mt-2 cursor-pointer rounded-xl border-2 border-dashed border-slate-200 p-5 text-center transition-colors">
                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-400"></i>
                        <p class="mt-2 text-sm text-slate-500">Dragdrop file atau <span class="text-primary underline">pilih file</span></p>
                        <p class="text-xs text-slate-400">Format: .pdf (maksimal 50 MB)</p>
                        <input type="file" name="pdf_file" id="pdfFileInput" accept="application/pdf,.pdf" required class="hidden">
                        <div id="pdfFileSelected" class="mt-3 hidden">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                File terpilih: <span id="pdfSelectedFileName"></span>
                            </div>
                        </div>
                    </div>
                    @error('pdf_file')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="rounded-lg border border-primary/20 bg-primary/10 px-3 py-2 text-xs text-primary">
                    <i class="fas fa-info-circle mr-2"></i>
                    Upload PDF akan disimpan sebagai arsip terpisah untuk diproses lebih lanjut.
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="uploadPdfModal">Batal</button>
                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90">Upload PDF</button>
            </div>
        </form>
    </div>
</div>

<div id="uploadCsvModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[1px]" data-modal-overlay="uploadCsvModal"></div>
    <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
        <form action="{{ route('banksoal.arsip.dosen.upload-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Upload CSV Arsip</h2>
                    <p class="text-xs text-slate-500">Gunakan template CSV agar format data sesuai.</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="uploadCsvModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Template CSV</p>
                        <p class="text-xs text-slate-500">Unduh template sebelum unggah file CSV.</p>
                    </div>
                    <a href="{{ route('banksoal.soal.dosen.export-csv') }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-primary/20 bg-white px-3 py-2 text-xs font-semibold text-primary transition-colors hover:bg-primary/10">
                        <i class="fas fa-download"></i> Unduh Template
                    </a>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">File CSV <span class="text-rose-500">*</span></label>
                    <div id="csvUploadZone" class="mt-2 cursor-pointer rounded-xl border-2 border-dashed border-slate-200 p-5 text-center transition-colors">
                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-400"></i>
                        <p class="mt-2 text-sm text-slate-500">Dragdrop file atau <span class="text-primary underline">pilih file</span></p>
                        <p class="text-xs text-slate-400">Format: .csv, .txt, .xls, .xlsx (maksimal 50 MB)</p>
                        <input type="file" name="csv_file" id="csvFileInput" accept=".csv,.txt,.xls,.xlsx" required class="hidden">
                        <div id="csvFileSelected" class="mt-3 hidden">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                File terpilih: <span id="csvSelectedFileName"></span>
                            </div>
                        </div>
                    </div>
                    @error('csv_file')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="rounded-lg border border-primary/20 bg-primary/10 px-3 py-2 text-xs text-primary">
                    <i class="fas fa-info-circle mr-2"></i>
                    CSV akan diproses sesuai template yang diunduh dari tombol di atas.
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="uploadCsvModal">Batal</button>
                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90">Upload CSV</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleArsipUploadMenu() {
        const menu = document.getElementById('arsipUploadMenu');
        if (!menu) return;

        const isOpen = menu.dataset.open === 'true';
        menu.dataset.open = isOpen ? 'false' : 'true';
        menu.classList.toggle('opacity-0', isOpen);
        menu.classList.toggle('scale-95', isOpen);
        menu.classList.toggle('translate-y-1', isOpen);
        menu.classList.toggle('pointer-events-none', isOpen);
        menu.classList.toggle('opacity-100', !isOpen);
        menu.classList.toggle('scale-100', !isOpen);
        menu.classList.toggle('translate-y-0', !isOpen);
    }

    function openArsipUploadModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const menu = document.getElementById('arsipUploadMenu');
        if (menu) {
            menu.dataset.open = 'false';
            menu.classList.add('opacity-0', 'scale-95', 'translate-y-1', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeArsipUploadModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function initUploadZone(zoneId, inputId, selectedId, fileNameId) {
        const zone = document.getElementById(zoneId);
        const input = document.getElementById(inputId);
        const selected = document.getElementById(selectedId);
        const fileName = document.getElementById(fileNameId);

        if (!zone || !input || !selected || !fileName) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
            zone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            zone.addEventListener(eventName, function () {
                zone.classList.add('border-primary/30', 'bg-primary/10');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            zone.addEventListener(eventName, function () {
                zone.classList.remove('border-primary/30', 'bg-primary/10');
            });
        });

        zone.addEventListener('click', function () {
            input.click();
        });

        zone.addEventListener('drop', function (event) {
            if (!event.dataTransfer.files.length) return;
            input.files = event.dataTransfer.files;
            if (input.files.length > 0) {
                fileName.textContent = input.files[0].name;
                selected.classList.remove('hidden');
            }
        });

        input.addEventListener('change', function () {
            if (input.files.length > 0) {
                fileName.textContent = input.files[0].name;
                selected.classList.remove('hidden');
            } else {
                selected.classList.add('hidden');
            }
        });
    }

    document.addEventListener('click', function (event) {
        const menu = document.getElementById('arsipUploadMenu');
        const trigger = event.target.closest('#arsipUploadTrigger');
        if (!menu || trigger || menu.contains(event.target)) return;

        menu.dataset.open = 'false';
        menu.classList.add('opacity-0', 'scale-95', 'translate-y-1', 'pointer-events-none');
        menu.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
    });

    document.addEventListener('click', function (event) {
        const closeButton = event.target.closest('[data-modal-close]');
        if (closeButton) {
            closeArsipUploadModal(closeButton.getAttribute('data-modal-close'));
            return;
        }

        const overlay = event.target.closest('[data-modal-overlay]');
        if (overlay) {
            closeArsipUploadModal(overlay.getAttribute('data-modal-overlay'));
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        initUploadZone('pdfUploadZone', 'pdfFileInput', 'pdfFileSelected', 'pdfSelectedFileName');
        initUploadZone('csvUploadZone', 'csvFileInput', 'csvFileSelected', 'csvSelectedFileName');

        const pageData = document.getElementById('arsip-dosen-page-data');
        const initialArsipUploadModal = pageData ? pageData.dataset.initialModal : '';
        if (initialArsipUploadModal) {
            openArsipUploadModal(initialArsipUploadModal);
        }
    });
</script>

</x-banksoal::layouts.dosen-admin>
