@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Buat Pengaduan Konfidensial')

@push('styles')
    <style>
        .custom-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }
        .btn-custom {
            background-color: #4D4DFF;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
            font-size: 15px;
        }
        .btn-custom:hover {
            background-color: #3b3be5;
            color: white;
            transform: translateY(-1px);
        }
        .form-control-custom, .form-select-custom {
            background-color: #f9fafb;
            border: 2px solid #f3f4f6;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.2s;
            font-weight: 500;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            background-color: #ffffff;
            border-color: #a5a5ff;
            box-shadow: 0 0 0 4px rgba(77, 77, 255, 0.1);
            outline: none;
        }
        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #111827;
            font-size: 16px;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f3f4f6;
        }
        .info-box {
            background: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h3 class="fw-bold mb-1 text-dark">Form Pengaduan</h3>
        <p class="text-muted mb-0 fw-medium">Silakan isi detail pengaduan Anda. Identitas Anda tidak akan ditampilkan kepada publik maupun admin.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="background-color: #fee2e2; color: #dc2626; border-radius: 12px;">
            <div class="fw-bold mb-2">⚠ Terdapat kesalahan pada input:</div>
            <ul class="mb-0 fw-medium" style="font-size: 14px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.anon.store', ['token' => $token]) }}" class="custom-card">
        @csrf

        <div class="info-box">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="material-symbols-outlined" style="font-size: 18px; color: #64748b;">shield_lock</span>
                <span class="fw-bold" style="color: #334155; font-size: 14px;">Identitas Dilindungi</span>
            </div>
            <div style="color: #64748b; font-size: 13px; line-height: 1.5;">
                Nama Anda ({{ auth()->user()->name ?? 'Mahasiswa' }}) tetap direkam di dalam sistem demi integritas data, namun <strong>tidak akan ditampilkan</strong> di daftar tiket maupun pada layar penyelesaian.
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label-custom d-block">Kategori Pengaduan <span class="text-danger">*</span></label>
            <div class="d-flex flex-column gap-2">
                @foreach($kategoriList as $value => $meta)
                    <label class="d-flex align-items-start justify-content-between gap-3 p-3" style="border: 2px solid #f3f4f6; border-radius: 10px; background: #f9fafb; cursor: pointer;">
                        <span class="d-flex align-items-start gap-2" style="min-width: 0;">
                            <input
                                class="form-check-input mt-1"
                                type="radio"
                                name="kategori"
                                value="{{ $value }}"
                                {{ old('kategori') === $value ? 'checked' : '' }}
                                {{ $loop->first ? 'required' : '' }}
                                style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;">
                            <span style="min-width: 0;">
                                <span class="fw-bold text-dark d-block" style="font-size: 14px;">{{ $meta['label'] }}</span>
                                <span class="text-muted d-block" style="font-size: 12px; line-height: 1.4;">Contoh: {{ $meta['example'] }}</span>
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label-custom d-block">Angkatan <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
            <input type="text" class="form-control form-control-custom" name="template[angkatan]"
                value="{{ old('template.angkatan') }}" placeholder="Contoh: 2022">
        </div>

        <div class="mt-5">
            <h6 class="section-title">
                <span class="material-symbols-outlined" style="vertical-align: text-bottom; margin-right: 6px;">edit_document</span> Detail Pengaduan
            </h6>

            <div class="mb-4">
                <label class="form-label-custom d-block">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-custom" name="template[judul]" value="{{ old('template.judul') }}"
                    placeholder="Contoh: AC Ruang 3.12 tidak berfungsi" required>
            </div>

            <div class="mb-4">
                <label class="form-label-custom d-block">Hal Aduan <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-custom" name="template[hal_aduan]" rows="3"
                    placeholder="Tuliskan ringkas inti aduan Anda…" required>{{ old('template.hal_aduan') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label-custom d-block">Kronologi / Isi Pengaduan <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-custom" name="template[kronologi]" rows="6"
                    placeholder="Ceritakan dengan jelas masalah yang Anda hadapi…"
                    required>{{ old('template.kronologi') }}</textarea>
                <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">Minimal 20 karakter untuk memberikan konteks yang jelas.</div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Lokasi Kejadian <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[lokasi]" value="{{ old('template.lokasi') }}"
                        placeholder="Contoh: Lab Komputer">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Waktu Kejadian <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="datetime-local" class="form-control form-control-custom" name="template[waktu_kejadian]"
                        value="{{ old('template.waktu_kejadian') ?? old('template.tanggal_kejadian') }}">
                </div>
            </div>

            <div class="row g-4 mt-0">
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Mata Kuliah <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[mata_kuliah]"
                        value="{{ old('template.mata_kuliah') }}" placeholder="Contoh: Basis Data">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Dosen Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <select class="form-select form-control-custom" name="template[nama_dosen]">
                        <option value="" {{ old('template.nama_dosen') ? '' : 'selected' }}>Pilih dosen…</option>
                        @foreach(($dosenList ?? []) as $namaDosen)
                            <option value="{{ $namaDosen }}" {{ old('template.nama_dosen') === $namaDosen ? 'selected' : '' }}>
                                {{ $namaDosen }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Tendik Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[nama_tendik]"
                        value="{{ old('template.nama_tendik') }}" placeholder="Contoh: Bu Siti">
                </div>
            </div>

            <div class="row g-4 mt-0 mb-1 pt-4">
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Seberapa Sering Terjadi <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <select class="form-select form-control-custom" name="template[frekuensi]">
                        <option value="" {{ old('template.frekuensi') ? '' : 'selected' }}>Pilih frekuensi…</option>
                        @foreach(($frekuensiList ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ old('template.frekuensi') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Link Bukti Dukung <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="url" class="form-control form-control-custom" name="template[link_bukti]"
                        value="{{ old('template.link_bukti') }}"
                        placeholder="Contoh: https://drive.google.com/...">
                    <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">
                        Bukti berupa screenshot/foto/dokumen, simpan di drive yang bisa diakses.
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4" style="border-top: 1px solid #f3f4f6;">
            <button type="submit" class="btn-custom">Kirim Pengaduan</button>
        </div>
    </form>
@endsection