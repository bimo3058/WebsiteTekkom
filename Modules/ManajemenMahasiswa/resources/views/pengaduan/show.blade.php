<x-dynamic-component :component="$isAdmin ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="fw-bold mb-1">Detail Pengaduan</h4>
            <p class="text-muted mb-0">#{{ $pengaduan->id }} • {{ optional($pengaduan->created_at)->translatedFormat('d F Y H:i') }}</p>
        </div>

        <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ data_get($pengaduan, 'data_template.judul', '-') }}</h5>
                            <div class="text-muted">
                                <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', ucfirst($pengaduan->kategori)) }}</span>
                                <span class="ms-2 badge bg-secondary">{{ ucfirst($pengaduan->status) }}</span>
                                @if($pengaduan->is_anonim)
                                    <span class="ms-2 badge bg-dark">Anonim</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    @if($isAdmin)
                        <div class="mb-3">
                            <div class="text-muted small">Pelapor</div>
                            <div class="fw-semibold">
                                @if($pengaduan->is_anonim)
                                    Anonim
                                @else
                                    {{ optional($pengaduan->pelapor)->name ?? '—' }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="text-muted small">Kronologi</div>
                        <div class="mt-1" style="white-space: pre-wrap;">{{ data_get($pengaduan, 'data_template.kronologi', '-') }}</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Lokasi</div>
                            <div class="fw-semibold">{{ data_get($pengaduan, 'data_template.lokasi', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Tanggal Kejadian</div>
                            <div class="fw-semibold">{{ data_get($pengaduan, 'data_template.tanggal_kejadian', '—') ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-4">
                            <div class="text-muted small">Mata Kuliah</div>
                            <div class="fw-semibold">{{ data_get($pengaduan, 'data_template.mata_kuliah', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Nama Dosen</div>
                            <div class="fw-semibold">{{ data_get($pengaduan, 'data_template.nama_dosen', '—') ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Nama Tendik</div>
                            <div class="fw-semibold">{{ data_get($pengaduan, 'data_template.nama_tendik', '—') ?: '—' }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Jawaban Admin</h6>

                    @if($pengaduan->jawaban)
                        <div class="alert alert-success" style="white-space: pre-wrap;">{{ $pengaduan->jawaban }}</div>
                        <div class="text-muted small">
                            Dijawab: {{ optional($pengaduan->answered_at)->translatedFormat('d F Y H:i') ?? '—' }}
                        </div>
                    @else
                        <div class="text-muted">Belum ada jawaban.</div>
                    @endif

                    @if($isAdmin)
                        <hr class="my-3">
                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.reply', $pengaduan->id) }}">
                            @csrf

                            <div class="mb-2">
                                <label class="form-label fw-semibold">Tulis Jawaban</label>
                                <textarea class="form-control" name="jawaban" rows="6" placeholder="Tulis jawaban untuk pengaduan ini…">{{ old('jawaban', $pengaduan->jawaban) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Kirim Jawaban</button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>

</x-dynamic-component>
