<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            /* ── Back Button ──────────────────────────────────────── */
            .btn-back {
                font-weight: 600; font-size: 13px; text-decoration: none;
                border-radius: 12px; padding: 8px 16px;
                display: inline-flex; align-items: center; gap: 6px;
                transition: all 0.2s; background: transparent;
                border: none; color: #4b5563;
            }
            .btn-back:hover { background: #f3f4f6; color: #111827; }

            /* ── Base Card ────────────────────────────────────────── */
            .detail-card {
                background: #ffffff; border-radius: 12px; padding: 24px 28px;
                border: 1px solid #DDE1E8;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            }

            /* ── Tags (Forum pattern) ─────────────────────────────── */
            .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
            .tag-label {
                font-size: 11px; font-weight: 600; padding: 4px 12px;
                border-radius: 20px; display: inline-flex;
                align-items: center; gap: 4px; white-space: nowrap;
            }
            .tag-kategori { background: #e0e7ff; color: #4f46e5; }
            .tag-baru { background: #f3f4f6; color: #4b5563; }
            .tag-dibaca { background: #e0f2fe; color: #0284c7; }
            .tag-didelegasikan { background: #ffedd5; color: #ea580c; }
            .tag-ditanggapi_dosen { background: #e0e7ff; color: #4f46e5; }
            .tag-dijawab { background: #dcfce7; color: #16a34a; }
            .tag-diajukan_ulang { background: #fef3c7; color: #d97706; }
            .tag-selesai { background: #bbf7d0; color: #15803d; }
            .tag-anonim { background: #111827; color: #fff; }

            /* ── Section Labels ────────────────────────────────────── */
            .section-label {
                font-size: 11px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px;
            }
            .section-value {
                font-size: 15px; font-weight: 600; color: #111827;
            }



            /* ── Info Grid ─────────────────────────────────────── */
            .info-grid {
                display: grid; grid-template-columns: repeat(4, 1fr);
                gap: 10px 20px;
            }
            @media (max-width: 992px) { .info-grid { grid-template-columns: repeat(3, 1fr); } }
            @media (max-width: 768px) { .info-grid { grid-template-columns: repeat(2, 1fr); } }
            @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
            .info-item-label {
                font-size: 11px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px;
            }
            .info-item-value { font-size: 14px; font-weight: 600; color: #1e293b; }


            /* ── Status Banner (Admin) ─────────────────────────────── */
            .status-banner {
                border-radius: 12px; padding: 14px 20px;
                display: flex; align-items: center; gap: 12px;
                margin-bottom: 24px; border-left: 4px solid;
                font-weight: 600; font-size: 14px;
            }
            .status-banner-sub {
                font-size: 12px; opacity: 0.75; margin-left: auto; font-weight: 500;
            }
            .status-menunggu { background: #fffbeb; color: #92400e; border-color: #fbbf24; }
            .status-dibaca { background: #eff6ff; color: #1d4ed8; border-color: #60a5fa; }
            .status-delegasi { background: #fff7ed; color: #c2410c; border-color: #fb923c; }
            .status-selesai { background: #ecfdf5; color: #065f46; border-color: #10b981; }

            /* ── Ticket Page Header ────────────────────────────────── */
            .ticket-page-header {
                display: flex; justify-content: space-between; align-items: center;
                margin-bottom: 24px; gap: 12px; flex-wrap: wrap;
            }
            .ticket-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }



            /* ── Section Divider ──────────────────────────────────── */
            .section-divider {
                display: flex; align-items: center; gap: 12px; margin: 16px 0 12px;
            }
            .section-divider span {
                font-size: 12px; font-weight: 800; color: #374151;
                text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap;
            }
            .section-divider::after { content: ''; flex: 1; height: 1px; background: #DDE1E8; }

            /* ── Delegasi Card (Admin) — white card + border-left accent ─ */
            .delegasi-card {
                background: #ffffff; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 24px 32px;
                border-left: 4px solid;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            }
            .delegasi-card.delegasi-aktif   { border-left-color: #f59e0b; }
            .delegasi-card.delegasi-ditanggapi { border-left-color: #22c55e; }
            .delegasi-card.delegasi-ditolak { border-left-color: #ef4444; }
            .delegasi-card.delegasi-default { border-left-color: #9ca3af; }

            /* ── Dosen Action Prompt ──────────────────────────────── */
            .dosen-action-prompt {
                background: #ffffff; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 24px;
                border-left: 4px solid #22c55e;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            }
            .dosen-prompt-header {
                font-size: 14px; font-weight: 700; color: #15803d;
                margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
            }

            /* ── Form Control ─────────────────────────────────────── */
            .form-control-custom {
                background-color: #f9fafb; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 16px; font-size: 14px;
                transition: all 0.2s;
            }
            .form-control-custom:focus {
                background-color: #ffffff; border-color: #293C79;
                box-shadow: 0 0 0 3px rgba(41,60,121,0.12); outline: none;
            }

            /* ── Timeline Log ─────────────────────────────────────── */
            .timeline-container {
                position: relative; padding-left: 32px;
                max-height: 420px; overflow-y: auto; padding-right: 8px;
            }
            .timeline-container::-webkit-scrollbar { width: 5px; }
            .timeline-container::-webkit-scrollbar-track { background: transparent; }
            .timeline-container::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
            .timeline-item { position: relative; margin-bottom: 24px; }
            .timeline-item:last-child { margin-bottom: 0; }
            /* Per-item connector line — only between items, never extends beyond dots */
            .timeline-item:not(:last-child)::before {
                content: ''; position: absolute;
                left: -21px;
                top: 23px;    /* dot top — line starts here, dot covers overlap via z-index */
                bottom: -47px; /* reaches next dot top: -(margin:24 + nextDotTop:23) */
                width: 2px; background: #e2e8f0; border-radius: 1px;
            }
            .timeline-icon {
                position: absolute; left: -28px; top: 23px;
                width: 14px; height: 14px; border-radius: 50%;
                background: #fff; border: 3px solid #293C79;
                z-index: 1;
            }
            .timeline-content {
                background: #ffffff; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 12px 16px;
            }
            .timeline-date {
                font-size: 11px; font-weight: 600;
                color: #94a3b8; margin-bottom: 4px;
            }

            /* ── Modal (dm-box pattern) ───────────────────────────── */
            .modal-custom .modal-content {
                border-radius: 18px; border: none;
                box-shadow: 0 24px 60px rgba(0,0,0,.18);
            }

            /* ── Pelapor Info ─────────────────────────────────────── */
            .pelapor-info { display: flex; align-items: center; gap: 12px; }
            .pelapor-avatar {
                width: 36px; height: 36px; border-radius: 50%;
                background: linear-gradient(135deg, #293C79, #415086);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0; color: #fff;
            }
            .pelapor-name { font-size: 14px; font-weight: 700; color: #1e293b; }
            .pelapor-sub { font-size: 12px; color: #64748b; }

            /* ── Delegasi Panel ────────────────────────────────────── */
            .delegasi-status-badge {
                font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
            }
            .delegasi-info-grid { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
            .delegasi-info-col { flex: 1; min-width: 150px; }
            .delegasi-info-label {
                font-size: 11px; font-weight: 700; text-transform: uppercase;
                letter-spacing: 0.5px; margin-bottom: 3px; color: #6b7280;
            }
            .delegasi-info-val { font-size: 14px; font-weight: 700; color: #111827; }
        </style>
    @endpush

    @php
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
        $kategoriLabel = $kategoriLabel ?? ucwords(str_replace('_', ' ', (string) $pengaduan->kategori));
        $statusLower = strtolower($pengaduan->status);
    @endphp

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- MAHASISWA VIEW — Ultra Simple                         --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    @if(!$isStaff)

        {{-- ── Header ─────────────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-back">
                    <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                    Kembali
                </a>
                <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                    #{{ $pengaduan->id }}
                </span>
            </div>
        </div>

        {{-- ── Single Card ────────────────────────────────── --}}
        <div class="detail-card mb-4">

            {{-- Tags --}}
            <div class="tags-row">
                <span class="tag-label tag-kategori">{{ $kategoriLabel }}</span>
                @php
                    $statusStyle = match($statusLower) {
                        'selesai' => 'tag-selesai',
                        'dibaca' => 'tag-dibaca',
                        'didelegasikan' => 'tag-didelegasikan',
                        default => 'tag-baru',
                    };
                @endphp
                <span class="tag-label {{ $statusStyle }}">{{ strtolower($pengaduan->status) === 'dibaca' ? 'Diproses' : ucfirst(str_replace('_', ' ', $pengaduan->status)) }}</span>
                @if($pengaduan->is_anonim)
                    <span class="tag-label tag-anonim">
                        <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
                    </span>
                @endif
            </div>

            {{-- Judul --}}
            <h4 class="fw-bold text-dark mb-2" style="font-size: 20px; line-height: 1.4;">
                {{ data_get($pengaduan, 'data_template.judul', '-') }}
            </h4>
            <div style="font-size: 13px; color: #9ca3af; margin-bottom: 16px;">
                Diajukan {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </div>

            <div class="section-divider" style="margin-top: 0;">
                <span>Detail Pengaduan</span>
            </div>

            {{-- Hal Aduan --}}
            <div class="mb-3">
                <div class="section-label">Hal Aduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
            </div>

            {{-- Kronologi --}}
            <div class="mb-3">
                <div class="section-label">Kronologi / Isi Pengaduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
            </div>

            {{-- Info Tambahan --}}
            @php
                $infoItems = collect([
                    ['label' => 'Lokasi', 'value' => data_get($pengaduan, 'data_template.lokasi')],
                    ['label' => 'Waktu Kejadian', 'value' => $waktuKejadian ? \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') : null],
                    ['label' => 'Angkatan', 'value' => data_get($pengaduan, 'data_template.angkatan')],
                    ['label' => 'Mata Kuliah', 'value' => data_get($pengaduan, 'data_template.mata_kuliah')],
                    ['label' => 'Nama Dosen', 'value' => data_get($pengaduan, 'data_template.nama_dosen')],
                    ['label' => 'Nama Tendik', 'value' => data_get($pengaduan, 'data_template.nama_tendik')],
                    ['label' => 'Frekuensi', 'value' => data_get($pengaduan, 'data_template.frekuensi')],
                ]);
            @endphp
            <div class="section-divider"><span>Informasi Tambahan</span></div>
            <div class="info-grid">
                @foreach($infoItems as $info)
                    <div>
                        <div class="info-item-label">{{ $info['label'] }}</div>
                        <div class="info-item-value" style="{{ empty($info['value']) ? 'color: #cbd5e1;' : '' }}">{{ $info['value'] ?: '—' }}</div>
                    </div>
                @endforeach
                <div>
                    <div class="info-item-label">Bukti Dukung</div>
                    <div class="info-item-value">
                        @if($linkBukti)
                            <a href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer" style="color: #293C79; text-decoration: none;">Lihat Bukti ↗</a>
                        @else
                            <span style="color: #cbd5e1;">—</span>
                        @endif
                    </div>
                </div>
            </div>


        </div>

    @else
    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- ADMIN / DOSEN VIEW — Internal Workflow                --}}
    {{-- ══════════════════════════════════════════════════════ --}}

        {{-- ── Ticket Page Header ─────────────────────────── --}}
        <div class="ticket-page-header">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-back">
                    <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                    Kembali ke Daftar
                </a>
                <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                    #{{ $pengaduan->id }}
                </span>
            </div>
            <div class="ticket-actions">
                @if($canReply && !$pengaduan->isSelesai())
                    @if($pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_BARU)
                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.mark.proses', $pengaduan->id) }}" class="d-inline mb-0">
                            @csrf
                            <button type="submit" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #ecfdf5; color: #059669; border: 1.5px solid #a7f3d0; border-radius: 8px;">
                                <x-manajemenmahasiswa::ui.icon name="check-circle" size="15" /> Mulai Proses
                            </button>
                        </form>
                    @endif
                    @if(in_array($pengaduan->status, [\Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN]))
                        <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fff7ed; color: #c2410c; border: 1.5px solid #fed7aa; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#delegateModal">
                            <x-manajemenmahasiswa::ui.icon name="arrow-circle-right" size="15" /> Delegasi Ulang?
                        </button>
                    @else
                        <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #293C79; color: #fff; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#delegateModal">
                            <x-manajemenmahasiswa::ui.icon name="mail-05" size="15" /> Delegasikan ke Dosen
                        </button>
                    @endif
                    <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fff7ed; color: #c2410c; border: 1.5px solid #fed7aa; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#closeAdminModal">
                        <x-manajemenmahasiswa::ui.icon name="minus" size="15" /> Tutup Tiket
                    </button>
                @endif
                @if($canDelete)
                    <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#deleteShowModal">
                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="15" /> Hapus
                    </button>
                @endif
            </div>
        </div>

        {{-- ── Flash Messages (Staff only) ────────────────── --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
                style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; border: none; font-weight: 600;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info border-0 mb-4" style="background-color: #e0f2fe; color: #0284c7; border-radius: 12px; font-weight: 500; font-size: 14px;">
                <div class="d-flex align-items-center gap-2">
                    <x-manajemenmahasiswa::ui.icon name="information-circle" size="18" />
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

        {{-- ── Status Banner ──────────────────────────────── --}}
        @php
            $bannerMap = [
                'baru'          => ['class'=>'status-menunggu',  'icon'=>'clock-02',        'label'=>'Menunggu Ditinjau Admin',      'sub'=>'Tiket baru, belum ada tindakan'],
                'dibaca'        => ['class'=>'status-dibaca',    'icon'=>'eye',             'label'=>'Sedang Ditinjau Admin',        'sub'=>'Admin sedang memproses'],
                'didelegasikan' => ['class'=>'status-delegasi',  'icon'=>'arrow-circle-right','label'=>'Sedang Ditinjau Dosen',      'sub'=>'Menunggu tanggapan dosen terkait'],
                'selesai'       => ['class'=>'status-selesai',   'icon'=>'check-circle',    'label'=>'Pengaduan Selesai',            'sub'=>'Tiket telah ditutup'],
            ];
            $banner = $bannerMap[$statusLower] ?? ['class'=>'status-menunggu','icon'=>'information-circle','label'=>ucfirst($pengaduan->status),'sub'=>''];
        @endphp
        <div class="status-banner {{ $banner['class'] }}">
            <x-manajemenmahasiswa::ui.icon name="{{ $banner['icon'] }}" size="20" />
            <strong>{{ $banner['label'] }}</strong>
            @if($banner['sub'])<span class="status-banner-sub">{{ $banner['sub'] }}</span>@endif
        </div>

        {{-- ── Main Card (Single Column) ──────────────────── --}}
        <div class="detail-card mb-4">
            <div class="tags-row">
                <span class="tag-label tag-kategori">{{ $kategoriLabel }}</span>
                @php
                    $statusStyle = match($statusLower) {
                        'dibaca'  => 'tag-dibaca',
                        'didelegasikan' => 'tag-didelegasikan',
                        'selesai' => 'tag-selesai',
                        default   => 'tag-baru',
                    };
                @endphp
                <span class="tag-label {{ $statusStyle }}">{{ strtolower($pengaduan->status) === 'dibaca' ? 'Diproses' : ucfirst(str_replace('_', ' ', $pengaduan->status)) }}</span>
                @if($pengaduan->is_anonim)
                    <span class="tag-label tag-anonim">
                        <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
                    </span>
                @endif
            </div>

            <h4 class="fw-bold text-dark mb-1" style="font-size: 20px; line-height: 1.4;">
                {{ data_get($pengaduan, 'data_template.judul', '-') }}
            </h4>
            <div style="font-size: 13px; color: #9ca3af; margin-bottom: 16px;">
                Diajukan {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </div>

            <div class="section-divider" style="margin-top: 0;">
                <span>Detail Pengaduan</span>
            </div>

            <div class="mb-3">
                <div class="section-label">Hal Aduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
            </div>
            <div class="mb-3">
                <div class="section-label">Kronologi / Isi Pengaduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
            </div>

            {{-- Info Tambahan --}}
            @php
                $adminInfoItems = collect([
                    ['label' => 'Lokasi', 'value' => data_get($pengaduan, 'data_template.lokasi')],
                    ['label' => 'Waktu Kejadian', 'value' => $waktuKejadian ? \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') : null],
                    ['label' => 'Angkatan', 'value' => data_get($pengaduan, 'data_template.angkatan')],
                    ['label' => 'Mata Kuliah', 'value' => data_get($pengaduan, 'data_template.mata_kuliah')],
                    ['label' => 'Dosen', 'value' => data_get($pengaduan, 'data_template.nama_dosen')],
                    ['label' => 'Tendik', 'value' => data_get($pengaduan, 'data_template.nama_tendik')],
                    ['label' => 'Frekuensi', 'value' => data_get($pengaduan, 'data_template.frekuensi')],
                ]);
            @endphp
            <div class="section-divider"><span>Informasi Tambahan</span></div>
            <div class="info-grid">
                @foreach($adminInfoItems as $info)
                    <div>
                        <div class="info-item-label">{{ $info['label'] }}</div>
                        <div class="info-item-value" style="{{ empty($info['value']) ? 'color: #cbd5e1;' : '' }}">{{ $info['value'] ?: '—' }}</div>
                    </div>
                @endforeach
                <div>
                    <div class="info-item-label">Bukti Dukung</div>
                    <div class="info-item-value">
                        @if($linkBukti)
                            <a href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer" style="color: #293C79; text-decoration: none;">Lihat Bukti ↗</a>
                        @else
                            <span style="color: #cbd5e1;">—</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pelapor (moved from sidebar) --}}
            <div class="section-divider"><span>Pelapor</span></div>
            <div class="pelapor-info">
                <div class="pelapor-avatar">
                    <x-manajemenmahasiswa::ui.icon name="user-01" size="16" />
                </div>
                <div>
                    @if($pengaduan->is_anonim)
                        <div class="pelapor-name">Konfidensial</div>
                        <div class="pelapor-sub">Identitas dilindungi sistem</div>
                    @else
                        <div class="pelapor-name">{{ optional($pengaduan->pelapor)->name ?? '—' }}</div>
                        @if(data_get($pengaduan, 'data_template.angkatan'))
                            <div class="pelapor-sub">Angkatan {{ data_get($pengaduan, 'data_template.angkatan') }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Panel Delegasi (tampil selama ada riwayat delegasi) ── --}}
        @if($delegasiPanel)
            @php
                $delegasiCardClass = match($delegasiPanel->status) {
                    'aktif'      => 'delegasi-aktif',
                    'ditanggapi' => 'delegasi-ditanggapi',
                    'ditolak'    => 'delegasi-ditolak',
                    default      => 'delegasi-default',
                };
                $delegasiHeaderColor = match($delegasiPanel->status) {
                    'aktif'      => '#b45309',
                    'ditanggapi' => '#15803d',
                    'ditolak'    => '#b91c1c',
                    default      => '#6b7280',
                };
            @endphp
            <div class="delegasi-card {{ $delegasiCardClass }} mb-3">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 15px; color: {{ $delegasiHeaderColor }};">
                            <x-manajemenmahasiswa::ui.icon name="arrow-circle-right" size="18" />
                            Status Delegasi
                            @php
                                $delegasiStatusBadge = match($delegasiPanel->status) {
                                    'aktif'      => ['label' => 'Menunggu Respons', 'bg' => '#fff7ed', 'color' => '#c2410c'],
                                    'ditanggapi' => ['label' => 'Sudah Ditanggapi', 'bg' => '#ecfdf5', 'color' => '#16a34a'],
                                    'ditolak'    => ['label' => 'Ditolak Dosen',    'bg' => '#fef2f2', 'color' => '#dc2626'],
                                    default      => ['label' => ucfirst($delegasiPanel->status), 'bg' => '#f3f4f6', 'color' => '#6b7280'],
                                };
                            @endphp
                            <span class="delegasi-status-badge" style="background: {{ $delegasiStatusBadge['bg'] }}; color: {{ $delegasiStatusBadge['color'] }};">
                                {{ $delegasiStatusBadge['label'] }}
                            </span>
                        </h5>

                        <div class="delegasi-info-grid">
                            <div class="delegasi-info-col">
                                <div class="delegasi-info-label">Didelegasikan Kepada</div>
                                <div class="delegasi-info-val d-flex align-items-center gap-2">
                                    <x-manajemenmahasiswa::ui.icon name="user-01" size="15" />
                                    {{ optional($delegasiPanel->delegatedTo)->name ?? '—' }}
                                </div>
                            </div>
                            <div class="delegasi-info-col">
                                <div class="delegasi-info-label">Waktu Delegasi</div>
                                <div class="delegasi-info-val">{{ $delegasiPanel->delegated_at->translatedFormat('d M Y, H:i') }}</div>
                            </div>
                            <div class="delegasi-info-col">
                                <div class="delegasi-info-label">Didelegasikan Oleh</div>
                                <div class="delegasi-info-val">{{ optional($delegasiPanel->delegatedBy)->name ?? '—' }}</div>
                            </div>
                        </div>

                        <details class="mb-3">
                            <summary style="cursor: pointer; font-size: 13px; font-weight: 600; color: {{ $delegasiHeaderColor }};">Lihat Catatan Admin</summary>
                            <div class="p-3 rounded mt-2" style="background: #fefce8; border: 1px dashed #fde68a; font-size: 13px;">
                                {{ $delegasiPanel->notes_admin }}
                            </div>
                        </details>

                        @if($delegasiPanel->status === 'ditolak' && $delegasiPanel->alasan_tolak)
                            <div class="p-3 rounded mb-3" style="background: #fef2f2; border: 1px solid #fecaca; font-size: 13px;">
                                <div class="fw-bold mb-1 d-flex align-items-center gap-1" style="color: #dc2626;">
                                    <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" /> Dosen Menolak Delegasi
                                </div>
                                <div style="color: #7f1d1d;">{{ $delegasiPanel->alasan_tolak }}</div>
                            </div>
                        @endif

                        @if($delegasiPanel->notes_balik)
                            <hr style="border-color: #DDE1E8; margin: 16px 0;">
                            <div class="p-3 bg-white rounded border" style="border-color: #e0e7ff !important;">
                                <div class="fw-bold mb-1" style="font-size: 14px; color: #4338ca;">Catatan Internal dari Dosen:</div>
                                <div style="font-size: 14px; line-height: 1.6; color: #374151; white-space: pre-wrap;">{{ $delegasiPanel->notes_balik }}</div>
                            </div>
                        @endif
                    </div>
            @endif

        {{-- ── Form Respons Dosen ─────────────────────── --}}
        @if($isDelegatedToMe)
            <div class="dosen-action-prompt mb-3">
                <div class="dosen-prompt-header">
                    <x-manajemenmahasiswa::ui.icon name="check-square" size="18" />
                    Selesaikan Penugasan
                </div>
                <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.respond', $pengaduan->delegasiAktif->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted" style="font-size: 12px;">Catatan Internal (Opsional)</label>
                        <textarea class="form-control form-control-custom w-100" name="notes_balik" rows="3"
                            placeholder="Pesan untuk Admin atau keterangan penyelesaian..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3">
                        <button type="button" class="btn btn-outline-danger fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal" style="border-radius: 8px; font-size: 13px;">
                            Tolak Delegasi
                        </button>
                        <button type="submit" class="btn fw-bold px-4 py-2" style="border-radius: 8px; font-size: 13px; background: #293C79; color: #fff;">
                            Selesaikan Penugasan & Tutup Tiket
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ── Riwayat Delegasi (collapsible — timeline pattern) ── --}}
        @if($pengaduan->delegasi->count() > 1)
            <div class="detail-card mb-3">
                <details style="cursor: pointer;">
                    <summary style="list-style: none; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">
                            <x-manajemenmahasiswa::ui.icon name="clock-03" size="14" />
                            Riwayat Delegasi ({{ $pengaduan->delegasi->count() }})
                        </span>
                        <x-manajemenmahasiswa::ui.icon name="chevron-down" size="14" />
                    </summary>
                    <div class="timeline-container mt-3">
                        @foreach($pengaduan->delegasi as $d)
                            @php
                                $dBadge = match($d->status) {
                                    'aktif'      => ['bg' => '#fff7ed', 'color' => '#c2410c', 'label' => 'Aktif'],
                                    'ditanggapi' => ['bg' => '#ecfdf5', 'color' => '#16a34a', 'label' => 'Ditanggapi'],
                                    'ditolak'    => ['bg' => '#fef2f2', 'color' => '#dc2626', 'label' => 'Ditolak'],
                                    default      => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => ucfirst($d->status)],
                                };
                            @endphp
                            <div class="timeline-item">
                                <div class="timeline-icon"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date">{{ $d->delegated_at->translatedFormat('d M Y, H:i') }}</div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                                {{ optional($d->delegatedTo)->name ?? '—' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 12px;">via {{ optional($d->delegatedBy)->name ?? '—' }}</div>
                                        </div>
                                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; background: {{ $dBadge['bg'] }}; color: {{ $dBadge['color'] }}; font-weight: 700;">{{ $dBadge['label'] }}</span>
                                    </div>
                                    @if($d->alasan_tolak)
                                        <div class="mt-2 text-muted" style="font-size: 12px; font-style: italic; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px dashed #fecaca; color: #dc2626;">
                                            "{{ $d->alasan_tolak }}"
                                        </div>
                                    @endif
                                    @if($d->tanggapan)
                                        <div class="mt-2 text-muted" style="font-size: 12px; font-style: italic; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                            "{{ Str::limit($d->tanggapan, 100) }}"
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
        @endif

        {{-- ── Riwayat Tiket (collapsible) ── --}}
        <div class="detail-card">
            <details style="cursor: pointer;">
                <summary style="list-style: none; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">
                        Riwayat Tiket ({{ $pengaduan->logs->count() }})
                    </span>
                    <x-manajemenmahasiswa::ui.icon name="chevron-down" size="14" />
                </summary>
                <div class="timeline-container mt-3">
                    @php
                    $actionLabels = [
                        'dibuat'             => 'Tiket Dibuat',
                        'dibaca'             => 'Dibaca Admin',
                        'diproses'           => 'Mulai Diproses Admin',
                        'didelegasikan'      => 'Didelegasikan ke Dosen',
                        'ditanggapi_dosen'   => 'Ditanggapi Dosen',
                        'ditolak_dosen'      => 'Ditolak Dosen',
                        'dijawab'            => 'Dijawab',
                        'ditutup_admin'      => 'Ditutup Admin',
                        'ditutup_mahasiswa'  => 'Ditutup Mahasiswa',
                        'ditutup_otomatis'   => 'Ditutup Otomatis',
                        'diajukan_ulang'     => 'Diajukan Ulang',
                        'selesai'            => 'Tiket Selesai',
                    ];
                    @endphp
                    @foreach($pengaduan->logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                    {{ $actionLabels[$log->action] ?? ucwords(str_replace('_', ' ', $log->action)) }}
                                </div>
                                @if($log->actor)
                                    <div class="text-muted mt-1" style="font-size: 12px;">Oleh: {{ $log->actor->name }}</div>
                                @endif
                                @if($log->notes)
                                    <div class="mt-2 text-muted" style="font-size: 12px; font-style: italic; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                        "{{ Str::limit($log->notes, 100) }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        </div>

    @endif

    {{-- ══ MODALS ════════════════════════════════════════════ --}}

    {{-- Delete Modal --}}
    @if($canDelete)
        <div class="modal fade modal-custom" id="deleteShowModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="margin-bottom: 16px; color: #f59e0b;">
                            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="48" />
                        </div>
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
    @if($isStaff && $canReply && !$pengaduan->isSelesai())
        @php $isRedelegasi = $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN; @endphp
        <div class="modal fade modal-custom" id="delegateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold d-flex align-items-center gap-2 mb-0" style="color: {{ $isRedelegasi ? '#c2410c' : '#111827' }};">
                            @if($isRedelegasi)
                                <x-manajemenmahasiswa::ui.icon name="arrow-circle-right" size="20" />
                                Delegasi Ulang ke Dosen Lain
                            @else
                                <x-manajemenmahasiswa::ui.icon name="mail-05" size="20" />
                                Delegasi ke Dosen
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegate', $pengaduan->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            @if($isRedelegasi && $delegasiPanel)
                                <div class="p-3 rounded mb-4" style="background: #fff7ed; border: 1px solid #fed7aa; font-size: 13px;">
                                    <div class="d-flex align-items-start gap-2">
                                        <span style="color: #ea580c; flex-shrink: 0; margin-top: 1px;">
                                            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" />
                                        </span>
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
                                <select name="delegated_to" class="form-select" style="border-radius: 12px;" required>
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
                                    style="border-radius: 12px;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn fw-bold px-4"
                                style="background: {{ $isRedelegasi ? '#ea580c' : '#293C79' }}; color: white; border: none; border-radius: 8px;">
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
                        <div style="margin-bottom: 16px; color: #f59e0b;">
                            <x-manajemenmahasiswa::ui.icon name="information-circle" size="48" />
                        </div>
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

    {{-- Reject Modal (Dosen) --}}
    @if($isStaff && $isDelegatedToMe)
        <div class="modal fade modal-custom" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                            <span style="color: #dc2626;">
                                <x-manajemenmahasiswa::ui.icon name="block-circle" size="20" />
                            </span>
                            Tolak Delegasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.reject', $pengaduan->delegasiAktif->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="text-muted" style="font-size: 14px;">Apakah Anda yakin ingin menolak tiket ini? Silakan berikan alasan penolakan untuk Admin.</p>
                            <textarea class="form-control" name="alasan_tolak" rows="3" required
                                placeholder="Alasan menolak delegasi ini..." style="border-radius: 12px;"></textarea>
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