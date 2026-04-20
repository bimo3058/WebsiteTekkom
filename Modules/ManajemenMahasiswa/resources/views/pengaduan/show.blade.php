<x-dynamic-component :component="$isAdmin ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .custom-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 32px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                border: none;
            }
            .custom-card-alt {
                background: #f8fafc;
                border-radius: 12px;
                padding: 24px;
                border: 1px solid #e2e8f0;
            }
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
            }
            .btn-outline-custom {
                background-color: transparent;
                color: #6b7280;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                padding: 8px 20px;
                font-weight: 600;
            }
            .btn-outline-custom:hover {
                background-color: #f3f4f6;
                color: #374151;
            }
            .badge-custom {
                font-size: 13px;
                font-weight: 600;
                padding: 6px 16px;
                border-radius: 20px;
            }
            .section-label {
                font-size: 12px;
                font-weight: 700;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }
            .section-value {
                font-size: 15px;
                font-weight: 600;
                color: #111827;
            }
            .chronology-box {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 20px;
                color: #334155;
                font-size: 14px;
                line-height: 1.6;
                white-space: pre-wrap;
            }
            .answer-box {
                background: #dcfce7;
                border: 1px solid #bbf7d0;
                border-radius: 12px;
                padding: 20px;
                color: #166534;
                font-size: 14px;
                line-height: 1.6;
                position: relative;
            }
            .answer-icon {
                position: absolute;
                top: -12px;
                left: -12px;
                background: #22c55e;
                color: white;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 3px solid white;
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
        </style>
    @endpush

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <h3 class="fw-bold mb-0 text-dark">Detail Pengaduan</h3>
                <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace;">
                    #{{ $pengaduan->id }}
                </span>
            </div>
            <p class="text-muted fw-medium mb-0">
                🕑 Dibuat pada {{ optional($pengaduan->created_at)->translatedFormat('d F Y H:i') }}
            </p>
        </div>

        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-outline-custom text-decoration-none">
            ← Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 fw-medium">
                <span>✓</span> {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Detail -->
        <div class="col-lg-7">
            <div class="custom-card position-relative overflow-hidden h-100">
                <div style="height: 4px; background: linear-gradient(135deg, #4D4DFF 0%, #6b6bff 100%); position: absolute; top: 0; left: 0; right: 0;"></div>
                
                <h4 class="fw-bold text-dark mb-3">
                    {{ data_get($pengaduan, 'data_template.judul', '-') }}
                </h4>
                
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge-custom" style="background: #e0e7ff; color: #4f46e5;">
                        {{ str_replace('_', ' ', ucfirst($pengaduan->kategori)) }}
                    </span>
                    @php
                        $statusBadge = match(strtolower($pengaduan->status)) {
                            'dijawab' => 'background: #dcfce7; color: #16a34a;',
                            'dibaca'  => 'background: #e0f2fe; color: #0284c7;',
                            default   => 'background: #f3f4f6; color: #4b5563;',
                        };
                    @endphp
                    <span class="badge-custom" style="{{ $statusBadge }}">
                        {{ ucfirst($pengaduan->status) }}
                    </span>
                    @if($pengaduan->is_anonim)
                        <span class="badge-custom" style="background: #111827; color: white;">
                            🔒 Anonim
                        </span>
                    @endif
                </div>

                <hr class="my-4" style="border-color: #f3f4f6;">

                @if($isAdmin)
                    <div class="mb-4">
                        <div class="section-label">Pelapor</div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                👤
                            </div>
                            <div>
                                @if($pengaduan->is_anonim)
                                    <div class="section-value">Anonim</div>
                                    <div class="text-muted" style="font-size: 12px;">Disembunyikan dari sistem</div>
                                @else
                                    <div class="section-value">{{ optional($pengaduan->pelapor)->name ?? '—' }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="section-label">Kronologi / Isi Pengaduan</div>
                    <div class="chronology-box mt-2">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
                </div>

                <div class="mb-4">
                    <div class="section-label">Hal Aduan</div>
                    <div class="section-value mt-2" style="white-space: pre-wrap;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div style="background: #fff7ed; padding: 10px; border-radius: 10px; color: #f97316;">📍</div>
                            <div>
                                <div class="section-label">Lokasi</div>
                                <div class="section-value">{{ data_get($pengaduan, 'data_template.lokasi', '—') ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div style="background: #eff6ff; padding: 10px; border-radius: 10px; color: #3b82f6;">📅</div>
                            <div>
                                <div class="section-label">Waktu Kejadian</div>
                                @php
                                    $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
                                        ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
                                @endphp
                                <div class="section-value">{{ $waktuKejadian ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div style="background: #f5f3ff; padding: 10px; border-radius: 10px; color: #7c3aed;">🧾</div>
                            <div>
                                <div class="section-label">Seberapa Sering Terjadi</div>
                                <div class="section-value">{{ data_get($pengaduan, 'data_template.frekuensi', '—') ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div style="background: #ecfeff; padding: 10px; border-radius: 10px; color: #0891b2;">🔗</div>
                            <div>
                                <div class="section-label">Link Bukti Dukung</div>
                                @php
                                    $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
                                @endphp
                                @if($linkBukti)
                                    <a class="section-value" href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer">
                                        {{ Str::limit($linkBukti, 60) }}
                                    </a>
                                @else
                                    <div class="section-value">—</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4" style="background: #f8fafc; border-radius: 12px;">
                    <div class="section-label mb-3">Informasi Akademik Terkait</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Angkatan</div>
                            <div class="section-value text-truncate" title="{{ data_get($pengaduan, 'data_template.angkatan', '—') ?: '—' }}">{{ data_get($pengaduan, 'data_template.angkatan', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Mata Kuliah</div>
                            <div class="section-value text-truncate" title="{{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}">{{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Nama Dosen</div>
                            <div class="section-value text-truncate" title="{{ data_get($pengaduan, 'data_template.nama_dosen', '—') ?: '—' }}">{{ data_get($pengaduan, 'data_template.nama_dosen', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Nama Tendik</div>
                            <div class="section-value text-truncate" title="{{ data_get($pengaduan, 'data_template.nama_tendik', '—') ?: '—' }}">{{ data_get($pengaduan, 'data_template.nama_tendik', '—') ?: '—' }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar Answer -->
        <div class="col-lg-5">
            <div class="custom-card-alt h-100 position-sticky" style="top: 2rem;">
                <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                    <span style="color: #4D4DFF;">💬</span> Tanggapan Admin
                </h6>

                @if($pengaduan->jawaban)
                    <div class="answer-box mb-3">
                        <div class="answer-icon">✓</div>
                        <div style="white-space: pre-wrap;">{{ $pengaduan->jawaban }}</div>
                    </div>
                    <div class="text-end text-muted fw-medium" style="font-size: 12px;">
                        Dijawab pada: {{ optional($pengaduan->answered_at)->translatedFormat('d F Y H:i') ?? '—' }}
                    </div>
                @else
                    <div class="text-center py-5" style="border: 2px dashed #cbd5e1; border-radius: 12px; background: white;">
                        <div style="font-size: 32px; color: #94a3b8; margin-bottom: 12px;">⏳</div>
                        <div class="fw-bold text-dark mb-1">Belum ada tanggapan</div>
                        <div class="text-muted" style="font-size: 13px;">Admin belum memberikan balasan untuk pengaduan ini.</div>
                    </div>
                @endif

                @if($isAdmin)
                    <hr class="my-4" style="border-color: #cbd5e1;">
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.reply', $pengaduan->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="section-label d-block text-dark">Tulis Jawaban Baru / Perbarui</label>
                            <textarea class="form-control w-100 form-control-custom" name="jawaban" rows="5" required placeholder="Tulis jawaban atau tindakan yang telah diambil…">{{ old('jawaban', $pengaduan->jawaban) }}</textarea>
                        </div>
                        <button type="submit" class="btn-custom w-100">
                            {{ $pengaduan->jawaban ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>