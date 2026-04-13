<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.sidebar', ['active' => 'rps'])
@include('banksoal::partials.dosen.topbar')

<!-- MAIN -->
<main class="main">

{{-- Flash Messages Snackbar --}}
@if ($message = Session::get('success'))
    <div class="snackbar snackbar-success" role="alert">
        <i class="fas fa-check-circle"></i>
        <span>{{ $message }}</span>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="snackbar snackbar-error" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $message }}</span>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

@if ($errors->any())
    <div class="snackbar snackbar-error" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <span style="display: block; margin-bottom: 8px;">Validasi gagal:</span>
            <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
    </div>
@endif

<div class="page-header">
    <h1>Manajemen RPS</h1>
    <p>Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung.</p>
</div>

{{-- STATUS BANNER --}}
@if($activePeriode)
    <div class="mb-8 p-5 bg-{{ $isUploadOpen ? 'success-50' : 'warning-50' }} border-l-4 border-{{ $isUploadOpen ? 'success-300' : 'warning-300' }} rounded-lg flex items-start justify-between gap-4">
        <div class="flex gap-4 items-start">
            @if($isUploadOpen)
                <i class="fas fa-calendar-check text-success-300 text-2xl mt-1"></i>
            @else
                <i class="fas fa-calendar-times text-warning-300 text-2xl mt-1"></i>
            @endif
            <div>
                <h3 class="font-bold text-slate-800 mb-1 text-lg">
                    {{ $activePeriode->judul }}
                </h3>
                <p class="text-grey-400 text-sm">
                    Batas akhir pengunggahan RPS untuk Semester {{ $activePeriode->semester }} {{ $activePeriode->tahun_ajaran }} adalah <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.
                    @if(!$isUploadOpen)
                        <span class="text-error-600 font-semibold block mt-1">Sesi unggah saat ini sedang ditutup.</span>
                    @endif
                </p>
            </div>
        </div>
        <a href="#" class="px-4 py-2 text-sm font-medium text-grey-600 bg-white border border-grey-100 rounded-md hover:bg-grey-25 transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-circle-question"></i> Panduan Pengisian
        </a>
    </div>

    {{-- H-7 REMINDER NOTIFICATION --}}
    @if($tenggatH7 && count($unsubmittedMkCodes) > 0)
    <div class="mb-8 p-4 bg-error-50 border border-error-200 text-error-800 rounded-lg flex items-start gap-4">
        <i class="fas fa-exclamation-triangle text-lg"></i>
        <div>
            Waktu tersisa <strong>{{ $daysLeft }} hari lagi!</strong> Anda belum mengunggah RPS untuk mata kuliah: 
            <span class="font-semibold">{{ implode(', ', $unsubmittedMkCodes) }}</span>. 
            Mohon segera lengkapi sebelum batas waktu berakhir.
        </div>
    </div>
    @endif
@else
    <div class="mb-8" style="background-color: #FFFFFF; border: 1px solid #F3E8C0; border-left: 4px solid #C6930A; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="flex-shrink-0" style="width: 40px; height: 40px; background-color: #FEF3C7; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-xmark" style="color: #C6930A; font-size: 18px;"></i>
            </div>
            <div>
                <h3 style="font-weight: 700; color: #1F2937; margin: 0 0 4px 0; font-size: 15px;">
                    Belum Ada Jadwal Pengajuan
                </h3>
                <p style="color: #6E6E83; font-size: 13px; margin: 0;">
                    Tidak ada sesi pengajuan RPS yang ditambahkan saat ini
                </p>
            </div>
        </div>
        <span class="flex-shrink-0" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; border-radius: 9999px; color: #92400E; background-color: #FEF3C7; border: 1px solid #F3E8C0; white-space: nowrap;">
            <i class="fas fa-exclamation-circle"></i> Belum Aktif
        </span>
    </div>
@endif

