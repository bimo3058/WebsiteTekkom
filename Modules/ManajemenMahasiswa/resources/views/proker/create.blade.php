<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    .form-card { background:#fff; border-radius:14px; padding:28px; box-shadow:0 4px 16px -2px rgba(0,0,0,0.07); margin-bottom:24px; }
    .form-card-title { font-weight:700; font-size:15px; color:#1f2937; margin-bottom:20px; display:flex; align-items:center; gap:9px; padding-bottom:14px; border-bottom:1px solid #f3f4f6; }
    .form-label-custom { font-weight:600; font-size:13px; color:#374151; margin-bottom:7px; display:block; }
    .form-label-custom .required { color:#dc2626; }
    .form-control-custom { border:1.5px solid #e5e7eb; border-radius:10px; padding:11px 14px; font-size:14px; font-weight:500; color:#1f2937; transition:all 0.2s; background:#fff; width:100%; }
    .form-control-custom:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,0.1); outline:none; }
    textarea.form-control-custom { min-height:150px; resize:vertical; line-height:1.7; }
    .btn-back { width:40px; height:40px; border-radius:50%; background:#fff; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; text-decoration:none; color:#374151; font-size:18px; box-shadow:0 1px 3px rgba(0,0,0,0.06); transition:all 0.2s; flex-shrink:0; }
    .btn-back:hover { background:#f3f4f6; }
    /* Draft button */
    .btn-draft { background:#f3f4f6; color:#374151; font-weight:600; font-size:14px; padding:11px 26px; border-radius:10px; border:1.5px solid #d1d5db; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; }
    .btn-draft:hover { background:#e5e7eb; border-color:#9ca3af; }
    .btn-submit { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; font-weight:600; font-size:14px; padding:11px 28px; border-radius:10px; border:none; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; box-shadow:0 4px 12px rgba(79,70,229,0.3); }
    .btn-submit:hover { background:linear-gradient(135deg,#4338ca,#6d28d9); transform:translateY(-1px); box-shadow:0 6px 16px rgba(79,70,229,0.35); }
    .btn-cancel { background:#fff; color:#6b7280; font-weight:600; font-size:14px; padding:11px 22px; border-radius:10px; border:1.5px solid #e5e7eb; cursor:pointer; text-decoration:none; transition:all 0.2s; display:inline-flex; align-items:center; }
    .btn-cancel:hover { background:#f9fafb; color:#374151; }
    /* Banner upload */
    .banner-upload-area { border:2px dashed #d1d5db; border-radius:14px; padding:36px 20px; text-align:center; cursor:pointer; transition:all 0.25s; background:#fafafa; position:relative; overflow:hidden; }
    .banner-upload-area:hover, .banner-upload-area.drag-over { border-color:#818cf8; background:#f5f3ff; }
    .banner-upload-area input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .banner-upload-icon { width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,#eef2ff,#e0e7ff); display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
    .banner-upload-title { font-weight:600; font-size:14px; color:#374151; margin-bottom:4px; }
    .banner-upload-hint { font-size:12px; color:#9ca3af; font-weight:400; }
    .banner-preview-box { display:none; border-radius:14px; overflow:hidden; position:relative; }
    .banner-preview-img { width:100%; max-height:260px; object-fit:cover; display:block; border-radius:14px; }
    .banner-preview-overlay { position:absolute; top:10px; right:10px; display:flex; gap:8px; }
    .banner-preview-overlay button { background:rgba(0,0,0,0.55); color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer; backdrop-filter:blur(4px); transition:all 0.15s; }
    .banner-preview-overlay button:hover { background:rgba(0,0,0,0.75); }
    /* Char counter */
    .char-counter { font-size:11px; color:#9ca3af; text-align:right; margin-top:4px; font-weight:500; }
    /* Action bar */
    .action-bar { background:#fff; border-radius:14px; padding:18px 24px; box-shadow:0 4px 16px -2px rgba(0,0,0,0.07); display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    .action-bar-left { display:flex; align-items:center; gap:10px; }
    .action-bar-right { display:flex; align-items:center; gap:10px; }
    .draft-badge { background:#fef3c7; color:#92400e; border:1.5px solid #fde68a; border-radius:20px; padding:4px 14px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:5px; }
    /* ── Lightbox Modal ── */
    .lightbox-modal { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(0, 0, 0, 0.92); align-items: center; justify-content: center; animation: lightboxFadeIn 0.25s ease; }
    .lightbox-modal.active { display: flex; }
    @keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
    .lightbox-content { position: relative; max-width: 90vw; max-height: 85vh; display: flex; align-items: center; justify-content: center; }
    .lightbox-content img { max-width: 90vw; max-height: 82vh; object-fit: contain; border-radius: 8px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4); animation: lightboxZoomIn 0.3s ease; }
    @keyframes lightboxZoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .lightbox-close { position: fixed; top: 20px; right: 24px; width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10001; }
    .lightbox-close:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
    .lightbox-info { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); text-align: center; z-index: 10001; }
    .lightbox-info .lightbox-title { color: #fff; font-size: 14px; font-weight: 600; margin-bottom: 4px; }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-back">&larr;</a>
    <div>
        <h3 class="fw-bold mb-0 text-dark">Buat Rencana Proker</h3>
        <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">Isi informasi umum program kerja — simpan sebagai draft atau langsung ajukan</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger" style="border-radius:12px;border:none;background:#fee2e2;color:#991b1b;font-size:14px;padding:14px 18px;">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('manajemenmahasiswa.proker.store') }}" method="POST" enctype="multipart/form-data" id="prokerForm">
    @csrf
    <input type="hidden" name="save_as_draft" id="saveAsDraft" value="1">
    {{-- ── Informasi Umum Proker ── --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
            Informasi Umum Proker
        </div>

        {{-- Judul --}}
        <div class="mb-4">
            <label class="form-label-custom">Judul Program Kerja <span class="required">*</span></label>
            <input type="text" name="judul" id="judulInput" class="form-control form-control-custom"
                   placeholder="Contoh: Seminar Nasional Teknologi Informasi 2026"
                   value="{{ old('judul') }}" required maxlength="255"
                   oninput="updateCharCount('judulInput','judulCount',255)">
            <div class="char-counter"><span id="judulCount">0</span>/255 karakter</div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-2">
            <label class="form-label-custom">Deskripsi Proker <span class="required">*</span></label>
            <textarea name="deskripsi" id="deskripsiInput" class="form-control form-control-custom"
                      placeholder="Jelaskan tujuan, sasaran, latar belakang, dan manfaat dari program kerja ini..."
                      required oninput="updateCharCount('deskripsiInput','deskripsiCount',3000)">{{ old('deskripsi') }}</textarea>
            <div class="char-counter"><span id="deskripsiCount">0</span>/3000 karakter</div>
        </div>
    </div>

    {{-- ── Kategori & Klasifikasi ── --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Kategori & Klasifikasi
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-custom">Kategori <span class="required">*</span></label>
                <div style="display:flex;flex-wrap:wrap;gap:9px;" id="kategoriGroup">
                    @foreach($kategoriList as $kategori)
                        <label style="display:inline-flex;align-items:center;gap:7px;padding:9px 14px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;cursor:pointer;transition:all 0.2s;font-size:13px;font-weight:500;color:#374151;user-select:none;"
                               id="kategoriCard{{ $kategori->id }}">
                            <input type="checkbox" name="kategori_kegiatan_id[]" value="{{ $kategori->id }}"
                                   style="width:15px;height:15px;accent-color:#4f46e5;"
                                   onchange="handleKategoriChange()"
                                   {{ is_array(old('kategori_kegiatan_id')) && in_array($kategori->id, old('kategori_kegiatan_id')) ? 'checked' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </label>
                    @endforeach
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-top:6px;">Pilih maksimal 2 kategori</div>
            </div>
            <div class="col-md-6" id="bidangFieldWrapper">
                <label class="form-label-custom">Bidang <span class="required">*</span></label>
                <div style="display:flex;flex-wrap:wrap;gap:9px;" id="bidangGroup">
                    @foreach($bidangList as $bidang)
                        <label style="display:inline-flex;align-items:center;gap:7px;padding:9px 14px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;cursor:pointer;transition:all 0.2s;font-size:13px;font-weight:500;color:#374151;user-select:none;"
                               id="bidangCard{{ $bidang->id }}">
                            <input type="checkbox" name="bidang_id[]" value="{{ $bidang->id }}"
                                   style="width:15px;height:15px;accent-color:#4f46e5;"
                                   {{ is_array(old('bidang_id')) && in_array($bidang->id, old('bidang_id')) ? 'checked' : '' }}>
                            {{ $bidang->nama_bidang }}
                        </label>
                    @endforeach
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-top:6px;">Pilih satu atau lebih bidang</div>
            </div>
        </div>
    </div>

    {{-- ── Upload Banner ── --}}
    <div class="form-card">
        <div class="form-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Banner Proker
            <span style="font-size:12px;font-weight:400;color:#9ca3af;margin-left:4px;">(opsional)</span>
        </div>

        {{-- Preview Box (shown after upload) --}}
        <div class="banner-preview-box" id="bannerPreviewBox" style="cursor:pointer;" onclick="openLightbox()">
            <img id="bannerPreviewImg" src="" alt="Preview banner" class="banner-preview-img">
            <div class="banner-preview-overlay">
                <button type="button" onclick="event.stopPropagation(); changeBanner()">&#128247; Ganti</button>
                <button type="button" onclick="event.stopPropagation(); clearBanner()" style="background:rgba(220,38,38,0.75);">&#10005; Hapus</button>
            </div>
        </div>

        {{-- Upload Area (hidden after upload) --}}
        <div class="banner-upload-area" id="bannerUploadArea"
             ondragover="event.preventDefault();this.classList.add('drag-over')"
             ondragleave="this.classList.remove('drag-over')"
             ondrop="handleBannerDrop(event)">
            <input type="file" name="banner" id="bannerInput" accept="image/*" onchange="previewBanner(this)">
            <div class="banner-upload-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            <div class="banner-upload-title">Klik atau drag & drop gambar banner</div>
            <div class="banner-upload-hint">PNG, JPG, WEBP — Maks. 2 MB · Rasio ideal 16:9 (1280×720px)</div>
        </div>
    </div>



    {{-- ── Bottom Action Bar ── --}}
    <div class="d-flex gap-3 justify-content-end mb-4">
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-cancel">Batal</a>
        <button type="submit" class="btn-submit">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan
        </button>
    </div>
    {{-- Lightbox Modal --}}
    <div class="lightbox-modal" id="lightboxModal">
        <button type="button" class="lightbox-close" onclick="closeLightbox()" title="Tutup">&#10005;</button>
        <div class="lightbox-content">
            <img src="" id="lightboxImg" alt="Preview">
        </div>
        <div class="lightbox-info">
            <div class="lightbox-title">Banner Preview</div>
        </div>
    </div>
</form>

<script>
// ── Lightbox ──────────────────────────────────────────────────────────────────
function openLightbox() {
    const src = document.getElementById('bannerPreviewImg').src;
    if(src && src !== window.location.href) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightboxModal').classList.add('active');
    }
}
function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('active');
}
document.getElementById('lightboxModal')?.addEventListener('click', e => {
    if (e.target.id === 'lightboxModal' || e.target.classList.contains('lightbox-content')) {
        closeLightbox();
    }
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});

// ── Char counter ──────────────────────────────────────────────────────────────
function updateCharCount(inputId, countId, max) {
    const len = document.getElementById(inputId).value.length;
    const el  = document.getElementById(countId);
    el.textContent = len;
    el.style.color = len > max * 0.9 ? '#f59e0b' : (len >= max ? '#dc2626' : '#9ca3af');
}
// Init counters on load
document.addEventListener('DOMContentLoaded', () => {
    ['judulInput','deskripsiInput'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.dispatchEvent(new Event('input'));
    });
});

// ── Banner upload ─────────────────────────────────────────────────────────────
function previewBanner(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('bannerPreviewImg').src = e.target.result;
        document.getElementById('bannerPreviewBox').style.display = 'block';
        document.getElementById('bannerUploadArea').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
function handleBannerDrop(event) {
    event.preventDefault();
    document.getElementById('bannerUploadArea').classList.remove('drag-over');
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        // Assign file to input and trigger preview
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('bannerInput').files = dt.files;
        previewBanner(document.getElementById('bannerInput'));
    }
}
function changeBanner() {
    document.getElementById('bannerInput').click();
}
function clearBanner() {
    document.getElementById('bannerInput').value = '';
    document.getElementById('bannerPreviewBox').style.display = 'none';
    document.getElementById('bannerUploadArea').style.display = 'block';
}

// ── Kategori checkbox ─────────────────────────────────────────────────────────
function handleKategoriChange() {
    const checked = document.querySelectorAll('#kategoriGroup input[type="checkbox"]:checked');
    let isOnlyProdi = false;
    let prodiChecked = false;
    let otherChecked = false;

    document.querySelectorAll('#kategoriGroup label').forEach(card => {
        const inp = card.querySelector('input');
        const isChecked = inp.checked;
        const text = card.textContent.trim().toLowerCase();
        
        if (text.includes('prodi')) {
            if (isChecked) prodiChecked = true;
        } else {
            if (isChecked) otherChecked = true;
        }

        card.style.borderColor    = isChecked ? '#4f46e5' : '#e5e7eb';
        card.style.background     = isChecked ? '#eef2ff' : '#fff';
        card.style.color          = isChecked ? '#4338ca' : '#374151';
        card.style.fontWeight     = isChecked ? '600' : '500';
        if (checked.length >= 2 && !isChecked) { card.style.opacity='0.4'; card.style.pointerEvents='none'; }
        else { card.style.opacity=''; card.style.pointerEvents=''; }
    });

    isOnlyProdi = prodiChecked && !otherChecked;
    const bidangWrapper = document.getElementById('bidangFieldWrapper');
    if (bidangWrapper) {
        if (isOnlyProdi) {
            bidangWrapper.style.display = 'none';
            document.querySelectorAll('#bidangGroup input[type="checkbox"]').forEach(inp => {
                if (inp.checked) {
                    inp.checked = false;
                    inp.dispatchEvent(new Event('change'));
                }
            });
        } else {
            bidangWrapper.style.display = 'block';
        }
    }
}
document.querySelectorAll('#bidangGroup input').forEach(inp => {
    inp.addEventListener('change', () => {
        const card = inp.closest('label');
        card.style.borderColor = inp.checked ? '#4f46e5' : '#e5e7eb';
        card.style.background  = inp.checked ? '#eef2ff' : '#fff';
        card.style.color       = inp.checked ? '#4338ca' : '#374151';
        card.style.fontWeight  = inp.checked ? '600' : '500';
    });
});
// Restore old values visual state
document.addEventListener('DOMContentLoaded', () => {
    handleKategoriChange();
    document.querySelectorAll('#bidangGroup input:checked').forEach(inp => inp.dispatchEvent(new Event('change')));
});
</script>

</x-manajemenmahasiswa::layouts.mahasiswa>
