<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            .page-title h3 {
                font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
                margin: 0 0 4px; letter-spacing: -.02em;
            }
            .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }

            .form-card {
                background: #ffffff; border-radius: 12px; padding: 32px;
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

            .section-label {
                font-size: 11px; font-weight: 700; color: #94a3b8;
                text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
            }
            .section-value {
                font-size: 14px; font-weight: 600; color: #111827; white-space: pre-wrap;
            }
        </style>
    @endpush

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h3>Konfirmasi Pengaduan</h3>
            <p>Periksa kembali data sebelum dikirim.</p>
        </div>
        <a href="{{ route('manajemenmahasiswa.pengaduan.create', ['jalur' => $payload['is_anonim'] ? 'konfidensial' : 'reguler']) }}" class="btn-back">
            <x-manajemenmahasiswa::ui.icon name="chevron-left" size="14" /> Ubah
        </a>
    </div>

    <div class="alert border-0" style="background-color: #fef3c7; color: #92400e; border-radius: 12px; font-weight: 600; font-size: 14px;">
        <div class="d-flex align-items-center gap-2">
            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" />
            Pastikan data sudah benar. Setelah dikirim, pengaduan tidak akan bisa diedit lagi.
        </div>
    </div>

    <div class="form-card">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="section-label">Kategori Masalah</div>
                <div class="section-value">{{ ucwords(str_replace('_', ' ', $payload['kategori'])) }}</div>
            </div>
            <div class="col-md-6">
                <div class="section-label">Anonim</div>
                <div class="section-value">{{ $payload['is_anonim'] ? 'Ya (identitas disembunyikan)' : 'Tidak' }}</div>
            </div>

            <div class="col-12"><hr style="border-color: #f3f4f6;"></div>

            <div class="col-12">
                <div class="section-label">Judul</div>
                <div class="section-value">{{ data_get($payload, 'template.judul', '—') ?: '—' }}</div>
            </div>

            <div class="col-12">
                <div class="section-label">Hal Aduan</div>
                <div class="section-value">{{ data_get($payload, 'template.hal_aduan', '—') ?: '—' }}</div>
            </div>

            <div class="col-12">
                <div class="section-label">Kronologi / Isi Pengaduan</div>
                <div class="section-value">{{ data_get($payload, 'template.kronologi', '—') ?: '—' }}</div>
            </div>

            <div class="col-md-6">
                <div class="section-label">Angkatan</div>
                <div class="section-value">{{ data_get($payload, 'template.angkatan', '—') ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="section-label">Waktu Kejadian</div>
                <div class="section-value">{{ data_get($payload, 'template.waktu_kejadian', '—') ?: '—' }}</div>
            </div>

            <div class="col-md-6">
                <div class="section-label">Mata Kuliah</div>
                <div class="section-value">{{ data_get($payload, 'template.mata_kuliah', '—') ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="section-label">Nama Dosen Yang Diadukan</div>
                <div class="section-value">{{ data_get($payload, 'template.nama_dosen', '—') ?: '—' }}</div>
            </div>

            <div class="col-md-6">
                <div class="section-label">Seberapa Sering Terjadi</div>
                <div class="section-value">{{ data_get($payload, 'template.frekuensi', '—') ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="section-label">Link Bukti Dukung</div>
                @php($link = data_get($payload, 'template.link_bukti'))
                <div class="section-value">
                    @if($link)
                        <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" style="color: #293C79;">{{ $link }}</a>
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.store') }}" class="mt-4">
            @csrf

            <input type="hidden" name="is_anonim" value="{{ $payload['is_anonim'] ? 1 : 0 }}">
            <input type="hidden" name="kategori" value="{{ $payload['kategori'] }}">

            @foreach(($payload['template'] ?? []) as $key => $value)
                @if(is_array($value))
                    @continue
                @endif
                <input type="hidden" name="template[{{ $key }}]" value="{{ (string) $value }}">
            @endforeach

            <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 1px solid #f3f4f6;">
                <a href="{{ route('manajemenmahasiswa.pengaduan.create', ['jalur' => $payload['is_anonim'] ? 'konfidensial' : 'reguler']) }}" class="btn-back">Kembali</a>
                <button type="submit" class="btn-post">
                    <x-manajemenmahasiswa::ui.icon name="check" size="16" /> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>

</x-manajemenmahasiswa::layouts.mahasiswa>
