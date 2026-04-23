<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Manajemen Jadwal RPS" subtitle="Kelola periode unggah RPS untuk Dosen">
        <x-slot:actions>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50" data-modal-open="modalUploadTemplate">
                <i class="fas fa-file-upload"></i> Upload Template
            </button>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700" data-modal-open="modalTambah">
                <i class="fas fa-plus"></i> Tambah Periode
            </button>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Info Periode</th>
                        <th class="px-6 py-4 text-left">Rentang Waktu</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($periodes as $index => $periode)
                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $periode->judul }}</p>
                                <p class="text-xs text-slate-500">Semester {{ $periode->semester }} &bull; TA {{ $periode->tahun_ajaran }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y, H:i') }}
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-rose-500">
                                    <i class="fas fa-flag-checkered"></i>
                                    <span class="text-slate-600">{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($periode->is_active)
                                    <span class="inline-flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50" data-modal-open="modalEdit{{ $periode->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" data-modal-open="modalHapus{{ $periode->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-calendar-times text-3xl text-slate-300"></i>
                                    <p class="text-sm font-semibold">Belum ada jadwal RPS</p>
                                    <p class="text-xs">Silakan tambah periode baru untuk mengaktifkan pengajuan RPS Dosen.</p>
                                    <button class="mt-2 inline-flex items-center gap-2 rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50" data-modal-open="modalTambah">
                                        <i class="fas fa-plus"></i> Tambah Periode Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($periodes as $periode)
        <div id="modalEdit{{ $periode->id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalEdit{{ $periode->id }}"></div>
            <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
                <form action="{{ route('banksoal.rps.gpm.periode-rps.update', $periode->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h2 class="text-sm font-semibold text-slate-900">Edit Periode RPS</h2>
                        <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalEdit{{ $periode->id }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Judul Periode <span class="text-rose-500">*</span></label>
                            <input type="text" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="judul" value="{{ $periode->judul }}" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                        </div>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Semester <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="semester" required>
                                    <option value="Ganjil" {{ $periode->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $periode->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tahun_ajaran" required>
                                    <option value="" disabled>Pilih Tahun Ajaran</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta }}" {{ $periode->tahun_ajaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Mulai <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_mulai" value="{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Waktu Selesai (Tenggat) <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_selesai" value="{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 text-xs text-slate-600">
                            <input class="mt-1" type="checkbox" name="is_active" value="1" {{ $periode->is_active ? 'checked' : '' }}>
                            <span>
                                <span class="font-semibold text-slate-700">Set sebagai periode aktif saat ini</span>
                                <span class="block text-[11px] text-slate-500">Hanya satu periode yang bisa aktif. Mengaktifkan ini akan menonaktifkan periode lainnya.</span>
                            </span>
                        </label>
                    </div>
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalEdit{{ $periode->id }}">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalHapus{{ $periode->id }}" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalHapus{{ $periode->id }}"></div>
            <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl">
                <div class="px-5 py-5 text-center">
                    <div class="text-rose-500 mb-3"><i class="fas fa-exclamation-triangle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Hapus Periode?</h3>
                    <p class="text-xs text-slate-500 mt-2">Anda yakin ingin menghapus jadwal <strong>{{ $periode->judul }}</strong>?</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalHapus{{ $periode->id }}">Batal</button>
                        <form action="{{ route('banksoal.rps.gpm.periode-rps.destroy', $periode->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div id="modalTambah" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalTambah"></div>
        <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.store') }}" method="POST">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Tambah Periode RPS Baru</h2>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalTambah">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Judul Periode <span class="text-rose-500">*</span></label>
                        <input type="text" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="judul" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026" value="{{ old('judul') }}">
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Semester <span class="text-rose-500">*</span></label>
                            <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="semester" required>
                                <option value="Ganjil" {{ old('semester', $currentSemester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester', $currentSemester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Tahun Ajaran <span class="text-rose-500">*</span></label>
                            <select class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tahun_ajaran" required>
                                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta }}" {{ old('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Waktu Mulai <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Waktu Selesai (Tenggat) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}">
                    </div>
                    <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-3 text-xs text-slate-600">
                        <input class="mt-1" type="checkbox" name="is_active" value="1" checked>
                        <span>
                            <span class="font-semibold text-slate-700">Otomatis aktifkan periode ini</span>
                            <span class="block text-[11px] text-slate-500">GPM hanya bisa membuka 1 sesi pengajuan dalam satu waktu.</span>
                        </span>
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalTambah">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Buat Periode</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalUploadTemplate" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40" data-modal-overlay="modalUploadTemplate"></div>
        <div class="relative mx-auto mt-16 w-full max-w-xl rounded-2xl bg-white shadow-xl">
            <form action="{{ route('banksoal.rps.gpm.template.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Upload Template RPS</h2>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close="modalUploadTemplate">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">File Template (Word Format) <span class="text-rose-500">*</span></label>
                        <div id="uploadBoxTemplate" class="mt-2 rounded-xl border-2 border-dashed border-slate-200 p-4 text-center cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                            <p class="text-sm text-slate-500">Dragdrop file atau <span class="text-blue-600 underline">pilih file</span></p>
                            <p class="text-xs text-slate-400">Format: .doc, .docx (Maksimal 1 MB)</p>
                            <input type="file" name="dokumen" id="fileTemplate" accept=".doc,.docx" required class="hidden">
                            <div id="fileSelected" class="mt-3 hidden">
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    File terpilih: <span id="selectedFileName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Keterangan (Opsional)</label>
                        <textarea class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="keterangan" rows="3" placeholder="Misal: Update struktur template, tambahan BAB, dll..."></textarea>
                    </div>
                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Template baru akan otomatis menjadi versi terbaru yang dapat diunduh dosen.
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalUploadTemplate">Batal</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Upload Template</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadBox = document.getElementById('uploadBoxTemplate');
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
                            <i class="fas fa-file-upload me-2" style="color: #667eea;"></i>Upload Template RPS Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">
                                <i class="fas fa-file-word me-1" style="color: #2563eb;"></i>
                                File Template <span class="text-danger">*</span>
                            </label>
                            <div class="upload-box-modal" id="uploadBoxModal" style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 2rem; text-align: center; cursor: pointer; background-color: #f8fafc; transition: all 0.2s;">
                                <div style="font-size: 2rem; color: #64748b; margin-bottom: 0.5rem;"><i class="fas fa-cloud-upload-alt"></i></div>
                                <div style="font-weight: 600; color: #111827; margin-bottom: 0.25rem;">Klik atau seret file</div>
                                <div style="font-size: 0.875rem; color: #64748b;">Format: .doc atau .docx (Maksimal 1MB)</div>
                                <input type="file" id="fileTemplateModal" name="dokumen" accept=".doc,.docx" required style="display: none;">
                            </div>
                            <div id="fileSelectedModal" style="margin-top: 0.75rem; padding: 0.75rem; background-color: #dcfce7; border-radius: 6px; color: #166534; display: none;">
                                <i class="fas fa-check-circle me-2"></i>
                                File terpilih: <strong id="selectedFileNameModal"></strong>
                            </div>
                            @error('dokumen')
                                <div style="color: #991b1b; font-size: 0.875rem; margin-top: 0.5rem;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">
                                <i class="fas fa-note-sticky me-1" style="color: #6b7280;"></i>
                                Catatan / Keterangan (Opsional)
                            </label>
                            <textarea 
                                name="keterangan" 
                                rows="2" 
                                class="form-control" 
                                placeholder="Misal: Update format standar, perbaikan, dll."
                                style="resize: vertical;"
                            ></textarea>
                            @error('keterangan')
                                <div style="color: #991b1b; font-size: 0.875rem; margin-top: 0.5rem;">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="alert alert-info alert-sm" role="alert" style="font-size: 0.875rem; padding: 0.75rem; margin-bottom: 0;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> Template baru akan otomatis menjadi versi terbaru yang dapat diunduh Dosen.
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Upload Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle drag & drop untuk upload template modal
        const uploadBoxModal = document.getElementById('uploadBoxModal');
        const fileInputModal = document.getElementById('fileTemplateModal');
        const fileSelectedModal = document.getElementById('fileSelectedModal');
        const selectedFileNameModal = document.getElementById('selectedFileNameModal');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBoxModal.addEventListener(eventName, preventDefaultsModal, false);
            document.body.addEventListener(eventName, preventDefaultsModal, false);
        });

        function preventDefaultsModal(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBoxModal.addEventListener(eventName, () => {
                uploadBoxModal.style.borderColor = '#667eea';
                uploadBoxModal.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadBoxModal.addEventListener(eventName, () => {
                uploadBoxModal.style.borderColor = '#cbd5e1';
                uploadBoxModal.style.backgroundColor = '#f8fafc';
            });
        });

        // Handle dropped files
        uploadBoxModal.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInputModal.files = files;
            updateFileDisplayModal();
        });

        // Handle file input change
        fileInputModal.addEventListener('change', updateFileDisplayModal);

        function updateFileDisplayModal() {
            if (fileInputModal.files.length > 0) {
                const fileName = fileInputModal.files[0].name;
                selectedFileNameModal.textContent = fileName;
                fileSelectedModal.style.display = 'block';
            } else {
                fileSelectedModal.style.display = 'none';
            }
        }

        // Make upload box clickable
        uploadBoxModal.addEventListener('click', () => {
            fileInputModal.click();
        });
    </script>

</x-banksoal::layouts.gpm-master>
