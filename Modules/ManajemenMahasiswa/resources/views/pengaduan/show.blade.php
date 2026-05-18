<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            /* ── Back Bar ─────────────────────────────────────────── */
            .back-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .back-bar a, .back-bar button {
                font-weight: 600;
                font-size: 13px;
                text-decoration: none;
                border-radius: 8px;
                padding: 8px 16px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.2s;
            }
            .btn-back {
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151;
            }
            .btn-back:hover { background: #f9fafb; color: #111827; }

            /* ── Base Card ─────────────────────────────────────────── */
            .pgd-card {
                background: #ffffff;
                border-radius: 14px;
                padding: 32px;
                border: 1px solid #e5e7eb;
            }


            /* ── Header Badge Row ─────────────────────────────────── */
            .pgd-badge {
                font-size: 12px;
                font-weight: 700;
                padding: 5px 14px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            /* ── Section Labels ────────────────────────────────────── */
            .section-label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                margin-bottom: 8px;
            }
            .section-value {
                font-size: 15px;
                font-weight: 600;
                color: #111827;
            }

            /* ── Kronologi Box ─────────────────────────────────────── */
            .chronology-box {
                background: #f8fafc;
                border-radius: 12px;
                padding: 24px;
                color: #334155;
                font-size: 14px;
                line-height: 1.8;
                white-space: pre-wrap;
            }


            /* ── Delegasi Card ─────────────────────────────────────── */
            .delegasi-card {
                background: #fffaf0;
                border: 1px solid #fde68a;
                border-top: 3px solid #f59e0b;
                border-radius: 14px;
                padding: 24px 32px;
            }

            /* ── Timeline Log ──────────────────────────────────────── */
            .timeline-container {
                position: relative;
                padding-left: 24px;
            }
            .timeline-container::before {
                content: '';
                position: absolute;
                left: 7px; top: 0; bottom: 0;
                width: 2px;
                background: #e2e8f0;
            }
            .timeline-item {
                position: relative;
                margin-bottom: 24px;
            }
            .timeline-item:last-child { margin-bottom: 0; }
            .timeline-icon {
                position: absolute;
                left: -24px; top: 0;
                width: 16px; height: 16px;
                border-radius: 50%;
                background: #fff;
                border: 3px solid #0B266E;
                transform: translateX(-50%);
            }
            .timeline-content {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 12px 16px;
            }
            .timeline-date {
                font-size: 11px;
                font-weight: 600;
                color: #94a3b8;
                margin-bottom: 4px;
            }

            /* ── Reply Form ───────────────────────────────────────── */
            .reply-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #e5e7eb;
                padding: 28px 32px;
                border-top: 3px solid #0B266E;
            }
            .form-control-custom {
                background-color: #f9fafb;
                border: 2px solid #f3f4f6;
                border-radius: 8px;
                padding: 16px;
                font-size: 14px;
                transition: all 0.2s;
            }
            .form-control-custom:focus {
                background-color: #ffffff;
                border-color: #0B266E;
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.08);
                outline: none;
            }

            /* ── Modal ──────────────────────────────────────────────── */
            .modal-custom .modal-content {
                border-radius: 16px;
                border: none;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            }

            /* ── Status Banner ─────────────────────────────── */
            .status-banner { border-radius: 12px; padding: 14px 20px; display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-left: 4px solid; font-weight: 600; font-size: 14px; }
            .status-banner .material-symbols-outlined { font-size: 20px; flex-shrink: 0; }
            .status-banner-sub { font-size: 12px; opacity: 0.75; margin-left: auto; font-weight: 500; }
            .status-menunggu   { background: #fffbeb; color: #92400e; border-color: #fbbf24; }
            .status-dibaca     { background: #eff6ff; color: #1d4ed8; border-color: #60a5fa; }
            .status-delegasi   { background: #fff7ed; color: #c2410c; border-color: #fb923c; }
            .status-ditanggapi { background: #f0fdf4; color: #15803d; border-color: #4ade80; }
            .status-dijawab    { background: #f0fdf4; color: #15803d; border-color: #22c55e; }
            .status-reopen     { background: #fef3c7; color: #b45309; border-color: #fcd34d; }
            .status-selesai    { background: #ecfdf5; color: #065f46; border-color: #10b981; }

            /* ── Sidebar Card ──────────────────────────────── */
            .sidebar-card { background: #fff; border-radius: 14px; border: 1px solid #e5e7eb; padding: 20px; margin-bottom: 16px; }
            .sidebar-card-title { font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
            .sidebar-info-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
            .sidebar-info-item:last-child { border-bottom: none; padding-bottom: 0; }
            .sidebar-info-label { font-size: 12px; color: #94a3b8; font-weight: 600; flex-shrink: 0; }
            .sidebar-info-value { font-size: 13px; color: #1e293b; font-weight: 700; text-align: right; word-break: break-word; }

            /* ── Section Divider ───────────────────────────── */
            .section-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0 16px; }
            .section-divider span { font-size: 12px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
            .section-divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

            /* ── Dosen Action Prompt ───────────────────────── */
            .dosen-action-prompt { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 24px; border-top: 3px solid #22c55e; }
            .dosen-prompt-header { font-size: 14px; font-weight: 700; color: #15803d; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

            /* ── Answer Card v2 ────────────────────────────── */
            .answer-card-v2 { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 24px; }
            .answer-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
            .answer-icon { background: #22c55e; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; font-size: 16px; }
            .answer-label { font-size: 13px; font-weight: 700; color: #166534; }
            .answer-meta { font-size: 11px; color: #86efac; margin-top: 2px; }
            .answer-body { font-size: 14px; color: #166534; line-height: 1.8; white-space: pre-wrap; margin: 0; }

            /* ── Ticket Page Header ────────────────────────── */
            .ticket-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 12px; flex-wrap: wrap; }
            .ticket-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        </style>
    @endpush

    {{-- ── Ticket Page Header ────────────────────────────────── --}}
    <div class="ticket-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-back">
                <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                Kembali ke Daftar
            </a>
            <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                #{{ $pengaduan->id }}
            </span>
        </div>
        <div class="ticket-actions">
            @if($canReply && !$pengaduan->isSelesai())
                @if(in_array($pengaduan->status, [\Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN]))
                    <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fff7ed; color: #c2410c; border: 1.5px solid #fed7aa; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#delegateModal">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">swap_horiz</span> Delegasi Ulang?
                    </button>
                @elseif(!in_array($pengaduan->status, [\Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DITANGGAPI_DOSEN]))
                    <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #0B266E; color: #fff; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#delegateModal">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">forward_to_inbox</span> Delegasikan ke Dosen
                    </button>
                @endif
                <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fff7ed; color: #c2410c; border: 1.5px solid #fed7aa; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#closeAdminModal">
                    <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">close</span> Tutup Tiket
                </button>
            @endif
            @if($canDelete)
                <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#deleteShowModal">
                    <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">delete</span>
                    Hapus
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 mb-4" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; font-weight: 500; font-size: 14px;">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info border-0 mb-4" style="background-color: #e0f2fe; color: #0284c7; border-radius: 12px; font-weight: 500; font-size: 14px;">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 18px;">info</span>
                {{ session('info') }}
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 mb-4" style="background-color: #fee2e2; color: #b91c1c; border-radius: 12px; font-weight: 500; font-size: 14px;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $bannerMap = [
            'menunggu'         => ['class'=>'status-menunggu',   'icon'=>'hourglass_empty', 'label'=>'Menunggu Ditinjau Admin',     'sub'=>'Tiket baru, belum ada tindakan'],
            'dibaca'           => ['class'=>'status-dibaca',     'icon'=>'visibility',      'label'=>'Sedang Ditinjau Admin',       'sub'=>'Admin sedang memproses'],
            'didelegasikan'    => ['class'=>'status-delegasi',   'icon'=>'sync',            'label'=>'Sedang Ditinjau Dosen',       'sub'=>'Menunggu tanggapan dosen terkait'],
            'ditanggapi_dosen' => ['class'=>'status-ditanggapi', 'icon'=>'task_alt',        'label'=>'Dosen Sudah Merespons',       'sub'=>'Admin perlu meneruskan ke mahasiswa'],
            'dijawab'          => ['class'=>'status-dijawab',    'icon'=>'mark_email_read', 'label'=>'Pengaduan Telah Dijawab',     'sub'=>'Menunggu konfirmasi mahasiswa'],
            'diajukan_ulang'   => ['class'=>'status-reopen',     'icon'=>'replay',          'label'=>'Diajukan Ulang',              'sub'=>'Mahasiswa belum puas dengan jawaban'],
            'selesai'          => ['class'=>'status-selesai',    'icon'=>'verified',        'label'=>'Pengaduan Selesai',           'sub'=>'Tiket telah ditutup'],
        ];
        $banner = $bannerMap[strtolower($pengaduan->status)] ?? ['class'=>'status-menunggu','icon'=>'info','label'=>ucfirst($pengaduan->status),'sub'=>''];
    @endphp
    <div class="status-banner {{ $banner['class'] }}">
        <span class="material-symbols-outlined">{{ $banner['icon'] }}</span>
        <strong>{{ $banner['label'] }}</strong>
        @if($banner['sub'])<span class="status-banner-sub">{{ $banner['sub'] }}</span>@endif
    </div>

    @php
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- ── Main Card ─────────────────────────────────────────── --}}
            <div class="pgd-card mb-4">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <h4 class="fw-bold text-dark mb-3" style="font-size: 20px; line-height: 1.4;">
                        {{ data_get($pengaduan, 'data_template.judul', '-') }}
                    </h4>
                </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="pgd-badge" style="background: #e0e7ff; color: #4f46e5;">
                {{ $kategoriLabel ?? ucwords(str_replace('_', ' ', (string) $pengaduan->kategori)) }}
            </span>
            @php
                $statusStyle = match(strtolower($pengaduan->status)) {
                    'dijawab' => 'background: #dcfce7; color: #16a34a;',
                    'dibaca'  => 'background: #e0f2fe; color: #0284c7;',
                    'didelegasikan' => 'background: #ffedd5; color: #ea580c;',
                    'ditanggapi_dosen' => 'background: #e0e7ff; color: #4f46e5;',
                    'diajukan_ulang' => 'background: #fef3c7; color: #d97706;',
                    'selesai' => 'background: #bbf7d0; color: #15803d;',
                    default   => 'background: #f3f4f6; color: #4b5563;',
                };
            @endphp
            <span class="pgd-badge" style="{{ $statusStyle }}">
                {{ ucfirst($pengaduan->status) }}
            </span>
            @if($pengaduan->is_anonim)
                <span class="pgd-badge" style="background: #111827; color: white;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">lock</span> Konfidensial
                </span>
            @endif
            <span class="pgd-badge" style="background: #f8fafc; color: #6b7280; border: 1px solid #e5e7eb;">
                🕑 {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </span>
            @if($pengaduan->reopen_count > 0)
                <span class="pgd-badge" style="background: #fee2e2; color: #b91c1c;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">replay</span> Reopen {{ $pengaduan->reopen_count }}/2
                </span>
            @endif
            @if($pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIJAWAB && $pengaduan->auto_close_at)
                <span class="pgd-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">schedule</span> Auto-close: {{ $pengaduan->auto_close_at->translatedFormat('d M') }}
                </span>
            @endif
        </div>

        <hr style="border-color: #f3f4f6; margin: 0 0 24px 0;">

        @if($pengaduan->reopen_reason && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIAJUKAN_ULANG)
            <div class="mb-4 p-3 rounded" style="background: #fffbeb; border: 1px solid #fde68a;">
                <div class="section-label" style="color: #d97706;">Alasan Ajukan Ulang</div>
                <div class="section-value" style="font-size: 14px; color: #92400e;">{{ $pengaduan->reopen_reason }}</div>
            </div>
        @endif

        <div class="mb-4">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
        </div>
        <div>
            <div class="section-label">Kronologi / Isi Pengaduan</div>
            <div class="chronology-box">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
        </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- Panel Delegasi (tampil selama ada riwayat delegasi apapun) --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            @if($isStaff && $delegasiPanel)
                <div class="delegasi-card mb-4">

                    {{-- ── Header Panel ── --}}
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 15px; color: #b45309;">
                        <span class="material-symbols-outlined" style="font-size: 20px;">sync</span>
                        Status Delegasi
                        @php
                            $delegasiStatusBadge = match($delegasiPanel->status) {
                                'aktif'      => ['label' => 'Menunggu Respons', 'bg' => '#fff7ed', 'color' => '#c2410c'],
                                'ditanggapi' => ['label' => 'Sudah Ditanggapi', 'bg' => '#ecfdf5', 'color' => '#16a34a'],
                                'ditolak'    => ['label' => 'Ditolak Dosen', 'bg' => '#fef2f2', 'color' => '#dc2626'],
                                default      => ['label' => ucfirst($delegasiPanel->status), 'bg' => '#f3f4f6', 'color' => '#6b7280'],
                            };
                        @endphp
                        <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: {{ $delegasiStatusBadge['bg'] }}; color: {{ $delegasiStatusBadge['color'] }}; margin-left: 4px;">
                            {{ $delegasiStatusBadge['label'] }}
                        </span>
                    </h5>

                    {{-- ── Info Dosen & Waktu ── --}}
                    <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
                        <div style="flex: 1; min-width: 150px;">
                            <div class="text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">Didelegasikan Kepada</div>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                                <span class="material-symbols-outlined" style="font-size: 16px; color: #f59e0b;">person</span>
                                {{ optional($delegasiPanel->delegatedTo)->name ?? '—' }}
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <div class="text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">Waktu Delegasi</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $delegasiPanel->delegated_at->translatedFormat('d M Y, H:i') }}</div>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <div class="text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">Didelegasikan Oleh</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ optional($delegasiPanel->delegatedBy)->name ?? '—' }}</div>
                        </div>
                    </div>
                    <details class="mb-3">
                        <summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: #b45309;">Lihat Catatan Admin</summary>
                        <div class="p-3 rounded mt-2" style="background: #fefce8; border: 1px dashed #fde68a; font-size: 13px;">
                            {{ $delegasiPanel->notes_admin }}
                        </div>
                    </details>

                    {{-- ── Jika dosen menolak ── --}}
                    @if($delegasiPanel->status === 'ditolak' && $delegasiPanel->alasan_tolak)
                        <div class="p-3 rounded mb-3" style="background: #fef2f2; border: 1px solid #fecaca; font-size: 13px;">
                            <div class="fw-bold mb-1" style="color: #dc2626;">⚠ Dosen Menolak Delegasi</div>
                            <div style="color: #7f1d1d;">{{ $delegasiPanel->alasan_tolak }}</div>
                        </div>
                    @endif

                    {{-- ── Tanggapan Dosen (tampil baik saat ditanggapi maupun setelah di-forward) ── --}}
                    @if($delegasiPanel->tanggapan)
                        <hr style="border-color: #fde68a; margin: 16px 0;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="fw-bold mb-0" style="font-size: 14px; color: #4338ca;">Tanggapan Dosen:</h6>
                            @if($delegasiPanel->responded_at)
                                <span class="text-muted" style="font-size: 11px;">{{ $delegasiPanel->responded_at->translatedFormat('d M Y, H:i') }}</span>
                            @endif
                        </div>
                        <div class="p-3 bg-white rounded border" style="border-color: #e0e7ff !important;">
                            <div style="font-size: 14px; line-height: 1.6; color: #374151; white-space: pre-wrap;">{{ $delegasiPanel->tanggapan }}</div>
                            @if($delegasiPanel->notes_balik)
                                <div class="mt-2 text-muted" style="font-size: 12px; border-top: 1px dashed #c7d2fe; padding-top: 8px;">
                                    <strong>Catatan Internal ke Admin:</strong> {{ $delegasiPanel->notes_balik }}
                                </div>
                            @endif
                        </div>

                        {{-- Form Forward ke Mahasiswa — hanya saat status ditanggapi_dosen --}}
                        @if($canReply && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DITANGGAPI_DOSEN)
                            <div class="mt-4 p-4 bg-white rounded shadow-sm border" style="border-color: #e2e8f0 !important;">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined" style="font-size: 18px; color: #16a34a;">forward</span> Teruskan ke Mahasiswa
                                </h6>
                                <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.forward', $pengaduan->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control form-control-custom" name="jawaban" rows="4" required
                                            placeholder="Sesuaikan jawaban akhir untuk mahasiswa...">{{ $delegasiPanel->tanggapan }}</textarea>
                                        <div class="form-text mt-2" style="font-size: 12px;">Pesan ini yang akan dilihat oleh mahasiswa. Anda dapat mengedit respons dari dosen sebelum meneruskannya.</div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success fw-bold px-4 py-2" style="border-radius: 8px;">Kirim Jawaban Final</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif

                </div>
            @endif

            {{-- ── Form Respons Dosen (hanya jika delegasi aktif dan ditujukan ke dosen yang login) ── --}}
            @if($isDelegatedToMe)
                <div class="dosen-action-prompt mb-4">
                    <div class="dosen-prompt-header">
                        <span class="material-symbols-outlined" style="font-size: 20px;">edit_square</span> Berikan Tanggapan
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.respond', $pengaduan->delegasiAktif->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted" style="font-size: 12px;">Tanggapan (Dikirim ke Mahasiswa via Admin)</label>
                            <textarea class="form-control form-control-custom w-100" name="tanggapan" rows="5" required
                                placeholder="Tulis jawaban lengkap atas pengaduan ini..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted" style="font-size: 12px;">Catatan Internal untuk Admin</label>
                            <textarea class="form-control form-control-custom w-100" name="notes_balik" rows="2"
                                placeholder="Pesan tambahan hanya untuk Admin..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3">
                            <button type="button" class="btn btn-outline-danger fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal" style="border-radius: 8px; font-size: 13px;">
                                Tolak Delegasi
                            </button>
                            <button type="submit" class="btn btn-success fw-bold px-4 py-2" style="border-radius: 8px; font-size: 13px;">
                                Kirim Tanggapan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Tanggapan Final --}}
            <div class="section-divider">
                <span>Jawaban Pengaduan</span>
            </div>

            <div class="mb-4">
                @if($pengaduan->jawaban)
                    <div class="answer-card-v2">
                        <div class="answer-header">
                            <div class="answer-icon"><span class="material-symbols-outlined" style="font-size: 18px;">check</span></div>
                            <div>
                                <div class="answer-label">Jawaban Resmi</div>
                                <div class="answer-meta">
                                    {{ optional($pengaduan->answered_at)->translatedFormat('d F Y, H:i') ?? '—' }} WIB
                                    @if($isStaff && $pengaduan->answered_by)
                                        · oleh {{ optional($pengaduan->dijawabOleh)->name ?? '—' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="answer-body">{{ $pengaduan->jawaban }}</div>
                    </div>

                    {{-- Mahasiswa Actions: Tandai Selesai atau Ajukan Ulang --}}
                    @if(!$isStaff && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIJAWAB)
                        <div class="mt-4 p-4 rounded bg-white border d-flex flex-column align-items-center text-center">
                            <h6 class="fw-bold text-dark mb-2">Apakah jawaban ini menyelesaikan masalah Anda?</h6>
                            <p class="text-muted mb-4" style="font-size: 13px;">Jika sudah sesuai, silakan tandai selesai. Jika belum, Anda dapat mengajukan ulang maksimal 2 kali.</p>
                            
                            <div class="d-flex gap-3 justify-content-center w-100 flex-wrap">
                                @if($pengaduan->canReopen())
                                    <button type="button" class="btn btn-outline-warning fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#reopenModal" style="border-radius: 8px;">
                                        Ajukan Ulang
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-secondary fw-bold px-4 py-2" disabled style="border-radius: 8px;">
                                        Batas Ajukan Ulang Habis
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.close', $pengaduan->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success fw-bold px-4 py-2" style="border-radius: 8px;">
                                        Ya, Tandai Selesai
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @else
                    <div style="border: 2px dashed #cbd5e1; border-radius: 14px; background: #f8fafc; text-align: center; padding: 48px 24px;">
                        <div style="color: #94a3b8; margin-bottom: 12px;"><span class="material-symbols-outlined" style="font-size: 40px;">hourglass_empty</span></div>
                        <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada tanggapan final</div>
                        <div class="text-muted" style="font-size: 13px;">Admin belum memberikan balasan untuk pengaduan ini.</div>
                    </div>
                @endif
            </div>

            {{-- Form Reply Langsung (Hanya jika belum selesai, tidak sedang menunggu dosen, dan bukan sedang menunggu forward) --}}
            @if($canReply && !$pengaduan->isSelesai() && !in_array($pengaduan->status, [\Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN, \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DITANGGAPI_DOSEN]))
                <div class="reply-card mt-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        ✏️ <span>{{ $pengaduan->jawaban ? 'Perbarui Jawaban' : 'Tulis Jawaban Langsung' }}</span>
                    </h6>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.reply', $pengaduan->id) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control w-100 form-control-custom" name="jawaban" rows="5" required
                                placeholder="Tulis jawaban atau tindakan yang telah diambil…">{{ old('jawaban', $pengaduan->jawaban) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn" style="background: #0B266E; color: #fff; border-radius: 8px; font-weight: 600; padding: 10px 24px;">
                                {{ $pengaduan->jawaban ? '🔄 Perbarui Jawaban' : '📤 Kirim Jawaban ke Mahasiswa' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        {{-- ── SIDEBAR ───────────────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="sidebar-card">
                <div class="sidebar-card-title">Informasi Lanjut</div>
                
                @if(data_get($pengaduan, 'data_template.lokasi'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Lokasi</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.lokasi') }}</div>
                </div>
                @endif
                
                @if($waktuKejadian)
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Waktu</div>
                    <div class="sidebar-info-value">{{ \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') }}</div>
                </div>
                @endif
                
                @if(data_get($pengaduan, 'data_template.angkatan'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Angkatan</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.angkatan') }}</div>
                </div>
                @endif
                
                @if(data_get($pengaduan, 'data_template.mata_kuliah'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Mata Kuliah</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.mata_kuliah') }}</div>
                </div>
                @endif
                
                @if(data_get($pengaduan, 'data_template.nama_dosen'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Dosen</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.nama_dosen') }}</div>
                </div>
                @endif
                
                @if(data_get($pengaduan, 'data_template.nama_tendik'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Tendik</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.nama_tendik') }}</div>
                </div>
                @endif
                
                @if(data_get($pengaduan, 'data_template.frekuensi'))
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Frekuensi</div>
                    <div class="sidebar-info-value">{{ data_get($pengaduan, 'data_template.frekuensi') }}</div>
                </div>
                @endif
                
                @if($linkBukti)
                <div class="sidebar-info-item">
                    <div class="sidebar-info-label">Bukti Dukung</div>
                    <div class="sidebar-info-value">
                        <a href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer" style="color: #0B266E; text-decoration: none;">Lihat Bukti ↗</a>
                    </div>
                </div>
                @endif
            </div>

            <div class="sidebar-card">
                <div class="sidebar-card-title">Pelapor</div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span class="material-symbols-outlined" style="font-size: 20px; color: #475569;">person</span>
                    </div>
                    <div>
                        @if($pengaduan->is_anonim)
                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Konfidensial</div>
                            <div style="font-size: 12px; color: #64748b;">Identitas dilindungi sistem</div>
                        @else
                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ optional($pengaduan->pelapor)->name ?? '—' }}</div>
                            @if($isStaff && data_get($pengaduan, 'data_template.angkatan'))
                                <div style="font-size: 12px; color: #64748b;">Angkatan {{ data_get($pengaduan, 'data_template.angkatan') }}</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @if($isStaff && $pengaduan->delegasi->count() > 1)
            <div class="sidebar-card">
                <details style="cursor: pointer;">
                    <summary class="fw-bold text-muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; list-style: none; display: flex; align-items: center; gap: 6px; margin-bottom: 0;">
                        <span class="material-symbols-outlined" style="font-size: 15px; vertical-align: middle;">history</span>
                        Riwayat Delegasi ({{ $pengaduan->delegasi->count() }})
                    </summary>
                    <div class="mt-3">
                        @foreach($pengaduan->delegasi as $d)
                            @php
                                $dBadge = match($d->status) {
                                    'aktif'      => ['bg' => '#fff7ed', 'color' => '#c2410c', 'label' => 'Aktif'],
                                    'ditanggapi' => ['bg' => '#ecfdf5', 'color' => '#16a34a', 'label' => 'Ditanggapi'],
                                    'ditolak'    => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Ditolak'],
                                    default      => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => ucfirst($d->status)],
                                };
                            @endphp
                            <div class="p-3 rounded mb-2" style="background: #fff; border: 1px solid #e2e8f0; font-size: 13px;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong>{{ optional($d->delegatedTo)->name ?? '—' }}</strong>
                                        <span class="text-muted ms-2" style="font-size: 11px;">via {{ optional($d->delegatedBy)->name ?? '—' }}</span>
                                    </div>
                                    <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; background: {{ $dBadge['bg'] }}; color: {{ $dBadge['color'] }}; font-weight: 700;">{{ $dBadge['label'] }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">{{ $d->delegated_at->translatedFormat('d M Y, H:i') }}</div>
                                @if($d->alasan_tolak)
                                    <div class="mt-1" style="color: #dc2626;">Ditolak: {{ $d->alasan_tolak }}</div>
                                @endif
                                @if($d->tanggapan)
                                    <div class="mt-1" style="color: #374151;">Tanggapan: {{ Str::limit($d->tanggapan, 100) }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
            @endif

            <div class="sidebar-card">
                <div class="sidebar-card-title">Riwayat Tiket</div>
                <div class="timeline-container">
                    @php
                    $actionLabels = [
                        'dibuat'           => 'Tiket Dibuat',
                        'dibaca'           => 'Dibaca Admin',
                        'didelegasikan'    => 'Didelegasikan ke Dosen',
                        'ditanggapi_dosen' => 'Dosen Merespons',
                        'dijawab'          => 'Jawaban Dikirim',
                        'diajukan_ulang'   => 'Diajukan Ulang',
                        'selesai'          => 'Tiket Selesai',
                    ];
                    @endphp
                    @foreach($pengaduan->logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                    {{ $actionLabels[$log->action] ?? ucwords(str_replace('_', ' ', $log->action)) }}
                                </div>
                                @if($isStaff && $log->actor)
                                    <div class="text-muted mt-1" style="font-size: 12px;">Oleh: {{ $log->actor->name }}</div>
                                @endif
                                @if($log->notes && $isStaff)
                                    <div class="mt-2 text-muted" style="font-size: 12px; font-style: italic; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                        "{{ Str::limit($log->notes, 100) }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    {{-- ── Modals ────────────────────────────────────────────────── --}}
    
    {{-- Delete Modal --}}
    @if($canDelete)
        <div class="modal fade modal-custom" id="deleteShowModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="margin-bottom: 16px;"><span class="material-symbols-outlined" style="font-size: 48px; color: #f59e0b;">warning</span></div>
                        <h4 class="fw-bold text-dark mb-2">Hapus Pengaduan?</h4>
                        <p class="text-muted mb-4" style="font-size: 14px;">
                            Pengaduan <strong>"{{ data_get($pengaduan, 'data_template.judul', '-') }}"</strong> akan dihapus permanen.
                        </p>
                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.destroy', $pengaduan->id) }}">
                            @csrf
                            @method('DELETE')
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal"
                                    style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; color: #4b5563;">Batal</button>
                                <button type="submit" class="btn px-4 py-2"
                                    style="background-color: #dc2626; color: white; border-radius: 8px; font-weight: 600;">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delegate Modal (Admin) — adaptif: menampilkan peringatan re-delegasi jika tiket sudah didelegasikan --}}
    @if($canReply && !$pengaduan->isSelesai())
        @php $isRedelegasi = $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN; @endphp
        <div class="modal fade modal-custom" id="delegateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold d-flex align-items-center gap-2 mb-0" style="color: {{ $isRedelegasi ? '#c2410c' : '#111827' }};">
                            <span class="material-symbols-outlined" style="color: {{ $isRedelegasi ? '#f59e0b' : '#0B266E' }};">
                                {{ $isRedelegasi ? 'swap_horiz' : 'forward_to_inbox' }}
                            </span>
                            {{ $isRedelegasi ? 'Delegasi Ulang ke Dosen Lain' : 'Delegasi ke Dosen' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegate', $pengaduan->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">

                            {{-- ⚠ Peringatan Re-delegasi --}}
                            @if($isRedelegasi && $delegasiPanel)
                                <div class="p-3 rounded mb-4" style="background: #fff7ed; border: 1px solid #fed7aa; font-size: 13px;">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="material-symbols-outlined flex-shrink-0" style="font-size: 18px; color: #ea580c; margin-top: 1px;">warning</span>
                                        <div>
                                            <div class="fw-bold mb-1" style="color: #c2410c;">Tiket Sedang Didelegasikan</div>
                                            <div style="color: #7c2d12;">
                                                Tiket ini saat ini didelegasikan ke <strong>{{ optional($delegasiPanel->delegatedTo)->name ?? '—' }}</strong>.
                                                Melanjutkan akan <strong>membatalkan delegasi aktif</strong> tersebut dan menggantinya dengan dosen yang baru dipilih.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Pilih Dosen Tujuan</label>
                                <select name="delegated_to" class="form-select" style="border-radius: 8px;" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosenList as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Catatan untuk Dosen (Wajib)</label>
                                <textarea class="form-control" name="notes_admin" rows="3" required
                                    placeholder="{{ $isRedelegasi ? 'Jelaskan alasan pergantian delegasi dan instruksi untuk dosen baru...' : 'Berikan instruksi atau konteks tambahan untuk dosen...' }}"
                                    style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn fw-bold px-4"
                                style="background: {{ $isRedelegasi ? '#ea580c' : '#0B266E' }}; color: white; border: none; border-radius: 8px;">
                                {{ $isRedelegasi ? '↪ Ganti Dosen' : 'Kirim Delegasi' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Close Admin Modal --}}
        <div class="modal fade modal-custom" id="closeAdminModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="margin-bottom: 16px;"><span class="material-symbols-outlined" style="font-size: 48px; color: #f59e0b;">info</span></div>
                        <h4 class="fw-bold text-dark mb-2">Tutup Tiket Secara Manual?</h4>
                        <p class="text-muted mb-4" style="font-size: 14px;">
                            Tiket akan ditandai selesai dan tidak dapat diubah atau diajukan ulang lagi. Lanjutkan?
                        </p>
                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.close.admin', $pengaduan->id) }}">
                            @csrf
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                <button type="submit" class="btn btn-warning px-4 py-2 fw-bold" style="border-radius: 8px;">Ya, Tutup Tiket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reopen Modal (Mahasiswa) --}}
    @if(!$isStaff && $pengaduan->canReopen())
        <div class="modal fade modal-custom" id="reopenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                            <span class="material-symbols-outlined" style="color: #d97706;">replay</span> Ajukan Ulang Pengaduan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.reopen', $pengaduan->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="alert alert-warning border-0" style="background: #fffbeb; font-size: 13px;">
                                Anda memiliki batas <strong>{{ \Modules\ManajemenMahasiswa\Models\Pengaduan::MAX_REOPEN - $pengaduan->reopen_count }} kali</strong> pengajuan ulang tersisa.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Alasan Pengajuan Ulang (Wajib)</label>
                                <textarea class="form-control" name="reopen_reason" rows="4" required
                                    placeholder="Jelaskan secara rinci mengapa tanggapan admin belum menyelesaikan masalah Anda..." style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold" style="border-radius: 8px;">Ajukan Ulang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal (Dosen) --}}
    @if($isDelegatedToMe)
        <div class="modal fade modal-custom" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                            <span class="material-symbols-outlined" style="color: #dc2626;">cancel</span> Tolak Delegasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.reject', $pengaduan->delegasiAktif->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="text-muted" style="font-size: 14px;">Apakah Anda yakin ingin menolak tiket ini? Silakan berikan alasan penolakan untuk Admin.</p>
                            <textarea class="form-control" name="alasan_tolak" rows="3" required
                                placeholder="Alasan menolak delegasi ini..." style="border-radius: 8px;"></textarea>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn btn-danger fw-bold" style="border-radius: 8px; background: #dc2626;">Tolak Delegasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</x-dynamic-component>