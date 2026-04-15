<x-manajemenmahasiswa::layouts.mahasiswa>

    <h4 class="fw-bold mb-1">Pengumuman & Informasi</h4>
    <p class="text-muted mb-4">Wadah Informasi untuk Mahasiswa dan Alumni</p>

    <!-- Search + Filter -->
    <div class="d-flex gap-3 mb-4 align-items-center">

        <input type="text" class="form-control" placeholder="Search" style="max-width: 500px;">

        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                Tag 2 × &nbsp; Tag 3 ×
            </button>

            <div class="dropdown-menu p-3" style="min-width: 220px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">Tag 1</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked>
                    <label class="form-check-label">Tag 2</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked>
                    <label class="form-check-label">Tag 3</label>
                </div>
            </div>
        </div>

    </div>

    <!-- List -->
    <div class="d-flex flex-column gap-3">

        <div class="card shadow-sm border-0 p-3 rounded-4">
            <h6 class="fw-bold">📢 Judul</h6>
            <p class="text-muted mb-2">Lorem ipsum dolor sit amet...</p>
            <small class="text-muted">27 Januari 2026</small>
        </div>

    </div>

</x-manajemenmahasiswa::layouts.mahasiswa>