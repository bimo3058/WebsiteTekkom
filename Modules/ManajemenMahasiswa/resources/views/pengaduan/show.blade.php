<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            /* ── Back Button ──────────────────────────────────────── */
            .detail-back {
                width: auto; min-width: 0; height: 32px; padding: 0 12px; gap: 8px;
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: 8px; background: #fff; border: 1px solid #DFE1E7;
                color: #353849; box-shadow: 0 1px 2px rgba(0,0,0,.05);
                text-decoration: none; transition: all .2s;
            }
            .detail-back:hover { background: #F6F8FA; color: #0D0D12; }
            .detail-back-label { color: inherit; font-size: 13px; font-weight: 600; line-height: 1.2; }

            /* ── Base Card ────────────────────────────────────────── */
            .detail-card {
                background: #ffffff; border-radius: 12px; padding: 24px 28px;
                border: 1px solid #DDE1E8;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            }

            /* ── Tags ─────────────────────────────────────────────── */
            .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
            .tag-label {
                font-size: 11px; font-weight: 600; padding: 4px 12px;
                border-radius: 20px; display: inline-flex;
                align-items: center; gap: 4px; white-space: nowrap;
            }
            .tag-kategori { background: #e0e7ff; color: #4f46e5; }
            .tag-baru { background: #fef3c7; color: #d97706; }
            .tag-tercatat { background: #bbf7d0; color: #15803d; }
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

            /* ── Status Banner ─────────────────────────────────────── */
            .status-banner {
                border-radius: 12px; padding: 14px 20px;
                display: flex; align-items: center; gap: 12px;
                margin-bottom: 24px; border-left: 4px solid;
                font-weight: 600; font-size: 14px;
            }
            .status-banner-sub {
                font-size: 12px; opacity: 0.75; margin-left: auto; font-weight: 500;
            }
            .status-baru-banner { background: #fffbeb; color: #92400e; border-color: #fbbf24; }
            .status-dibaca-banner { background: #eff6ff; color: #1d4ed8; border-color: #60a5fa; }
            .status-tercatat-banner { background: #ecfdf5; color: #065f46; border-color: #10b981; }

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
            .timeline-item:not(:last-child)::before {
                content: ''; position: absolute;
                left: -21px;
                top: 23px;
                bottom: -47px;
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

            /* ── Modal ───────────────────────────────────────────── */
            .modal-custom .modal-content {
                border-radius: 18px; border: none;
                box-shadow: 0 24px 60px rgba(0,0,0,.18);
            }
        </style>
    @endpush

    @php
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $kategoriLabel = $kategoriLabel ?? ucwords(str_replace('_', ' ', (string) $pengaduan->kategori));
        $statusLower = strtolower($pengaduan->status);
    @endphp

    {{-- ── Page Header ─────────────────────────────────── --}}
    <div class="ticket-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="detail-back" title="Kembali" aria-label="Kembali ke Daftar">
                <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                <span class="detail-back-label">Kembali</span>
            </a>
            <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px; padding: 6px 12px; border-radius: 8px;">
                #{{ $pengaduan->id }}
            </span>
        </div>
        <div class="ticket-actions">
            @if($canDelete)
                <button type="button" class="btn px-3 py-2 fw-bold" style="font-size: 13px; background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#deleteShowModal">
                    <x-manajemenmahasiswa::ui.icon name="minus-circle" size="15" /> Hapus
                </button>
            @endif
        </div>
    </div>

    {{-- ── Flash Messages ────────────────── --}}
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
            'baru'     => ['class' => 'status-baru-banner',     'icon' => 'clock-02',     'label' => 'Pengaduan Baru',           'sub' => 'Belum dibuka oleh admin'],
            'dibaca'   => ['class' => 'status-dibaca-banner',   'icon' => 'eye',           'label' => 'Pengaduan Telah Diterima', 'sub' => 'Sudah dibaca oleh admin'],
            'tercatat' => ['class' => 'status-tercatat-banner', 'icon' => 'check-circle',  'label' => 'Pengaduan Tercatat',       'sub' => 'Sudah dicatat oleh admin'],
        ];
        // Legacy fallback: didelegasikan & selesai → tercatat
        $banner = $bannerMap[$statusLower]
            ?? $bannerMap['tercatat']
            ?? ['class' => 'status-dibaca-banner', 'icon' => 'information-circle', 'label' => ucfirst($pengaduan->status), 'sub' => ''];
    @endphp
    <div class="status-banner {{ $banner['class'] }}">
        <x-manajemenmahasiswa::ui.icon name="{{ $banner['icon'] }}" size="20" />
        <strong>{{ $banner['label'] }}</strong>
        @if($banner['sub'])<span class="status-banner-sub">{{ $banner['sub'] }}</span>@endif
    </div>

    {{-- ── Main Detail Card ──────────────────────────── --}}
    <div class="detail-card mb-4">
        <div class="tags-row">
            <span class="tag-label tag-kategori">{{ $kategoriLabel }}</span>
            @php
                $statusStyle = match($statusLower) {
                    'baru' => 'tag-baru',
                    'tercatat', 'selesai', 'didelegasikan' => 'tag-tercatat',
                    default => '',
                };
                $statusText = $pengaduan->statusLabel();
            @endphp
            @if($statusText)
                <span class="tag-label {{ $statusStyle }}">{{ $statusText }}</span>
            @endif
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

        {{-- Hal Aduan sudah tidak ditanyakan; tampil hanya untuk tiket lama yang masih menyimpannya. --}}
        @if(data_get($pengaduan, 'data_template.hal_aduan'))
            <div class="mb-3">
                <div class="section-label">Hal Aduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan') }}</div>
            </div>
        @endif
        <div class="mb-3">
            <div class="section-label">Pesan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
        </div>

        {{-- Info Tambahan --}}
        @php
            $infoItems = collect([
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
            @foreach($infoItems as $info)
                <div>
                    <div class="info-item-label">{{ $info['label'] }}</div>
                    <div class="info-item-value" style="{{ empty($info['value']) ? 'color: #cbd5e1;' : '' }}">{{ $info['value'] ?: '—' }}</div>
                </div>
            @endforeach
            <div>
                <div class="info-item-label">Bukti Dukung</div>
                <div class="info-item-value">
                    @include('manajemenmahasiswa::pengaduan.partials.bukti-list', ['pengaduan' => $pengaduan])
                </div>
            </div>
        </div>

        {{-- Pelapor — hanya tampil untuk staff --}}
        @if($isStaff)
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
        @endif
    </div>

    {{-- ── Riwayat Tiket (collapsible) ── --}}
    @if($pengaduan->logs->count())
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
                        'diproses'           => 'Diterima Admin',
                        'dijawab'            => 'Tercatat',
                        'ditutup_admin'      => 'Ditutup Admin',
                        'ditutup_mahasiswa'  => 'Ditutup',
                        'ditutup_otomatis'   => 'Ditutup Otomatis',
                        'selesai'            => 'Tercatat',
                        'tercatat'           => 'Ditandai Tercatat',
                        'batal_tercatat'     => 'Tanda Tercatat Dicabut',
                        // Legacy labels — tetap ditampilkan tapi dengan label netral
                        'didelegasikan'      => 'Diteruskan',
                        'ditanggapi_dosen'   => 'Ditanggapi',
                        'ditolak_dosen'      => 'Dikembalikan',
                        'diajukan_ulang'     => 'Diajukan Ulang',
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

    {{-- ══ Delete Modal ══════════════════════════════════════ --}}
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

</x-dynamic-component>
