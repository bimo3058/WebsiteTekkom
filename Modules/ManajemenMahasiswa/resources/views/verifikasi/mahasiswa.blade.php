<x-dynamic-component :component="$layout">

    <style>
        /* ── Header ── */
        .verif-header {
            margin-bottom: 24px;
        }

        .verif-header h4 {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--c-fg);
            margin-bottom: 2px;
            letter-spacing: -.02em;
        }

        .verif-header p {
            color: var(--c-fg-muted);
            font-size: .82rem;
        }

        /* ── Status & Buttons ── */
        .status-verif {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 50px;
            font-size: .73rem;
            font-weight: 600;
        }

        .status-verif.pending {
            background: #FFFBEB;
            color: #d97706;
        }

        .status-verif.approved {
            background: #ECFDF5;
            color: #059669;
        }

        .status-verif.rejected {
            background: var(--c-error-subtle, #fef2f2);
            color: var(--c-error, #dc2626);
        }

        .btn-submit {
            background: #0B266E;
            color: #fff;
            font-weight: 600;
            font-size: .85rem;
            padding: 9px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background .15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none !important;
            white-space: nowrap;
        }

        .btn-submit:hover {
            background: #091958;
            color: #fff;
        }

        .btn-submit:disabled,
        .btn-submit[disabled] {
            background: #C1C7CF;
            color: #fff;
            cursor: not-allowed;
            opacity: .7;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: var(--c-fg-muted);
        }

        .empty-state .empty-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
            color: #E5E7EB;
        }

        /* ── Form Controls ── */
        .form-label-custom {
            font-weight: 600;
            font-size: .87rem;
            color: var(--c-fg-sec);
            margin-bottom: 6px;
        }

        .form-control-custom,
        .form-select-custom {
            border: 1.5px solid #B6BCC6;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .87rem;
            font-weight: 500;
            color: var(--c-fg-sec);
            transition: all .2s;
            background: #F1F3F5;
        }

        .form-control-custom:hover,
        .form-select-custom:hover {
            border-color: var(--c-primary-border);
            background: #EDEFF2;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: var(--c-primary);
            box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            outline: none;
            background: #fff;
        }

        .tingkat-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 50px;
            font-size: .73rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .tingkat-badge.internasional {
            background: #FFFBEB;
            color: #92400e;
        }

        .tingkat-badge.nasional {
            background: #dbeafe;
            color: #1e40af;
        }

        .tingkat-badge.regional {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .tingkat-badge.universitas {
            background: #ECFDF5;
            color: #059669;
        }

        .tingkat-badge.prodi {
            background: #eef2ff;
            color: var(--c-primary);
        }

        /* ── Reward Badge & Button ── */
        .claim-badge {
            font-size: .73rem;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
        }

        .claim-badge.belum {
            background: #f3f4f6;
            color: var(--c-fg-muted);
        }

        .claim-badge.diajukan {
            background: #dbeafe;
            color: #1e40af;
        }

        .claim-badge.disetujui {
            background: #ECFDF5;
            color: #059669;
        }

        .claim-badge.ditolak {
            background: var(--c-error-subtle, #fef2f2);
            color: var(--c-error, #dc2626);
        }

        /* Tombol sekunder di dalam modal (picker mata kuliah), bukan aksi baris. */
        .btn-claim {
            background: var(--c-primary-subtle);
            color: var(--c-primary);
            border: 1px solid rgba(11, 38, 110, 0.18);
            padding: 5px 14px;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-claim:hover {
            background: rgba(11, 38, 110, 0.12);
            border-color: var(--c-primary-border);
        }

        /* Kolom Reward memuat status saja; semua aksi baris berkumpul di kolom Aksi,
       sama seperti halaman admin (verifikasi/reward.blade.php). Rel ini rata
       kanan: tombol langkah maju di kiri, Tinjau selalu paling kanan, jadi
       tombol Tinjau segaris di semua baris. Kerangka modal (.tp-*) & .btn-tinjau
       ada di partials/tinjau-modal-styles.blade.php — dipakai bersama admin. */
        .aksi-rail {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Satu-satunya tombol berlatar pekat dalam sebuah baris: navy mengikuti tombol
       primer halaman Superadmin. Hierarkinya dibawa oleh bobot latar (pekat vs
       tipis berbingkai), bukan dengan melepas label Tinjau — labelnya tetap ada
       supaya seragam dengan tabel Riwayat Kegiatan dan halaman admin. */
        .btn-aksi-utama {
            background: var(--c-primary, #0B266E);
            color: #fff;
            border: 1px solid var(--c-primary, #0B266E);
            padding: 6px 14px;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-aksi-utama:hover {
            background: #081D55;
            border-color: #081D55;
        }

        /* Kuota habis: tombolnya tetap ada supaya kolom Aksi tidak berubah bentuk,
       tapi kelabu — langkah itu memang sudah tidak bisa diambil. */
        .btn-aksi-utama:disabled {
            background: #C1C7CF;
            border-color: #C1C7CF;
            cursor: not-allowed;
        }

        .btn-aksi-utama:disabled:hover {
            background: #C1C7CF;
            border-color: #C1C7CF;
        }

        .jatah-preview {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--c-primary-subtle);
            border: 1px solid rgba(11, 38, 110, 0.18);
            font-size: .87rem;
            color: var(--c-primary);
        }

        .jatah-preview strong {
            font-weight: 700;
        }

        .kuota-info {
            font-size: .8rem;
            color: var(--c-fg-muted);
            background: #fafafa;
            border: 1px solid var(--c-border);
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .kuota-info b {
            color: var(--c-primary);
        }

        /* Baris kedua banner: keterangan di kiri, pemicu rincian di kanan.
       Memakai ruang mendatar yang memang kosong, bukan menambah tinggi. */
        .kuota-info-baris {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        /* Rincian kuota tinggal di dalam modal, bukan menumpuk di atas tabel:
       di modul ini detail selalu dibaca lewat modal (pemicunya .btn-tinjau),
       jadi angka "2/2" tetap bisa diperiksa tanpa mendorong tabel keluar
       layar. Isinya memakai kelas yang sudah ada — .tinjau-info, .kuota-pill,
       .kuota-dipakai-*, .tp-chip — supaya tidak lahir pola baru. */
        .kuota-grup+.kuota-grup {
            margin-top: 18px;
        }

        .kuota-grup-judul {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 7px;
        }

        .kuota-grup-judul .tp-pane-heading {
            margin: 0;
        }

        .aturan-box {
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }

        .aturan-box-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .87rem;
            font-weight: 700;
            color: var(--c-fg);
            margin-bottom: 10px;
        }

        /* Picker usulan mata kuliah (reward) */
        .mk-chosen {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .mk-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--c-primary);
            background: var(--c-primary-subtle);
            border: 1px solid rgba(11, 38, 110, 0.18);
            border-radius: 50px;
            padding: 4px 6px 4px 12px;
        }

        .mk-chip .mk-sks {
            font-weight: 500;
            color: var(--c-primary-border);
        }

        .mk-chip .mk-remove {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(11, 38, 110, 0.18);
            color: var(--c-primary);
            border: none;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .mk-chip .mk-remove:hover {
            background: var(--c-primary-border);
        }

        .mk-counter {
            font-size: .8rem;
            font-weight: 600;
            color: var(--c-fg-muted);
            margin-top: 8px;
        }

        .mk-counter.over {
            color: var(--c-error);
        }

        .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .18);
        }

        .modal-header {
            border-bottom: 1px solid #f3f4f6;
            padding: 18px 22px;
        }

        .modal-header .modal-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--c-fg);
        }

        .modal-body {
            padding: 22px;
        }

        .modal-footer {
            border-top: 1px solid #f3f4f6;
            padding: 14px 22px;
        }

        /* Preview styles */
        .preview-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .preview-item {
            position: relative;
            width: 100px;
            text-align: center;
        }

        .preview-item img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--c-border);
            cursor: pointer;
            transition: opacity .15s;
        }

        .preview-item img:hover {
            opacity: 0.8;
        }

        .preview-item .preview-name {
            font-size: .68rem;
            color: var(--c-fg-muted);
            margin-top: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .preview-item .preview-remove {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--c-error);
            color: #fff;
            border: 2px solid #fff;
            font-size: .72rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            z-index: 2;
        }

        .preview-item .preview-remove:hover {
            background: #b91c1c;
        }

        .doc-preview-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .doc-preview-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #fafafa;
            border: 1px solid var(--c-border);
            border-radius: 10px;
            font-size: .87rem;
        }

        .doc-preview-item .doc-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: .68rem;
            color: #fff;
            flex-shrink: 0;
        }

        .doc-preview-item .doc-icon.pdf {
            background: var(--c-error);
        }

        .doc-preview-item .doc-icon.doc {
            background: #2563eb;
        }

        .doc-preview-item .doc-icon.xls {
            background: #16a34a;
        }

        .doc-preview-item .doc-icon.ppt {
            background: #ea580c;
        }

        .doc-preview-item .doc-icon.other {
            background: var(--c-fg-muted);
        }

        .doc-preview-item .doc-name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: var(--c-fg-sec);
        }

        .doc-preview-item .doc-remove {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--c-error-subtle);
            color: var(--c-error);
            border: 1px solid #fecaca;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .doc-preview-item .doc-remove:hover {
            background: #fee2e2;
        }

        /* Lightbox */
        .lightbox-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .lightbox-overlay.active {
            display: flex;
        }

        .lightbox-overlay img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 14px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            cursor: default;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #fff;
            font-size: 36px;
            font-weight: 300;
            cursor: pointer;
            z-index: 100000;
            line-height: 1;
        }

        .lightbox-close:hover {
            color: var(--c-border);
        }

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

    @include('manajemenmahasiswa::verifikasi.partials.tinjau-modal-styles')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div
        style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
        <div>
            @if($tab === 'prestasi')
                <h4
                    style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">
                    Prestasi Saya</h4>
                <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Ajukan prestasi lomba untuk diverifikasi
                    admin. Prestasi yang sudah disetujui bisa Anda ajukan rewardnya (konversi nilai mata kuliah, SK FT 774).
                </p>
            @else
                <h4
                    style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">
                    Riwayat Kegiatan Saya</h4>
                <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Ajukan riwayat keikutsertaan kegiatan untuk
                    diverifikasi admin.</p>
            @endif
        </div>
    </div>
    @php
        $canSubmit = auth()->user()->hasAnyRole([
            'mahasiswa',
            'pengurus_himpunan',
            'ketua_himpunan',
            'ketua_bidang',
            'ketua_unit',
            'staff_himpunan',
            'superadmin',
            'admin',
            'admin_kemahasiswaan'
        ]);
    @endphp

    @if($tab === 'riwayat')
        <!-- Riwayat Kegiatan - Global Style Table Card -->
        <div
            style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom:18px;">
            <!-- Table Toolbar -->
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
                <h2
                    style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; display:flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="16" x2="16" y1="2" y2="6"></line>
                        <line x1="8" x2="8" y1="2" y2="6"></line>
                        <line x1="3" x2="21" y1="10" y2="10"></line>
                    </svg>
                    Riwayat Kegiatan
                </h2>
                @if($canSubmit)
                    <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addRiwayatModal">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Ajukan Riwayat Kegiatan
                    </button>
                @endif
            </div>

            @if($riwayatData->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:600px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                                <th
                                    style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">
                                    No</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:200px;">
                                    Nama Kegiatan</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Peran</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Tanggal</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Status</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:120px;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatData as $i => $rw)
                                @php
                                    // Payload modal Tinjau — modal yang sama dengan halaman admin,
                                    // hanya tanpa panel keputusan dan tanpa identitas mahasiswa
                                    // (di sini semua barisnya memang milik yang sedang login).
                                    $rwDiputus = $rw->verification_status !== 'pending';
                                    $tinjauRiwayatPayload = [
                                        'judul' => 'Detail Pengajuan Kegiatan',
                                        'pending' => !$rwDiputus,
                                        'readonly' => $rwDiputus
                                            ? 'Pengajuan ini sudah diverifikasi admin.'
                                            : 'Pengajuan ini masih menunggu verifikasi admin.',
                                        'sections' => [
                                            [
                                                'judul' => 'Data yang Anda ajukan',
                                                'items' => array_values(array_filter([
                                                    ['Kegiatan', $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui'],
                                                    ['Peran', $rw->peran_manual ?? ucfirst($rw->peran ?? '')],
                                                    ['Tanggal', $rw->tanggal_kegiatan?->translatedFormat('d M Y')],
                                                    ['Diajukan', $rw->created_at?->translatedFormat('d M Y, H:i')],
                                                    // Badge memakai kelas yang sama dengan kolom Status di tabel,
                                                    // jadi statusnya terbaca sama di daftar maupun di modal.
                                                    [
                                                        'Status',
                                                        $rwDiputus
                                                        ? ($rw->verification_status === 'approved' ? 'Disetujui' : 'Ditolak')
                                                        : 'Menunggu Persetujuan',
                                                        'status-verif ' . $rw->verification_status
                                                    ],
                                                    $rwDiputus && $rw->verified_at ? ['Diverifikasi', $rw->verified_at->translatedFormat('d M Y, H:i')] : null,
                                                    $rwDiputus && $rw->verification_note ? ['Catatan', $rw->verification_note] : null,
                                                ])),
                                            ]
                                        ],
                                        'bukti' => ($rw->buktiFiles ?? collect())->map(fn($b) => [
                                            'url' => $b->public_url,
                                            'nama' => $b->nama_file,
                                            'is_image' => $b->isImage(),
                                        ])->values()->all(),
                                    ];
                                @endphp
                                <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:14px 12px; font-size:13px; color:var(--c-fg-muted); width:48px;">{{ $i + 1 }}
                                    </td>
                                    <td style="padding:14px 16px; min-width:200px;">
                                        <p
                                            style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:260px;">
                                            {{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui' }}</p>
                                    </td>
                                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-sec);">
                                        {{ $rw->peran_manual ?? ucfirst($rw->peran ?? '') }}</td>
                                    <td style="padding:14px 16px; white-space:nowrap;">
                                        @if($rw->tanggal_kegiatan)
                                            <span
                                                style="font-size:12px; font-weight:500; color:var(--c-fg-sec);">{{ $rw->tanggal_kegiatan->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;">
                                        {{-- Badge saja — catatan verifikasi dibaca utuh di modal Tinjau,
                                        bukan sebagai potongan kalimat di dalam sel ini. --}}
                                        <span class="status-verif {{ $rw->verification_status }}">
                                            @if($rw->verification_status === 'pending') Menunggu Persetujuan
                                            @elseif($rw->verification_status === 'approved') Disetujui
                                            @else Ditolak
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding:14px 16px; text-align:center;">
                                        {{-- Satu pintu masuk seperti halaman admin: bukti, tanggal, dan
                                        catatan verifikasi semuanya dibuka dari sini. --}}
                                        <button type="button" class="btn-tinjau" onclick="openTinjau(@js($tinjauRiwayatPayload))">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Tinjau
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;"
                            stroke="currentColor" stroke-width="1.5">
                            <path d="M21 8V21H3V8"></path>
                            <path d="M23 3H1v5h22V3z"></path>
                            <path d="M10 12h4"></path>
                        </svg></div>
                    <p
                        style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">
                        Belum ada riwayat kegiatan</p>
                    <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Riwayat kegiatan yang Anda
                        ajukan akan muncul di sini</p>
                </div>
            @endif
        </div>
    @endif

    @if($tab === 'prestasi')
        <!-- Prestasi Lomba - Global Style Table Card -->
        <div
            style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom:18px;">
            <!-- Table Toolbar -->
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
                <h2
                    style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; display:flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                    Prestasi Lomba
                </h2>
                @if($canSubmit)
                    <button class="btn-submit" data-bs-toggle="modal" data-bs-target="#addPrestasiModal">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Ajukan Prestasi
                    </button>
                @endif
            </div>

            <!-- Info & Aturan (inside card, above table) -->
            @php
                $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class;
                // Kedua grup penuh = tidak ada pilihan kategori apa pun yang masih bisa
                // lolos, jadi tombol Ajukan Reward di kolom Aksi boleh dikelabukan.
                // Kalau hanya salah satu yang penuh, grupnya baru ketahuan setelah
                // mahasiswa memilih kategori — rambunya menyusul di dalam modal.
                $kuotaSemuaPenuh = true;
                foreach ($P::KUOTA_MAKS as $grupCek => $maksCek) {
                    if (($kuota[$grupCek] ?? 0) < $maksCek) {
                        $kuotaSemuaPenuh = false;
                        break;
                    }
                }
                $kuotaAdaIsi = collect($kuotaDipakai ?? [])->flatten(1)->isNotEmpty();
            @endphp
            <div style="padding:14px 16px; border-bottom:1px solid #e5e7eb;">
                <div class="kuota-info" style="margin-bottom:10px;">
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <span>Kuota reward Anda (SK FT 774):</span>
                        @foreach($P::KUOTA_MAKS as $grup => $maks)
                            @php $pakai = $kuota[$grup] ?? 0; @endphp
                            <span class="kuota-pill {{ $pakai >= $maks ? 'penuh' : '' }}">
                                {{ $P::KUOTA_LABELS[$grup] }} {{ $pakai }}/{{ $maks }}{{ $pakai >= $maks ? ' — penuh' : '' }}
                            </span>
                        @endforeach
                    </div>
                    <div class="kuota-info-baris">
                        <span>Reward = peningkatan nilai mata kuliah; mata kuliah final ditetapkan departemen.</span>

                        {{-- Pemicu rincian. Bentuknya .btn-tinjau — di modul ini tombol
                        pembuka modal detail selalu berbentuk itu, dan rinciannya tidak
                        boleh menambah tinggi banner karena tabelnya tepat di bawah. --}}
                        @if($kuotaAdaIsi)
                            <button type="button" class="btn-tinjau" data-bs-toggle="modal"
                                data-bs-target="#rincianKuotaModal">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="8" y1="6" x2="21" y2="6" />
                                    <line x1="8" y1="12" x2="21" y2="12" />
                                    <line x1="8" y1="18" x2="21" y2="18" />
                                    <line x1="3" y1="6" x2="3.01" y2="6" />
                                    <line x1="3" y1="12" x2="3.01" y2="12" />
                                    <line x1="3" y1="18" x2="3.01" y2="18" />
                                </svg>
                                Rincian kuota
                            </button>
                        @endif
                    </div>
                </div>

            </div>

            @if($prestasiData->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:920px;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                                <th
                                    style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">
                                    No</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">
                                    Nama Prestasi</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Tingkat</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Tanggal</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Status Verifikasi</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">
                                    Reward</th>
                                {{-- Rata kanan: tombol Tinjau jadi elemen paling kanan di setiap baris,
                                sehingga kolomnya membentuk satu rel lurus meski lebar tombol
                                utamanya berbeda-beda (atau tidak ada sama sekali). --}}
                                <th
                                    style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:290px;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prestasiData as $i => $p)
                                @php
                                    $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class;
                                    $pDiputus = $p->verification_status !== 'pending';
                                    $pDisetujui = $p->verification_status === 'approved';
                                    $pBukti = ($p->buktiFiles ?? collect())->map(fn($b) => [
                                        'url' => $b->public_url,
                                        'nama' => $b->nama_file,
                                        'is_image' => $b->isImage(),
                                    ])->values()->all();

                                    // Satu baris = satu prestasi, jadi satu tempat melihatnya: blok data
                                    // pengajuan dan blok klaim rewardnya berada di modal yang sama.
                                    $blokPengajuan = [
                                        'judul' => 'Data yang Anda ajukan',
                                        'items' => array_values(array_filter([
                                            ['Prestasi', $p->nama_prestasi],
                                            ['Tingkat', ucfirst($p->tingkat)],
                                            ['Tgl Raih', $p->tanggal?->translatedFormat('d M Y')],
                                            ['Diajukan', $p->created_at?->translatedFormat('d M Y, H:i')],
                                            // Badge memakai kelas yang sama dengan kolom Status di tabel,
                                            // jadi statusnya terbaca sama di daftar maupun di modal.
                                            [
                                                'Status',
                                                $pDiputus
                                                ? ($pDisetujui ? 'Disetujui' : 'Ditolak')
                                                : 'Menunggu Persetujuan',
                                                'status-verif ' . $p->verification_status
                                            ],
                                            $pDiputus && $p->verified_at ? ['Diverifikasi', $p->verified_at->translatedFormat('d M Y, H:i')] : null,
                                            $pDiputus && $p->verification_note ? ['Catatan', $p->verification_note] : null,
                                        ])),
                                    ];

                                    // Blok reward hanya ada setelah prestasinya disetujui — sama persis
                                    // dengan syarat munculnya isi kolom Reward di tabel.
                                    $mkUsulan = $p->reward_mk_diajukan ?? [];
                                    // Nama kelas badge mengikuti .claim-badge di tabel, bukan nilai
                                    // statusnya — 'belum_ajukan' tidak punya padanan kelas.
                                    $rewardKelas = match ($p->reward_status) {
                                            $P::CLAIM_DIAJUKAN => 'diajukan',
                                            $P::CLAIM_DISETUJUI => 'disetujui',
                                            $P::CLAIM_DITOLAK => 'ditolak',
                                        default => 'belum',
                                    };
                                    $blokReward = $pDisetujui ? [
                                        'judul' => 'Reward (SK FT 774)',
                                        'items' => array_values(array_filter([
                                            ['Status', $p->reward_status_label, 'claim-badge ' . $rewardKelas],
                                            $p->reward_penyelenggara_label ? ['Penyelenggara', $p->reward_penyelenggara_label] : null,
                                            $p->reward_capaian_label ? [
                                                'Capaian',
                                                $p->reward_capaian_label
                                                . ($p->reward_is_invention ? ' · invention/expo/fair' : '')
                                            ] : null,
                                            $p->reward_jml_mk_max ? ['Jatah', $p->reward_jml_mk_max . ' MK · maks ' . $p->reward_sks_max . ' SKS'] : null,
                                            count($mkUsulan) ? [($p->reward_status === $P::CLAIM_DISETUJUI ? 'MK disetujui' : 'MK usulan'), $mkUsulan] : null,
                                            (!count($mkUsulan) && $p->reward_mk_disetujui) ? ['MK disetujui', $p->reward_mk_disetujui] : null,
                                            $p->reward_note ? [($p->reward_status === $P::CLAIM_DITOLAK ? 'Alasan' : 'Catatan'), $p->reward_note] : null,
                                            $p->reward_status === $P::CLAIM_BELUM_AJUKAN
                                            ? ['Keterangan', 'Reward belum diajukan. Tekan "Ajukan Reward" pada kolom Aksi.']
                                            : null,
                                        ])),
                                    ] : null;

                                    // Payload modal Tinjau — modal yang sama dengan halaman admin,
                                    // hanya tanpa panel keputusan dan tanpa identitas mahasiswa
                                    // (di sini semua barisnya memang milik yang sedang login).
                                    $tinjauPrestasiPayload = [
                                        'judul' => 'Detail Pengajuan Prestasi',
                                        'pending' => !$pDiputus,
                                        'readonly' => $pDiputus
                                            ? 'Pengajuan ini sudah diverifikasi admin.'
                                            : 'Pengajuan ini masih menunggu verifikasi admin.',
                                        'sections' => array_values(array_filter([$blokPengajuan, $blokReward])),
                                        'bukti' => $pBukti,
                                        // Aksi mundur diletakkan di bawah datanya, bukan di baris tabel
                                        'aksi' => $p->reward_status === $P::CLAIM_DIAJUKAN ? [
                                            'label' => 'Batalkan Pengajuan Reward',
                                            'gaya' => 'tolak',
                                            'panggil' => 'openBatalConfirm',
                                            'args' => ['batalRewardForm' . $p->id, $p->nama_prestasi],
                                        ] : null,
                                    ];

                                    // Payload modal Ajukan/Ajukan Ulang Reward — kerangkanya sama dengan
                                    // modal Tinjau (bukti di kiri, data di kanan), isinya formulir.
                                    $ajukanRewardPayload = [
                                        'id' => $p->id,
                                        'judul' => $p->reward_status === $P::CLAIM_DITOLAK
                                            ? 'Ajukan Ulang Reward Prestasi'
                                            : 'Ajukan Reward Prestasi',
                                        'sections' => [$blokPengajuan],
                                        'bukti' => $pBukti,
                                    ];

                                    // Langkah maju yang benar-benar bisa diambil pada baris ini. Dipakai
                                    // kolom Aksi; labelnya menyebut "Reward" karena tombolnya sudah tidak
                                    // berada di bawah header kolom Reward lagi.
                                    $rewardBisaDiajukan = $pDisetujui
                                        && !($isAlumni ?? false)
                                        && !in_array($p->reward_status, [$P::CLAIM_DIAJUKAN, $P::CLAIM_DISETUJUI], true);
                                    $rewardLabelAksi = $p->reward_status === $P::CLAIM_DITOLAK
                                        ? 'Ajukan Ulang Reward'
                                        : 'Ajukan Reward';
                                @endphp
                                <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:14px 12px; font-size:13px; color:var(--c-fg-muted); width:48px;">{{ $i + 1 }}
                                    </td>
                                    <td style="padding:14px 16px; min-width:180px;">
                                        <p
                                            style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:220px;">
                                            {{ $p->nama_prestasi }}</p>
                                    </td>
                                    <td style="padding:14px 16px;"><span
                                            class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                                    <td style="padding:14px 16px; white-space:nowrap;">
                                        @if($p->tanggal)
                                            <span
                                                style="font-size:12px; font-weight:500; color:var(--c-fg-sec);">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px;">
                                        {{-- Badge saja — catatan verifikasi dibaca utuh di modal Tinjau,
                                        bukan sebagai potongan kalimat di dalam sel ini. --}}
                                        <span class="status-verif {{ $p->verification_status }}">
                                            @if($p->verification_status === 'pending') Menunggu Persetujuan
                                            @elseif($p->verification_status === 'approved') Disetujui
                                            @else Ditolak
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding:14px 16px;">
                                        {{-- Kolom status murni, sebangun dengan kolom Status Verifikasi di
                                        sebelahnya: badge saja, tinggi barisnya ikut rata. Tombolnya
                                        pindah ke kolom Aksi. --}}
                                        @if($pDisetujui)
                                            @if($p->reward_status === $P::CLAIM_DIAJUKAN)
                                                <span class="claim-badge diajukan">Menunggu persetujuan</span>
                                                {{-- Tombol Batalkan tinggal di dalam modal Tinjau; formnya
                                                tetap di sini karena satu form milik satu baris. --}}
                                                <form method="POST" id="batalRewardForm{{ $p->id }}"
                                                    action="{{ route('manajemenmahasiswa.verifikasi.prestasi.reward.batal', $p->id) }}"
                                                    style="display:none;">
                                                    @csrf @method('PATCH')
                                                </form>
                                            @elseif($p->reward_status === $P::CLAIM_DISETUJUI)
                                                <span class="claim-badge disetujui">Reward disetujui</span>
                                            @elseif($p->reward_status === $P::CLAIM_DITOLAK)
                                                <span class="claim-badge ditolak">Reward ditolak</span>
                                            @else
                                                <span class="claim-badge belum">Belum diajukan</span>
                                            @endif
                                        @else
                                            <span style="color: #d1d5db;">—</span>
                                            <div style="font-size: 11px; color: var(--c-fg-muted); margin-top: 4px;">Tersedia setelah
                                                disetujui</div>
                                        @endif
                                    </td>
                                    <td style="padding:14px 16px; text-align:right;">
                                        {{-- Semua aksi baris berkumpul di sini, seperti halaman admin.
                                        Langkah maju (kalau ada) jadi satu-satunya tombol navy pekat;
                                        Tinjau — pintu masuk ke bukti, tanggal, catatan verifikasi, dan
                                        pembatalan reward — tetap berlabel dengan gaya yang sama persis
                                        seperti di tabel Riwayat Kegiatan dan halaman admin. --}}
                                        <div class="aksi-rail">
                                            @if($rewardBisaDiajukan)
                                                @if($kuotaSemuaPenuh)
                                                    {{-- Kedua kelompok kuota habis: kategori apa pun yang
                                                    dipilih pasti tertahan, jadi jangan biarkan mahasiswa
                                                    mengisi formulir lalu menunggu penolakan. --}}
                                                    <button type="button" class="btn-aksi-utama" disabled
                                                        title="Kuota reward Anda sudah penuh untuk kedua kelompok. Buka &quot;Rincian kuota&quot; di atas tabel untuk melihat prestasi mana yang memakainya.">
                                                        Kuota reward penuh
                                                    </button>
                                                @else
                                                    <button type="button" class="btn-aksi-utama"
                                                        onclick="openAjukanReward(@js($ajukanRewardPayload))">{{ $rewardLabelAksi }}</button>
                                                @endif
                                            @endif
                                            <button type="button" class="btn-tinjau"
                                                onclick="openTinjau(@js($tinjauPrestasiPayload))">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                Tinjau
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;"
                            stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg></div>
                    <p
                        style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">
                        Belum ada prestasi lomba</p>
                    <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Prestasi lomba yang Anda ajukan
                        akan muncul di sini</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Modal Tinjau — kerangka & perilakunya sama persis dengan halaman admin
    (partial yang sama), hanya tanpa panel keputusan: mahasiswa membaca, tidak
    memutus. Sejak kolom Bukti dihapus dari tabel, berkas bukti dilihat di sini. --}}
    @include('manajemenmahasiswa::verifikasi.partials.tinjau-modal', ['tinjauAksi' => false])

    {{-- Modal Rincian Kuota Reward — isi dari banner kuota di atas tabel.
    Ditaruh di modal, bukan dibentang inline, karena tabel Prestasi berada tepat
    di bawah banner: rincian yang dibuka inline akan mendorong tabel itu keluar
    layar, padahal rinciannya dibaca justru untuk dibandingkan dengan tabel. --}}
    @if($tab === 'prestasi' && ($kuotaAdaIsi ?? false))
        <div class="modal fade" id="rincianKuotaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="vertical-align:-3px;">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                            Rincian Kuota Reward
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:11.5px; color:var(--c-fg-muted); margin:0 0 16px;">
                            Dasar aturan: {{ $P::SK_BERLAKU }}
                        </p>

                        {{-- Kedua kelompok selalu tampil, termasuk yang belum terpakai:
                        kuota yang masih utuh adalah jawaban yang sama pentingnya. --}}
                        @foreach($P::KUOTA_MAKS as $grup => $maks)
                            @php $daftar = $kuotaDipakai[$grup] ?? []; @endphp
                            <div class="kuota-grup">
                                <div class="kuota-grup-judul">
                                    <p class="tp-pane-heading">Kelompok {{ $P::KUOTA_LABELS[$grup] }}</p>
                                    <span class="kuota-pill {{ count($daftar) >= $maks ? 'penuh' : '' }}">
                                        {{ count($daftar) }}/{{ $maks }}{{ count($daftar) >= $maks ? ' — penuh' : '' }}
                                    </span>
                                </div>

                                @if(count($daftar))
                                    <div class="tinjau-info">
                                        @foreach($daftar as $k)
                                            <div class="kuota-dipakai-item">
                                                <div class="kuota-dipakai-nama">{{ $k['nama'] }}</div>
                                                @if(!empty($k['mk_list']))
                                                    <div class="tp-chips" style="margin-top:5px;">
                                                        @foreach($k['mk_list'] as $mk)
                                                            <span class="tp-chip">{{ $mk }}</span>
                                                        @endforeach
                                                    </div>
                                                @elseif($k['mk'])
                                                    <div class="kuota-dipakai-ket">Mata kuliah: {{ $k['mk'] }}</div>
                                                @endif
                                                @if($k['tanggal'])
                                                    <div class="kuota-dipakai-ket">Disetujui {{ $k['tanggal'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="tinjau-info" style="color:var(--c-fg-muted);">
                                        Belum terpakai — jatah kelompok ini masih utuh.
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <p style="font-size:11px; color:var(--c-fg-muted); line-height:1.5; margin:16px 0 0;">
                            Ini mata kuliah yang nilainya sudah dinaikkan lewat reward prestasi — bukan daftar mata
                            kuliah yang Anda ambil di KRS.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ajukan Riwayat -->
    <div class="modal fade" id="addRiwayatModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.riwayat.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Ajukan Riwayat Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label-custom mb-0">Nama Kegiatan <span
                                        style="color: #dc2626;">*</span></label>
                                <span class="text-muted" style="font-size: 11px;" id="charCount_nama_kegiatan_manual">0
                                    / 50 huruf</span>
                            </div>
                            <input type="text" name="nama_kegiatan_manual" class="form-control form-control-custom"
                                required maxlength="50" placeholder="Contoh: Lomba Debat Nasional 2026"
                                oninput="document.getElementById('charCount_nama_kegiatan_manual').innerText = this.value.length + ' / 50 huruf'">
                            <small class="text-muted" style="font-size: 11px;">Ketik nama kegiatan yang pernah Anda
                                ikuti</small>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label-custom mb-0">Peran <span
                                        style="color: #dc2626;">*</span></label>
                                <span class="text-muted" style="font-size: 11px;" id="charCount_peran_manual">0 / 50
                                    huruf</span>
                            </div>
                            <input type="text" name="peran_manual" class="form-control form-control-custom" required
                                maxlength="50" placeholder="Contoh: Peserta, Delegasi, Koordinator"
                                oninput="document.getElementById('charCount_peran_manual').innerText = this.value.length + ' / 50 huruf'">
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Tanggal Kegiatan <span
                                    style="color: #dc2626;">*</span></label>
                            <input type="date" name="tanggal_kegiatan" class="form-control form-control-custom"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    style="vertical-align: -2px;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Bukti Kegiatan <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="file" name="bukti_docs[]" id="riwayatDocs"
                                class="form-control form-control-custom" required accept="application/pdf,.pdf"
                                style="padding: 8px 14px;">
                            <small class="text-muted" style="font-size: 11px;">Gabungkan semua bukti (sertifikat, surat
                                tugas, foto, dsb.) dalam <b>1 file PDF</b>. Maks 10MB.</small>
                            <div class="doc-preview-list" id="riwayatDocsPreview"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
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
                <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.prestasi.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Ajukan Prestasi Lomba</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label-custom mb-0">Nama Prestasi <span
                                        style="color: #dc2626;">*</span></label>
                                <span class="text-muted" style="font-size: 11px;" id="charCount_nama_prestasi">0 / 50
                                    huruf</span>
                            </div>
                            <input type="text" name="nama_prestasi" class="form-control form-control-custom" required
                                maxlength="50" placeholder="Contoh: Juara 1 Hackathon IT Del 2026"
                                oninput="document.getElementById('charCount_nama_prestasi').innerText = this.value.length + ' / 50 huruf'">
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
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    style="vertical-align: -2px;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Bukti Kegiatan <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="file" name="bukti_docs[]" id="prestasiDocs"
                                class="form-control form-control-custom" required accept="application/pdf,.pdf"
                                style="padding: 8px 14px;">
                            <small class="text-muted" style="font-size: 11px;">Gabungkan semua bukti (sertifikat, surat
                                tugas/lomba, foto, dsb.) dalam <b>1 file PDF</b>. Maks 10MB.</small>
                            <div class="doc-preview-list" id="prestasiDocsPreview"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                        <button type="submit" class="btn-submit" data-submit-once>Ajukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ajukan Reward Prestasi (SK FT 774) -->
    {{-- Memakai kerangka yang sama dengan modal Tinjau: bukti di kiri, data di kanan.
    Bedanya kolom kanan berisi formulir, bukan hasil keputusan — jadi mahasiswa
    bisa membaca sertifikatnya sendiri sambil memilih kategori & capaian. --}}
    <div class="modal fade tinjau-modal" id="ajukanRewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="overflow: hidden;">
                <form method="POST" id="ajukanRewardForm">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="vertical-align:-3px;">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                            <span id="arJudul">Ajukan Reward Prestasi</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 0;">
                        <div class="tp-grid">
                            @include('manajemenmahasiswa::verifikasi.partials.tinjau-bukti-pane')

                            <div class="tp-pane-data">
                                {{-- Blok data prestasi disusun oleh tpRenderSections — daftar yang
                                sama persis dengan yang tampil di modal Tinjau. --}}
                                <div id="arFields"></div>

                                <div class="tp-section">
                                    <p class="tp-pane-heading">Formulir reward</p>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Kategori Penyelenggara <span
                                                style="color:#dc2626;">*</span></label>
                                        <select name="reward_penyelenggara" id="arPenyelenggara"
                                            class="form-select form-select-custom" required>
                                            <option value="">Pilih kategori...</option>
                                            @foreach(\Modules\ManajemenMahasiswa\Models\Prestasi::PENYELENGGARA_LABELS as $val => $lbl)
                                                <option value="{{ $val }}">{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-custom">Capaian / Peringkat <span
                                                style="color:#dc2626;">*</span></label>
                                        <select name="reward_capaian" id="arCapaian"
                                            class="form-select form-select-custom" required disabled>
                                            <option value="">Pilih penyelenggara dulu...</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="arInventionWrap" style="display:none;">
                                        <label
                                            style="font-size:13px; color:#374151; display:flex; align-items:flex-start; gap:8px; cursor:pointer;">
                                            <input type="checkbox" name="reward_is_invention" id="arInvention" value="1"
                                                style="margin-top:3px;">
                                            <span>Kegiatan bertema <b>invention / innovation / exhibition / convention /
                                                    expo / inventor / fair</b> dsb. (SK 774 poin 2.e–2.f). Jika
                                                dicentang, jatah maks 2 SKS &amp; kuota khusus 1×.</span>
                                        </label>
                                    </div>
                                    {{-- Rambu kuota tampil begitu kelompoknya ketahuan (kategori,
                                    plus centang invention) — sebelum itu sistem memang belum
                                    tahu klaim ini masuk kelompok yang mana. --}}
                                    <div id="arKuotaWarn" style="display:none; margin-bottom:10px;"></div>

                                    <div class="jatah-preview" id="arJatahPreview" style="display:none;"></div>

                                    <div class="mb-3 mt-3" id="arMkWrap" style="display:none;">
                                        <label class="form-label-custom">
                                            Usulan Mata Kuliah yang Dinaikkan Nilainya <span
                                                style="color:#dc2626;">*</span>
                                            <span style="font-weight:400; color:#666D80;">(maks <span
                                                    id="arMkMax">0</span> MK)</span>
                                        </label>
                                        <div class="d-flex gap-2">
                                            <select id="arMkSelect" class="form-select form-select-custom"
                                                style="flex:1;">
                                                <option value="">Pilih mata kuliah...</option>
                                                @foreach(\Modules\ManajemenMahasiswa\Models\Prestasi::MATA_KULIAH as $smt => $mks)
                                                    <optgroup label="{{ $smt }}">
                                                        @foreach($mks as $namaMk => $sksMk)
                                                            <option value="{{ $namaMk }}">{{ $namaMk }} ({{ $sksMk }} SKS)
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn-claim" id="arMkAddBtn"
                                                style="white-space:nowrap;">+ Tambah</button>
                                        </div>
                                        <div id="arMkChosen" class="mk-chosen"></div>
                                        <div id="arMkCounter" class="mk-counter"></div>
                                        <div id="arMkHidden"></div>
                                        <small class="text-muted" style="font-size:11px;">Pilih MK kurikulum Teknik
                                            Komputer yang nilainya ingin dinaikkan (syarat min. C). Ini usulan; MK final
                                            ditetapkan departemen.</small>
                                    </div>

                                    <div style="font-size:11px; color:#666D80; margin-top:10px; line-height:1.5;">
                                        Catatan: mata kuliah yang dinaikkan nilainya ditetapkan departemen/prodi saat
                                        persetujuan. Reward hanya untuk MK bernilai minimal C, maks 2× (atau 1× untuk
                                        invention) selama studi.
                                    </div>
                                </div>

                                {{-- Tombol memakai bentuk yang sama dengan panel keputusan di modal
                                Tinjau, jadi kedua modal terasa satu keluarga. --}}
                                <div class="tp-aksi">
                                    <button type="button" class="tp-btn-netral" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" id="arSubmitBtn" class="tp-btn-utama" disabled>
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                            <polyline points="22 4 12 14.01 9 11.01" />
                                        </svg>
                                        <span id="arSubmitLabel">Ajukan Reward</span>
                                    </button>
                                </div>
                            </div>
                        </div>
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
                    <div id="claimConfirmIcon"
                        style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    </div>
                    <h5 id="claimConfirmTitle" class="fw-bold mb-2" style="color: #1f2937;"></h5>
                    <p id="claimConfirmText"
                        style="color: #666D80; font-size: 14px; line-height: 1.5; margin-bottom: 0;"></p>
                </div>
                <div class="modal-footer" style="justify-content: center; gap: 8px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="border-radius: 10px; font-weight: 600; padding: 10px 20px;">Batal</button>
                    <button type="button" id="claimConfirmBtn"
                        style="border-radius: 10px; font-weight: 600; font-size: 14px; padding: 10px 20px; border: none; cursor: pointer; color: #fff;"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal "Detail Reward" dihapus: rincian rewardnya kini jadi salah satu blok
    di dalam modal Tinjau, supaya satu baris hanya punya satu tempat melihat. --}}

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
                        else if (['doc', 'docx'].includes(ext)) { iconClass = 'doc'; iconText = 'DOC'; }
                        else if (['xls', 'xlsx'].includes(ext)) { iconClass = 'xls'; iconText = 'XLS'; }
                        else if (['ppt', 'pptx'].includes(ext)) { iconClass = 'ppt'; iconText = 'PPT'; }

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
        window.__fpm_riwayatDocs = new FilePreviewManager('riwayatDocs', 'riwayatDocsPreview', 'doc');
        window.__fpm_prestasiDocs = new FilePreviewManager('prestasiDocs', 'prestasiDocsPreview', 'doc');

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
        (function () {
            const modalEl = document.getElementById('claimConfirmModal');
            if (!modalEl) return;

            const ccModal = new bootstrap.Modal(modalEl);
            const iconEl = document.getElementById('claimConfirmIcon');
            const titleEl = document.getElementById('claimConfirmTitle');
            const textEl = document.getElementById('claimConfirmText');
            const btnEl = document.getElementById('claimConfirmBtn');

            const ICON_WARN = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';

            let activeFormId = null;

            window.openBatalConfirm = function (formId, namaPrestasi) {
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

            btnEl.addEventListener('click', function () {
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
            modalEl.addEventListener('hidden.bs.modal', function () {
                btnEl.disabled = false;
                btnEl.style.opacity = '';
                btnEl.style.cursor = 'pointer';
            });
        })();

        // =========================================================================
        // Modal Ajukan Reward — opsi capaian dinamis + preview jatah (SK FT 774)
        // =========================================================================
        (function () {
            const modalEl = document.getElementById('ajukanRewardModal');
            if (!modalEl) return;

            const arModal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('ajukanRewardForm');
            const judulEl = document.getElementById('arJudul');
            const fieldsEl = document.getElementById('arFields');
            const submitLabelEl = document.getElementById('arSubmitLabel');
            const penyEl = document.getElementById('arPenyelenggara');
            const capEl = document.getElementById('arCapaian');
            const invWrap = document.getElementById('arInventionWrap');
            const invEl = document.getElementById('arInvention');
            const previewEl = document.getElementById('arJatahPreview');
            const kuotaEl = document.getElementById('arKuotaWarn');
            const submitBtn = document.getElementById('arSubmitBtn');

            // Picker usulan mata kuliah
            const mkWrap = document.getElementById('arMkWrap');
            const mkSelect = document.getElementById('arMkSelect');
            const mkAddBtn = document.getElementById('arMkAddBtn');
            const mkChosen = document.getElementById('arMkChosen');
            const mkCounter = document.getElementById('arMkCounter');
            const mkHidden = document.getElementById('arMkHidden');
            const mkMaxEl = document.getElementById('arMkMax');

            const LABELS = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_LABELS);
            const CAP_BY_PENY = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::CAPAIAN_BY_PENYELENGGARA);
            const JATAH = @json($rewardJatahMap);
            const JATAH_INV = @json($rewardJatahInvention);
            const PENY_LAINNYA = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::PENYELENGGARA_LAINNYA);
            const MK_SKS = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::mataKuliahFlat());
            const BASE_URL = @json(url('manajemen-mahasiswa/verifikasi'));

            // Kuota milik mahasiswa ini — angkanya sama dengan yang dipakai guard server
            const KUOTA_PAKAI = @json($kuota);
            const KUOTA_MAKS = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::KUOTA_MAKS);
            const KUOTA_LABEL = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::KUOTA_LABELS);
            const KUOTA_UMUM = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::KUOTA_UMUM);
            const KUOTA_INV = @json(\Modules\ManajemenMahasiswa\Models\Prestasi::KUOTA_INVENTION);

            let arMkList = [];   // nama MK yang dipilih
            let arJatahOk = false;
            let arKuotaOk = true; // kelompok kuota yang dituju masih tersisa
            let arCap = 0;    // maks jumlah MK
            let arSksMax = 0;    // maks total SKS

            function renderMk() {
                mkChosen.innerHTML = '';
                mkHidden.innerHTML = '';
                let totalSks = 0;
                arMkList.forEach(function (name) {
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
                return arMkList.reduce(function (sum, name) { return sum + (MK_SKS[name] || 0); }, 0);
            }

            function refreshSubmit() {
                const totalSks = currentTotalSks();
                const valid = arKuotaOk && arJatahOk && arMkList.length >= 1 && arMkList.length <= arCap && totalSks <= arSksMax;
                // Tampilan nonaktifnya diatur .tp-btn-utama:disabled, jadi cukup flagnya
                submitBtn.disabled = !valid;
            }

            // Rambu kuota. Aturan kelompoknya disalin dari Prestasi::tentukanKuotaGrup()
            // — kalau kelompok itu sudah habis, pengajuan pasti tertahan guard server,
            // jadi lebih baik ditahan di sini sekalian dengan alasannya.
            function updateKuota() {
                const peny = penyEl.value;
                kuotaEl.innerHTML = '';

                if (!peny) {
                    kuotaEl.style.display = 'none';
                    arKuotaOk = true;
                    return;
                }

                const grup = (peny === PENY_LAINNYA && invEl.checked) ? KUOTA_INV : KUOTA_UMUM;
                const pakai = KUOTA_PAKAI[grup] || 0;
                const maks = KUOTA_MAKS[grup] || 0;
                const penuh = pakai >= maks;
                arKuotaOk = !penuh;

                const pill = document.createElement('span');
                pill.className = 'kuota-pill' + (penuh ? ' penuh' : '');
                pill.textContent = 'Kuota ' + (KUOTA_LABEL[grup] || grup) + ': ' + pakai + '/' + maks
                    + (penuh ? ' — PENUH' : ' terpakai');
                kuotaEl.appendChild(pill);

                if (penuh) {
                    const ket = document.createElement('div');
                    ket.className = 'sk-lawas';
                    ket.textContent = 'Kelompok kuota ini sudah habis, jadi pengajuannya tidak bisa dikirim. '
                        + 'Buka "Rincian kuota" di atas tabel untuk melihat prestasi mana yang memakainya.';
                    kuotaEl.appendChild(ket);
                }

                kuotaEl.style.display = 'block';
            }

            mkAddBtn.addEventListener('click', function () {
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

            mkChosen.addEventListener('click', function (e) {
                const btn = e.target.closest('.mk-remove');
                if (!btn) return;
                const name = btn.dataset.mk;
                arMkList = arMkList.filter(function (x) { return x !== name; });
                renderMk();
                refreshSubmit();
            });

            // Payloadnya sebentuk dengan modal Tinjau (judul + sections + bukti), jadi
            // blok data prestasi & penampil buktinya digambar oleh fungsi yang sama.
            window.openAjukanReward = function (data) {
                form.action = BASE_URL + '/prestasi/' + data.id + '/reward/ajukan';
                judulEl.textContent = data.judul || 'Ajukan Reward Prestasi';
                submitLabelEl.textContent = data.judul && data.judul.indexOf('Ulang') !== -1
                    ? 'Ajukan Ulang'
                    : 'Ajukan Reward';
                tpRenderSections(fieldsEl, data.sections);
                tpRenderBukti(modalEl.querySelector('[data-tp-bukti]'), data.bukti, 0);
                penyEl.value = '';
                capEl.innerHTML = '<option value="">Pilih penyelenggara dulu...</option>';
                capEl.disabled = true;
                invWrap.style.display = 'none';
                invEl.checked = false;
                previewEl.style.display = 'none';
                kuotaEl.style.display = 'none';
                kuotaEl.innerHTML = '';
                arMkList = [];
                arJatahOk = false;
                arKuotaOk = true;
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
                    CAP_BY_PENY[peny].forEach(function (code) {
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
                updateKuota();

                const peny = penyEl.value;
                const cap = capEl.value;
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
                    arCap = jatah.jml_mk_max;
                    arSksMax = jatah.sks_max;
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

            penyEl.addEventListener('change', function () { rebuildCapaian(); updatePreview(); });
            capEl.addEventListener('change', updatePreview);
            invEl.addEventListener('change', updatePreview);
        })();

        // =========================================================================
        // Anti Double-Submit — cegah form di-submit lebih dari sekali
        // =========================================================================
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (e) {
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
        (function () {
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