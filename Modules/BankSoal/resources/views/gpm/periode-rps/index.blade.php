<x-banksoal::layouts.gpm-master>
    @section('page-title', 'Manajemen Jadwal RPS')
    @section('page-subtitle', 'Kelola periode unggah RPS untuk Dosen')

    <style>
        .table-container { background-color: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
        .table-periode th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 600; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; letter-spacing: 0.5px;}
        .table-periode td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-periode tr:last-child td { border-bottom: none; }
        .badge-active { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.35rem 0.75rem; font-size: 0.75rem; border-radius: 0.375rem; font-weight: 600; }
        .badge-inactive { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 0.35rem 0.75rem; font-size: 0.75rem; border-radius: 0.375rem; font-weight: 600; }
        .btn-action { padding: 0.4rem 0.75rem; font-size: 0.85rem; border-radius: 0.375rem; margin-right: 0.25rem; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Manajemen Jadwal RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Kelola periode unggah RPS untuk Dosen";
            });
        </script>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold text-dark">Daftar Jadwal Pengajuan RPS</h5>
            <button type="button" class="btn btn-primary rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Periode
            </button>
        </div>

        <div class="table-container mb-4">
            <div class="table-responsive">
                <table class="table table-periode mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Info Periode</th>
                            <th>Rentang Waktu</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $index => $periode)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $periode->judul }}</div>
                                <div class="text-muted" style="font-size: 0.85rem;">Semester {{ $periode->semester }} - TA {{ $periode->tahun_ajaran }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i> 
                                    {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y, H:i') }}
                                </div>
                                <div style="font-size: 0.9rem;" class="mt-1">
                                    <i class="fas fa-flag-checkered text-danger me-2"></i>
                                    {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td>
                                @if($periode->is_active)
                                    <span class="badge-active"><i class="fas fa-circle text-success me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Aktif</span>
                                @else
                                    <span class="badge-inactive">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-action btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $periode->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-action btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $periode->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $periode->id }}" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('banksoal.rps.gpm.periode-rps.update', $periode->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold" id="modalEditLabel">Edit Periode RPS</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark">Judul Periode <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="judul" value="{{ $periode->judul }}" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium text-dark">Semester <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="semester" required>
                                                        <option value="Ganjil" {{ $periode->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                        <option value="Genap" {{ $periode->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="tahun_ajaran" value="{{ $periode->tahun_ajaran }}" required placeholder="Contoh: 2025/2026">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark">Waktu Mulai <span class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" name="tanggal_mulai" value="{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium text-dark">Waktu Selesai (Tenggat) <span class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" name="tanggal_selesai" value="{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d\TH:i') }}" required>
                                            </div>
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $periode->id }}" value="1" {{ $periode->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="isActiveEdit{{ $periode->id }}">Set sebagai periode aktif saat ini</label>
                                                <div class="form-text mt-1 text-muted" style="font-size: 0.8rem;">Hanya satu periode yang bisa aktif. Mengaktifkan ini akan menonaktifkan periode lainnya.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div class="modal fade" id="modalHapus{{ $periode->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-body text-center p-4">
                                        <div class="text-danger mb-3">
                                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                                        </div>
                                        <h5 class="mb-2 fw-bold text-dark">Hapus Periode?</h5>
                                        <p class="text-muted mb-4" style="font-size: 0.9rem;">Anda yakin ingin menghapus jadwal <strong>{{ $periode->judul }}</strong>?</p>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('banksoal.rps.gpm.periode-rps.destroy', $periode->id) }}" method="POST" class="w-50">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                                    <h5>Belum ada jadwal RPS</h5>
                                    <p>Silakan tambah periode baru untuk mengaktifkan pengajuan RPS Dosen.</p>
                                    <button class="btn btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Periode Pertama</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <form action="{{ route('banksoal.rps.gpm.periode-rps.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Periode RPS Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Judul Periode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026" value="{{ old('judul') }}">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" name="semester" required>
                                    <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tahun_ajaran" required placeholder="Contoh: 2025/2026" value="{{ old('tahun_ajaran') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Waktu Selesai (Tenggat) <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}">
                        </div>
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveAdd" value="1" checked>
                            <label class="form-check-label ms-2" for="isActiveAdd">Otomatis aktifkan periode ini</label>
                            <div class="form-text mt-1 text-muted" style="font-size: 0.8rem;">GPM hanya bisa membuka 1 sesi pengajuan dalam satu waktu.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Buat Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-banksoal::layouts.gpm-master>
