<x-banksoal::layouts.dosen-admin>
    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header 
        title="Edit Rencana Pembelajaran Semester" 
        subtitle="Perbarui data rencana pembelajaran semester dan dokumen pendukung." 
    />

    <div class="card mb-8">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-slate-900">Formulir Edit RPS</h2>
            <p class="text-sm text-slate-600 mt-1">Status: <span class="badge {{ match($rps->status->value) {
                'diajukan' => 'badge-warning',
                'revisi' => 'badge-danger',
                'disetujui' => 'badge-success',
                default => 'badge-secondary'
            } }}">{{ $rps->status->label() }}</span></p>
        </div>

        <form action="{{ route('banksoal.rps.dosen.update', $rps->id) }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-6" data-route-dosen="{{ route('banksoal.rps.dosen.dosen') }}" data-route-cpl="{{ route('banksoal.rps.dosen.cpl') }}" data-route-cpmk="{{ route('banksoal.rps.dosen.cpmk') }}">
            @csrf
            @method('PUT')

            <!-- Row 1: Mata Kuliah & Dosen Lain -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-group-compact">
                    <label class="form-label form-label-required">Mata Kuliah</label>
                    <select name="mata_kuliah_id" id="mkSelect" class="form-control compact-control" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                        <option value="" disabled>Pilih Mata Kuliah</option>
                        @foreach ($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ $mk->id == $rps->mk_id ? 'selected' : '' }}>
                                {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)
                            </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group-compact">
                    <label class="form-label">Dosen Pengampu Lain</label>
                    <div id="dosenMs" class="form-control compact-control" data-name="dosen_lain[]" data-placeholder="Pilih dosen pengampu tambahan" data-disabled="false"></div>
                    <small class="form-hint">Pilih satu atau lebih dosen pengampu tambahan.</small>
                </div>
            </div>

            <!-- Row 2: Semester & Tahun Ajaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-group-compact">
                    <label class="form-label">Semester</label>
                    <select name="semester" id="semester" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                        <option value="Ganjil" {{ $rps->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="Genap" {{ $rps->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                <div class="form-group-compact">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-control compact-control" {{ !$isUploadOpen ? 'disabled' : '' }}>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta }}" {{ $ta == $rps->tahun_ajaran ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- CPL (Full Width) -->
            <div class="form-group">
                <label class="form-label form-label-required">Capaian Pembelajaran Lulusan (CPL)</label>
                <div id="cplMs" class="form-control" data-name="cpl_ids[]" data-placeholder="Pilih CPL" data-disabled="false"></div>
                <small class="form-hint">CPL akan tersedia setelah mata kuliah dipilih.</small>
                @error('cpl_ids')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- CPMK (Full Width) -->
            <div class="form-group">
                <label class="form-label form-label-required">Capaian Pembelajaran Mata Kuliah (CPMK)</label>
                <div id="cpmkMs" class="form-control" data-name="cpmk_ids[]" data-placeholder="Pilih CPMK" data-disabled="false"></div>
                <small class="form-hint">CPMK akan tersedia setelah CPL dipilih.</small>
                @error('cpmk_ids')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label">Dokumen RPS</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="{{ route('banksoal.rps.dosen.preview', $rps->id) }}" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Lihat dokumen saat ini">
                            <i class="fas fa-file-pdf"></i> Lihat Dokumen
                        </a>
                        <a href="{{ route('rps.template.download') }}" target="_blank" style="color: #2563eb; text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;" title="Download template RPS">
                            <i class="fas fa-download"></i> Template
                        </a>
                    </div>
                </div>
                <label class="upload-zone {{ !$isUploadOpen ? 'upload-zone-closed' : '' }}" id="uploadZone">
                    <input type="file" name="dokumen" accept=".pdf" id="fileInput" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <i class="fas fa-cloud-upload-alt {{ !$isUploadOpen ? 'upload-icon-closed' : '' }}" id="uploadIcon"></i>
                    <strong id="uploadText" class="{{ !$isUploadOpen ? 'upload-text-closed' : '' }}">
                        {{ !$isUploadOpen ? 'Upload ditutup' : 'Klik untuk unggah atau seret file ke sini' }}
                    </strong>
                    <span id="uploadSub">PDF (Maks. 1MB) - Kosongkan jika tidak ingin mengganti dokumen</span>
                </label>
                @error('dokumen')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @once
                <style>
                    .upload-zone {
                        border: 2px dashed #cbd5e1;
                        border-radius: 12px;
                        padding: 36px 20px;
                        text-align: center;
                        cursor: pointer;
                        transition: border-color 0.2s, background-color 0.2s;
                        background: #f8fafc;
                        display: block;
                    }

                    .upload-zone:hover {
                        border-color: #111827;
                        background: #f1f5f9;
                    }

                    .upload-zone i {
                        font-size: 32px;
                        color: #111827;
                        margin-bottom: 10px;
                        display: block;
                    }

                    .upload-zone strong {
                        font-size: 14px;
                        font-weight: 600;
                        color: #111827;
                        display: block;
                        margin-bottom: 4px;
                    }

                    .upload-zone span {
                        font-size: 12px;
                        color: #64748b;
                    }

                    .upload-zone input {
                        display: none;
                    }

                    .upload-zone-closed {
                        background-color: #f5f5fa;
                        border-color: #dfdfe6;
                        cursor: not-allowed;
                        opacity: 0.7;
                    }

                    .upload-icon-closed {
                        color: #ababba;
                    }

                    .upload-text-closed {
                        color: #6e6e83;
                    }

                    .compact-control {
                        height: 52px;
                        min-height: 52px;
                        padding-top: 10px;
                        padding-bottom: 10px;
                    }

                </style>
            @endonce

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('banksoal.rps.dosen.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-primary" id="submitBtn" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <i class="fas fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- History Log Card -->
    <div class="card overflow-hidden">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i>
                <h3 class="text-lg font-semibold text-slate-900">Riwayat Aktivitas RPS</h3>
            </div>
        </div>
        <div class="p-4">
            @if($history->count() > 0)
                <div class="space-y-3">
                    @foreach($history as $item)
                    <div class="flex items-start gap-3 pb-3 {{ !$loop->last ? 'border-b border-slate-200' : '' }}">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full" 
                                 style="background-color: {{ $loop->first ? '#3b82f6' : '#eab308' }}; color: white;">
                                <i class="fas {{ $loop->first ? 'fa-circle-check' : 'fa-edit' }} text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h6 class="font-semibold text-slate-900 text-sm">{{ ucfirst($item->action) }}</h6>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y - H:i') }}
                            </p>
                            <p class="text-sm text-slate-700 mt-2">{{ $item->processed_description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-info-circle text-slate-400 text-2xl mb-2"></i>
                <p class="text-slate-500 text-sm">Belum ada riwayat aktivitas untuk RPS ini</p>
            </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('modules/banksoal/js/Banksoal/components/Dropdown.js') }}"></script>
    <script src="{{ asset('modules/banksoal/js/Banksoal/components/MultiSelect.js') }}"></script>
    <script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsForm.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const dropdownManager = new Dropdown();
            dropdownManager.initAll({
                '#mkSelect': 'Pilih Mata Kuliah',
                '#semester': 'Pilih Semester',
                '#tahun_ajaran': 'Pilih Tahun Ajaran',
            });

            const dosenMs = new MultiSelect(document.getElementById('dosenMs'), { maxWidth: 467, keepOpen: true });
            const cplMs = new MultiSelect(document.getElementById('cplMs'), { maxWidth: 467, keepOpen: true });
            const cpmkMs = new MultiSelect(document.getElementById('cpmkMs'), { maxWidth: 467, keepOpen: true });

            // Pre-select existing data
            const selectedDosenIds = @json($selectedDosenIds ?? []);
            const selectedCplIds = @json($selectedCplIds ?? []);
            const selectedCpmkIds = @json($selectedCpmkIds ?? []);

            // Initialize RPS Form with MultiSelect instances
            const rpsForm = new RpsFormComponent();
            rpsForm.init({ dosenMs, cplMs, cpmkMs });

            // Initialize dosen multi-select with existing data if any
            if (selectedDosenIds.length > 0) {
                const mkId = document.getElementById('mkSelect').value;
                if (mkId) {
                    fetch(`{{ route('banksoal.rps.dosen.dosen') }}?mk_id=${mkId}`)
                        .then(response => response.json())
                        .then(data => {
                            const options = data.map(d => ({
                                id: d.id,
                                label: d.name,
                                selected: selectedDosenIds.includes(d.id)
                            }));
                            dosenMs.setItems(options, 'Pilih dosen pengampu tambahan');
                        })
                        .catch(err => console.error('Error loading dosen:', err));
                }
            }

            // Initialize CPL multi-select with existing data if any
            if (selectedCplIds.length > 0) {
                fetch(`{{ route('banksoal.rps.dosen.cpl') }}`)
                    .then(response => response.json())
                    .then(data => {
                        const options = data.map(c => ({
                            id: c.id,
                            label: c.kode,
                            selected: selectedCplIds.includes(c.id)
                        }));
                        cplMs.setItems(options, 'Pilih CPL');
                    })
                    .catch(err => console.error('Error loading CPL:', err));
            }

            // Initialize CPMK multi-select with existing data if any
            if (selectedCpmkIds.length > 0) {
                const cplIds = selectedCplIds;
                if (cplIds.length > 0) {
                    const queryString = cplIds.map(id => `cpl_ids[]=${id}`).join('&');
                    fetch(`{{ route('banksoal.rps.dosen.cpmk') }}?${queryString}`)
                        .then(response => response.json())
                        .then(data => {
                            const options = data.map(c => ({
                                id: c.id,
                                label: c.kode,
                                selected: selectedCpmkIds.includes(c.id)
                            }));
                            cpmkMs.setItems(options, 'Pilih CPMK');
                        })
                        .catch(err => console.error('Error loading CPMK:', err));
                }
            }

            // Trigger initial cascading if mata kuliah is already selected
            const mkSelect = document.getElementById('mkSelect');
            if (mkSelect.value) {
                mkSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            console.log('RPS Edit Form initialized successfully');
        } catch (error) {
            console.error('Error during RPS edit form initialization:', error);
        }
    });
    </script>

    @include('banksoal::partials.dosen.layout-scripts')
</x-banksoal::layouts.dosen-admin>
