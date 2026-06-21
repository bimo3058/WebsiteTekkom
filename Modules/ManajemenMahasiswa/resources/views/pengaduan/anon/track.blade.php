@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Lacak Pengaduan Konfidensial')

@push('styles')
    <style>
        /* ── Card ── */
        .detail-card {
            background: #ffffff; border-radius: 12px; padding: 32px;
            border: 1px solid #DDE1E8;
            box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
        }

        /* ── Page Title ── */
        .page-title h3 {
            font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
            margin: 0 0 4px; letter-spacing: -.02em;
        }
        .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }

        /* ── Tags ── */
        .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
        .tag-label {
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 20px; display: inline-flex;
            align-items: center; gap: 4px; white-space: nowrap;
        }
        .tag-kategori { background: #e0e7ff; color: #4f46e5; }
        .tag-baru { background: #f3f4f6; color: #4b5563; }
        .tag-dibaca { background: #e0f2fe; color: #0284c7; }
        .tag-didelegasikan { background: #ffedd5; color: #ea580c; }
        .tag-selesai { background: #bbf7d0; color: #15803d; }
        .tag-anonim { background: #111827; color: #fff; }

        /* ── Section Labels ── */
        .section-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;
        }
        .section-value { font-size: 15px; font-weight: 600; color: #111827; }



        /* ── Section Divider ── */
        .section-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0 16px; }
        .section-divider span { font-size: 12px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
        .section-divider::after { content: ''; flex: 1; height: 1px; background: #DDE1E8; }

        /* ── Info Grid ── */
        .info-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px 20px;
        }
        @media (max-width: 992px) { .info-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .info-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px;
        }
        .info-item-value { font-size: 14px; font-weight: 600; color: #1e293b; }



        /* ── Process Info Card ── */
        .process-info-card {
            background: #fffaf0; border: 1px solid #fde68a;
            border-top: 3px solid #f59e0b; border-radius: 12px; padding: 24px 28px;
        }
    </style>
@endpush

@section('content')
    <div class="page-title mb-4">
        <h3>Status Pengaduan</h3>
        <p>Pantau progress tindak lanjut aduan konfidensial Anda di sini.</p>
    </div>

    @php
        $status = strtolower($pengaduan->status);
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
    @endphp

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
            style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; border: none; font-weight: 600;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

    {{-- ── Single Card (same as mahasiswa view in show.blade.php) ── --}}
    <div class="detail-card mb-4">

        {{-- Tags --}}
        <div class="tags-row">
            @php
                $kategoriRaw = (string) $pengaduan->kategori;
                $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
                $statusStyle = match($status) {
                    'selesai' => 'tag-selesai',
                    'dibaca' => 'tag-dibaca',
                    'didelegasikan' => 'tag-didelegasikan',
                    default => 'tag-baru',
                };
            @endphp
            <span class="tag-label tag-kategori">{{ ucwords(str_replace('_', ' ', $kategori)) }}</span>
            <span class="tag-label {{ $statusStyle }}">{{ $pengaduan->statusLabel() }}</span>
            <span class="tag-label tag-anonim">
                <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Konfidensial
            </span>
        </div>

        {{-- Judul --}}
        <h4 class="fw-bold text-dark mb-2" style="font-size: 20px; line-height: 1.4;">
            {{ data_get($pengaduan, 'data_template.judul', '-') }}
        </h4>
        <div style="font-size: 13px; color: #9ca3af; margin-bottom: 24px;">
            Diajukan {{ optional($pengaduan->created_at)->translatedFormat('d F Y, H:i') }} WIB
        </div>

        <div class="section-divider" style="margin-top: 0;">
            <span>Detail Pengaduan</span>
        </div>

        {{-- Hal Aduan --}}
        <div class="mb-4">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—' }}</div>
        </div>

        {{-- Kronologi --}}
        <div class="mb-4">
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
                ['label' => 'Dosen', 'value' => data_get($pengaduan, 'data_template.nama_dosen')],
                ['label' => 'Tendik', 'value' => data_get($pengaduan, 'data_template.nama_tendik')],
                ['label' => 'Frekuensi', 'value' => data_get($pengaduan, 'data_template.frekuensi')],
            ]);
        @endphp

        <div class="section-divider">
            <span>Informasi Tambahan</span>
        </div>
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
                        <a href="{{ $linkBukti }}" target="_blank" rel="noopener noreferrer"
                           style="color: #293C79; text-decoration: none;">Lihat Bukti ↗</a>
                    @else
                        <span style="color: #cbd5e1;">—</span>
                    @endif
                </div>
            </div>
        </div>


    </div>

    {{-- ── Delegasi Info (simplified for anon) ── --}}
    @php $delegasiInfo = $pengaduan->delegasiAktif ?? $pengaduan->delegasiTerakhir; @endphp

    @if($delegasiInfo && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN)
        <div class="process-info-card mb-4">
            <div class="d-flex align-items-start gap-3">
                <div style="background: #fef3c7; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #d97706;">
                    <x-manajemenmahasiswa::ui.icon name="clock-02" size="20" />
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 15px;">Pengaduan Sedang Ditinjau Pihak Berwenang</h6>
                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                        Pengaduan Anda telah diteruskan ke pihak yang berwenang untuk ditindaklanjuti.
                        Proses ini membutuhkan waktu.
                    </p>
                    <div class="mt-2 d-flex align-items-center gap-1" style="font-size: 12px; color: #b45309;">
                        <x-manajemenmahasiswa::ui.icon name="clock-02" size="13" />
                        Didelegasikan sejak: {{ $delegasiInfo->delegated_at->translatedFormat('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($delegasiInfo && $delegasiInfo->status === 'ditolak' && !in_array($pengaduan->status, [
        \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_SELESAI,
    ]))
        <div class="process-info-card mb-4" style="background: #fefce8; border-color: #fde68a;">
            <div class="d-flex align-items-start gap-3">
                <div style="background: #e0f2fe; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #0284c7;">
                    <x-manajemenmahasiswa::ui.icon name="arrow-circle-left" size="20" />
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

@endsection
