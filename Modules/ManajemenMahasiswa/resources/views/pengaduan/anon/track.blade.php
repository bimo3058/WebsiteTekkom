@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Lacak Pengaduan Konfidensial')

@push('styles')
    <style>
        .pgd-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            border: 1px solid #DDE1E8;
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
            border-radius: 12px;
            padding: 24px;
            color: #334155;
            font-size: 14px;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .info-grid {
            background: #fff;
            border-radius: 12px;
            padding: 28px 32px;
            border: 1px solid #DDE1E8;
        }
        .section-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0 16px; }
        .section-divider span { font-size: 12px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
        .section-divider::after { content: ''; flex: 1; height: 1px; background: #DDE1E8; }
        /* ── Delegasi / Process Info Card ───────────────────── */
        .process-info-card {
            background: #fffaf0;
            border: 1px solid #fde68a;
            border-top: 3px solid #f59e0b;
            border-radius: 12px;
            padding: 24px 28px;
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
            border: 3px solid #293C79;
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
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            position: relative;
            padding: 0 20px;
            width: 100%;
        }
        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 15px; left: 40px; right: 40px;
            height: 2px;
            background: #DDE1E8;
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
            border: 2px solid #DDE1E8;
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
            border-color: #293C79;
            color: #293C79;
            box-shadow: 0 0 0 4px rgba(11, 38, 110, 0.1);
        }
        .step.completed .step-icon {
            background: #293C79;
            border-color: #293C79;
            color: #fff;
        }
        .step-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
        }
        .step.active .step-label { color: #111827; }
        .step.completed .step-label { color: #293C79; }
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
            'baru', 'dibaca' => 1,
            'didelegasikan' => 2,
            'selesai' => 3,
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
            <div class="step-label">Diproses</div>
        </div>
        <div class="step {{ $step >= 3 ? 'completed active' : '' }}">
            <div class="step-icon">
                @if($step >= 3) <span class="material-symbols-outlined" style="font-size: 18px;">check</span> @else 3 @endif
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
                    'dibaca'  => 'background: #e0f2fe; color: #0284c7;',
                    'didelegasikan' => 'background: #ffedd5; color: #ea580c;',
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
            <span class="pgd-badge" style="background: #f8fafc; color: #6b7280; border: 1px solid #DDE1E8;">
                🕑 {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }}
            </span>
        </div>

        <hr style="border-color: #f3f4f6; margin: 0 0 24px 0;">



        <div class="mb-4">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
        </div>
        <div>
            <div class="section-label">Kronologi / Isi Pengaduan</div>
            <div class="chronology-box">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
        </div>
    </div>


    {{-- ── Status Proses & Tanggapan Final ──────────────────────── --}}
    <div class="mb-4">

        {{-- ── Panel Status Proses (Info delegasi untuk pelapor anonim) ── --}}
        @php
            $delegasiInfo = $pengaduan->delegasiAktif ?? $pengaduan->delegasiTerakhir;
        @endphp

            @if($delegasiInfo && in_array($pengaduan->status, [
                \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN
            ]))
                <div class="process-info-card mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div style="background: #fef3c7; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 20px; color: #d97706;">hourglass_top</span>
                        </div>
                        <div>
                            @if($pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN)
                                <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 15px;">Pengaduan Sedang Ditinjau Pihak Berwenang</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                                    Pengaduan Anda telah diteruskan ke pihak yang berwenang untuk ditindaklanjuti.
                                    Proses ini membutuhkan waktu.
                                </p>
                                <div class="mt-2" style="font-size: 12px; color: #b45309;">
                                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: text-bottom;">schedule</span>
                                    Didelegasikan sejak: {{ $delegasiInfo->delegated_at->translatedFormat('d F Y, H:i') }} WIB
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Info jika delegasi ditolak dan tiket kembali ke admin ── --}}
            @if($delegasiInfo && $delegasiInfo->status === 'ditolak' && !in_array($pengaduan->status, [
                \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_SELESAI,
            ]))
                <div class="process-info-card mb-4" style="background: #fefce8; border-color: #fde68a;">
                    <div class="d-flex align-items-start gap-3">
                        <div style="background: #e0f2fe; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 20px; color: #0284c7;">replay</span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 15px;">Pengaduan Dikembalikan ke Admin</h6>
                            <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                                Pihak yang ditunjuk mengembalikan pengaduan ini ke Admin untuk ditinjau ulang.
                                Admin akan menindaklanjuti sendiri atau meneruskan ke pihak lain yang lebih tepat.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
    </div>

@endsection
