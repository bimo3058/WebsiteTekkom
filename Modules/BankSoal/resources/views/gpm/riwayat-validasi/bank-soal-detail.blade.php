<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Detail Riwayat Validasi')
    @section('page-subtitle', 'Rincian hasil review paket soal mata kuliah')

    <style>
        .page-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .opt-list { padding-left: 0; list-style: none; margin-top: 1rem; }
        .opt-item { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem; }
        .opt-item.is-correct { background-color: #f0fdf4; border-color: #bbf7d0; }
        .opt-label { font-weight: 700; color: #64748b; min-width: 25px; }
        .opt-item.is-correct .opt-label { color: #16a34a; }
        .review-box { background-color: #f8fafc; border-left: 4px solid #2563eb; border-radius: 0 0.5rem 0.5rem 0; padding: 1.25rem; margin-top: 1.5rem; }
        .review-box.status-sesuai { border-left-color: #16a34a; background-color: #f0fdf4; }
        .review-box.status-kurang-sesuai { border-left-color: #f59e0b; background-color: #fffbeb; }
        .review-box.status-revisi { border-left-color: #ef4444; background-color: #fef2f2; }
        .question-card { border: 1px solid #cbd5e1; border-top: none; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #fff; margin-bottom: 1.5rem; }
        .question-color-bar { height: 5px; width: 100%; }
        .bg-color-sesuai { background-color: #16a34a; }
        .bg-color-kurang-sesuai { background-color: #f59e0b; }
        .bg-color-revisi { background-color: #ef4444; }
        .pagination-kanan nav > div.d-flex.justify-content-between { justify-content: flex-end !important; flex: none !important; gap: 1.5rem; align-items: center; }
        .pagination-kanan p.text-muted { margin-bottom: 0 !important; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-dark mb-1" style="font-size: 1.5rem;">{{ $mataKuliah->nama }}</h4>
                <div class="d-flex align-items-center text-muted" style="font-size: 0.9rem; gap: 0.5rem;">
                    <span>{{ $mataKuliah->kode }}</span><span>&bull;</span><span>Program Studi Teknik Komputer</span>
                </div>
            </div>
            <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="btn btn-light border shadow-sm rounded-3 fw-semibold px-4 text-dark" style="background:#fff;">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="pagination-kanan d-flex justify-content-end mb-4">
            {{ $riwayatSoal->links('pagination::bootstrap-5') }}
        </div>

        <div class="row g-4">
            @forelse($riwayatSoal as $index => $soal)
            <div class="col-12">
                @php
                    $isSesuai = $soal->status_review == 'Sesuai';
                    $isKurang = $soal->status_review == 'Kurang Sesuai';
                    $colorClass = $isSesuai ? 'sesuai' : ($isKurang ? 'kurang-sesuai' : 'revisi');
                    $badgeBg = $isSesuai ? 'bg-success text-white' : ($isKurang ? 'bg-warning text-dark' : 'bg-danger text-white');
                    $iconStatus = $isSesuai ? 'fa-check-circle' : ($isKurang ? 'fa-exclamation-triangle' : 'fa-times-circle');
                @endphp
                <div class="question-card">
                    <div class="question-color-bar bg-color-{{ $colorClass }}"></div>
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">SOAL #{{ $riwayatSoal->firstItem() + $index }}</span>
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">LEVEL: {{ strtoupper($soal->kesulitan) }}</span>
                                @if($soal->cpl)
                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="fas fa-tag me-1"></i> {{ $soal->cpl->kode }}</span>
                                @endif
                                <span class="text-muted ms-2" style="font-size: 0.85rem;">Dinilai pada: {{ \Carbon\Carbon::parse($soal->tanggal_review)->translatedFormat('d M Y') }}</span>
                            </div>
                            <span class="badge {{ $badgeBg }} px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-2 shadow-sm" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="fas {{ $iconStatus }}"></i> {{ strtoupper($soal->status_review) }}
                            </span>
                        </div>
                        <h5 class="text-dark fw-bold mb-4" style="line-height: 1.6; font-size: 1.15rem;">{!! nl2br(e($soal->soal)) !!}</h5>
                        
                        @if($soal->jawaban && count($soal->jawaban) > 0)
                            <ul class="opt-list">
                                @foreach($soal->jawaban as $idx => $jawab)
                                    @php $char = chr(65 + $idx); @endphp
                                    <li class="opt-item {{ $jawab->is_benar ? 'is-correct' : '' }}">
                                        <div class="opt-label">{{ $jawab->opsi ?? $char }}.</div>
                                        <div class="text-dark" style="font-size: 0.95rem;">{{ $jawab->deskripsi }}</div>
                                        @if($jawab->is_benar)
                                            <div class="ms-auto text-success" title="Kunci Jawaban"><i class="fas fa-check-circle fs-5"></i></div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="review-box status-{{ $colorClass }}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.05); margin-right: 0.75rem;">
                                        <i class="fas fa-comment-dots" style="color: inherit; opacity: 0.7;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem; color: inherit; opacity: 0.9;">Catatan Evaluator</h6>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $soal->id }}" style="border-radius: 6px; font-weight: 500;">
                                    <i class="fas fa-edit"></i> Edit Review
                                </button>
                            </div>
                            <p class="mb-0 text-dark ps-5" style="font-size: 0.95rem; line-height: 1.5;">{{ $soal->catatan ?: 'Tidak ada catatan.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Review -->
            <div class="modal fade" id="editModal{{ $soal->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $soal->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal.update', $soal->id) }}" method="POST" style="width: 100%;">
                        @csrf
                        @method('PUT')
                        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold text-dark" id="editModalLabel{{ $soal->id }}">Edit Hasil Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Status Review</label>
                                    <select name="status_review" class="form-select" required style="border-radius: 8px;">
                                        <option value="Sesuai" {{ $soal->status_review == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
                                        <option value="Kurang Sesuai" {{ $soal->status_review == 'Kurang Sesuai' ? 'selected' : '' }}>Kurang Sesuai</option>
                                        <option value="Revisi" {{ $soal->status_review == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Catatan Evaluator</label>
                                    <textarea name="catatan" class="form-control" rows="4" style="border-radius: 8px;" placeholder="Tuliskan catatan revisi jika ada...">{{ $soal->catatan }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top bg-light" style="border-radius: 0 0 12px 12px;">
                                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 my-5 bg-white border rounded-4">
                <i class="fas fa-folder-open mb-3 text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="fw-bold text-dark">Riwayat Validasi Kosong</h5>
                <p class="text-muted" style="font-size: 0.95rem;">Belum ada hasil review soal yang bisa ditampilkan untuk mata kuliah ini.</p>
                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="btn btn-primary mt-3 px-4">Kembali ke Daftar</a>
            </div>
            @endforelse
        </div>
        
        <div class="pagination-kanan d-flex justify-content-center mt-2 mb-5">
            {{ $riwayatSoal->links('pagination::bootstrap-5') }}
        </div>
    </div>
</x-banksoal::layouts.gpm-master>