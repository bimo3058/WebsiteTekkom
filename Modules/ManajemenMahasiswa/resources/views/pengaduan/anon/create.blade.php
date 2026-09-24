@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Buat Pengaduan Konfidensial')

@push('styles')
    <style>
        .form-card {
            background: #ffffff; border-radius: 12px; padding: 32px;
            box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            border: 1px solid #DDE1E8; margin-bottom: 24px;
        }
        .page-title h4 {
            font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
            margin: 0 0 4px; letter-spacing: -.02em;
        }
        .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }
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
            border-radius: 12px; padding: 8px 16px;
            display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s; background: transparent;
            border: 1px solid #DDE1E8; color: #6b7280;
        }
        .btn-back:hover { background: #E7E8F0; color: #374151; border-color: #293C79; }
        .detail-back {
            width: auto; min-width: 0; height: 32px; padding: 0 12px; gap: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; background: #fff; border: 1px solid #DFE1E7;
            color: #353849; box-shadow: 0 1px 2px rgba(0,0,0,.05);
            text-decoration: none; transition: all .2s;
        }
        .detail-back:hover { background: #F6F8FA; color: #0D0D12; }
        .detail-back-label { color: inherit; font-size: 13px; font-weight: 600; line-height: 1.2; }
        .form-control-custom, .form-select-custom {
            background-color: #f9fafb; border: 1px solid #DDE1E8;
            border-radius: 12px; padding: 12px 16px;
            font-size: 14px; transition: all 0.2s; font-weight: 500;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            background-color: #ffffff; border-color: #293C79;
            box-shadow: 0 0 0 3px rgba(41,60,121,.12); outline: none;
        }
        .form-label-custom {
            font-size: 13px; font-weight: 600; color: #4b5563;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
        }
        .section-title {
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; color: #111827; font-size: 16px;
            margin-bottom: 24px; padding-bottom: 12px;
            border-bottom: 1px solid #DDE1E8;
        }
        .kategori-option {
            border: 2px solid #f3f4f6; border-radius: 10px;
            background: #f9fafb; padding: 14px 16px; cursor: pointer;
            transition: all .2s;
        }
        .kategori-option:has(:checked) {
            border-color: #293C79; background: #f0f2ff;
        }
        .kategori-option:hover { border-color: #c7d2fe; }

        /* ── Anon full-width overrides ── */
        .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

        /* Cap field widths so they don't stretch on wide screens, left-aligned */
        .form-fields { max-width: 660px; }
    </style>
@endpush

@section('content')
    {{-- Seluruh teks halaman (judul, banner jalur, error) berada di dalam card. --}}
    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.anon.confirm', ['token' => $token]) }}" class="form-card" enctype="multipart/form-data" style="position: relative;">
        @csrf
        @include('manajemenmahasiswa::pengaduan.partials.honeypot')

        {{-- ── Header: judul di kiri, tombol Kembali di kanan atas ── --}}
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div class="page-title">
                <h4>Buat Pengaduan</h4>
                <p>Isi form di bawah ini dengan detail yang jelas dan valid.</p>
            </div>
            <a href="{{ route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]) }}" class="detail-back flex-shrink-0" title="Kembali" aria-label="Kembali ke Pilih Jalur">
                <x-manajemenmahasiswa::ui.icon name="chevron-left" size="16" />
                <span class="detail-back-label">Kembali</span>
            </a>
        </div>

        {{-- Jalur Indicator Banner --}}
        <div class="d-flex align-items-center gap-3 mb-4 p-3 px-4 rounded-3" style="background: #f1f5f9; border: 1.5px solid #e2e8f0;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #334155;">
                <x-manajemenmahasiswa::ui.icon name="shield-02" size="20" />
            </div>
            <div>
                <div class="fw-bold text-dark" style="font-size: 14px;">Jalur Konfidensial</div>
                <div class="text-muted" style="font-size: 12px;">Identitas Anda tidak akan ditampilkan kepada publik maupun admin. <a href="{{ route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]) }}" style="color: #6b7280;">Ganti jalur</a></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 mb-4" style="background-color: #fee2e2; color: #dc2626; border-radius: 12px;">
                <div class="fw-bold mb-2 d-flex align-items-center gap-2">
                    <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="16" /> Terdapat kesalahan pada input:
                </div>
                <ul class="mb-0 fw-medium" style="font-size: 14px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-fields">

        <div class="mb-3">
            <label class="form-label-custom d-block">Kategori Pengaduan <span class="text-danger">*</span></label>
            <div class="d-flex flex-column gap-2">
                @foreach($kategoriList as $value => $meta)
                    <label class="kategori-option d-flex align-items-start gap-3">
                        <input class="form-check-input mt-1" type="radio" name="kategori"
                            value="{{ $value }}" {{ old('kategori') === $value ? 'checked' : '' }}
                            {{ $loop->first ? 'required' : '' }}
                            style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;">
                        <span style="min-width: 0;">
                            <span class="fw-bold text-dark d-block" style="font-size: 14px;">{{ $meta['label'] }}</span>
                            <span class="text-muted d-block" style="font-size: 12px; line-height: 1.4;">Contoh: {{ $meta['example'] }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-custom d-block">Angkatan <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
            <input type="text" class="form-control form-control-custom" name="template[angkatan]"
                value="{{ old('template.angkatan') }}" placeholder="Contoh: 2022">
        </div>

        <div class="mt-5">
            <h6 class="section-title">
                <x-manajemenmahasiswa::ui.icon name="file-01" size="18" />
                Detail Pengaduan
            </h6>

            <div class="mb-3">
                <label class="form-label-custom d-block">Subjek <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-custom" name="template[judul]" value="{{ old('template.judul') }}"
                    placeholder="Contoh: AC Ruang 3.12 tidak berfungsi" required>
            </div>

            <div class="mb-3">
                <label class="form-label-custom d-block">Pesan <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-custom" name="template[kronologi]" rows="6"
                    placeholder="Ceritakan dengan jelas masalah yang Anda hadapi…"
                    required>{{ old('template.kronologi') }}</textarea>
                <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">Minimal 20 karakter untuk memberikan konteks yang jelas.</div>
            </div>

            <div class="row g-3 mb-3">
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

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Mata Kuliah <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[mata_kuliah]"
                        value="{{ old('template.mata_kuliah') }}" placeholder="Contoh: Basis Data">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Dosen Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <x-manajemenmahasiswa::ui.select name="template[nama_dosen]" size="lg">
                        <option value="" {{ old('template.nama_dosen') ? '' : 'selected' }}>Pilih dosen…</option>
                        @foreach(($dosenList ?? []) as $namaDosen)
                            <option value="{{ $namaDosen }}" {{ old('template.nama_dosen') === $namaDosen ? 'selected' : '' }}>
                                {{ $namaDosen }}
                            </option>
                        @endforeach
                    </x-manajemenmahasiswa::ui.select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Tendik Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[nama_tendik]"
                        value="{{ old('template.nama_tendik') }}" placeholder="Contoh: Bu Siti">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Seberapa Sering Terjadi <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <x-manajemenmahasiswa::ui.select name="template[frekuensi]" size="lg">
                        <option value="" {{ old('template.frekuensi') ? '' : 'selected' }}>Pilih frekuensi…</option>
                        @foreach(($frekuensiList ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ old('template.frekuensi') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-manajemenmahasiswa::ui.select>
                </div>
            </div>

            {{-- Bukti dukung berdiri sendiri selebar form: tingginya ikut jumlah berkas,
                 jadi bila disandingkan dalam satu baris kolom lain jadi menggantung. --}}
            <div class="row g-3">
                <div class="col-12">
                    @include('manajemenmahasiswa::pengaduan.partials.bukti-input', ['buktiPendingItems' => $buktiPendingItems ?? [], 'confidential' => true])
                </div>
            </div>
        </div>

        </div>{{-- /.form-fields --}}

        <div class="d-flex justify-content-end gap-3 mt-5 pt-4" style="border-top: 1px solid #f3f4f6;">
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn-back text-decoration-none">Batal</a>
            <button type="submit" class="btn-post">Lanjut Konfirmasi</button>
        </div>
    </form>
@endsection
