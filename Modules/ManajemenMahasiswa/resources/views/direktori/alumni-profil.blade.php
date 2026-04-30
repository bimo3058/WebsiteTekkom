<x-dynamic-component :component="$layout">

@push('styles')
<style>
    .main-wrapper {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .page-header-profil {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .page-header-profil::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .page-header-profil h3 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
    }
    .page-header-profil p {
        font-size: 14px;
        color: rgba(255,255,255,0.8);
        margin: 0;
    }

    .card-section {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 28px;
    }

    .identity-card {
        text-align: center;
        padding: 32px 24px;
    }
    .identity-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: bold;
        margin: 0 auto 16px;
        overflow: hidden;
        border: 3px solid #e0e7ff;
    }
    .identity-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .identity-name {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }
    .identity-nim {
        font-family: monospace;
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 12px;
    }
    .identity-badges {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .identity-badges .badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #374151;
    }
    .identity-note {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed #e2e8f0;
        font-size: 12.5px;
        color: #94a3b8;
        line-height: 1.6;
    }

    .form-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f5f9;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control-custom,
    .form-select-custom {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.2s;
        width: 100%;
    }
    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: #ffffff;
        outline: none;
    }
    .btn-submit {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        border: none;
        padding: 11px 28px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
</style>
@endpush

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #991b1b; font-weight: 500; font-size: 14px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="page-header-profil">
    <h3>📝 Profil Karir Alumni</h3>
    <p>Perbarui data pekerjaan dan karir Anda. Data ini digunakan untuk akreditasi dan jejaring alumni.</p>
</div>

<div class="row g-4">
    <!-- Identity Card -->
    <div class="col-lg-4">
        <div class="card-section identity-card">
            <div class="identity-avatar">
                @if($alumni->user && $alumni->user->avatar_url)
                    <img src="{{ $alumni->user->avatar_url }}" alt="Avatar">
                @else
                    {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div class="identity-name">{{ $alumni->user->name ?? 'Tanpa Nama' }}</div>
            <div class="identity-nim">{{ $alumni->nim }}</div>

            <div class="identity-badges">
                <span class="badge">Angkatan {{ $alumni->angkatan }}</span>
                <span class="badge">Lulus {{ $alumni->tahun_lulus }}</span>
            </div>

            <div class="identity-note">
                Data karir ini digunakan untuk keperluan <strong>akreditasi</strong> program studi dan membangun jejaring alumni.
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="col-lg-8">
        <div class="card-section">
            <h5 class="form-title">Form Data Karir</h5>

            <form action="{{ route('manajemenmahasiswa.direktori.alumni.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Status Karir</label>
                        <select name="status_karir" class="form-select form-select-custom @error('status_karir') is-invalid @enderror">
                            <option value="">— Pilih Status —</option>
                            @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" {{ old('status_karir', $alumni->status_karir) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status_karir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Bidang Industri</label>
                        <select name="bidang_industri" class="form-select form-select-custom @error('bidang_industri') is-invalid @enderror">
                            <option value="">— Pilih Bidang —</option>
                            @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::BIDANG_INDUSTRI_LIST as $key => $label)
                                <option value="{{ $key }}" {{ old('bidang_industri', $alumni->bidang_industri) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bidang_industri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Perusahaan / Instansi / Nama Usaha</label>
                        <input type="text" name="perusahaan" value="{{ old('perusahaan', $alumni->perusahaan) }}"
                               class="form-control form-control-custom @error('perusahaan') is-invalid @enderror"
                               placeholder="Contoh: PT Teknologi Indonesia">
                        @error('perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jabatan / Posisi</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $alumni->jabatan) }}"
                               class="form-control form-control-custom @error('jabatan') is-invalid @enderror"
                               placeholder="Contoh: Software Engineer">
                        @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tahun Mulai Bekerja / Usaha</label>
                        <input type="number" name="tahun_mulai_bekerja" value="{{ old('tahun_mulai_bekerja', $alumni->tahun_mulai_bekerja) }}"
                               class="form-control form-control-custom @error('tahun_mulai_bekerja') is-invalid @enderror"
                               placeholder="Contoh: 2023" min="2000" max="{{ date('Y') + 1 }}">
                        @error('tahun_mulai_bekerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Link LinkedIn (Opsional)</label>
                        <input type="url" name="linkedin" value="{{ old('linkedin', $alumni->linkedin) }}"
                               class="form-control form-control-custom @error('linkedin') is-invalid @enderror"
                               placeholder="https://linkedin.com/in/username">
                        @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-4 mt-3" style="border-top: 1px solid #f1f5f9;">
                    <button type="submit" class="btn-submit">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-dynamic-component>
