<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            /* ── Base Card ─────────────────────────────────────────── */
            .pgd-card {
                background: #ffffff;
                border-radius: 14px;
                padding: 32px;
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
                border: none;
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

            /* ── Section Informasi Lanjut ───────────────────────────── */
            .info-grid {
                background: #f8fafc;
                border-radius: 14px;
                padding: 28px 32px;
                border: 1px solid #e2e8f0;
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
                background: #cbd5e1;
            }
            .info-item {
                min-width: 0;
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

            /* ── Badges ────────────────────────────────────────────── */
            .pgd-badge {
                font-size: 13px;
                font-weight: 600;
                padding: 6px 16px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            /* ── Section Labels ────────────────────────────────────── */
            .section-label {
                font-size: 12px;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 0.5px;
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

            /* ── Answer / Reply ────────────────────────────────────── */
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
                padding: 40px 24px;
            }

            /* ── Buttons ───────────────────────────────────────────── */
            .btn-custom {
                background-color: #4D4DFF;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 10px 24px;
                font-weight: 600;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-custom:hover {
                background-color: #3b3be5;
                color: white;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(77, 77, 255, 0.3);
            }
            .btn-outline-custom {
                background-color: transparent;
                color: #6b7280;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                padding: 8px 20px;
                font-weight: 600;
                transition: all 0.2s;
            }
            .btn-outline-custom:hover {
                background-color: #f3f4f6;
                color: #374151;
            }
            .btn-danger-soft {
                background-color: #fef2f2;
                color: #dc2626;
                border: 1.5px solid #fecaca;
                border-radius: 8px;
                padding: 8px 18px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-danger-soft:hover {
                background-color: #fee2e2;
                color: #b91c1c;
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

            /* ── Pelapor Box ───────────────────────────────────────── */
            .pelapor-box {
                background: #ffffff;
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

            /* ── Delete Modal ──────────────────────────────────────── */
            .modal-custom .modal-content {
                border-radius: 16px;
                border: none;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            }
        </style>
    @endpush

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h3 class="fw-bold mb-0 text-dark">Detail Pengaduan</h3>
                <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 13px;">
                    #{{ $pengaduan->id }}
                </span>
            </div>
            <p class="text-muted fw-medium mb-0">
                🕑 Dibuat pada {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($canDelete)
                <button type="button" class="btn-danger-soft" data-bs-toggle="modal" data-bs-target="#deleteShowModal">
                    🗑️ Hapus
                </button>
            @endif
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-outline-custom text-decoration-none">
                ← Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 fw-medium">
                <span>✓</span> {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ── Main Card: Judul + Badges + Hal Aduan + Kronologi ─── --}}
    <div class="pgd-card mb-4">

        <h4 class="fw-bold text-dark mb-3" style="font-size: 22px;">
            {{ data_get($pengaduan, 'data_template.judul', '-') }}
        </h4>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="pgd-badge" style="background: #e0e7ff; color: #4f46e5;">
                {{ $kategoriLabel ?? ucwords(str_replace('_', ' ', (string) $pengaduan->kategori)) }}
            </span>
            @php
                $statusStyle = match(strtolower($pengaduan->status)) {
                    'dijawab' => 'background: #dcfce7; color: #16a34a;',
                    'dibaca'  => 'background: #e0f2fe; color: #0284c7;',
                    default   => 'background: #f3f4f6; color: #4b5563;',
                };
            @endphp
            <span class="pgd-badge" style="{{ $statusStyle }}">
                {{ ucfirst($pengaduan->status) }}
            </span>
            @if($pengaduan->is_anonim)
                <span class="pgd-badge" style="background: #111827; color: white;">
                    🔒 Anonim
                </span>
            @endif
        </div>

        <hr style="border-color: #f3f4f6; margin: 0 0 28px 0;">

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="section-label">Hal Aduan</div>
                <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
            </div>
            <div class="col-lg-8">
                <div class="section-label">Kronologi / Isi Pengaduan</div>
                <div class="chronology-box">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Informasi Lanjut ─────────────────────────────────────── --}}
    @php
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
    @endphp

    <div class="info-grid mb-4">
        <div class="info-grid-title">Informasi Lanjut</div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Lokasi Kejadian</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.lokasi') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.lokasi', '—') ?: '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Waktu Kejadian</div>
                    <div class="info-item-value {{ !$waktuKejadian ? 'empty' : '' }}">
                        @if($waktuKejadian)
                            {{ \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Angkatan</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.angkatan') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.angkatan', '—') ?: '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Mata Kuliah</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.mata_kuliah') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Dosen Terkait</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.nama_dosen') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.nama_dosen', '—') ?: '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Tendik Terkait</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.nama_tendik') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.nama_tendik', '—') ?: '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
                    <div class="info-item-label">Seberapa Sering Terjadi</div>
                    <div class="info-item-value {{ !data_get($pengaduan, 'data_template.frekuensi') ? 'empty' : '' }}">
                        {{ data_get($pengaduan, 'data_template.frekuensi', '—') ?: '—' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-item">
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
        </div>

        {{-- Pelapor --}}
        <hr style="border-color: #cbd5e1; margin: 8px 0 20px 0;">
        <div class="info-item-label mb-2">Pelapor</div>
        <div class="pelapor-box" style="max-width: 360px;">
            <div class="pelapor-avatar">👤</div>
            <div>
                @if($pengaduan->is_anonim)
                    <div class="fw-bold text-dark" style="font-size: 15px;">Anonim</div>
                    <div class="text-muted" style="font-size: 12px;">Disembunyikan sistem</div>
                @else
                    <div class="fw-bold text-dark" style="font-size: 15px;">{{ optional($pengaduan->pelapor)->name ?? '—' }}</div>
                    @if($isStaff && data_get($pengaduan, 'data_template.angkatan'))
                        <div class="text-muted" style="font-size: 12px;">Angkatan {{ data_get($pengaduan, 'data_template.angkatan') }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ── Tanggapan Admin ─────────────────────────────────────── --}}
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <span style="color: #4D4DFF;">💬</span> Tanggapan Admin
        </h5>

        @if($pengaduan->jawaban)
            <div class="answer-card">
                <div class="d-flex align-items-start gap-3">
                    <div class="answer-icon-badge">✓</div>
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
        @else
            <div class="empty-answer">
                <div style="font-size: 40px; color: #94a3b8; margin-bottom: 12px;">⏳</div>
                <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada tanggapan</div>
                <div class="text-muted" style="font-size: 13px;">Admin belum memberikan balasan untuk pengaduan ini.</div>
            </div>
        @endif
    </div>

    {{-- ── Form Reply (Staff only) ─────────────────────────────── --}}
    @if($canReply)
        <div class="pgd-card" style="border-top: 3px solid #4D4DFF;">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                ✏️ <span>{{ $pengaduan->jawaban ? 'Perbarui Jawaban' : 'Tulis Jawaban' }}</span>
            </h6>
            <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.reply', $pengaduan->id) }}">
                @csrf
                <div class="mb-3">
                    <textarea class="form-control w-100 form-control-custom" name="jawaban" rows="5" required
                        placeholder="Tulis jawaban atau tindakan yang telah diambil…">{{ old('jawaban', $pengaduan->jawaban) }}</textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-custom">
                        {{ $pengaduan->jawaban ? '🔄 Perbarui Jawaban' : '📤 Kirim Jawaban' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- ── Delete Modal ─────────────────────────────────────────── --}}
    @if($canDelete)
        <div class="modal fade modal-custom" id="deleteShowModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
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