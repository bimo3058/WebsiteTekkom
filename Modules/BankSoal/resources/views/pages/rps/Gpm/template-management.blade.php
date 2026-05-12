<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Manajemen Template RPS" subtitle="Kelola template RPS yang dapat diunduh oleh dosen" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-4">
                <i class="fas fa-upload text-blue-600"></i> Upload Template RPS Baru
            </h3>

            <form action="{{ route('banksoal.rps.gpm.template.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="fileTemplate" class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-word text-blue-600"></i> File Template (Word/DOCX)
                    </label>
                    <div id="uploadBox" class="mt-2 rounded-xl border-2 border-dashed border-slate-200 p-6 text-center cursor-pointer">
                        <div class="text-2xl text-slate-400 mb-2"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="text-sm font-semibold text-slate-900">Klik di sini atau seret file untuk upload</div>
                        <div class="text-xs text-slate-500">Format: .doc atau .docx (Maksimal 1MB)</div>
                        <input type="file" id="fileTemplate" name="dokumen" accept=".doc,.docx" required class="hidden">
                        <div id="fileSelected" class="mt-3 hidden">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                <i class="fas fa-check-circle mr-2"></i> File terpilih: <span id="selectedFileName"></span>
                            </div>
                        </div>
                    </div>
                    @error('dokumen')
                        <div class="mt-2 text-xs text-rose-600"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="keterangan" class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-note-sticky text-slate-500"></i> Catatan / Keterangan (Opsional)
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        placeholder="Masukkan keterangan tentang template ini, misal: Perbaikan format, update standar, dll."
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                    ></textarea>
                    @error('keterangan')
                        <div class="mt-2 text-xs text-rose-600"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <button type="reset" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600">Reset</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700" id="submitBtn">
                        <i class="fas fa-arrow-up-from-bracket mr-2"></i> Upload Template
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 text-white">
                <h3 class="text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-list"></i> Daftar Template ({{ $templates->count() }} versi)
                </h3>
            </div>
            <div class="p-4">
                @if($templates->isEmpty())
                    <div class="flex flex-col items-center gap-2 py-6 text-slate-500">
                        <i class="fas fa-file-circle-question text-3xl text-slate-300"></i>
                        <p class="text-sm font-semibold">Belum ada template yang diupload</p>
                        <p class="text-xs">Upload template pertama Anda di atas untuk memulai</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-200">
                        @foreach($templates as $template)
                            <li class="py-4 flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <i class="fas fa-file-word text-2xl text-blue-600"></i>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $template->original_filename }}
                                            @if($template->is_latest)
                                                <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                                    <i class="fas fa-star mr-1"></i> Versi Terbaru
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700">v{{ $template->version }}</span>
                                            <span class="ml-2">Upload oleh: <strong>{{ $template->uploadedBy->name }}</strong></span>
                                            <span class="ml-2">Tanggal: <strong>{{ $template->created_at->format('d M Y H:i') }}</strong></span>
                                            @if($template->keterangan)
                                                <span class="block">Keterangan: <em>{{ $template->keterangan }}</em></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($template->is_latest)
                                        <span class="rounded-lg bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">AKTIF</span>
                                    @else
                                        <form action="{{ route('banksoal.rps.gpm.template.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus template ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" title="Hapus template">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-blue-700">
            <h4 class="text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-info-circle"></i> Informasi Penting
            </h4>
            <ul class="mt-2 text-xs list-disc pl-5 space-y-1">
                <li>Template disimpan dengan struktur folder: <code>TemplateRPS_V1</code>, <code>TemplateRPS_V2</code>, dst</li>
                <li>Dosen hanya dapat mengunduh <strong>versi tertinggi</strong> (V dengan angka paling besar)</li>
                <li>Versi sebelumnya tetap tersimpan untuk referensi dan riwayat</li>
                <li>Hanya satu template yang dapat menjadi "Versi Terbaru" pada saat tertentu</li>
                <li>Ketika upload template baru, template sebelumnya otomatis menjadi tidak aktif</li>
                <li>Template disimpan di Supabase: <code>rps/templates/rps/</code></li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadBox = document.getElementById('uploadBox');
            const fileInput = document.getElementById('fileTemplate');
            const fileSelected = document.getElementById('fileSelected');
            const selectedFileName = document.getElementById('selectedFileName');

            if (!uploadBox || !fileInput) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadBox.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadBox.addEventListener(eventName, () => {
                    uploadBox.classList.add('border-blue-300', 'bg-blue-50/40');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadBox.addEventListener(eventName, () => {
                    uploadBox.classList.remove('border-blue-300', 'bg-blue-50/40');
                });
            });

            uploadBox.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                fileInput.files = files;
                updateFileDisplay();
            });

            fileInput.addEventListener('change', updateFileDisplay);

            function updateFileDisplay() {
                if (fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name;
                    selectedFileName.textContent = fileName;
                    fileSelected.classList.remove('hidden');
                } else {
                    fileSelected.classList.add('hidden');
                }
            }

            uploadBox.addEventListener('click', () => {
                fileInput.click();
            });
        });
    </script>
</x-banksoal::layouts.gpm-master>