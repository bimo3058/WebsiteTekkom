<x-manajemenmahasiswa::layouts.admin>

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
            .back-bar a {
                font-weight: 600;
                font-size: 13px;
                text-decoration: none;
                border-radius: 8px;
                padding: 8px 16px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: all 0.2s;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151;
            }
            .back-bar a:hover { background: #f9fafb; color: #111827; }

            /* ── Cards ─────────────────────────────────────────────── */
            .custom-card {
                background: #ffffff;
                border-radius: 14px;
                padding: 32px;
                border: 1px solid #e5e7eb;
                position: relative;
                overflow: hidden;
                margin-bottom: 24px;
            }
            .card-header-accent {
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 4px;
            }

            /* ── Labels ────────────────────────────────────────────── */
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
                white-space: pre-wrap;
                line-height: 1.7;
            }

            /* ── Action Forms ──────────────────────────────────────── */
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
            .btn-action {
                font-weight: 600;
                border-radius: 8px;
                padding: 12px 24px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }
            .btn-reject {
                background: #fff;
                color: #dc2626;
                border: 1px solid #fecaca;
            }
            .btn-reject:hover { background: #fef2f2; }
            .btn-submit {
                background: linear-gradient(135deg, #16a34a, #22c55e);
                color: white;
                border: none;
            }
            .btn-submit:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(34,197,94,0.3);
                color: white;
            }
        </style>
    @endpush

    {{-- ── Back Bar ──────────────────────────────────────────── --}}
    <div class="back-bar">
        <a href="{{ route('manajemenmahasiswa.pengaduan.delegasi.index') }}">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Kembali ke Daftar Delegasi
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 mb-4" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; font-weight: 500; font-size: 14px;">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                {{ session('success') }}
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

    {{-- ── Delegasi Info Card ────────────────────────────────── --}}
    <div class="custom-card" style="background: #fffaf0; border-color: #fde68a;">
        <div class="card-header-accent" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"></div>
        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="color: #b45309 !important;">
            <span class="material-symbols-outlined">sync</span> Instruksi Delegasi
        </h5>
        
        <div class="row g-4 mb-3">
            <div class="col-md-6">
                <div class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Didelegasikan Oleh Admin</div>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706;">
                        <span class="material-symbols-outlined" style="font-size: 18px;">admin_panel_settings</span>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 14px; color: #111827;">{{ optional($delegasi->delegatedBy)->name ?? 'Admin' }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ $delegasi->delegated_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 rounded" style="background: #ffffff; border: 1px dashed #fcd34d;">
            <div class="text-muted mb-2" style="font-size: 12px; font-weight: 600;">CATATAN / INSTRUKSI ADMIN:</div>
            <div style="font-size: 15px; color: #374151; line-height: 1.6;">{{ $delegasi->notes_admin }}</div>
        </div>
    </div>

    {{-- ── Detail Pengaduan Card ─────────────────────────────── --}}
    @php
        $pengaduan = $delegasi->pengaduan;
        $kategoriRaw = (string) $pengaduan->kategori;
        $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
    @endphp
    <div class="custom-card">
        <div class="card-header-accent" style="background: linear-gradient(135deg, #4D4DFF, #7c7cff);"></div>
        
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="text-muted mb-1" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px;">TIKET #{{ $pengaduan->id }}</div>
                <h4 class="fw-bold text-dark m-0" style="font-size: 20px;">{{ data_get($pengaduan, 'data_template.judul', '-') }}</h4>
            </div>
            <span class="badge" style="background: #e0e7ff; color: #4f46e5; padding: 6px 12px; font-size: 12px;">
                {{ ucwords(str_replace('_', ' ', $kategori)) }}
            </span>
        </div>

        <div class="mb-4">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
        </div>
        
        <div class="mb-4">
            <div class="section-label">Kronologi Lengkap</div>
            <div class="p-4 rounded" style="background: #f8fafc; border-left: 4px solid #4D4DFF; color: #334155; font-size: 14px; line-height: 1.8; white-space: pre-wrap;">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
        </div>

        <div class="row g-4 mt-2 p-4 rounded" style="background: #fafbfc; border: 1px solid #f1f5f9;">
            <div class="col-md-4">
                <div class="section-label">Waktu Kejadian</div>
                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">
                    {{ data_get($pengaduan, 'data_template.waktu_kejadian') ?? data_get($pengaduan, 'data_template.tanggal_kejadian', '—') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="section-label">Lokasi</div>
                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">
                    {{ data_get($pengaduan, 'data_template.lokasi', '—') ?: '—' }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="section-label">Mata Kuliah / Bukti</div>
                <div style="font-size: 13px; font-weight: 600; color: #1e293b;">
                    @if(data_get($pengaduan, 'data_template.link_bukti'))
                        <a href="{{ data_get($pengaduan, 'data_template.link_bukti') }}" target="_blank" style="color: #4D4DFF;">Lihat Bukti ↗</a>
                    @else
                        {{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Action Card ───────────────────────────────────────── --}}
    @if($delegasi->status === 'aktif')
        <div class="custom-card" style="border-top: 3px solid #16a34a;">
            <h5 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="color: #16a34a;">edit_square</span> Berikan Tanggapan
            </h5>
            
            <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.respond', $delegasi->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted" style="font-size: 13px;">Tanggapan / Jawaban (Dikirim ke Mahasiswa)</label>
                    <textarea class="form-control form-control-custom w-100" name="tanggapan" rows="6" required
                        placeholder="Tulis jawaban lengkap atas pengaduan ini. Pesan ini akan diteruskan ke mahasiswa..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted" style="font-size: 13px;">Catatan Internal untuk Admin (Opsional)</label>
                    <textarea class="form-control form-control-custom w-100" name="notes_balik" rows="2"
                        placeholder="Pesan tambahan hanya untuk Admin..."></textarea>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-reject btn-action" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <span class="material-symbols-outlined" style="font-size: 18px;">cancel</span> Tolak Delegasi
                    </button>
                    <button type="submit" class="btn btn-submit btn-action">
                        <span class="material-symbols-outlined" style="font-size: 18px;">send</span> Kirim Tanggapan
                    </button>
                </div>
            </form>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark mb-0">Tolak Delegasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.delegasi.reject', $delegasi->id) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <p class="text-muted" style="font-size: 14px;">Apakah Anda yakin ingin menolak tiket ini? Silakan berikan alasan penolakan untuk Admin.</p>
                            <textarea class="form-control" name="notes_balik" rows="3" required
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
    @else
        <div class="custom-card" style="background: #f8fafc;">
            <div class="text-center py-4">
                @if($delegasi->status === 'ditanggapi')
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #16a34a; margin-bottom: 12px;">task_alt</span>
                    <h5 class="fw-bold text-dark">Anda telah menanggapi tiket ini</h5>
                    <p class="text-muted mb-0" style="font-size: 14px;">Tanggapan telah dikirim kembali ke Admin untuk diteruskan ke Mahasiswa.</p>
                @elseif($delegasi->status === 'ditolak')
                    <span class="material-symbols-outlined" style="font-size: 48px; color: #dc2626; margin-bottom: 12px;">cancel</span>
                    <h5 class="fw-bold text-dark">Anda telah menolak tiket ini</h5>
                    <p class="text-muted mb-0" style="font-size: 14px;">Alasan penolakan: {{ $delegasi->notes_balik }}</p>
                @endif
            </div>
        </div>
    @endif

</x-manajemenmahasiswa::layouts.admin>
