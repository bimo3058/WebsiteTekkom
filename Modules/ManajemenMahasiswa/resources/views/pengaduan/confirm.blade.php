<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            .page-title h4 {
                font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
                margin: 0 0 4px; letter-spacing: -.02em;
            }
            .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }

            .detail-card {
                background: #ffffff; border-radius: 12px; padding: 24px 28px;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
                border: 1px solid #DDE1E8;
            }

            .btn-post {
                display: inline-flex; align-items: center; gap: 8px;
                background-color: #293C79; color: white; border: none;
                border-radius: 12px; padding: 10px 24px;
                font-weight: 600; transition: all 0.2s; text-decoration: none;
            }
            .btn-post:hover {
                background-color: #415086; color: white;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(41,60,121,.3);
            }
            .btn-back {
                font-weight: 600; font-size: 13px; text-decoration: none;
                border-radius: 12px; padding: 8px 20px;
                display: inline-flex; align-items: center; gap: 6px;
                transition: all 0.2s; background: transparent;
                border: 1px solid #DDE1E8; color: #6b7280;
            }
            .btn-back:hover { background: #E7E8F0; color: #374151; border-color: #293C79; }

            /* ── Tags (Forum pattern) ── */
            .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
            .tag-label {
                font-size: 11px; font-weight: 600; padding: 4px 12px;
                border-radius: 20px; display: inline-flex;
                align-items: center; gap: 4px; white-space: nowrap;
            }
            .tag-kategori { background: #e0e7ff; color: #4f46e5; }
            .tag-jalur-reguler { background: #E7E8F0; color: #293C79; }
            .tag-jalur-konfidensial { background: #111827; color: #fff; }

            /* ── Section Labels ── */
            .section-label {
                font-size: 11px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px;
            }
            .section-value { font-size: 15px; font-weight: 600; color: #111827; }



            /* ── Section Divider (same as show.blade.php) ── */
            .section-divider {
                display: flex; align-items: center; gap: 12px; margin: 16px 0 12px;
            }
            .section-divider span {
                font-size: 12px; font-weight: 800; color: #374151;
                text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap;
            }
            .section-divider::after { content: ''; flex: 1; height: 1px; background: #DDE1E8; }

            /* ── Info Grid (same as show.blade.php) ── */
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
        </style>
    @endpush

    @php
        $kategoriLabel = ucwords(str_replace('_', ' ', $payload['kategori']));
        $isAnonim = (bool) $payload['is_anonim'];
        $backUrl = route('manajemenmahasiswa.pengaduan.create', ['jalur' => $isAnonim ? 'konfidensial' : 'reguler']);

        $infoItems = collect([
            ['label' => 'Lokasi', 'value' => data_get($payload, 'template.lokasi')],
            ['label' => 'Waktu Kejadian', 'value' => data_get($payload, 'template.waktu_kejadian')],
            ['label' => 'Angkatan', 'value' => data_get($payload, 'template.angkatan')],
            ['label' => 'Mata Kuliah', 'value' => data_get($payload, 'template.mata_kuliah')],
            ['label' => 'Dosen', 'value' => data_get($payload, 'template.nama_dosen')],
            ['label' => 'Tendik', 'value' => data_get($payload, 'template.nama_tendik')],
            ['label' => 'Frekuensi', 'value' => data_get($payload, 'template.frekuensi')],
        ]);

        $linkBukti = data_get($payload, 'template.link_bukti');
    @endphp

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h4>Konfirmasi Pengaduan</h4>
            <p>Periksa kembali data sebelum dikirim.</p>
        </div>
        <a href="{{ $backUrl }}" class="btn-back">
            <x-manajemenmahasiswa::ui.icon name="chevron-left" size="14" /> Ubah
        </a>
    </div>

    <div class="alert border-0" style="background-color: #fef3c7; color: #92400e; border-radius: 12px; font-weight: 600; font-size: 14px;">
        <div class="d-flex align-items-center gap-2">
            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" />
            Pastikan data sudah benar. Setelah dikirim, pengaduan tidak akan bisa diedit lagi.
        </div>
    </div>

    <div class="detail-card">

        {{-- Tags --}}
        <div class="tags-row">
            <span class="tag-label tag-kategori">{{ $kategoriLabel }}</span>
            @if($isAnonim)
                <span class="tag-label tag-jalur-konfidensial">
                    <x-manajemenmahasiswa::ui.icon name="shield-02" size="11" /> Konfidensial
                </span>
            @else
                <span class="tag-label tag-jalur-reguler">
                    <x-manajemenmahasiswa::ui.icon name="user-circle" size="11" /> Reguler
                </span>
            @endif
        </div>

        {{-- Judul --}}
        <h4 class="fw-bold text-dark mb-2" style="font-size: 20px; line-height: 1.4;">
            {{ data_get($payload, 'template.judul', '-') }}
        </h4>
        <div style="font-size: 13px; color: #9ca3af; margin-bottom: 16px;">
            Oleh {{ auth()->user()->name ?? 'Mahasiswa' }} · {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>

        <div class="section-divider" style="margin-top: 0;">
            <span>Detail Pengaduan</span>
        </div>

        {{-- Hal Aduan --}}
        <div class="mb-3">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($payload, 'template.hal_aduan', '—') ?: '—' }}</div>
        </div>

        {{-- Kronologi --}}
        <div class="mb-4">
            <div class="section-label">Kronologi / Isi Pengaduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;">{{ data_get($payload, 'template.kronologi', '-') }}</div>
        </div>

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

        {{-- Submit Form --}}
        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.store') }}" class="mt-3">
            @csrf

            <input type="hidden" name="is_anonim" value="{{ $payload['is_anonim'] ? 1 : 0 }}">
            <input type="hidden" name="kategori" value="{{ $payload['kategori'] }}">

            @foreach(($payload['template'] ?? []) as $key => $value)
                @if(is_array($value))
                    @continue
                @endif
                <input type="hidden" name="template[{{ $key }}]" value="{{ (string) $value }}">
            @endforeach

            <div class="d-flex justify-content-end gap-3 mt-3 pt-3" style="border-top: 1px solid #f3f4f6;">
                <a href="{{ $backUrl }}" class="btn-back">Kembali</a>
                <button type="submit" class="btn-post">
                    <x-manajemenmahasiswa::ui.icon name="check" size="16" /> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>

</x-manajemenmahasiswa::layouts.mahasiswa>
