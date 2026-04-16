<x-dynamic-component :component="$isAdmin ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Buat Pengaduan</h4>
            <p class="text-muted mb-0">Isi template pengaduan, lalu sistem akan mengirimkannya ke admin terkait.</p>
        </div>
        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Periksa kembali input:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.store') }}" class="card border-0 shadow-sm rounded-4">
        @csrf

        <div class="card-body">

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_anonim" value="1" id="isAnonim" {{ old('is_anonim') ? 'checked' : '' }}>
                <label class="form-check-label" for="isAnonim">
                    Kirim sebagai anonim (identitas tidak ditampilkan di sistem)
                </label>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori Pengaduan</label>
                <select name="kategori" class="form-select" required>
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori…</option>
                    @foreach($kategoriList as $value => $label)
                        <option value="{{ $value }}" {{ old('kategori') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3">Template Pengaduan</h6>

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul</label>
                <input type="text" class="form-control" name="template[judul]" value="{{ old('template.judul') }}" placeholder="Contoh: Keluhan terkait jadwal kuliah" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kronologi / Isi Pengaduan</label>
                <textarea class="form-control" name="template[kronologi]" rows="6" placeholder="Jelaskan masalah secara ringkas dan jelas…" required>{{ old('template.kronologi') }}</textarea>
                <div class="form-text">Minimal 20 karakter.</div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi (opsional)</label>
                    <input type="text" class="form-control" name="template[lokasi]" value="{{ old('template.lokasi') }}" placeholder="Contoh: Ruang 3.12 / Gedung A">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Kejadian (opsional)</label>
                    <input type="date" class="form-control" name="template[tanggal_kejadian]" value="{{ old('template.tanggal_kejadian') }}">
                </div>
            </div>

            <div class="row g-3 mt-0">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mata Kuliah (opsional)</label>
                    <input type="text" class="form-control" name="template[mata_kuliah]" value="{{ old('template.mata_kuliah') }}" placeholder="Contoh: Pemrograman Web">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Dosen (opsional)</label>
                    <input type="text" class="form-control" name="template[nama_dosen]" value="{{ old('template.nama_dosen') }}" placeholder="Contoh: Dr. A">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Tendik (opsional)</label>
                    <input type="text" class="form-control" name="template[nama_tendik]" value="{{ old('template.nama_tendik') }}" placeholder="Contoh: Staf TU">
                </div>
            </div>

        </div>

        <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
        </div>
    </form>

</x-dynamic-component>
