<x-dynamic-component :component="$layout">

<style>
    .verif-header { margin-bottom: 24px; }
    .verif-header h3 { font-weight: 800; color: #1f2937; margin-bottom: 4px; }
    .verif-header p { color: #6b7280; font-size: 14px; }

    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 28px; }
    .stat-card {
        background: #fff; border: 1px solid #f3f4f6; border-radius: 12px;
        padding: 18px 20px; text-align: center;
    }
    .stat-card .stat-number { font-size: 28px; font-weight: 800; line-height: 1.2; }
    .stat-card .stat-label { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-card.pending .stat-number { color: #d97706; }
    .stat-card.approved .stat-number { color: #166534; }
    .stat-card.rejected .stat-number { color: #dc2626; }

    .form-card {
        background: #ffffff; border-radius: 12px; padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px; border: 1px solid #f3f4f6;
    }
    .form-card-title {
        font-weight: 700; font-size: 16px; color: #1f2937; margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px; padding-bottom: 14px;
        border-bottom: 1px solid #f3f4f6;
    }

    .verif-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .verif-table thead th {
        background: #f8fafc; padding: 10px 14px; font-size: 12px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb; border-top: 1px solid #e5e7eb;
    }
    .verif-table thead th:first-child { border-top-left-radius: 8px; border-left: 1px solid #e5e7eb; }
    .verif-table thead th:last-child { border-top-right-radius: 8px; border-right: 1px solid #e5e7eb; }
    
    .verif-table tbody td {
        padding: 12px 14px; font-size: 14px; color: #374151;
        border-bottom: 1px solid #e5e7eb; vertical-align: middle;
    }
    .verif-table tbody td:first-child { border-left: 1px solid #e5e7eb; }
    .verif-table tbody td:last-child { border-right: 1px solid #e5e7eb; }
    .verif-table tbody tr:hover td { background: #f8fafc; }
    .verif-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
    .verif-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }

    .status-verif { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; }
    .status-verif.pending { background: #fef3c7; color: #d97706; }
    .status-verif.approved { background: #dcfce7; color: #166534; }
    .status-verif.rejected { background: #fef2f2; color: #dc2626; }

    .btn-submit {
        background: #4f46e5; color: #ffffff; font-weight: 600; font-size: 14px;
        padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer;
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        text-decoration: none !important;
    }
    .btn-submit:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); color: #fff; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
    .empty-state h5 { color: #6b7280; font-weight: 600; margin-bottom: 4px; }
    .empty-state p { font-size: 14px; color: #9ca3af; }

    /* ── Form Controls (Manajemen Kegiatan Style) ── */
    .form-label-custom { font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 6px; }
    .form-control-custom, .form-select-custom {
        border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 14px;
        font-size: 14px; font-weight: 500; color: #1f2937; transition: all 0.2s; background: #fff;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); outline: none;
    }

    .tingkat-badge {
        font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px; text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #fef3c7; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #dcfce7; color: #166534; }
    .tingkat-badge.prodi { background: #eef2ff; color: #4f46e5; }

    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; }

    /* Preview styles */
    .preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .preview-item {
        position: relative; width: 100px; text-align: center;
    }
    .preview-item img {
        width: 100px; height: 80px; object-fit: cover; border-radius: 8px;
        border: 1px solid #e5e7eb; cursor: pointer; transition: opacity 0.2s;
    }
    .preview-item img:hover { opacity: 0.8; }
    .preview-item .preview-name {
        font-size: 10px; color: #6b7280; margin-top: 4px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .preview-item .preview-remove {
        position: absolute; top: -6px; right: -6px; width: 20px; height: 20px;
        border-radius: 50%; background: #dc2626; color: #fff; border: 2px solid #fff;
        font-size: 11px; font-weight: 700; cursor: pointer; display: flex;
        align-items: center; justify-content: center; line-height: 1; z-index: 2;
    }
    .preview-item .preview-remove:hover { background: #b91c1c; }

    .doc-preview-list { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
    .doc-preview-item {
        display: flex; align-items: center; gap: 8px; padding: 8px 12px;
        background: #f8fafc; border: 1px solid #f3f4f6; border-radius: 8px; font-size: 13px;
    }
    .doc-preview-item .doc-icon {
        width: 32px; height: 32px; border-radius: 6px; display: flex;
        align-items: center; justify-content: center; font-weight: 800; font-size: 10px;
        color: #fff; flex-shrink: 0;
    }
    .doc-preview-item .doc-icon.pdf { background: #dc2626; }
    .doc-preview-item .doc-icon.doc { background: #2563eb; }
    .doc-preview-item .doc-icon.xls { background: #16a34a; }
    .doc-preview-item .doc-icon.ppt { background: #ea580c; }
    .doc-preview-item .doc-icon.other { background: #6b7280; }
    .doc-preview-item .doc-name {
        flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #374151;
    }
    .doc-preview-item .doc-remove {
        width: 20px; height: 20px; border-radius: 50%; background: #fef2f2; color: #dc2626;
        border: 1px solid #fecaca; font-size: 12px; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .doc-preview-item .doc-remove:hover { background: #fee2e2; }

    /* Lightbox */
    .lightbox-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.85); z-index: 99999; justify-content: center;
        align-items: center; cursor: pointer;
    }
    .lightbox-overlay.active { display: flex; }
    .lightbox-overlay img {
        max-width: 90%; max-height: 90%; border-radius: 12px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5); cursor: default;
    }
    .lightbox-close {
        position: absolute; top: 20px; right: 30px; color: #fff; font-size: 36px;
        font-weight: 300; cursor: pointer; z-index: 100000; line-height: 1;
    }
    .lightbox-close:hover { color: #e5e7eb; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Verifikasi Data Saya</h3>
        <p class="text-dark fw-bold mb-0" style="font-size: 14px;">Ajukan riwayat kegiatan dan prestasi lomba Anda untuk diverifikasi oleh admin</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="stat-cards">
    <div class="stat-card pending">
        <div class="stat-number">{{ $stats['pending'] }}</div>
        <div class="stat-label">● Menunggu Verifikasi</div>
    </div>
    <div class="stat-card approved">
        <div class="stat-number">{{ $stats['approved'] }}</div>
        <div class="stat-label">✓ Disetujui</div>
    </div>
    <div class="stat-card rejected">
        <div class="stat-number">{{ $stats['rejected'] }}</div>
        <div class="stat-label">✗ Ditolak</div>
    </div>
</div>

<!-- Riwayat Kegiatan -->
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-card-title mb-0 border-0 pb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Riwayat Kegiatan
        </div>
        <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addRiwayatModal">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Ajukan Riwayat
        </button>
    </div>

    @if($riwayatData->count() > 0)
        <div style="overflow-x: auto;">
            <table class="verif-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatData as $i => $rw)
                        <tr>
                            <td style="color: #9ca3af;">{{ $i + 1 }}</td>
                            <td style="font-weight: 600;">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui' }}</td>
                            <td>{{ $rw->peran_manual ?? ucfirst($rw->peran ?? '') }}</td>
                            <td style="font-size: 13px; color: #6b7280;">
                                @if($rw->tanggal_kegiatan)
                                    {{ $rw->tanggal_kegiatan->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($rw->buktiFiles && $rw->buktiFiles->count() > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach($rw->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb;">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #eef2ff; border: 1px solid #e5e7eb; font-size: 14px;">📄</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-verif {{ $rw->verification_status }}">
                                    @if($rw->verification_status === 'pending') ● Pending
                                    @elseif($rw->verification_status === 'approved') ✓ Disetujui
                                    @else ✗ Ditolak
                                    @endif
                                </span>
                                @if($rw->verification_status === 'rejected' && $rw->verification_note)
                                    <div style="font-size: 11px; color: #dc2626; margin-top: 4px; font-style: italic;">
                                        "{{ $rw->verification_note }}"
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state" style="padding: 30px 20px;">
            <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
            <h5>Belum ada riwayat kegiatan</h5>
            <p>Riwayat kegiatan yang Anda ajukan akan muncul di sini</p>
        </div>
    @endif
</div>

<!-- Prestasi Lomba -->
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-card-title mb-0 border-0 pb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            Prestasi Lomba
        </div>
        <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addPrestasiModal">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Ajukan Prestasi
        </button>
    </div>

    @if($prestasiData->count() > 0)
        <div style="overflow-x: auto;">
            <table class="verif-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Prestasi</th>
                        <th>Tingkat</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiData as $i => $p)
                        <tr>
                            <td style="color: #9ca3af;">{{ $i + 1 }}</td>
                            <td style="font-weight: 600;">{{ $p->nama_prestasi }}</td>
                            <td><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                            <td style="font-size: 13px; color: #6b7280;">
                                @if($p->tanggal)
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($p->buktiFiles && $p->buktiFiles->count() > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach($p->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb;">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #eef2ff; border: 1px solid #e5e7eb; font-size: 14px;">📄</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-verif {{ $p->verification_status }}">
                                    @if($p->verification_status === 'pending') ● Pending
                                    @elseif($p->verification_status === 'approved') ✓ Disetujui
                                    @else ✗ Ditolak
                                    @endif
                                </span>
                                @if($p->verification_status === 'rejected' && $p->verification_note)
                                    <div style="font-size: 11px; color: #dc2626; margin-top: 4px; font-style: italic;">
                                        "{{ $p->verification_note }}"
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state" style="padding: 30px 20px;">
            <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></div>
            <h5>Belum ada prestasi lomba</h5>
            <p>Prestasi lomba yang Anda ajukan akan muncul di sini</p>
        </div>
    @endif
</div>

<!-- Modal Ajukan Riwayat -->
<div class="modal fade" id="addRiwayatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.riwayat.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ajukan Riwayat Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom">Nama Kegiatan <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="nama_kegiatan_manual" class="form-control form-control-custom" required
                               placeholder="Contoh: Lomba Debat Nasional 2026">
                        <small class="text-muted" style="font-size: 11px;">Ketik nama kegiatan yang pernah Anda ikuti</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Peran <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="peran_manual" class="form-control form-control-custom" required
                               placeholder="Contoh: Peserta, Delegasi, Koordinator">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Tanggal Kegiatan <span style="color: #9ca3af; font-weight: 400;">(opsional)</span></label>
                        <input type="date" name="tanggal_kegiatan" class="form-control form-control-custom">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                            Bukti Gambar
                            <span style="color: #9ca3af; font-weight: 400;">(opsional, maks 5 gambar)</span>
                        </label>
                        <input type="file" name="bukti_images[]" id="riwayatImages" class="form-control form-control-custom" multiple
                               accept="image/jpeg,image/png,image/gif,image/webp" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG, GIF, WEBP. Maks 10MB per file.</small>
                        <div class="preview-grid" id="riwayatImagesPreview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Bukti Dokumen
                            <span style="color: #9ca3af; font-weight: 400;">(opsional, maks 5 dokumen)</span>
                        </label>
                        <input type="file" name="bukti_docs[]" id="riwayatDocs" class="form-control form-control-custom" multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Format: PDF, DOC, DOCX, XLS, PPT. Maks 10MB per file.</small>
                        <div class="doc-preview-list" id="riwayatDocsPreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                    <button type="submit" class="btn-submit">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajukan Prestasi -->
<div class="modal fade" id="addPrestasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.prestasi.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ajukan Prestasi Lomba</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom">Nama Prestasi <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="nama_prestasi" class="form-control form-control-custom" required
                               placeholder="Contoh: Juara 1 Hackathon IT Del 2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Tingkat <span style="color: #dc2626;">*</span></label>
                        <select name="tingkat" class="form-select form-select-custom" required>
                            <option value="">Pilih tingkat...</option>
                            <option value="internasional">Internasional</option>
                            <option value="nasional">Nasional</option>
                            <option value="regional">Regional</option>
                            <option value="universitas">Universitas</option>
                            <option value="prodi">Prodi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Tanggal <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="tanggal" class="form-control form-control-custom" required
                               value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                            Bukti Gambar
                            <span style="color: #9ca3af; font-weight: 400;">(opsional, maks 5 gambar)</span>
                        </label>
                        <input type="file" name="bukti_images[]" id="prestasiImages" class="form-control form-control-custom" multiple
                               accept="image/jpeg,image/png,image/gif,image/webp" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG, GIF, WEBP. Maks 10MB per file.</small>
                        <div class="preview-grid" id="prestasiImagesPreview"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Bukti Dokumen
                            <span style="color: #9ca3af; font-weight: 400;">(opsional, maks 5 dokumen)</span>
                        </label>
                        <input type="file" name="bukti_docs[]" id="prestasiDocs" class="form-control form-control-custom" multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Format: PDF, DOC, DOCX, XLS, PPT. Maks 10MB per file.</small>
                        <div class="doc-preview-list" id="prestasiDocsPreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                    <button type="submit" class="btn-submit">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lightbox Overlay -->
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImg" src="" alt="Preview">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// =========================================================================
// File Preview Manager — handles image thumbnails + doc list with remove
// =========================================================================
class FilePreviewManager {
    constructor(inputId, previewId, type) {
        this.input = document.getElementById(inputId);
        this.previewContainer = document.getElementById(previewId);
        this.type = type; // 'image' or 'doc'
        this.files = [];
        if (this.input) this.input.addEventListener('change', () => this.handleFiles());
    }

    handleFiles() {
        const newFiles = Array.from(this.input.files);
        this.files = [...this.files, ...newFiles];
        this.syncInput();
        this.render();
    }

    removeFile(index) {
        this.files.splice(index, 1);
        this.syncInput();
        this.render();
    }

    syncInput() {
        const dt = new DataTransfer();
        this.files.forEach(f => dt.items.add(f));
        this.input.files = dt.files;
    }

    render() {
        this.previewContainer.innerHTML = '';
        if (this.type === 'image') {
            this.files.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    const removeBtn = document.createElement('span');
                    removeBtn.className = 'preview-remove';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = () => this.removeFile(idx);

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.title = 'Klik untuk preview';
                    img.onclick = () => openLightbox(e.target.result);

                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'preview-name';
                    nameDiv.title = file.name;
                    nameDiv.textContent = file.name;

                    item.appendChild(removeBtn);
                    item.appendChild(img);
                    item.appendChild(nameDiv);
                    this.previewContainer.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        } else {
            this.files.forEach((file, idx) => {
                const ext = file.name.split('.').pop().toLowerCase();
                let iconClass = 'other';
                let iconText = ext.toUpperCase();
                if (ext === 'pdf') iconClass = 'pdf';
                else if (['doc','docx'].includes(ext)) { iconClass = 'doc'; iconText = 'DOC'; }
                else if (['xls','xlsx'].includes(ext)) { iconClass = 'xls'; iconText = 'XLS'; }
                else if (['ppt','pptx'].includes(ext)) { iconClass = 'ppt'; iconText = 'PPT'; }

                const item = document.createElement('div');
                item.className = 'doc-preview-item';

                const icon = document.createElement('div');
                icon.className = 'doc-icon ' + iconClass;
                icon.textContent = iconText;

                const nameEl = document.createElement('div');
                nameEl.className = 'doc-name';
                nameEl.title = file.name;
                nameEl.textContent = file.name;

                const removeBtn = document.createElement('span');
                removeBtn.className = 'doc-remove';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = () => this.removeFile(idx);

                item.appendChild(icon);
                item.appendChild(nameEl);
                item.appendChild(removeBtn);
                this.previewContainer.appendChild(item);
            });
        }
    }
}

// Initialize preview managers
window.__fpm_riwayatImages  = new FilePreviewManager('riwayatImages',  'riwayatImagesPreview',  'image');
window.__fpm_riwayatDocs    = new FilePreviewManager('riwayatDocs',    'riwayatDocsPreview',    'doc');
window.__fpm_prestasiImages = new FilePreviewManager('prestasiImages', 'prestasiImagesPreview', 'image');
window.__fpm_prestasiDocs   = new FilePreviewManager('prestasiDocs',   'prestasiDocsPreview',   'doc');

// Reset previews when modals close
['addRiwayatModal', 'addPrestasiModal'].forEach(modalId => {
    const el = document.getElementById(modalId);
    if (el) el.addEventListener('hidden.bs.modal', () => {
        const prefix = modalId === 'addRiwayatModal' ? 'riwayat' : 'prestasi';
        ['Images', 'Docs'].forEach(suffix => {
            const mgr = window[`__fpm_${prefix}${suffix}`];
            if (mgr) { mgr.files = []; mgr.syncInput(); mgr.render(); }
        });
    });
});

// Lightbox
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
});
</script>
</x-dynamic-component>