{{-- FORM CARD --}}
<div class="form-card">
    <div class="form-card-title">Formulir Rencana Pembelajaran</div>

    <form action="{{ route('banksoal.rps.dosen.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid-2">
            {{-- Mata Kuliah --}}
            <div class="select-wrap">
                <label class="form-label">Mata Kuliah <span class="text-error-600">*</span></label>
                <select name="mata_kuliah_id" id="mkSelect" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    @foreach ($mataKuliahs as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->sks }} SKS)</option>
                    @endforeach
                </select>
            </div>

            {{-- Dosen Pengampu Lain — Multi-select --}}
            <div class="select-wrap">
                <label class="form-label">Dosen Pengampu Lain</label>
                <div id="dosenMs" class="ms-wrapper"
                     data-name="dosen_lain[]"
                     data-placeholder="{{ !$isUploadOpen ? 'Ditutup' : 'Pilih mata kuliah terlebih dahulu' }}"
                     data-disabled="true"></div>
                <small class="form-hint">Pilih satu atau lebih dosen pengampu tambahan.</small>
            </div>

            {{-- Semester --}}
            <div class="select-wrap">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <option value="Ganjil" {{ $semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap"  {{ $semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="select-wrap">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" {{ !$isUploadOpen ? 'disabled' : '' }}>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta }}" {{ $ta == $academicYear ? 'selected' : '' }}>{{ $ta }}</option>
                    @endforeach
                </select>
            </div>

            {{-- CPL — Multi-select --}}
            <div class="select-wrap full">
                <label class="form-label">Capaian Pembelajaran Lulusan (CPL) <span class="text-error-600">*</span></label>
                <div id="cplMs" class="ms-wrapper"
                     data-name="cpl_ids[]"
                     data-placeholder="{{ !$isUploadOpen ? 'Ditutup' : 'Pilih mata kuliah terlebih dahulu' }}"
                     data-disabled="true"></div>
                <small class="form-hint">CPL akan tersedia setelah mata kuliah dipilih.</small>
            </div>

            {{-- CPMK — Multi-select --}}
            <div class="select-wrap full">
                <label class="form-label">Capaian Pembelajaran Mata Kuliah (CPMK) <span class="text-error-600">*</span></label>
                <div id="cpmkMs" class="ms-wrapper"
                     data-name="cpmk_ids[]"
                     data-placeholder="{{ !$isUploadOpen ? 'Ditutup' : 'Pilih CPL terlebih dahulu' }}"
                     data-disabled="true"></div>
                <small class="form-hint">CPMK akan tersedia setelah CPL dipilih.</small>
            </div>

            {{-- Upload --}}
            <div class="form-group full">
                <label class="form-label">Dokumen RPS <span class="text-error-600">*</span></label>
                <label class="upload-zone" id="uploadZone" style="{{ !$isUploadOpen ? 'background-color: #f5f5fa; border-color: #dfdfe6; cursor: not-allowed; opacity: 0.7;' : '' }}">
                    <input type="file" name="dokumen" accept=".pdf" id="fileInput" required {{ !$isUploadOpen ? 'disabled' : '' }}>
                    <i class="fas fa-cloud-upload-alt" id="uploadIcon" style="{{ !$isUploadOpen ? 'color: #ababba;' : '' }}"></i>
                    <strong id="uploadText" style="{{ !$isUploadOpen ? 'color: #6e6e83;' : '' }}">
                        {{ !$isUploadOpen ? 'Upload ditutup' : 'Klik untuk unggah atau seret file ke sini' }}
                    </strong>
                    <span id="uploadSub">PDF (Maks. 1MB)</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">Batal</button>
            <button type="submit" class="btn-primary" id="submitBtn" {{ !$isUploadOpen ? 'disabled style="background: #94a3b8; cursor: not-allowed;"' : '' }}>
                <i class="fas fa-floppy-disk"></i> Simpan RPS
            </button>
        </div>
    </form>
</div>

{{-- HISTORY TABLE --}}
<div class="history-card">
    <div class="history-title">Riwayat Pengunggahan</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tahun/Semester</th>
                    <th>Mata Kuliah</th>
                    <th>Tanggal Unggah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    <tr>
                        <td>{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                        <td>{{ $item->mataKuliah?->nama ?? 'N/A' }} ({{ $item->mataKuliah?->kode ?? 'N/A' }})</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $item->status->badgeClass() }}">
                                {{ $item->status->label() }}
                            </span>
                        </td>
                        <td>
                            @if ($item->dokumen)
                                <a href="javascript:void(0)" onclick="previewDokumen({{ $item->id }}, '{{ $item->mataKuliah?->nama ?? 'Dokumen' }}', '{{ $item->dokumen }}')" class="action-link">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 20px;">
                            <i class="fas fa-inbox"></i> Belum ada riwayat pengunggahan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL PREVIEW DOKUMEN -->
<div id="dokumenModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 10px; width: 90%; max-width: 860px; height: 88vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header Modal -->
        <div style="padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; flex-shrink: 0;">
            <h3 id="modalTitle" style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Preview Dokumen</h3>
            <button onclick="closeDokumenModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;">×</button>
        </div>
        <!-- Content Modal -->
        <div style="flex: 1; overflow: hidden; min-height: 0;">
            <iframe id="dokumenFrame" style="width: 100%; height: 100%; border: none; display: none;"></iframe>
            <div id="dokumenEmbed" style="width: 100%; height: 100%; display: none;"></div>
        </div>
        <!-- Footer Modal -->
        <div style="padding: 12px 20px; border-top: 1px solid #eee; text-align: right; background: #f8f9fa; flex-shrink: 0;">
            <button onclick="closeDokumenModal()" style="padding: 10px 24px; background: #007bff; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: 500; font-size: 14px; transition: all 0.2s;">Tutup</button>
        </div>
    </div>
</div>

</main>

{{-- ═══ Component Scripts ═══ --}}
<script src="{{ asset('modules/banksoal/js/Banksoal/components/Dropdown.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/MultiSelect.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/RpsForm.js') }}"></script>
<script src="{{ asset('modules/banksoal/js/Banksoal/components/FileUploadHandler.js') }}"></script>

{{-- ═══ Page Initialization ═══ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page initialized, checking TomSelect...');
    console.log('TomSelect available:', typeof TomSelect !== 'undefined');
    console.log('TomSelectDropdown available:', typeof TomSelectDropdown !== 'undefined');
    
    try {
        // ── Initialize TomSelect Dropdowns ──
        const dropdownManager = new Dropdown();
        console.log('TomSelectDropdown instance created');
        
        dropdownManager.initAll({
            '#mkSelect': 'Pilih Mata Kuliah',
            '#semester': 'Pilih Semester',
            '#tahun_ajaran': 'Pilih Tahun Ajaran',
        });
        console.log('TomSelect dropdowns initialized');

        // Initialize MultiSelect instances
        const dosenMs  = new MultiSelect(document.getElementById('dosenMs'), { maxWidth: 467, keepOpen: true });
        const cplMs    = new MultiSelect(document.getElementById('cplMs'), { maxWidth: 467, keepOpen: true });
        const cpmkMs   = new MultiSelect(document.getElementById('cpmkMs'), { maxWidth: 467, keepOpen: true });
        console.log('MultiSelect instances created');

        // Initialize RPS form with MultiSelect instances
        RpsForm.init({ dosenMs, cplMs, cpmkMs });
        console.log('RpsForm initialized');
    } catch (error) {
        console.error('Error during initialization:', error);
    }
});
</script>

@include('banksoal::partials.dosen.layout-scripts')

</x-banksoal::layouts.dosen-master>