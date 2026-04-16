@extends('layouts.mahasiswa')

@section('content')

    <h4 class="fw-bold mb-1">Pengumuman & Informasi</h4>
    <p class="text-muted mb-4">Wadah Informasi untuk Mahasiswa dan Alumni</p>

    <!-- Search + Filter -->
    <div class="d-flex gap-3 mb-4 align-items-center">

        <!-- Search -->
        <input type="text" class="form-control" placeholder="Search" style="max-width: 500px;">

        <!-- Filter Dropdown -->
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Tag 2 × &nbsp; Tag 3 ×
            </button>

            <div class="dropdown-menu p-3" style="min-width: 220px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="tag1">
                    <label class="form-check-label">Tag 1</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="tag2" checked>
                    <label class="form-check-label">Tag 2</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="tag3" checked>
                    <label class="form-check-label">Tag 3</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="tag4">
                    <label class="form-check-label">Tag 4</label>
                </div>
            </div>
        </div>

    </div>

    <!-- List Pengumuman -->
    <div class="d-flex flex-column gap-3">

        <!-- Card -->
        <div class="card shadow-sm border-0 p-3 rounded-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="fw-bold">📢 Judul</h6>
                    <p class="text-muted mb-2">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit...
                    </p>
                    <small class="text-muted">27 Januari 2026</small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 p-3 rounded-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="fw-bold">📢 Jadwal KRS Semester Genap 2025/2026</h6>
                    <p class="text-muted mb-2">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit...
                    </p>
                    <small class="text-muted">27 Januari 2026</small>
                </div>
                <a href="#" class="align-self-end text-decoration-none">Baca Selengkapnya</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 p-3 rounded-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="fw-bold">📢 Judul</h6>
                    <p class="text-muted mb-2">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit...
                    </p>
                    <small class="text-muted">27 Januari 2026</small>
                </div>
                <a href="#" class="align-self-end text-decoration-none">Baca Selengkapnya</a>
            </div>
        </div>

    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4 gap-3">
        <button class="btn btn-light rounded-circle">‹</button>
        <button class="btn btn-light rounded-circle">›</button>
    </div>

@endsection