<x-dynamic-component :component="$layout">

<style>
    .form-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 16px;
        padding: 32px;
        max-width: 720px;
    }
    .form-title {
        font-size: 20px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .form-subtitle {
        font-size: 14px;
        color: #9ca3af;
        margin-bottom: 28px;
    }
    .form-label-custom {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-control-custom {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-select-custom {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-select-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .btn-primary-custom {
        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary-custom:hover {
        background: #4338ca;
    }
    .btn-outline-custom {
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
        padding: 10px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-outline-custom:hover {
        background: #f8fafc;
        border-color: #d1d5db;
        color: #374151;
    }
</style>

<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="btn-outline-custom" style="font-size: 12px; padding: 6px 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>
</div>

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fef2f2; color: #991b1b; font-size: 14px;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-card">
    <div class="form-title">Edit Biodata Mahasiswa</div>
    <div class="form-subtitle">Perbarui informasi biodata mahasiswa: {{ $mhs->nama }}</div>

    <form method="POST" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.update', $mhs->id) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Nama -->
            <div class="col-12">
                <label class="form-label-custom">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama" class="form-control form-control-custom"
                       value="{{ old('nama', $mhs->nama) }}" required>
            </div>

            <!-- NIM -->
            <div class="col-md-6">
                <label class="form-label-custom">NIM <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nim" class="form-control form-control-custom"
                       value="{{ old('nim', $mhs->nim) }}" required>
            </div>

            <!-- Angkatan -->
            <div class="col-md-6">
                <label class="form-label-custom">Angkatan <span style="color: #ef4444;">*</span></label>
                <input type="number" name="angkatan" class="form-control form-control-custom"
                       value="{{ old('angkatan', $mhs->angkatan) }}" min="2000" max="2099" required>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label-custom">Status <span style="color: #ef4444;">*</span></label>
                <select name="status" class="form-select form-select-custom" required>
                    <option value="aktif" {{ old('status', $mhs->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="alumni" {{ old('status', $mhs->status) == 'alumni' ? 'selected' : '' }}>Lulus (Alumni)</option>
                    <option value="cuti" {{ old('status', $mhs->status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="drop_out" {{ old('status', $mhs->status) == 'drop_out' ? 'selected' : '' }}>Drop Out</option>
                </select>
            </div>

            <!-- Tahun Lulus -->
            <div class="col-md-6">
                <label class="form-label-custom">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" class="form-control form-control-custom"
                       value="{{ old('tahun_lulus', $mhs->tahun_lulus) }}" min="2000" max="2099"
                       placeholder="Kosongkan jika belum lulus">
            </div>

            <!-- Profesi -->
            <div class="col-md-6">
                <label class="form-label-custom">Profesi</label>
                <input type="text" name="profesi" class="form-control form-control-custom"
                       value="{{ old('profesi', $mhs->profesi) }}" placeholder="Opsional">
            </div>

            <!-- Kontak -->
            <div class="col-md-6">
                <label class="form-label-custom">Kontak</label>
                <input type="text" name="kontak" class="form-control form-control-custom"
                       value="{{ old('kontak', $mhs->kontak) }}" placeholder="No. HP / WhatsApp">
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex gap-3 mt-4 pt-3" style="border-top: 1px solid #f3f4f6;">
            <button type="submit" class="btn-primary-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="btn-outline-custom">
                Batal
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
