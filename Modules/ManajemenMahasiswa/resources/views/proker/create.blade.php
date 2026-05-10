<x-manajemenmahasiswa::layouts.mahasiswa>

@php
    // Reuse CSS from kegiatan/create - shared styles
@endphp

<style>
    .form-card { background:#fff; border-radius:12px; padding:24px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom:20px; }
    .form-card-title { font-weight:700; font-size:16px; color:#1f2937; margin-bottom:20px; display:flex; align-items:center; gap:8px; padding-bottom:14px; border-bottom:1px solid #f3f4f6; }
    .form-label-custom { font-weight:600; font-size:13px; color:#374151; margin-bottom:6px; display:block; }
    .form-label-custom .required { color:#dc2626; }
    .form-control-custom, .form-select-custom { border:1.5px solid #e5e7eb; border-radius:10px; padding:10px 14px; font-size:14px; font-weight:500; color:#1f2937; transition:all 0.2s; background:#fff; width:100%; }
    .form-control-custom:focus, .form-select-custom:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,0.1); outline:none; }
    textarea.form-control-custom { min-height:140px; resize:vertical; }
    .checkbox-card-group { display:flex; flex-wrap:wrap; gap:10px; }
    .checkbox-card { position:relative; display:flex; align-items:center; gap:8px; padding:10px 16px; border:1.5px solid #e5e7eb; border-radius:10px; background:#fff; cursor:pointer; transition:all 0.2s; font-size:13px; font-weight:500; color:#374151; user-select:none; }
    .checkbox-card:hover { border-color:#a5b4fc; background:#f5f3ff; }
    .checkbox-card input[type="checkbox"] { width:16px; height:16px; accent-color:#4f46e5; cursor:pointer; }
    .checkbox-card.checked { border-color:#4f46e5; background:#eef2ff; color:#4338ca; font-weight:600; }
    .checkbox-hint { font-size:11px; color:#9ca3af; font-weight:400; margin-top:6px; }
    .search-select-wrapper { position:relative; }
    .search-select-dropdown { position:absolute; top:100%; left:0; right:0; background:#fff; border:1.5px solid #e5e7eb; border-top:none; border-radius:0 0 10px 10px; max-height:200px; overflow-y:auto; z-index:100; display:none; box-shadow:0 8px 25px rgba(0,0,0,0.1); }
    .search-select-dropdown.show { display:block; }
    .search-select-option { padding:10px 14px; cursor:pointer; font-size:13px; font-weight:500; color:#374151; transition:background 0.15s; border-bottom:1px solid #f9fafb; }
    .search-select-option:hover { background:#eef2ff; color:#4f46e5; }
    .search-select-option .sub-text { font-size:11px; color:#9ca3af; font-weight:400; }
    .btn-back { width:40px; height:40px; border-radius:50%; background:#fff; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; text-decoration:none; color:#374151; font-size:18px; box-shadow:0 1px 3px rgba(0,0,0,0.06); transition:all 0.2s; flex-shrink:0; }
    .btn-back:hover { background:#f3f4f6; }
    .btn-submit { background:#4f46e5; color:#fff; font-weight:600; font-size:14px; padding:12px 28px; border-radius:10px; border:none; cursor:pointer; transition:all 0.2s; }
    .btn-submit:hover { background:#4338ca; transform:translateY(-1px); }
    .btn-cancel { background:#f3f4f6; color:#374151; font-weight:600; font-size:14px; padding:12px 28px; border-radius:10px; border:none; cursor:pointer; text-decoration:none; transition:all 0.2s; }
    .btn-cancel:hover { background:#e5e7eb; }
    /* Panitia multi-select */
    .panitia-chips-container { display:flex; flex-wrap:wrap; gap:8px; min-height:44px; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:10px; background:#fff; cursor:text; transition:all 0.2s; align-items:center; }
    .panitia-chips-container:focus-within { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,0.1); }
    .panitia-chip { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; background:#eef2ff; color:#4338ca; border-radius:20px; font-size:12px; font-weight:600; border:1px solid #c7d2fe; }
    .panitia-chip-remove { width:16px; height:16px; border-radius:50%; background:#c7d2fe; color:#4338ca; border:none; font-size:11px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
    .panitia-chip-remove:hover { background:#dc2626; color:#fff; }
    .panitia-search-input { border:none; outline:none; font-size:13px; font-weight:500; color:#1f2937; flex:1; min-width:120px; background:transparent; }
    .panitia-dropdown { position:absolute; top:100%; left:0; right:0; background:#fff; border:1.5px solid #e5e7eb; border-top:none; border-radius:0 0 10px 10px; max-height:220px; overflow-y:auto; z-index:200; display:none; box-shadow:0 8px 25px rgba(0,0,0,0.1); }
    .panitia-dropdown.show { display:block; }
    .panitia-option { padding:10px 14px; cursor:pointer; font-size:13px; font-weight:500; color:#374151; transition:background 0.15s; border-bottom:1px solid #f9fafb; }
    .panitia-option:hover { background:#eef2ff; color:#4f46e5; }
    .panitia-option.selected { background:#f0fdf4; color:#16a34a; pointer-events:none; opacity:0.6; }
    .panitia-option .sub-text { font-size:11px; color:#9ca3af; font-weight:400; }
    .banner-upload-area { border:2px dashed #d1d5db; border-radius:12px; padding:30px; text-align:center; cursor:pointer; transition:all 0.2s; background:#fafafa; }
    .banner-upload-area:hover { border-color:#818cf8; background:#f5f3ff; }
    .banner-preview { width:100%; max-height:220px; object-fit:cover; border-radius:10px; margin-top:12px; display:none; }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-back">&larr;</a>
    <div>
        <h3 class="fw-bold mb-0 text-dark">Buat Rencana Proker</h3>
        <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">Isi formulir perencanaan program kerja — akan disimpan sebagai draft</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger" style="border-radius:10px;border:none;background:#fee2e2;color:#991b1b;font-size:14px;">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('manajemenmahasiswa.proker.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Info Utama --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
            Informasi Proker
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Judul Program Kerja <span class="required">*</span></label>
            <input type="text" name="judul" class="form-control form-control-custom"
                   placeholder="Contoh: Seminar Nasional IT 2026" value="{{ old('judul') }}" required maxlength="255">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-custom">Kategori <span class="required">*</span></label>
                <div class="checkbox-card-group" id="kategoriGroup">
                    @foreach($kategoriList as $kategori)
                        <label class="checkbox-card {{ is_array(old('kategori_kegiatan_id')) && in_array($kategori->id, old('kategori_kegiatan_id')) ? 'checked' : '' }}" id="kategoriCard{{ $kategori->id }}">
                            <input type="checkbox" name="kategori_kegiatan_id[]" value="{{ $kategori->id }}"
                                   data-is-prodi="{{ stripos($kategori->nama_kategori, 'prodi') !== false ? '1' : '0' }}"
                                   onchange="handleKategoriChange()"
                                   {{ is_array(old('kategori_kegiatan_id')) && in_array($kategori->id, old('kategori_kegiatan_id')) ? 'checked' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </label>
                    @endforeach
                </div>
                <div class="checkbox-hint">Pilih maksimal 2 kategori</div>
            </div>
            <div class="col-md-4" id="bidangFieldWrapper">
                <label class="form-label-custom">Bidang</label>
                <div class="checkbox-card-group" id="bidangGroup">
                    @foreach($bidangList as $bidang)
                        <label class="checkbox-card {{ is_array(old('bidang_id')) && in_array($bidang->id, old('bidang_id')) ? 'checked' : '' }}" id="bidangCard{{ $bidang->id }}">
                            <input type="checkbox" name="bidang_id[]" value="{{ $bidang->id }}"
                                   {{ is_array(old('bidang_id')) && in_array($bidang->id, old('bidang_id')) ? 'checked' : '' }}>
                            {{ $bidang->nama_bidang }}
                        </label>
                    @endforeach
                </div>
                <div class="checkbox-hint">Pilih satu atau lebih bidang (kosongkan jika Prodi)</div>
            </div>
            <div class="col-md-4">
                <label class="form-label-custom">Tahun</label>
                <select name="tahun" class="form-select form-select-custom">
                    <option value="">— Pilih Tahun —</option>
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ old('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom">Deskripsi &amp; Latar Belakang <span class="required">*</span></label>
            <textarea name="deskripsi" class="form-control form-control-custom"
                      placeholder="Jelaskan tujuan, sasaran, dan latar belakang proker..." required>{{ old('deskripsi') }}</textarea>
        </div>
    </div>

    {{-- Waktu & Lokasi --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            Rencana Waktu &amp; Lokasi
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label-custom">Rencana Tanggal Mulai <span class="required">*</span></label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-custom" value="{{ old('tanggal_mulai') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control form-control-custom" value="{{ old('jam_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Rencana Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control form-control-custom" value="{{ old('tanggal_selesai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control form-control-custom" value="{{ old('jam_selesai') }}">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-custom">Rencana Lokasi</label>
                <input type="text" name="lokasi" class="form-control form-control-custom"
                       placeholder="Contoh: Gedung A Lt. 3, Undip Tembalang" value="{{ old('lokasi') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Target Peserta</label>
                <input type="number" name="target_peserta" class="form-control form-control-custom"
                       placeholder="Jml. orang" value="{{ old('target_peserta') }}" min="1">
            </div>
            <div class="col-md-3">
                <label class="form-label-custom">Estimasi Anggaran (Rp)</label>
                <input type="number" name="anggaran" class="form-control form-control-custom"
                       placeholder="0" value="{{ old('anggaran') }}" min="0">
            </div>
        </div>
    </div>

    {{-- Personel --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Personel Kegiatan
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-custom">Rencana Ketua Pelaksana</label>
                <div class="search-select-wrapper">
                    <input type="hidden" name="ketua_pelaksana_id" id="ketuaPelaksanaId" value="{{ old('ketua_pelaksana_id') }}">
                    <input type="text" class="form-control form-control-custom" id="ketuaPelaksanaSearch"
                           placeholder="Cari nama mahasiswa..." autocomplete="off"
                           onfocus="showDropdown('ketuaPelaksanaDropdown')"
                           oninput="filterOptions('ketuaPelaksanaSearch','ketuaPelaksanaDropdown')">
                    <div class="search-select-dropdown" id="ketuaPelaksanaDropdown">
                        @foreach($mahasiswaList as $mhs)
                            <div class="search-select-option"
                                 onclick="selectOption('ketuaPelaksanaId','{{ $mhs->id }}','ketuaPelaksanaSearch','{{ $mhs->user->name ?? 'N/A' }}','ketuaPelaksanaDropdown')"
                                 data-name="{{ strtolower($mhs->user->name ?? '') }}">
                                {{ $mhs->user->name ?? 'N/A' }}
                                <div class="sub-text">NIM: {{ $mhs->student_number }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom">Dosen Pendamping</label>
                <div class="search-select-wrapper">
                    <input type="hidden" name="dosen_pendamping_id" id="dosenPendampingId" value="{{ old('dosen_pendamping_id') }}">
                    <input type="text" class="form-control form-control-custom" id="dosenPendampingSearch"
                           placeholder="Cari nama dosen..." autocomplete="off"
                           onfocus="showDropdown('dosenPendampingDropdown')"
                           oninput="filterOptions('dosenPendampingSearch','dosenPendampingDropdown')">
                    <div class="search-select-dropdown" id="dosenPendampingDropdown">
                        @foreach($dosenList as $dosen)
                            <div class="search-select-option"
                                 onclick="selectOption('dosenPendampingId','{{ $dosen->id }}','dosenPendampingSearch','{{ $dosen->user->name ?? 'N/A' }}','dosenPendampingDropdown')"
                                 data-name="{{ strtolower($dosen->user->name ?? '') }}">
                                {{ $dosen->user->name ?? 'N/A' }}
                                <div class="sub-text">NIP: {{ $dosen->employee_number }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Panitia --}}
        <div>
            <label class="form-label-custom">Rencana Panitia <span style="color:#9ca3af;font-weight:400;">(opsional)</span></label>
            <div style="position:relative;" id="panitiaSelectWrapper">
                <div class="panitia-chips-container" id="panitiaChipsContainer" onclick="focusPanitiaSearch()">
                    <input type="text" class="panitia-search-input" id="panitiaSearchInput"
                           placeholder="Cari dan tambah panitia..." autocomplete="off"
                           oninput="filterPanitiaOptions(this.value)" onfocus="showPanitiaDropdown()">
                </div>
                <div class="panitia-dropdown" id="panitiaDropdown">
                    @foreach($mahasiswaList as $mhs)
                        <div class="panitia-option"
                             data-id="{{ $mhs->id }}"
                             data-name="{{ $mhs->user->name ?? 'N/A' }}"
                             data-name-lower="{{ strtolower($mhs->user->name ?? '') }}"
                             data-nim="{{ $mhs->student_number }}"
                             onclick="addPanitia(this)">
                            {{ $mhs->user->name ?? 'N/A' }}
                            <div class="sub-text">NIM: {{ $mhs->student_number }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="hiddenPanitiaInputs"></div>
        </div>
    </div>

    {{-- Surat Proker PDF --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            Surat Proker (PDF)
        </div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
            Upload file PDF surat proker yang akan ditandatangani secara digital oleh Ketua Himpunan, Bendahara, DPM, dan Ketua Departemen. PDF <strong>wajib</strong> diupload sebelum mengajukan proker.
        </p>
        <div class="banner-upload-area" id="suratUploadArea" onclick="document.getElementById('suratProkerInput').click()" style="border-color:#e5e7eb;">
            <div style="font-size:36px;margin-bottom:8px;opacity:0.5;">📄</div>
            <p id="suratUploadText">Klik untuk pilih file PDF surat proker</p>
            <small>Format PDF • Maks. 20MB</small>
        </div>
        <input type="file" name="surat_proker" id="suratProkerInput" accept=".pdf" style="display:none;"
               onchange="previewSurat(this)">
        <div id="suratPreviewBox" style="display:none;margin-top:12px;padding:14px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:24px;">✅</span>
                <div>
                    <div style="font-weight:600;font-size:14px;color:#166534;" id="suratFileName">file.pdf</div>
                    <div style="font-size:12px;color:#6b7280;" id="suratFileSize"></div>
                </div>
                <button type="button" onclick="clearSurat()" style="margin-left:auto;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:4px 10px;font-size:12px;cursor:pointer;">✕ Hapus</button>
            </div>
        </div>
    </div>

    {{-- Banner --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            Banner Proker
        </div>
        <div class="banner-upload-area" onclick="document.getElementById('bannerInput').click()">
            <div style="font-size:36px;margin-bottom:8px;opacity:0.5;">&#128247;</div>
            <p>Klik untuk pilih gambar banner</p>
            <small>JPG, PNG, WEBP • Maks. 10MB</small>
        </div>
        <input type="file" name="banner" id="bannerInput" accept="image/*" style="display:none;" onchange="previewBanner(this)">
        <img id="bannerPreview" class="banner-preview" alt="Preview Banner">
    </div>

    {{-- Submit --}}
    <div class="d-flex gap-3 justify-content-end">
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-cancel">Batal</a>
        <button type="submit" class="btn-submit">Simpan sebagai Draft</button>
    </div>
</form>

<script>
// ── Dropdown helpers ──────────────────────────────────────────────────────────
function showDropdown(id) { document.getElementById(id).classList.add('show'); }
function hideAllDropdowns() { document.querySelectorAll('.search-select-dropdown').forEach(d=>d.classList.remove('show')); }
function filterOptions(inputId, dropdownId) {
    const q = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#'+dropdownId+' .search-select-option').forEach(opt => {
        opt.style.display = opt.dataset.name.includes(q) ? '' : 'none';
    });
    showDropdown(dropdownId);
}
function selectOption(hiddenId, value, inputId, label, dropdownId) {
    document.getElementById(hiddenId).value = value;
    document.getElementById(inputId).value = label;
    document.getElementById(dropdownId).classList.remove('show');
}
document.addEventListener('click', e => { if (!e.target.closest('.search-select-wrapper')) hideAllDropdowns(); });

// ── Kategori checkbox ─────────────────────────────────────────────────────────
function handleKategoriChange() {
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    document.querySelectorAll('#kategoriGroup .checkbox-card').forEach(c => {
        c.classList.toggle('checked', c.querySelector('input').checked);
        if (checked.length >= 2 && !c.querySelector('input').checked) { c.style.opacity='0.4'; c.style.pointerEvents='none'; }
        else { c.style.opacity=''; c.style.pointerEvents=''; }
    });
}
document.querySelectorAll('#bidangGroup input').forEach(inp => {
    inp.addEventListener('change', () => inp.closest('.checkbox-card').classList.toggle('checked', inp.checked));
});

// ── Banner preview ────────────────────────────────────────────────────────────
function previewBanner(input) {
    const preview = document.getElementById('bannerPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Surat Proker PDF preview ──────────────────────────────────────────────────
function previewSurat(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const area = document.getElementById('suratUploadArea');
        const box  = document.getElementById('suratPreviewBox');
        const name = document.getElementById('suratFileName');
        const size = document.getElementById('suratFileSize');
        name.textContent = file.name;
        size.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        area.style.display = 'none';
        box.style.display  = 'block';
    }
}
function clearSurat() {
    document.getElementById('suratProkerInput').value = '';
    document.getElementById('suratUploadArea').style.display = 'block';
    document.getElementById('suratPreviewBox').style.display = 'none';
}

// ── Panitia multi-select ──────────────────────────────────────────────────────
let selectedPanitia = {};
function showPanitiaDropdown() { document.getElementById('panitiaDropdown').classList.add('show'); }
function focusPanitiaSearch() { document.getElementById('panitiaSearchInput').focus(); showPanitiaDropdown(); }
function filterPanitiaOptions(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#panitiaDropdown .panitia-option').forEach(opt => {
        opt.style.display = opt.dataset.nameLower.includes(q) ? '' : 'none';
    });
    showPanitiaDropdown();
}
function addPanitia(el) {
    const id = el.dataset.id, name = el.dataset.name;
    if (selectedPanitia[id]) return;
    selectedPanitia[id] = name;
    el.classList.add('selected');
    document.getElementById('panitiaSearchInput').value = '';
    renderPanitiaChips();
}
function removePanitia(id) {
    delete selectedPanitia[id];
    const opt = document.querySelector(`#panitiaDropdown .panitia-option[data-id="${id}"]`);
    if (opt) opt.classList.remove('selected');
    renderPanitiaChips();
}
function renderPanitiaChips() {
    const container = document.getElementById('panitiaChipsContainer');
    const searchInput = document.getElementById('panitiaSearchInput');
    container.innerHTML = '';
    Object.entries(selectedPanitia).forEach(([id, name]) => {
        const chip = document.createElement('span');
        chip.className = 'panitia-chip';
        chip.innerHTML = `${name} <button type="button" class="panitia-chip-remove" onclick="removePanitia('${id}')">&times;</button>`;
        container.appendChild(chip);
    });
    container.appendChild(searchInput);
    renderHiddenPanitiaInputs();
}
function renderHiddenPanitiaInputs() {
    const container = document.getElementById('hiddenPanitiaInputs');
    container.innerHTML = '';
    Object.keys(selectedPanitia).forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'panitia_ids[]'; inp.value = id;
        container.appendChild(inp);
    });
}
document.addEventListener('click', e => {
    if (!e.target.closest('#panitiaSelectWrapper')) document.getElementById('panitiaDropdown').classList.remove('show');
});
</script>

</x-manajemenmahasiswa::layouts.mahasiswa>
