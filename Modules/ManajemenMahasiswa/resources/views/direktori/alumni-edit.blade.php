<x-dynamic-component :component="$layout">

@push('styles')
<style>
    .main-wrapper {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .back-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .btn-back {
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #374151;
        transition: all 0.2s;
    }
    .btn-back:hover { background: #f9fafb; color: #111827; }

    .edit-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 32px;
    }
    .section-divider {
        font-size: 15px;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eef2ff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: #ffffff;
    }
    .btn-save {
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
    .btn-save:hover {
        background: linear-gradient(135deg, #4338ca, #4f46e5);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
</style>
@endpush

<div class="back-bar">
    <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alumni->id) }}" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Batal
    </a>
    <div>
        <h5 class="fw-bold mb-0" style="font-size: 18px; color: #111827;">Edit Data Alumni</h5>
        <p class="text-muted mb-0" style="font-size: 13px;">Admin — perbarui biodata dan karir alumni</p>
    </div>
</div>

<div class="edit-card">
    <form action="{{ route('manajemenmahasiswa.direktori.alumni.update', $alumni->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Section: Akademik -->
        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            Informasi Akademik
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim', $alumni->nim) }}" required>
                @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Program Studi</label>
                <input type="text" name="program_studi" class="form-control @error('program_studi') is-invalid @enderror" value="{{ old('program_studi', $alumni->program_studi) }}">
                @error('program_studi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror" value="{{ old('angkatan', $alumni->angkatan) }}" required min="2000" max="2099">
                @error('angkatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" class="form-control @error('tahun_lulus') is-invalid @enderror" value="{{ old('tahun_lulus', $alumni->tahun_lulus) }}" required min="2000" max="2099">
                @error('tahun_lulus') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Section: Karir -->
        <div class="section-divider">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Informasi Karir
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Status Karir</label>
                <select name="status_karir" class="form-select @error('status_karir') is-invalid @enderror">
                    <option value="">— Pilih Status —</option>
                    @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::STATUS_LABELS as $key => $label)
                        <option value="{{ $key }}" {{ old('status_karir', $alumni->status_karir) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status_karir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Bidang Industri</label>
                <select name="bidang_industri" class="form-select @error('bidang_industri') is-invalid @enderror">
                    <option value="">— Pilih Bidang —</option>
                    @foreach(\Modules\ManajemenMahasiswa\Models\Alumni::BIDANG_INDUSTRI_LIST as $key => $label)
                        <option value="{{ $key }}" {{ old('bidang_industri', $alumni->bidang_industri) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('bidang_industri') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">Perusahaan / Instansi / Usaha</label>
                <input type="text" name="perusahaan" class="form-control @error('perusahaan') is-invalid @enderror" value="{{ old('perusahaan', $alumni->perusahaan) }}">
                @error('perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan / Posisi</label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $alumni->jabatan) }}">
                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tahun Mulai Bekerja</label>
                <input type="number" name="tahun_mulai_bekerja" class="form-control @error('tahun_mulai_bekerja') is-invalid @enderror" value="{{ old('tahun_mulai_bekerja', $alumni->tahun_mulai_bekerja) }}" min="2000" max="{{ date('Y') + 1 }}">
                @error('tahun_mulai_bekerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin" class="form-control @error('linkedin') is-invalid @enderror" value="{{ old('linkedin', $alumni->linkedin) }}" placeholder="https://linkedin.com/in/username">
                @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end pt-4 mt-3" style="border-top: 1px solid #f1f5f9;">
            <button type="submit" class="btn-save">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

</x-dynamic-component>
