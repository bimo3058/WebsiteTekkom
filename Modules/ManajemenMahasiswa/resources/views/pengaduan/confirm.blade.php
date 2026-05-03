<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .custom-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 32px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                border: none;
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
                text-decoration: none;
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
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-outline-custom:hover {
                background-color: #f3f4f6;
                color: #374151;
            }
            .section-label {
                font-size: 12px;
                font-weight: 700;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 6px;
            }
            .section-value {
                font-size: 14px;
                font-weight: 600;
                color: #111827;
                white-space: pre-wrap;
            }
        </style>
    @endpush

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Konfirmasi Pengaduan</h3>
            <p class="text-muted mb-0 fw-medium">Periksa kembali data sebelum dikirim.</p>
        </div>
        <a href="{{ route('manajemenmahasiswa.pengaduan.create') }}" class="btn-outline-custom">← Ubah</a>
    </div>

    <div class="alert alert-warning border-0 shadow-sm" style="background-color: #fef3c7; color: #92400e; border-radius: 12px;">
        <div class="fw-medium">Pastikan data sudah benar. Setelah dikirim, pengaduan akan masuk ke daftar Admin/Dosen/GPM.</div>
    </div>

    <div class="custom-card">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="section-label">Kategori Masalah</div>
                <div class="section-value">{{ ucwords(str_replace('_', ' ', $payload['kategori'])) }}</div>
            </div>
            <div class="col-md-6">
                <div class="section-label">Anonim</div>
                <div class="section-value">{{ $payload['is_anonim'] ? 'Ya (identitas disembunyikan)' : 'Tidak' }}</div>
            </div>

            <div class="col-12"><hr style="border-color:#f3f4f6;"></div>

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
                        <a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $link }}</a>
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
                <a href="{{ route('manajemenmahasiswa.pengaduan.create') }}" class="btn-outline-custom">Kembali</a>
                <button type="submit" class="btn-custom">Kirim Pengaduan</button>
            </div>
        </form>
    </div>

</x-manajemenmahasiswa::layouts.mahasiswa>
