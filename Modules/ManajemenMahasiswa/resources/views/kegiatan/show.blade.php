<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    /* ── Back Button & Header ── */
    .detail-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }
    .btn-back {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #374151;
        font-size: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-back:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        color: #1f2937;
    }

    /* ── Banner ── */
    .detail-banner {
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 50%, #a5b4fc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .detail-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .detail-banner .placeholder-icon {
        font-size: 64px;
        opacity: 0.4;
    }

    /* ── Info Card (matching forum-card style) ── */
    .detail-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .detail-card-title {
        font-weight: 700;
        font-size: 16px;
        color: #1f2937;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── Badges ── */
    .badge-bidang {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background: #eef2ff;
        color: #4f46e5;
    }
    .badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .badge-status.akan_datang { background: #fef3c7; color: #d97706; }
    .badge-status.berlangsung { background: #dbeafe; color: #2563eb; }
    .badge-status.selesai { background: #dcfce7; color: #16a34a; }
    .badge-kategori {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background: #fef3c7;
        color: #92400e;
    }

    /* ── Metadata Grid ── */
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        margin-top: 18px;
    }
    .meta-item {
        background: #f9fafb;
        border-radius: 10px;
        padding: 14px 16px;
        border: 1px solid #f3f4f6;
    }
    .meta-item-label {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }
    .meta-item-value {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ── Description ── */
    .detail-description {
        font-size: 14px;
        color: #374151;
        line-height: 1.75;
        white-space: pre-line;
    }

    /* ── Photo Gallery ── */
    .gallery-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .gallery-count {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        background: #f3f4f6;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
        margin-top: 16px;
    }
    .photo-gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        aspect-ratio: 4/3;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .photo-gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(79, 70, 229, 0.15);
        border-color: #a5b4fc;
    }
    .photo-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .photo-gallery-item:hover img {
        transform: scale(1.05);
    }
    .photo-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 14px;
    }
    .photo-gallery-item:hover .photo-overlay {
        opacity: 1;
    }
    .photo-overlay .photo-name {
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .photo-overlay .photo-zoom {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        transition: all 0.2s;
    }
    .photo-overlay .photo-zoom:hover {
        background: rgba(255,255,255,0.35);
        transform: scale(1.1);
    }

    /* ── Lightbox Modal ── */
    .lightbox-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.92);
        align-items: center;
        justify-content: center;
        animation: lightboxFadeIn 0.25s ease;
    }
    .lightbox-modal.active {
        display: flex;
    }
    @keyframes lightboxFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lightbox-content img {
        max-width: 90vw;
        max-height: 82vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        animation: lightboxZoomIn 0.3s ease;
    }
    @keyframes lightboxZoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .lightbox-close {
        position: fixed;
        top: 20px;
        right: 24px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10001;
    }
    .lightbox-close:hover {
        background: rgba(255,255,255,0.2);
        transform: scale(1.05);
    }
    .lightbox-nav {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        font-size: 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10001;
    }
    .lightbox-nav:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-50%) scale(1.08);
    }
    .lightbox-nav.prev { left: 20px; }
    .lightbox-nav.next { right: 20px; }
    .lightbox-info {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        z-index: 10001;
    }
    .lightbox-info .lightbox-title {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .lightbox-info .lightbox-counter {
        color: rgba(255,255,255,0.6);
        font-size: 12px;
        font-weight: 500;
    }

    /* ── Document Cards ── */
    .document-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 16px;
    }
    .document-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        text-decoration: none !important;
        transition: all 0.25s ease;
    }
    .document-card:hover {
        background: #eef2ff;
        border-color: #c7d2fe;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08);
    }
    .document-card .doc-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .doc-icon-pdf { background: #fee2e2; }
    .doc-icon-word { background: #dbeafe; }
    .doc-icon-excel { background: #dcfce7; }
    .doc-icon-ppt { background: #fef3c7; }
    .doc-icon-other { background: #f3f4f6; }
    .document-card .doc-details {
        flex: 1;
        min-width: 0;
    }
    .document-card .doc-title {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 2px;
    }
    .document-card .doc-meta {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .document-card .doc-ext-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .ext-pdf { background: #fee2e2; color: #dc2626; }
    .ext-doc, .ext-docx { background: #dbeafe; color: #2563eb; }
    .ext-xls, .ext-xlsx { background: #dcfce7; color: #16a34a; }
    .ext-ppt, .ext-pptx { background: #fef3c7; color: #d97706; }
    .ext-default { background: #f3f4f6; color: #6b7280; }
    .document-card .doc-download-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .document-card:hover .doc-download-btn {
        background: #4f46e5;
        color: #fff;
        border-color: #4f46e5;
    }

    /* ── Video Section ── */
    .video-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }
    .video-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: #000;
        transition: all 0.25s ease;
    }
    .video-card:hover {
        border-color: #a5b4fc;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.12);
    }
    .video-card video {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .video-card .video-info {
        background: #fff;
        padding: 12px 14px;
    }
    .video-card .video-name {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .video-card .video-type {
        font-size: 11px;
        color: #9ca3af;
        font-weight: 500;
    }

    /* ── Empty State ── */
    .empty-luaran {
        text-align: center;
        padding: 40px 20px;
    }
    .empty-luaran-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 32px;
    }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Header with back button -->
<div class="d-flex justify-content-between align-items-start">
    <div class="detail-header">
        <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}" class="btn-back">
            &larr;
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">Detail Kegiatan</h3>
            <p class="text-muted mb-0" style="font-size: 14px; font-weight: 500;">Informasi lengkap tentang kegiatan ini</p>
        </div>
    </div>
    @if($isAdmin)
        <div class="d-flex gap-2">
            <a href="{{ route('manajemenmahasiswa.kegiatan.edit', $kegiatan->id) }}"
               class="btn d-flex align-items-center gap-2"
               style="background: #4f46e5; color: #fff; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
            <button type="button" class="btn d-flex align-items-center gap-2"
                    style="background: #fee2e2; color: #dc2626; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px; border: none;"
                    onclick="document.getElementById('deleteModal').style.display='flex'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Hapus
            </button>
        </div>
    @endif
</div>

<!-- Banner -->
<div class="detail-banner">
    @if($kegiatan->banner)
        <img src="{{ $kegiatan->banner_url }}" alt="{{ $kegiatan->judul }}">
    @else
        <span class="placeholder-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
    @endif
</div>

<!-- Title & Badges -->
<div class="detail-card">
    <!-- Badges -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if($kegiatan->bidangs && $kegiatan->bidangs->count() > 0)
            @foreach($kegiatan->bidangs as $b)
                <span class="badge-bidang">{{ $b->nama_bidang }}</span>
            @endforeach
        @elseif($kegiatan->bidang)
            <span class="badge-bidang">{{ $kegiatan->bidang->nama_bidang }}</span>
        @else
            <span class="badge-bidang" style="background: #f3e8ff; color: #7c3aed;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg> Prodi</span>
        @endif
        @if($kegiatan->kategoris && $kegiatan->kategoris->count() > 0)
            @foreach($kegiatan->kategoris as $kat)
                <span class="badge-kategori">{{ $kat->nama_kategori }}</span>
            @endforeach
        @elseif($kegiatan->kategoriKegiatan)
            <span class="badge-kategori">{{ $kegiatan->kategoriKegiatan->nama_kategori }}</span>
        @endif
        @if($kegiatan->status)
            <span class="badge-status {{ $kegiatan->status }}">{{ $kegiatan->status_label }}</span>
        @endif
    </div>

    <!-- Title -->
    <h4 class="fw-bold text-dark mb-3">{{ $kegiatan->judul }}</h4>

    <!-- Meta Grid -->
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-item-label">Tanggal Pelaksanaan</div>
            <div class="meta-item-value">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg> {{ $kegiatan->tanggal_mulai->translatedFormat('d F Y') }}
                @if($kegiatan->jam_mulai)
                    pukul {{ $kegiatan->jam_mulai_formatted }}
                @endif
                @if($kegiatan->tanggal_selesai)
                    — {{ $kegiatan->tanggal_selesai->translatedFormat('d F Y') }}
                    @if($kegiatan->jam_selesai)
                        pukul {{ $kegiatan->jam_selesai_formatted }}
                    @endif
                @endif
            </div>
        </div>

        @if($kegiatan->lokasi)
        <div class="meta-item">
            <div class="meta-item-label">Lokasi</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> {{ $kegiatan->lokasi }}</div>
        </div>
        @endif

        @if($kegiatan->ketuaPelaksana)
        <div class="meta-item">
            <div class="meta-item-label">Ketua Pelaksana</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> {{ $kegiatan->ketuaPelaksana->user->name ?? '-' }}
                <span style="font-size: 11px; color: #9ca3af; font-weight: 400;">({{ $kegiatan->ketuaPelaksana->student_number }})</span>
            </div>
        </div>
        @elseif($kegiatan->penanggung_jawab)
        <div class="meta-item">
            <div class="meta-item-label">Ketua Pelaksana</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> {{ $kegiatan->penanggung_jawab }}</div>
        </div>
        @endif

        @if($kegiatan->dosenPendamping)
        <div class="meta-item">
            <div class="meta-item-label">Dosen Pendamping</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg> {{ $kegiatan->dosenPendamping->user->name ?? '-' }}
                <span style="font-size: 11px; color: #9ca3af; font-weight: 400;">({{ $kegiatan->dosenPendamping->employee_number }})</span>
            </div>
        </div>
        @endif

        @if($kegiatan->tahun)
        <div class="meta-item">
            <div class="meta-item-label">Tahun</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> {{ $kegiatan->tahun }}</div>
        </div>
        @endif

        @if($kegiatan->target_peserta)
        <div class="meta-item">
            <div class="meta-item-label">Target Peserta</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> {{ $kegiatan->target_peserta }} orang</div>
        </div>
        @endif

        @if($kegiatan->anggaran)
        <div class="meta-item">
            <div class="meta-item-label">Anggaran</div>
            <div class="meta-item-value"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Rp {{ number_format($kegiatan->anggaran, 0, ',', '.') }}</div>
        </div>
        @endif
    </div>

    {{-- Panitia Kegiatan --}}
    @if($kegiatan->panitia && $kegiatan->panitia->count() > 0)
    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
        <div class="meta-item-label" style="margin-bottom: 10px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            PANITIA KEGIATAN
            <span style="font-size: 10px; font-weight: 600; background: #eef2ff; color: #4f46e5; padding: 1px 7px; border-radius: 20px; margin-left: 4px;">{{ $kegiatan->panitia->count() }} orang</span>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($kegiatan->panitia as $p)
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; background: #eef2ff; color: #4338ca; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #c7d2fe;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    {{ $p->user->name ?? '-' }}
                    <span style="font-size: 10px; color: #818cf8; font-weight: 400;">({{ $p->student_number }})</span>
                </span>
            @endforeach
        </div>
    </div>
    @endif
</div>
</div>

<!-- Deskripsi -->
<div class="detail-card">
    <div class="detail-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Deskripsi & Tujuan Kegiatan</div>
    <div class="detail-description">{{ $kegiatan->deskripsi }}</div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- Foto, Video & Dokumen Kegiatan — Enhanced Gallery                    -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
@php
    $images    = $kegiatan->repoMulmed ? $kegiatan->repoMulmed->where('tipe_file', 'image') : collect();
    $videos    = $kegiatan->repoMulmed ? $kegiatan->repoMulmed->where('tipe_file', 'video') : collect();
    $documents = $kegiatan->repoMulmed ? $kegiatan->repoMulmed->where('tipe_file', 'document') : collect();
    $totalFiles = $images->count() + $videos->count() + $documents->count();
@endphp

@if($totalFiles > 0)

    {{-- ─── Photo Gallery with Lightbox ──────────────────────────────── --}}
    @if($images->count() > 0)
    <div class="detail-card">
        <div class="gallery-header">
            <div class="detail-card-title" style="margin-bottom: 0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg> Galeri Foto Kegiatan</div>
            <span class="gallery-count">{{ $images->count() }} foto</span>
        </div>
        <div class="photo-gallery-grid">
            @foreach($images->values() as $idx => $img)
                <div class="photo-gallery-item" onclick="openLightbox({{ $idx }})">
                    <img src="{{ $img->url }}" alt="{{ $img->judul_file }}" loading="lazy">
                    <div class="photo-overlay">
                        <span class="photo-zoom">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                <line x1="11" y1="8" x2="11" y2="14"></line>
                                <line x1="8" y1="11" x2="14" y2="11"></line>
                            </svg>
                        </span>
                        <span class="photo-name">{{ $img->judul_file ?: $img->nama_file }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Lightbox Modal --}}
    <div class="lightbox-modal" id="lightboxModal">
        <button class="lightbox-close" onclick="closeLightbox()" title="Tutup">&times;</button>
        @if($images->count() > 1)
            <button class="lightbox-nav prev" onclick="prevImage()" title="Sebelumnya">&lsaquo;</button>
            <button class="lightbox-nav next" onclick="nextImage()" title="Selanjutnya">&rsaquo;</button>
        @endif
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
        </div>
        <div class="lightbox-info">
            <div class="lightbox-title" id="lightboxTitle"></div>
            <div class="lightbox-counter" id="lightboxCounter"></div>
        </div>
    </div>
    @endif

    {{-- ─── Video Section ────────────────────────────────────────────── --}}
    @if($videos->count() > 0)
    <div class="detail-card">
        <div class="gallery-header">
            <div class="detail-card-title" style="margin-bottom: 0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> Video Kegiatan</div>
            <span class="gallery-count">{{ $videos->count() }} video</span>
        </div>
        <div class="video-gallery-grid">
            @foreach($videos as $vid)
                <div class="video-card">
                    <video controls preload="metadata">
                        <source src="{{ $vid->url }}" type="video/mp4">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                    <div class="video-info">
                        <div class="video-name">{{ $vid->judul_file ?: $vid->nama_file }}</div>
                        <div class="video-type">Video</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Document Download Section ────────────────────────────────── --}}
    @if($documents->count() > 0)
    <div class="detail-card">
        <div class="gallery-header">
            <div class="detail-card-title" style="margin-bottom: 0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> Dokumen & Laporan</div>
            <span class="gallery-count">{{ $documents->count() }} dokumen</span>
        </div>
        <div class="document-list">
            @foreach($documents as $doc)
                @php
                    $ext = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
                    $iconMap = [
                        'pdf'  => ['PDF', 'doc-icon-pdf', 'ext-pdf'],
                        'doc'  => ['DOC', 'doc-icon-word', 'ext-doc'],
                        'docx' => ['DOC', 'doc-icon-word', 'ext-docx'],
                        'xls'  => ['XLS', 'doc-icon-excel', 'ext-xls'],
                        'xlsx' => ['XLS', 'doc-icon-excel', 'ext-xlsx'],
                        'ppt'  => ['PPT', 'doc-icon-ppt', 'ext-ppt'],
                        'pptx' => ['PPT', 'doc-icon-ppt', 'ext-pptx'],
                    ];
                    $iconInfo = $iconMap[$ext] ?? ['FILE', 'doc-icon-other', 'ext-default'];
                @endphp
                <a href="{{ $doc->url }}" target="_blank" class="document-card" download>
                    <div class="doc-icon-wrapper {{ $iconInfo[1] }}">
                        {{ $iconInfo[0] }}
                    </div>
                    <div class="doc-details">
                        <div class="doc-title">{{ $doc->judul_file ?: $doc->nama_file }}</div>
                        <div class="doc-meta">
                            <span class="doc-ext-badge {{ $iconInfo[2] }}">{{ strtoupper($ext) }}</span>
                            <span>Klik untuk mengunduh</span>
                        </div>
                    </div>
                    <div class="doc-download-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

@else
    {{-- Empty state when no files uploaded --}}
    <div class="detail-card">
        <div class="detail-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> Foto & Dokumen Kegiatan</div>
        <div class="empty-luaran">
            <div class="empty-luaran-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
            <h6 style="font-weight: 600; color: #6b7280; margin-bottom: 4px;">Belum ada file untuk kegiatan ini</h6>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">Foto dan dokumen kegiatan akan ditampilkan di sini setelah diunggah oleh admin</p>
        </div>
    </div>
@endif

<!-- Delete Confirmation Modal -->
@if($isAdmin)
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 16px; padding: 32px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h5 style="font-weight: 700; color: #1f2937; margin-bottom: 8px;">Hapus Kegiatan?</h5>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">
            Kegiatan <strong>{{ $kegiatan->judul }}</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button"
                    onclick="document.getElementById('deleteModal').style.display='none'"
                    style="padding: 10px 24px; border-radius: 10px; border: 1px solid #e5e7eb; background: #fff; color: #374151; font-weight: 600; font-size: 14px; cursor: pointer;">
                Batal
            </button>
            <form action="{{ route('manajemenmahasiswa.kegiatan.destroy', $kegiatan->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="padding: 10px 24px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ─── Lightbox JavaScript ──────────────────────────────────────────── --}}
<script>
// Photo gallery data
const galleryImages = [
    @if(isset($images) && $images->count() > 0)
        @foreach($images->values() as $img)
        {
            src: "{{ $img->url }}",
            title: "{{ addslashes($img->judul_file ?: $img->nama_file) }}"
        },
        @endforeach
    @endif
];

let currentImageIndex = 0;

function openLightbox(index) {
    currentImageIndex = index;
    updateLightboxImage();
    document.getElementById('lightboxModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('active');
    document.body.style.overflow = '';
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightboxImage();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateLightboxImage();
}

function updateLightboxImage() {
    if (galleryImages.length === 0) return;
    const img = galleryImages[currentImageIndex];
    document.getElementById('lightboxImage').src = img.src;
    document.getElementById('lightboxImage').alt = img.title;
    document.getElementById('lightboxTitle').textContent = img.title;
    document.getElementById('lightboxCounter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (!modal || !modal.classList.contains('active')) return;

    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'ArrowRight') nextImage();
});

// Close lightbox on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (modal && e.target === modal) closeLightbox();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>

