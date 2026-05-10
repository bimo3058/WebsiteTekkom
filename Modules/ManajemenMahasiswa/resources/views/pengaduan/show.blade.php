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
            .btn-del {
                background: #fef2f2;
                border: 1.5px solid #fecaca;
                color: #dc2626;
                cursor: pointer;
            }
            .btn-del:hover { background: #fee2e2; color: #b91c1c; }
            .btn-primary-custom {
                background: linear-gradient(135deg, #4D4DFF, #6b6bff);
                border: none;
                color: white;
                cursor: pointer;
            }
            .btn-primary-custom:hover { background: linear-gradient(135deg, #3b3be5, #4D4DFF); color: white; }
            .btn-warning-custom {
                background: #fffbeb;
                border: 1.5px solid #fde68a;
                color: #d97706;
                cursor: pointer;
            }
            .btn-warning-custom:hover { background: #fef3c7; color: #b45309; }

            /* ── Base Card ─────────────────────────────────────────── */
            .pgd-card {
                background: #ffffff;
                border-radius: 14px;
                padding: 32px;
                border: 1px solid #e5e7eb;
                position: relative;
                overflow: hidden;
            }
            .pgd-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 4px;
                background: linear-gradient(135deg, #4D4DFF 0%, #7c7cff 50%, #4D4DFF 100%);
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
                border-left: 4px solid #4D4DFF;
                border-radius: 0 12px 12px 0;
                padding: 24px;
                color: #334155;
                font-size: 14px;
                line-height: 1.8;
                white-space: pre-wrap;
            }

            /* ── Info Grid ────────────────────────────────────────── */
            .info-grid {
                background: #fff;
                border-radius: 14px;
                padding: 28px 32px;
                border: 1px solid #e5e7eb;
            }
            .info-grid-title {
                font-size: 13px;
                font-weight: 800;
                color: #475569;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 24px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .info-grid-title::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e2e8f0;
            }
            .info-item-label {
                font-size: 11px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                margin-bottom: 6px;
            }
            .info-item-value {
                font-size: 14px;
                font-weight: 700;
                color: #1e293b;
                word-break: break-word;
            }
            .info-item-value.empty {
                color: #94a3b8;
                font-weight: 500;
            }

            /* ── Pelapor Box ───────────────────────────────────────── */
            .pelapor-box {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 16px 20px;
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .pelapor-avatar {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
            }

            /* ── Answer ────────────────────────────────────────────── */
            .answer-card {
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                border-radius: 14px;
                padding: 28px 32px;
                position: relative;
            }
            .answer-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%);
                border-radius: 14px 14px 0 0;
            }
            .answer-icon-badge {
                background: #22c55e;
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                font-weight: bold;
                box-shadow: 0 2px 8px rgba(34,197,94,0.3);
                flex-shrink: 0;
            }
            .empty-answer {
                border: 2px dashed #cbd5e1;
                border-radius: 14px;
                background: #f8fafc;
                text-align: center;
                padding: 48px 24px;
            }

            /* ── Delegasi Card ─────────────────────────────────────── */
            .delegasi-card {
                background: #fffaf0;
                border: 1px solid #fde68a;
                border-radius: 14px;
                padding: 24px 32px;
                position: relative;
            }
            .delegasi-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
                border-radius: 14px 14px 0 0;
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
                border: 3px solid #4D4DFF;
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
                border-top: 3px solid #4D4DFF;
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
                border-color: #a5a5ff;
                box-shadow: 0 0 0 4px rgba(77, 77, 255, 0.1);
                outline: none;
            }
            .btn-submit {
                background: linear-gradient(135deg, #4D4DFF, #6b6bff);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }
            .btn-submit:hover {
                background: linear-gradient(135deg, #3b3be5, #4D4DFF);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(77, 77, 255, 0.3);
            }

            /* ── Modal ──────────────────────────────────────────────── */
            .modal-custom .modal-content {
                border-radius: 16px;
                border: none;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            }
        </style>
    @endpush

    {{-- ── Back Bar ──────────────────────────────────────────── --}}
    <div class="back-bar">
        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-back">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Kembali
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                #{{ $pengaduan->id }}
            </span>
            @if($canReply && !$pengaduan->isSelesai())
                <button type="button" class="btn-warning-custom px-3 py-2 fw-bold rounded" style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#closeAdminModal">
                    <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">close</span> Tutup Tiket
                </button>
            @endif
            @if($canDelete)
                <button type="button" class="btn-del" data-bs-toggle="modal" data-bs-target="#deleteShowModal">
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

    {{-- ── Main Card ─────────────────────────────────────────── --}}
    <div class="pgd-card mb-4">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <h4 class="fw-bold text-dark mb-3" style="font-size: 20px; line-height: 1.4;">
                {{ data_get($pengaduan, 'data_template.judul', '-') }}
            </h4>
            @if($canReply && !$pengaduan->isSelesai() && !in_array($pengaduan->status, [\Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN, \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DITANGGAPI_DOSEN]))
                <button type="button" class="btn-primary-custom px-3 py-2 fw-bold rounded flex-shrink-0" style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#delegateModal">
                    <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">forward_to_inbox</span> Delegasikan ke Dosen
                </button>
            @endif
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

    {{-- ── Informasi Lanjut ──────────────────────────────────── --}}
    @php
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
    @endphp

    <div class="info-grid mb-4">
        <div class="info-grid-title">Informasi Lanjut</div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Lokasi Kejadian</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.lokasi') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.lokasi', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Waktu Kejadian</div>
                <div class="info-item-value {{ !$waktuKejadian ? 'empty' : '' }}">
                    @if($waktuKejadian)
                        {{ \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Angkatan</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.angkatan') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.angkatan', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Mata Kuliah</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.mata_kuliah') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Dosen Terkait</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.nama_dosen') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.nama_dosen', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Tendik Terkait</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.nama_tendik') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.nama_tendik', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Frekuensi Kejadian</div>
                <div class="info-item-value {{ !data_get($pengaduan, 'data_template.frekuensi') ? 'empty' : '' }}">
                    {{ data_get($pengaduan, 'data_template.frekuensi', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item-label">Link Bukti Dukung</div>
                @if($linkBukti)
                    <a class="info-item-value d-block text-decoration-none" href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer" style="color: #4D4DFF;">
                        {{ Str::limit($linkBukti, 40) }} ↗
                    </a>
                @else
                    <div class="info-item-value empty">—</div>
                @endif
            </div>
        </div>

        {{-- Pelapor --}}
        <hr style="border-color: #e2e8f0; margin: 8px 0 20px 0;">
        <div class="info-item-label mb-2">Pelapor</div>
        <div class="pelapor-box" style="max-width: 360px;">
            <div class="pelapor-avatar"><span class="material-symbols-outlined" style="font-size: 18px;">person</span></div>
            <div>
                @if($pengaduan->is_anonim)
                    <div class="fw-bold text-dark" style="font-size: 15px;">Konfidensial</div>
                    <div class="text-muted" style="font-size: 12px;">Identitas dilindungi sistem</div>
                @else
                    <div class="fw-bold text-dark" style="font-size: 15px;">{{ optional($pengaduan->pelapor)->name ?? '—' }}</div>
                    @if($isStaff && data_get($pengaduan, 'data_template.angkatan'))
                        <div class="text-muted" style="font-size: 12px;">Angkatan {{ data_get($pengaduan, 'data_template.angkatan') }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ── Kolom Kiri: Riwayat / Timeline ──────────────────────── --}}
        <div class="col-lg-4 order-lg-2">
            <div class="info-grid" style="padding: 24px;">
                <div class="info-grid-title mb-4" style="font-size: 12px;">Riwayat Tiket</div>
                <div class="timeline-container">
                    @foreach($pengaduan->logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
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

        {{-- ── Kolom Kanan: Status & Tanggapan ─────────────────────── --}}
        <div class="col-lg-8 order-lg-1">

            {{-- Panel Status Delegasi (Jika ada delegasi) --}}
            @if($isStaff && $pengaduan->delegasiAktif)
                <div class="delegasi-card mb-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 15px; color: #b45309 !important;">
                        <span class="material-symbols-outlined" style="font-size: 20px;">sync</span> Status Delegasi
                    </h5>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <div style="flex: 1; min-width: 150px;">
                            <div class="text-muted" style="font-size: 12px; margin-bottom: 2px;">Didelegasikan Kepada:</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ optional($pengaduan->delegasiAktif->delegatedTo)->name ?? '—' }}</div>
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <div class="text-muted" style="font-size: 12px; margin-bottom: 2px;">Waktu Delegasi:</div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">{{ $pengaduan->delegasiAktif->delegated_at->translatedFormat('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    <div class="text-muted" style="font-size: 13px;">
                        <strong>Catatan Admin:</strong> {{ $pengaduan->delegasiAktif->notes_admin }}
                    </div>

                    @if($pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DITANGGAPI_DOSEN)
                        <hr style="border-color: #fde68a; margin: 16px 0;">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 14px; color: #4338ca !important;">Tanggapan Dosen:</h6>
                        <div class="p-3 bg-white rounded border" style="border-color: #e0e7ff !important;">
                            <div style="font-size: 14px; line-height: 1.6; color: #374151; white-space: pre-wrap;">{{ $pengaduan->delegasiAktif->tanggapan }}</div>
                            <div class="mt-2 text-muted" style="font-size: 12px; border-top: 1px dashed #c7d2fe; padding-top: 8px;">
                                <strong>Catatan ke Admin:</strong> {{ $pengaduan->delegasiAktif->notes_balik }}
                            </div>
                        </div>

                        {{-- Form Forward ke Mahasiswa --}}
                        @if($canReply)
                            <div class="mt-4 p-4 bg-white rounded shadow-sm border" style="border-color: #e2e8f0 !important;">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined" style="font-size: 18px; color: #16a34a;">forward</span> Teruskan ke Mahasiswa
                                </h6>
                                <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.forward', $pengaduan->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control form-control-custom" name="jawaban" rows="4" required
                                            placeholder="Sesuaikan jawaban akhir untuk mahasiswa...">{{ $pengaduan->delegasiAktif->tanggapan }}</textarea>
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

            {{-- Tanggapan Final --}}
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 16px;">
                    <span class="material-symbols-outlined" style="color: #4D4DFF; font-size: 20px; vertical-align: text-bottom;">chat</span> Jawaban Pengaduan
                </h5>

                @if($pengaduan->jawaban)
                    <div class="answer-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="answer-icon-badge"><span class="material-symbols-outlined" style="font-size: 16px; font-weight: bold;">check</span></div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="white-space: pre-wrap; color: #166534; font-size: 14px; line-height: 1.8;">{{ $pengaduan->jawaban }}</div>
                                <div class="mt-3 text-muted fw-medium" style="font-size: 12px;">
                                    Dijawab pada: {{ optional($pengaduan->answered_at)->translatedFormat('d F Y, H:i') ?? '—' }} WIB
                                    @if($isStaff && $pengaduan->answered_by)
                                        · oleh {{ optional($pengaduan->dijawabOleh)->name ?? '—' }}
                                    @endif
                                </div>
                            </div>
                        </div>
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
                    <div class="empty-answer">
                        <div style="color: #94a3b8; margin-bottom: 12px;"><span class="material-symbols-outlined" style="font-size: 40px;">hourglass_empty</span></div>
                        <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada tanggapan final</div>
                        <div class="text-muted" style="font-size: 13px;">Admin belum memberikan balasan untuk pengaduan ini.</div>
                    </div>
                @endif
            </div>

            {{-- Form Reply Langsung (Hanya jika belum selesai dan tidak sedang didelegasi) --}}
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
                            <button type="submit" class="btn-submit">
                                {{ $pengaduan->jawaban ? '🔄 Perbarui Jawaban' : '📤 Kirim Jawaban ke Mahasiswa' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

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

    {{-- Delegate Modal (Admin) --}}
    @if($canReply && !$pengaduan->isSelesai())
        <div class="modal fade modal-custom" id="delegateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                            <span class="material-symbols-outlined" style="color: #4D4DFF;">forward_to_inbox</span> Delegasi ke Dosen
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegate', $pengaduan->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
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
                                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Catatan Admin (Wajib)</label>
                                <textarea class="form-control" name="notes_admin" rows="3" required
                                    placeholder="Berikan instruksi atau konteks tambahan untuk dosen..." style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn btn-primary" style="background: #4D4DFF; border: none; border-radius: 8px;">Kirim Delegasi</button>
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

</x-dynamic-component>