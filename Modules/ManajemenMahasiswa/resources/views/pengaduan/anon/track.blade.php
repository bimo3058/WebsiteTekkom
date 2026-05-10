@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Lacak Pengaduan Konfidensial')

@push('styles')
    <style>
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
        .pgd-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
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
        .info-grid {
            background: #fff;
            border-radius: 14px;
            padding: 28px 32px;
            border: 1px solid #e5e7eb;
        }
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
        .empty-answer {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            background: #f8fafc;
            text-align: center;
            padding: 48px 24px;
        }
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

        /* Stepper Styles */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
            padding: 0 20px;
        }
        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 15px; left: 40px; right: 40px;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 60px;
        }
        .step-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #e5e7eb;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #9ca3af;
            transition: all 0.3s;
        }
        .step.active .step-icon {
            border-color: #4D4DFF;
            color: #4D4DFF;
            box-shadow: 0 0 0 4px rgba(77, 77, 255, 0.1);
        }
        .step.completed .step-icon {
            background: #4D4DFF;
            border-color: #4D4DFF;
            color: #fff;
        }
        .step-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
        }
        .step.active .step-label { color: #111827; }
        .step.completed .step-label { color: #4D4DFF; }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Status Pengaduan</h3>
        <p class="text-muted mb-0" style="font-size: 14px;">Pantau progress tindak lanjut aduan konfidensial Anda di sini.</p>
    </div>

    @php
        $status = strtolower($pengaduan->status);
        $step = match($status) {
            'baru' => 1,
            'dibaca' => 2,
            'didelegasikan', 'ditanggapi_dosen' => 3,
            'dijawab', 'diajukan_ulang' => 4,
            'selesai' => 5,
            default => 1,
        };
    @endphp

    <div class="stepper-wrapper">
        <div class="step {{ $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' }}">
            <div class="step-icon">
                @if($step > 1) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 1 @endif
            </div>
            <div class="step-label">Terkirim</div>
        </div>
        <div class="step {{ $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' }}">
            <div class="step-icon">
                @if($step > 2) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 2 @endif
            </div>
            <div class="step-label">Dibaca</div>
        </div>
        <div class="step {{ $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' }}">
            <div class="step-icon">
                @if($step > 3) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 3 @endif
            </div>
            <div class="step-label">Diproses</div>
        </div>
        <div class="step {{ $step >= 4 ? ($step > 4 ? 'completed' : 'active') : '' }}">
            <div class="step-icon">
                @if($step > 4) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 4 @endif
            </div>
            <div class="step-label">Dijawab</div>
        </div>
        <div class="step {{ $step >= 5 ? 'completed active' : '' }}">
            <div class="step-icon">
                @if($step >= 5) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 5 @endif
            </div>
            <div class="step-label">Selesai</div>
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
        <h4 class="fw-bold text-dark mb-3" style="font-size: 20px; line-height: 1.4;">
            {{ data_get($pengaduan, 'data_template.judul', '-') }}
        </h4>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @php
                $kategoriRaw = (string) $pengaduan->kategori;
                $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
            @endphp
            <span class="pgd-badge" style="background: #e0e7ff; color: #4f46e5;">
                {{ ucwords(str_replace('_', ' ', $kategori)) }}
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
            <span class="pgd-badge" style="background: #111827; color: white;">
                <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">lock</span> Konfidensial
            </span>
            <span class="pgd-badge" style="background: #f8fafc; color: #6b7280; border: 1px solid #e5e7eb;">
                🕑 {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }}
            </span>
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

    <div class="row g-4 mb-4">
        {{-- ── Kolom Kiri: Riwayat ─────────────────────────────── --}}
        <div class="col-lg-4 order-lg-2">
            <div class="info-grid" style="padding: 24px;">
                <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="font-size: 14px; text-transform: uppercase;">
                    Riwayat Tiket
                </h6>
                <div class="timeline-container">
                    @foreach($pengaduan->logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan: Tanggapan Final ────────────────────── --}}
        <div class="col-lg-8 order-lg-1">
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 16px;">
                    <span class="material-symbols-outlined" style="color: #4D4DFF; font-size: 20px; vertical-align: text-bottom;">chat</span> Jawaban Pengaduan
                </h5>

                @if($pengaduan->jawaban)
                    <div class="answer-card">
                        <div class="d-flex align-items-start gap-3">
                            <div style="background: #22c55e; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(34,197,94,0.3); flex-shrink: 0;">
                                <span class="material-symbols-outlined" style="font-size: 16px; font-weight: bold;">check</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="white-space: pre-wrap; color: #166534; font-size: 14px; line-height: 1.8;">{{ $pengaduan->jawaban }}</div>
                                <div class="mt-3 text-muted fw-medium" style="font-size: 12px;">
                                    Dijawab pada: {{ optional($pengaduan->answered_at)->translatedFormat('d F Y, H:i') ?? '—' }} WIB
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mahasiswa Actions: Tandai Selesai atau Ajukan Ulang --}}
                    @if($pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIJAWAB)
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
                                <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.track.close', $pengaduan->anon_token) }}">
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
                        <div class="text-muted" style="font-size: 13px;">Admin sedang meninjau atau mendelegasikan aduan ini.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reopen Modal --}}
    @if($pengaduan->canReopen() && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIJAWAB)
        <div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none;">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                            <span class="material-symbols-outlined" style="color: #d97706;">replay</span> Ajukan Ulang Pengaduan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.track.reopen', $pengaduan->anon_token) }}">
                        @csrf
                        <div class="modal-body px-4 py-4">
                            <div class="alert alert-warning border-0" style="background: #fffbeb; font-size: 13px;">
                                Anda memiliki batas <strong>{{ \Modules\ManajemenMahasiswa\Models\Pengaduan::MAX_REOPEN - $pengaduan->reopen_count }} kali</strong> pengajuan ulang tersisa.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted" style="font-size: 13px;">Alasan Pengajuan Ulang (Wajib)</label>
                                <textarea class="form-control" name="reopen_reason" rows="4" required
                                    placeholder="Jelaskan secara rinci mengapa tanggapan admin belum menyelesaikan masalah Anda..." style="border-radius: 8px; background: #f9fafb; border: 2px solid #f3f4f6;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                            <button type="submit" class="btn btn-warning fw-bold" style="border-radius: 8px;">Ajukan Ulang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
