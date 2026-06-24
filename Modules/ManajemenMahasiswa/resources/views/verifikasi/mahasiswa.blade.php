<x-dynamic-component :component="$layout">

<style>
    /* ── Header ── */
    .verif-header { margin-bottom: 24px; }
    .verif-header h4 { font-size: 1.45rem; font-weight: 700; color: var(--c-fg); margin-bottom: 2px; letter-spacing: -.02em; }
    .verif-header p { color: var(--c-fg-muted); font-size: .82rem; }

    /* ── Status & Buttons ── */
    .status-verif {
        display: inline-flex; align-items: center; padding: 3px 9px;
        border-radius: 50px; font-size: .73rem; font-weight: 600;
    }
    .status-verif.pending { background: #FFFBEB; color: #d97706; }
    .status-verif.approved { background: #ECFDF5; color: #059669; }
    .status-verif.rejected { background: var(--c-error-subtle); color: var(--c-error); }

    .btn-submit {
        background: #0B266E; color: #fff; font-weight: 600; font-size: .85rem;
        padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer;
        transition: background .15s; display: inline-flex; align-items: center; gap: 6px;
        text-decoration: none !important; white-space: nowrap;
    }
    .btn-submit:hover { background: #091958; color: #fff; }
    .btn-submit:disabled, .btn-submit[disabled] {
        background: #C1C7CF; color: #fff; cursor: not-allowed; opacity: .7;
    }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 60px 24px; color: var(--c-fg-muted); }
    .empty-state .empty-icon { display: flex; justify-content: center; margin-bottom: 12px; color: #E5E7EB; }

    /* ── Form Controls ── */
    .form-label-custom { font-weight: 600; font-size: .87rem; color: var(--c-fg-sec); margin-bottom: 6px; }
    .form-control-custom, .form-select-custom {
        border: 1.5px solid #B6BCC6; border-radius: 10px; padding: 10px 14px;
        font-size: .87rem; font-weight: 500; color: var(--c-fg-sec); transition: all .2s; background: #F1F3F5;
    }
    .form-control-custom:hover, .form-select-custom:hover {
        border-color: var(--c-primary-border); background: #EDEFF2;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: var(--c-primary); box-shadow: 0 0 0 3px rgba(11,38,110, 0.1); outline: none; background: #fff;
    }

    .tingkat-badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 50px; font-size: .73rem; font-weight: 600; text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #FFFBEB; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #ECFDF5; color: #059669; }
    .tingkat-badge.prodi { background: #eef2ff; color: var(--c-primary); }

    /* ── Reward Badge & Button ── */
    .claim-badge { font-size: .73rem; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-flex; align-items: center; }
    .claim-badge.belum     { background: #f3f4f6; color: var(--c-fg-muted); }
    .claim-badge.diajukan  { background: #dbeafe; color: #1e40af; }
    .claim-badge.disetujui { background: #ECFDF5; color: #059669; }
    .claim-badge.ditolak   { background: var(--c-error-subtle); color: var(--c-error); }

    .btn-claim {
        background: var(--c-primary-subtle); color: var(--c-primary); border: 1px solid rgba(11,38,110,0.18); padding: 5px 14px;
        border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-claim:hover { background: rgba(11,38,110,0.12); border-color: var(--c-primary-border); }
    .btn-unclaim {
        background: #f3f4f6; color: var(--c-fg-muted); border: 1px solid var(--c-border); padding: 5px 14px;
        border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-unclaim:hover { background: var(--c-border); }

    /* Kotak detail & preview jatah reward */
    .reward-detail {
        font-size: .72rem; color: var(--c-fg-sec); margin-top: 6px; padding: 6px 10px;
        background: #fafafa; border: 1px solid var(--c-border); border-radius: 8px;
        line-height: 1.5; max-width: 100%;
    }
    .reward-detail .lbl { color: var(--c-fg-muted); }
    .reward-detail.ditolak { background: var(--c-error-subtle); border-color: #fecaca; color: #b91c1c; }

    /* Kolom reward: badge + tombol */
    .reward-stack { display: flex; flex-direction: column; gap: 8px; align-items: flex-start; }
    .reward-actions { display: flex; flex-wrap: wrap; gap: 6px; }
    .btn-tinjau {
        background: var(--c-primary-subtle); color: var(--c-primary); border: 1px solid rgba(11,38,110,0.18); padding: 5px 14px;
        border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-tinjau:hover { background: rgba(11,38,110,0.12); border-color: var(--c-primary-border); }

    /* Isi modal detail reward */
    .rd-info { font-size: .87rem; color: var(--c-fg-sec); background: #fafafa; border: 1px solid var(--c-border); border-radius: 10px; padding: 12px 14px; display: flex; flex-direction: column; gap: 8px; }
    .rc-line { font-size: .87rem; color: var(--c-fg-sec); line-height: 1.4; }
    .rc-line .rc-inv { font-size: .72rem; color: var(--c-fg-muted); }
    .rc-row { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
    .rc-lbl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--c-fg-muted); min-width: 42px; }
    .rc-pill { font-size: .8rem; font-weight: 600; color: var(--c-primary); background: var(--c-primary-subtle); border-radius: 50px; padding: 2px 10px; }
    .rc-chips { display: flex; flex-wrap: wrap; gap: 4px; }
    .rc-chip { font-size: .8rem; color: var(--c-primary); background: #fff; border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 2px 10px; }
    .rc-note { font-size: .8rem; color: var(--c-fg-sec); line-height: 1.4; overflow-wrap: anywhere; }
    .rc-note.ditolak { color: #b91c1c; }

    /* Catatan verifikasi ringkas */
    .verif-note {
        font-size: .72rem; margin-top: 5px; max-width: 210px; line-height: 1.4;
        overflow-wrap: anywhere;
    }
    .verif-note.approved { color: var(--c-success); }
    .verif-note.rejected { color: #b91c1c; }
    .jatah-preview {
        margin-top: 10px; padding: 10px 12px; border-radius: 10px;
        background: var(--c-primary-subtle); border: 1px solid rgba(11,38,110,0.18); font-size: .87rem; color: var(--c-primary);
    }
    .jatah-preview strong { font-weight: 700; }
    .kuota-info {
        font-size: .8rem; color: var(--c-fg-muted); background: #fafafa; border: 1px solid var(--c-border);
        border-radius: 10px; padding: 8px 12px; margin-bottom: 12px;
    }
    .kuota-info b { color: var(--c-primary); }

    .aturan-box { background: #fff; border: 1px solid var(--c-border); border-radius: 12px; padding: 12px 14px; margin-bottom: 14px; }
    .aturan-box-title { display: flex; align-items: center; gap: 6px; font-size: .87rem; font-weight: 700; color: var(--c-fg); margin-bottom: 10px; }

    /* Picker usulan mata kuliah (reward) */
    .mk-chosen { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .mk-chip {
        display: inline-flex; align-items: center; gap: 6px; font-size: .8rem; font-weight: 600;
        color: var(--c-primary); background: var(--c-primary-subtle); border: 1px solid rgba(11,38,110,0.18);
        border-radius: 50px; padding: 4px 6px 4px 12px;
    }
    .mk-chip .mk-sks { font-weight: 500; color: var(--c-primary-border); }
    .mk-chip .mk-remove {
        width: 18px; height: 18px; border-radius: 50%; background: rgba(11,38,110,0.18); color: var(--c-primary);
        border: none; font-size: .8rem; font-weight: 700; cursor: pointer; line-height: 1;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .mk-chip .mk-remove:hover { background: var(--c-primary-border); }
    .mk-counter { font-size: .8rem; font-weight: 600; color: var(--c-fg-muted); margin-top: 8px; }
    .mk-counter.over { color: var(--c-error); }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; color: var(--c-fg); }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 14px 22px; }

    /* Preview styles */
    .preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .preview-item { position: relative; width: 100px; text-align: center; }
    .preview-item img {
        width: 100px; height: 80px; object-fit: cover; border-radius: 10px;
        border: 1px solid var(--c-border); cursor: pointer; transition: opacity .15s;
    }
    .preview-item img:hover { opacity: 0.8; }
    .preview-item .preview-name {
        font-size: .68rem; color: var(--c-fg-muted); margin-top: 4px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .preview-item .preview-remove {
        position: absolute; top: -6px; right: -6px; width: 20px; height: 20px;
        border-radius: 50%; background: var(--c-error); color: #fff; border: 2px solid #fff;
        font-size: .72rem; font-weight: 700; cursor: pointer; display: flex;
        align-items: center; justify-content: center; line-height: 1; z-index: 2;
    }
    .preview-item .preview-remove:hover { background: #b91c1c; }

    .doc-preview-list { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
    .doc-preview-item {
        display: flex; align-items: center; gap: 8px; padding: 8px 12px;
        background: #fafafa; border: 1px solid var(--c-border); border-radius: 10px; font-size: .87rem;
    }
    .doc-preview-item .doc-icon {
        width: 32px; height: 32px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; font-weight: 800; font-size: .68rem;
        color: #fff; flex-shrink: 0;
    }
    .doc-preview-item .doc-icon.pdf { background: var(--c-error); }
    .doc-preview-item .doc-icon.doc { background: #2563eb; }
    .doc-preview-item .doc-icon.xls { background: #16a34a; }
    .doc-preview-item .doc-icon.ppt { background: #ea580c; }
    .doc-preview-item .doc-icon.other { background: var(--c-fg-muted); }
    .doc-preview-item .doc-name {
        flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--c-fg-sec);
    }
    .doc-preview-item .doc-remove {
        width: 20px; height: 20px; border-radius: 50%; background: var(--c-error-subtle); color: var(--c-error);
        border: 1px solid #fecaca; font-size: .8rem; font-weight: 700; cursor: pointer;
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
        max-width: 90%; max-height: 90%; border-radius: 14px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5); cursor: default;
    }
    .lightbox-close {
        position: absolute; top: 20px; right: 30px; color: #fff; font-size: 36px;
        font-weight: 300; cursor: pointer; z-index: 100000; line-height: 1;
    }
    .lightbox-close:hover { color: var(--c-border); }

    /* ── Enhanced Table Visibility ── */
    table thead tr {
        background: #eef0f4 !important;
        border-bottom: 2px solid #d1d5db !important;
    }
    table thead th {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #374151 !important;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
    }
    table tbody tr {
        border-bottom: 1px solid #e5e7eb !important;
    }
    table tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    table tbody tr:hover {
        background: #eef2ff !important;
        box-shadow: inset 3px 0 0 0 #0B266E;
    }
    table tbody td {
        font-size: 13px;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
    }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
    <div>
        @if($tab === 'prestasi')
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Prestasi Saya</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Ajukan prestasi lomba untuk diverifikasi admin. Prestasi yang sudah disetujui bisa Anda ajukan rewardnya (konversi nilai mata kuliah, SK FT 774).</p>
        @else
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Riwayat Kegiatan Saya</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Ajukan riwayat keikutsertaan kegiatan untuk diverifikasi admin.</p>
        @endif
    </div>
</div>
@php
    $canSubmit = auth()->user()->hasAnyRole([
        'mahasiswa','pengurus_himpunan',
        'ketua_himpunan','ketua_bidang',
        'ketua_unit','staff_himpunan','superadmin','admin','admin_kemahasiswaan'
    ]);
@endphp

@if($tab === 'riwayat')
<!-- Riwayat Kegiatan - Global Style Table Card -->
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom:18px;">
    <!-- Table Toolbar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Riwayat Kegiatan
        </h2>
        @if($canSubmit)
            <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addRiwayatModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Ajukan Riwayat
            </button>
        @endif
    </div>

    @if($riwayatData->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:600px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:200px;">Nama Kegiatan</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Peran</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Bukti</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatData as $i => $rw)
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; color:var(--c-fg-muted); width:48px;">{{ $i + 1 }}</td>
                            <td style="padding:14px 16px; min-width:200px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:260px;">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui' }}</p>
                            </td>
                            <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-sec);">{{ $rw->peran_manual ?? ucfirst($rw->peran ?? '') }}</td>
                            <td style="padding:14px 16px; white-space:nowrap;">
                                @if($rw->tanggal_kegiatan)
                                    <span style="font-size:12px; font-weight:500; color:var(--c-fg-sec);">{{ $rw->tanggal_kegiatan->translatedFormat('d M Y') }}</span>
                                @else
                                    <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                @if($rw->buktiFiles && $rw->buktiFiles->count() > 0)
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        @foreach($rw->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid var(--c-border);">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #fef2f2; border: 1px solid #fecaca;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line></svg></span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                <span class="status-verif {{ $rw->verification_status }}">
                                    @if($rw->verification_status === 'pending') Pending
                                    @elseif($rw->verification_status === 'approved') Disetujui
                                    @else Ditolak
                                    @endif
                                </span>
                                @if($rw->verification_status === 'rejected' && $rw->verification_note)
                                    <div class="verif-note rejected" title="{{ $rw->verification_note }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:3px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>"{{ Str::limit($rw->verification_note, 60) }}"
                                    </div>
                                @endif
                                @if($rw->verification_status === 'approved' && $rw->verification_note)
                                    <div class="verif-note approved" title="{{ $rw->verification_note }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:3px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>"{{ Str::limit($rw->verification_note, 60) }}"
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Belum ada riwayat kegiatan</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Riwayat kegiatan yang Anda ajukan akan muncul di sini</p>
        </div>
    @endif
</div>
@endif

@if($tab === 'prestasi')
<!-- Prestasi Lomba - Global Style Table Card -->
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom:18px;">
    <!-- Table Toolbar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            Prestasi Lomba
        </h2>
        @if($canSubmit)
            <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addPrestasiModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ajukan Prestasi
            </button>
        @endif
    </div>

    <!-- Info & Aturan (inside card, above table) -->
    <div style="padding:14px 16px; border-bottom:1px solid #e5e7eb;">
        <div class="kuota-info" style="margin-bottom:10px;">
            Kuota reward Anda (SK FT 774): umum <b>{{ $kuota['umum'] ?? 0 }}/2</b> &nbsp;•&nbsp; invention/expo/fair <b>{{ $kuota['invention'] ?? 0 }}/1</b>.
            Reward = peningkatan nilai mata kuliah; mata kuliah final ditetapkan departemen.
        </div>

        @if($rewardAturan->count())
            <div class="aturan-box" style="margin-bottom:0;">
                <div class="aturan-box-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Aturan Reward (SK FT 774)
                </div>
                @include('manajemenmahasiswa::verifikasi._aturan_links', ['items' => $rewardAturan])
            </div>
        @endif
    </div>

    @if($prestasiData->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:750px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">Nama Prestasi</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tingkat</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Bukti</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status Verifikasi</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Reward</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiData as $i => $p)
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; color:var(--c-fg-muted); width:48px;">{{ $i + 1 }}</td>
                            <td style="padding:14px 16px; min-width:180px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:220px;">{{ $p->nama_prestasi }}</p>
                            </td>
                            <td style="padding:14px 16px;"><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                            <td style="padding:14px 16px; white-space:nowrap;">
                                @if($p->tanggal)
                                    <span style="font-size:12px; font-weight:500; color:var(--c-fg-sec);">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</span>
                                @else
                                    <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                @if($p->buktiFiles && $p->buktiFiles->count() > 0)
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        @foreach($p->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid var(--c-border);">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #fef2f2; border: 1px solid #fecaca;"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line></svg></span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                <span class="status-verif {{ $p->verification_status }}">
                                    @if($p->verification_status === 'pending') Pending
                                    @elseif($p->verification_status === 'approved') Disetujui
                                    @else Ditolak
                                    @endif
                                </span>
                                @if($p->verification_status === 'rejected' && $p->verification_note)
                                    <div class="verif-note rejected" title="{{ $p->verification_note }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:3px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>"{{ Str::limit($p->verification_note, 60) }}"
                                    </div>
                                @endif
                                @if($p->verification_status === 'approved' && $p->verification_note)
                                    <div class="verif-note approved" title="{{ $p->verification_note }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:3px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>"{{ Str::limit($p->verification_note, 60) }}"
                                    </div>
                                @endif
                            </td>
                            <td style="padding:14px 16px;">
                                @if($p->verification_status === 'approved')
                                    @php
                                        $rdData = [
                                            'nama'          => $p->nama_prestasi,
                                            'status'        => $p->reward_status,
                                            'penyelenggara' => $p->reward_penyelenggara_label,
                                            'capaian'       => $p->reward_capaian_label,
                                            'invention'     => (bool) $p->reward_is_invention,
                                            'jml_mk_max'    => $p->reward_jml_mk_max,
                                            'sks_max'       => $p->reward_sks_max,
                                            'mk'            => $p->reward_mk_diajukan ?? [],
                                            'mk_disetujui'  => $p->reward_mk_disetujui,
                                            'note'          => $p->reward_note,
                                        ];
                                    @endphp
                                    @if($p->reward_status === \Modules\ManajemenMahasiswa\Models\Prestasi::CLAIM_DIAJUKAN)
                                        <div class="reward-stack">
                                            <span class="claim-badge diajukan">Menunggu persetujuan</span>
                                            <div class="reward-actions">
                                                <button type="button" class="btn-tinjau" onclick="openRewardDetail(@js($rdData))">Tinjau</button>
                                                <form method="POST" id="batalRewardForm{{ $p->id }}" action="{{ route('manajemenmahasiswa.verifikasi.prestasi.reward.batal', $p->id) }}" style="margin:0;">
                                                    @csrf @method('PATCH')
                                                    <button type="button" class="btn-unclaim"
                                                            onclick="openBatalConfirm('batalRewardForm{{ $p->id }}', @js($p->nama_prestasi))">Batalkan</button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($p->reward_status === \Modules\ManajemenMahasiswa\Models\Prestasi::CLAIM_DISETUJUI)
                                        <div class="reward-stack">
                                            <span class="claim-badge disetujui">Reward disetujui</span>
                                            <div class="reward-actions">
                                                <button type="button" class="btn-tinjau" onclick="openRewardDetail(@js($rdData))">Tinjau</button>
                                            </div>
                                        </div>
                                    @elseif($p->reward_status === \Modules\ManajemenMahasiswa\Models\Prestasi::CLAIM_DITOLAK)
                                        <div class="reward-stack">
                                            <span class="claim-badge ditolak">Reward ditolak</span>
                                            <div class="reward-actions">
                                                <button type="button" class="btn-tinjau" onclick="openRewardDetail(@js($rdData))">Tinjau</button>
                                                @unless($isAlumni ?? false)
                                                    <button type="button" class="btn-claim"
                                                            onclick="openAjukanReward({{ $p->id }}, @js($p->nama_prestasi))">Ajukan Ulang</button>
                                                @endunless
                                            </div>
                                        </div>
                                    @else
                                        <div class="reward-stack">
                                            <span class="claim-badge belum">Belum diajukan</span>
                                            @unless($isAlumni ?? false)
                                                <button type="button" class="btn-claim"
                                                        onclick="openAjukanReward({{ $p->id }}, @js($p->nama_prestasi))">Ajukan Reward</button>
                                            @endunless
                                        </div>
                                    @endif
                                @else
                                    <span style="color: #d1d5db;">—</span>
                                    <div style="font-size: 11px; color: var(--c-fg-muted); margin-top: 4px;">Tersedia setelah disetujui</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg></div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Belum ada prestasi lomba</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Prestasi lomba yang Anda ajukan akan muncul di sini</p>
        </div>
    @endif
</div>
@endif

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
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Nama Kegiatan <span style="color: #dc2626;">*</span></label>
                            <span class="text-muted" style="font-size: 11px;" id="charCount_nama_kegiatan_manual">0 / 50 huruf</span>
                        </div>
                        <input type="text" name="nama_kegiatan_manual" class="form-control form-control-custom" required maxlength="50"
                               placeholder="Contoh: Lomba Debat Nasional 2026" oninput="document.getElementById('charCount_nama_kegiatan_manual').innerText = this.value.length + ' / 50 huruf'">
                        <small class="text-muted" style="font-size: 11px;">Ketik nama kegiatan yang pernah Anda ikuti</small>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Peran <span style="color: #dc2626;">*</span></label>
                            <span class="text-muted" style="font-size: 11px;" id="charCount_peran_manual">0 / 50 huruf</span>
                        </div>
                        <input type="text" name="peran_manual" class="form-control form-control-custom" required maxlength="50"
                               placeholder="Contoh: Peserta, Delegasi, Koordinator" oninput="document.getElementById('charCount_peran_manual').innerText = this.value.length + ' / 50 huruf'">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Tanggal Kegiatan <span style="color: #dc2626;">*</span></label>
                        <input type="date" name="tanggal_kegiatan" class="form-control form-control-custom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Bukti Kegiatan <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="file" name="bukti_docs[]" id="riwayatDocs" class="form-control form-control-custom" required
                               accept="application/pdf,.pdf" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Gabungkan semua bukti (sertifikat, surat tugas, foto, dsb.) dalam <b>1 file PDF</b>. Maks 10MB.</small>
                        <div class="doc-preview-list" id="riwayatDocsPreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                    <button type="submit" class="btn-submit" data-submit-once>Ajukan</button>
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
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label-custom mb-0">Nama Prestasi <span style="color: #dc2626;">*</span></label>
                            <span class="text-muted" style="font-size: 11px;" id="charCount_nama_prestasi">0 / 50 huruf</span>
                        </div>
                        <input type="text" name="nama_prestasi" class="form-control form-control-custom" required maxlength="50"
                               placeholder="Contoh: Juara 1 Hackathon IT Del 2026" oninput="document.getElementById('charCount_nama_prestasi').innerText = this.value.length + ' / 50 huruf'">
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
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            Bukti Kegiatan <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="file" name="bukti_docs[]" id="prestasiDocs" class="form-control form-control-custom" required
                               accept="application/pdf,.pdf" style="padding: 8px 14px;">
                        <small class="text-muted" style="font-size: 11px;">Gabungkan semua bukti (sertifikat, surat tugas/lomba, foto, dsb.) dalam <b>1 file PDF</b>. Maks 10MB.</small>
                        <div class="doc-preview-list" id="prestasiDocsPreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                    <button type="submit" class="btn-submit" data-submit-once>Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajukan Reward Prestasi (SK FT 774) -->
<div class="modal fade" id="ajukanRewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="ajukanRewardForm">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Ajukan Reward Prestasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 13px; color: #666D80; margin-bottom: 16px;">
                        Prestasi: <strong id="arNamaPrestasi" style="color:#1f2937;"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label-custom">Kategori Penyelenggara <span style="color:#dc2626;">*</span></label>
                        <select name="reward_penyelenggara" id="arPenyelenggara" class="form-select form-select-custom" required>
                            <option value="">Pilih kategori...</option>
                            @foreach(\Modules\ManajemenMahasiswa\Models\Prestasi::PENYELENGGARA_LABELS as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-custom">Capaian / Peringkat <span style="color:#dc2626;">*</span></label>
                        <select name="reward_capaian" id="arCapaian" class="form-select form-select-custom" required disabled>
                            <option value="">Pilih penyelenggara dulu...</option>
                        </select>
                    </div>
                    <div class="mb-3" id="arInventionWrap" style="display:none;">
                        <label style="font-size:13px; color:#374151; display:flex; align-items:flex-start; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="reward_is_invention" id="arInvention" value="1" style="margin-top:3px;">
                            <span>Kegiatan bertema <b>invention / innovation / exhibition / convention / expo / inventor / fair</b> dsb. (SK 774 poin 2.e–2.f). Jika dicentang, jatah maks 2 SKS &amp; kuota khusus 1×.</span>
                        </label>
                    </div>
                    <div class="jatah-preview" id="arJatahPreview" style="display:none;"></div>

                    <div class="mb-3 mt-3" id="arMkWrap" style="display:none;">
                        <label class="form-label-custom">
                            Usulan Mata Kuliah yang Dinaikkan Nilainya <span style="color:#dc2626;">*</span>
                            <span style="font-weight:400; color:#666D80;">(maks <span id="arMkMax">0</span> MK)</span>
                        </label>
                        <div class="d-flex gap-2">
                            <select id="arMkSelect" class="form-select form-select-custom" style="flex:1;">
                                <option value="">Pilih mata kuliah...</option>
                                @foreach(\Modules\ManajemenMahasiswa\Models\Prestasi::MATA_KULIAH as $smt => $mks)
                                    <optgroup label="{{ $smt }}">
                                        @foreach($mks as $namaMk => $sksMk)
                                            <option value="{{ $namaMk }}">{{ $namaMk }} ({{ $sksMk }} SKS)</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <button type="button" class="btn-claim" id="arMkAddBtn" style="white-space:nowrap;">+ Tambah</button>
                        </div>
                        <div id="arMkChosen" class="mk-chosen"></div>
                        <div id="arMkCounter" class="mk-counter"></div>
                        <div id="arMkHidden"></div>
                        <small class="text-muted" style="font-size:11px;">Pilih MK kurikulum Teknik Komputer yang nilainya ingin dinaikkan (syarat min. C). Ini usulan; MK final ditetapkan departemen.</small>
                    </div>

                    <div style="font-size:11px; color:#666D80; margin-top:10px; line-height:1.5;">
                        Catatan: mata kuliah yang dinaikkan nilainya ditetapkan departemen/prodi saat persetujuan. Reward hanya untuk MK bernilai minimal C, maks 2× (atau 1× untuk invention) selama studi.
                    </div>
                </div>
                <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; font-weight:600; padding:10px 20px;">Batal</button>
                    <button type="submit" id="arSubmitBtn" disabled
                            style="background:#0B266E; color:#fff; font-weight:600; font-size:.87rem; padding:10px 24px; border-radius:10px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .15s; opacity:.5;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Ajukan Reward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $rewardJatahMap = [];
    foreach (\Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_BY_PENYELENGGARA as $peny => $caps) {
        foreach ($caps as $cap) {
            $rewardJatahMap[$peny][$cap] = \Modules\ManajemenMahasiswa\Models\Prestasi::hitungJatahReward($peny, $cap, false);
        }
    }
    $rewardJatahInvention = \Modules\ManajemenMahasiswa\Models\Prestasi::hitungJatahReward(
        \Modules\ManajemenMahasiswa\Models\Prestasi::PENYELENGGARA_LAINNYA,
        \Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_FINALIS,
        true
    );
@endphp

<!-- Modal Konfirmasi Batalkan Pengajuan Reward -->
<div class="modal fade" id="claimConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 28px 24px 20px; text-align: center;">
                <div id="claimConfirmIcon" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;"></div>
                <h5 id="claimConfirmTitle" class="fw-bold mb-2" style="color: #1f2937;"></h5>
                <p id="claimConfirmText" style="color: #666D80; font-size: 14px; line-height: 1.5; margin-bottom: 0;"></p>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 8px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                <button type="button" id="claimConfirmBtn" style="border-radius: 10px; font-weight: 600; font-size: 14px; padding: 10px 20px; border: none; cursor: pointer; color: #fff;"></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Reward (Tinjau) -->
<div class="modal fade" id="rewardDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color:#0B266E;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    Detail Reward Prestasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:13px; color:#666D80; margin-bottom:12px;">Prestasi: <strong id="rdNama" style="color:#1f2937;"></strong></p>
                <div id="rdStatus" style="margin-bottom:12px;"></div>
                <div class="rd-info" id="rdInfo"></div>
                <div style="font-size:11px; color:#666D80; margin-top:12px; line-height:1.5;">
                    Reward = peningkatan nilai mata kuliah (SK FT 774). Mata kuliah final ditetapkan departemen; keputusan akhir di Bidang Akademik Fakultas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px; font-weight:600; padding:10px 20px;">Tutup</button>
            </div>
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
// Detail Reward (Tinjau) — tampilkan info reward di modal (read-only)
// =========================================================================
function rdEscape(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function openRewardDetail(data) {
    document.getElementById('rdNama').textContent = data.nama || '-';

    const map = {
        diajukan:  ['diajukan',  'Menunggu persetujuan'],
        disetujui: ['disetujui', 'Reward disetujui'],
        ditolak:   ['ditolak',   'Reward ditolak'],
    };
    const m = map[data.status] || ['belum', 'Belum diajukan'];
    document.getElementById('rdStatus').innerHTML = '<span class="claim-badge ' + m[0] + '">' + m[1] + '</span>';

    let html = '';
    const inv = data.invention ? ' <span class="rc-inv">(invention/expo/fair)</span>' : '';
    if (data.penyelenggara) {
        html += '<div class="rc-line">' + rdEscape(data.penyelenggara) + ' · ' + rdEscape(data.capaian) + inv + '</div>';
    }
    html += '<div class="rc-row"><span class="rc-lbl">Jatah</span>'
          + '<span class="rc-pill">' + rdEscape(data.jml_mk_max) + ' MK</span>'
          + '<span class="rc-pill">' + rdEscape(data.sks_max) + ' SKS</span></div>';

    const mks = data.mk || [];
    if (mks.length) {
        const chips = mks.map(x => '<span class="rc-chip">' + rdEscape(x) + '</span>').join('');
        const lbl = data.status === 'disetujui' ? 'MK disetujui' : 'MK usulan';
        html += '<div class="rc-row"><span class="rc-lbl">' + lbl + '</span><span class="rc-chips">' + chips + '</span></div>';
    } else if (data.status === 'disetujui' && data.mk_disetujui) {
        html += '<div class="rc-row"><span class="rc-lbl">MK</span><span class="rc-note">' + rdEscape(data.mk_disetujui) + '</span></div>';
    }

    if (data.note) {
        const isReject = data.status === 'ditolak';
        html += '<div class="rc-row"><span class="rc-lbl">' + (isReject ? 'Alasan' : 'Catatan') + '</span>'
              + '<span class="rc-note' + (isReject ? ' ditolak' : '') + '">' + rdEscape(data.note) + '</span></div>';
    }

    document.getElementById('rdInfo').innerHTML = html;
    new bootstrap.Modal(document.getElementById('rewardDetailModal')).show();
}

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
        // Input multiple menumpuk; input tunggal (PDF bukti) mengganti file lama.
        this.files = this.input.multiple ? [...this.files, ...newFiles] : newFiles;
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

// Initialize preview managers (bukti = 1 file PDF per modal)
window.__fpm_riwayatDocs    = new FilePreviewManager('riwayatDocs',    'riwayatDocsPreview',    'doc');
window.__fpm_prestasiDocs   = new FilePreviewManager('prestasiDocs',   'prestasiDocsPreview',   'doc');

// Reset previews when modals close
['addRiwayatModal', 'addPrestasiModal'].forEach(modalId => {
    const el = document.getElementById(modalId);
    if (el) el.addEventListener('hidden.bs.modal', () => {
        const prefix = modalId === 'addRiwayatModal' ? 'riwayat' : 'prestasi';
        const mgr = window[`__fpm_${prefix}Docs`];
        if (mgr) { mgr.files = []; mgr.syncInput(); mgr.render(); }
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

// =========================================================================
// Konfirmasi Batalkan Pengajuan Reward (reuse claimConfirmModal markup)
// =========================================================================
(function() {
    const modalEl = document.getElementById('claimConfirmModal');
    if (!modalEl) return;

    const ccModal = new bootstrap.Modal(modalEl);
    const iconEl  = document.getElementById('claimConfirmIcon');
    const titleEl = document.getElementById('claimConfirmTitle');
    const textEl  = document.getElementById('claimConfirmText');
    const btnEl   = document.getElementById('claimConfirmBtn');

    const ICON_WARN = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';

    let activeFormId = null;

    window.openBatalConfirm = function(formId, namaPrestasi) {
        activeFormId = formId;
        iconEl.style.background = '#FFFBEB';
        iconEl.innerHTML = ICON_WARN;
        titleEl.textContent = 'Batalkan Pengajuan Reward';
        textEl.innerHTML = 'Batalkan pengajuan reward untuk prestasi <strong class="cc-nama"></strong>? Anda bisa mengajukan ulang nanti.';
        const namaEl = textEl.querySelector('.cc-nama');
        if (namaEl) namaEl.textContent = '"' + namaPrestasi + '"';
        btnEl.textContent = 'Ya, Batalkan';
        btnEl.style.background = '#dc2626';
        ccModal.show();
    };

    btnEl.addEventListener('click', function() {
        if (!activeFormId) return;
        const form = document.getElementById(activeFormId);
        if (form) {
            btnEl.disabled = true;
            btnEl.style.opacity = '0.65';
            btnEl.style.cursor = 'not-allowed';
            form.submit();
        }
    });

    // Reset tombol konfirmasi tiap kali modal ditutup
    modalEl.addEventListener('hidden.bs.modal', function() {
        btnEl.disabled = false;
        btnEl.style.opacity = '';
        btnEl.style.cursor = 'pointer';
    });
})();

// =========================================================================
// Modal Ajukan Reward — opsi capaian dinamis + preview jatah (SK FT 774)
// =========================================================================
(function() {
    const modalEl = document.getElementById('ajukanRewardModal');
    if (!modalEl) return;

    const arModal   = new bootstrap.Modal(modalEl);
    const form      = document.getElementById('ajukanRewardForm');
    const namaEl    = document.getElementById('arNamaPrestasi');
    const penyEl    = document.getElementById('arPenyelenggara');
    const capEl     = document.getElementById('arCapaian');
    const invWrap   = document.getElementById('arInventionWrap');
    const invEl     = document.getElementById('arInvention');
    const previewEl = document.getElementById('arJatahPreview');
    const submitBtn = document.getElementById('arSubmitBtn');

    // Picker usulan mata kuliah
    const mkWrap    = document.getElementById('arMkWrap');
    const mkSelect  = document.getElementById('arMkSelect');
    const mkAddBtn  = document.getElementById('arMkAddBtn');
    const mkChosen  = document.getElementById('arMkChosen');
    const mkCounter = document.getElementById('arMkCounter');
    const mkHidden  = document.getElementById('arMkHidden');
    const mkMaxEl   = document.getElementById('arMkMax');

    const LABELS       = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_LABELS);
    const CAP_BY_PENY  = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_BY_PENYELENGGARA);
    const JATAH        = @json($rewardJatahMap);
    const JATAH_INV    = @json($rewardJatahInvention);
    const PENY_LAINNYA = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::PENYELENGGARA_LAINNYA);
    const MK_SKS       = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::mataKuliahFlat());
    const BASE_URL     = @json(url('manajemen-mahasiswa/verifikasi'));

    let arMkList  = [];   // nama MK yang dipilih
    let arJatahOk = false;
    let arCap     = 0;    // maks jumlah MK
    let arSksMax  = 0;    // maks total SKS

    function renderMk() {
        mkChosen.innerHTML = '';
        mkHidden.innerHTML = '';
        let totalSks = 0;
        arMkList.forEach(function(name) {
            const sks = MK_SKS[name] || 0;
            totalSks += sks;
            const chip = document.createElement('span');
            chip.className = 'mk-chip';
            chip.appendChild(document.createTextNode(name + ' '));
            const s = document.createElement('span');
            s.className = 'mk-sks';
            s.textContent = '(' + sks + ' SKS)';
            chip.appendChild(s);
            const rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'mk-remove';
            rm.dataset.mk = name;
            rm.innerHTML = '&times;';
            chip.appendChild(rm);
            mkChosen.appendChild(chip);
            const hid = document.createElement('input');
            hid.type = 'hidden';
            hid.name = 'reward_mk_diajukan[]';
            hid.value = name;
            mkHidden.appendChild(hid);
        });
        const over = (arMkList.length > arCap) || (totalSks > arSksMax);
        mkCounter.className = 'mk-counter' + (over ? ' over' : '');
        mkCounter.textContent = 'Dipilih ' + arMkList.length + '/' + arCap + ' MK • Total ' + totalSks + ' SKS (maks ' + arSksMax + ')';
    }

    function currentTotalSks() {
        return arMkList.reduce(function(sum, name) { return sum + (MK_SKS[name] || 0); }, 0);
    }

    function refreshSubmit() {
        const totalSks = currentTotalSks();
        const valid = arJatahOk && arMkList.length >= 1 && arMkList.length <= arCap && totalSks <= arSksMax;
        submitBtn.disabled = !valid;
        submitBtn.style.opacity = valid ? '1' : '.5';
        submitBtn.style.cursor = valid ? 'pointer' : 'not-allowed';
    }

    mkAddBtn.addEventListener('click', function() {
        const v = mkSelect.value;
        if (!v) return;
        if (arMkList.indexOf(v) !== -1) { mkSelect.value = ''; return; }
        if (arMkList.length >= arCap) return;   // jumlah MK sudah penuh
        if (currentTotalSks() + (MK_SKS[v] || 0) > arSksMax) return;   // melebihi plafon SKS
        arMkList.push(v);
        mkSelect.value = '';
        renderMk();
        refreshSubmit();
    });

    mkChosen.addEventListener('click', function(e) {
        const btn = e.target.closest('.mk-remove');
        if (!btn) return;
        const name = btn.dataset.mk;
        arMkList = arMkList.filter(function(x) { return x !== name; });
        renderMk();
        refreshSubmit();
    });

    window.openAjukanReward = function(prestasiId, namaPrestasi) {
        form.action = BASE_URL + '/prestasi/' + prestasiId + '/reward/ajukan';
        namaEl.textContent = namaPrestasi;
        penyEl.value = '';
        capEl.innerHTML = '<option value="">Pilih penyelenggara dulu...</option>';
        capEl.disabled = true;
        invWrap.style.display = 'none';
        invEl.checked = false;
        previewEl.style.display = 'none';
        arMkList = [];
        arJatahOk = false;
        arCap = 0;
        arSksMax = 0;
        mkWrap.style.display = 'none';
        mkSelect.value = '';
        renderMk();
        submitBtn.disabled = true;
        arModal.show();
    };

    function rebuildCapaian() {
        const peny = penyEl.value;
        capEl.innerHTML = '<option value="">Pilih capaian...</option>';
        if (!peny || !CAP_BY_PENY[peny]) {
            capEl.disabled = true;
        } else {
            CAP_BY_PENY[peny].forEach(function(code) {
                const opt = document.createElement('option');
                opt.value = code;
                opt.textContent = LABELS[code] || code;
                capEl.appendChild(opt);
            });
            capEl.disabled = false;
        }
        invWrap.style.display = (peny === PENY_LAINNYA) ? 'block' : 'none';
        if (peny !== PENY_LAINNYA) invEl.checked = false;
    }

    function updatePreview() {
        const peny = penyEl.value;
        const cap  = capEl.value;
        let jatah = null;
        if (peny && cap) {
            if (peny === PENY_LAINNYA && invEl.checked) {
                jatah = JATAH_INV;
            } else {
                jatah = (JATAH[peny] && JATAH[peny][cap]) ? JATAH[peny][cap] : null;
            }
        }
        if (jatah) {
            previewEl.innerHTML = 'Jatah reward: maksimal <strong>' + jatah.jml_mk_max + ' mata kuliah</strong> dengan total <strong>' + jatah.sks_max + ' SKS</strong> (nilai dinaikkan satu tingkat).';
            previewEl.style.display = 'block';
            arJatahOk = true;
            arCap     = jatah.jml_mk_max;
            arSksMax  = jatah.sks_max;
            mkMaxEl.textContent = arCap;
            mkWrap.style.display = 'block';
            // bila jatah mengecil (ganti kategori), pangkas pilihan yang melebihi
            if (arMkList.length > arCap) arMkList = arMkList.slice(0, arCap);
            renderMk();
        } else {
            previewEl.style.display = 'none';
            arJatahOk = false;
            arCap = 0;
            arSksMax = 0;
            mkWrap.style.display = 'none';
        }
        refreshSubmit();
    }

    penyEl.addEventListener('change', function() { rebuildCapaian(); updatePreview(); });
    capEl.addEventListener('change', updatePreview);
    invEl.addEventListener('change', updatePreview);
})();

// =========================================================================
// Anti Double-Submit — cegah form di-submit lebih dari sekali
// =========================================================================
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('button[data-submit-once]');
        if (!btn) return;

        // Jika sudah pernah di-submit sebelumnya, tolak
        if (this.dataset.submitted === 'true') {
            e.preventDefault();
            return;
        }

        // Tandai form sudah di-submit
        this.dataset.submitted = 'true';

        // Disable tombol dan tampilkan loading state
        btn.disabled = true;
        btn.style.opacity = '0.65';
        btn.style.cursor = 'not-allowed';
        btn.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 style="animation: spin 1s linear infinite;">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            Mengajukan...
        `;
    });
});

// Tambahkan CSS animasi spin
(function() {
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
})();

// Reset form state ketika modal ditutup (agar bisa digunakan lagi jika dibuka ulang)
['addRiwayatModal', 'addPrestasiModal'].forEach(modalId => {
    const el = document.getElementById(modalId);
    if (el) el.addEventListener('hidden.bs.modal', () => {
        const form = el.querySelector('form');
        if (form) {
            delete form.dataset.submitted;
            const btn = form.querySelector('button[data-submit-once]');
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '';
                btn.style.cursor = '';
                btn.innerHTML = 'Ajukan';
            }
        }
    });
});
</script>
</x-dynamic-component>
