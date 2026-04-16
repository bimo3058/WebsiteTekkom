const fs = require('fs');
const file = 'Modules/BankSoal/resources/views/gpm/riwayat-validasi/bank-soal-detail.blade.php';
let content = fs.readFileSync(file, 'utf8');

const oldReviewBox = `                        <div class="review-box status-{{ $colorClass }}">
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.05); margin-right: 0.75rem;">
                                    <i class="fas fa-comment-dots" style="color: inherit; opacity: 0.7;"></i>
                                </div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.9rem; color: inherit; opacity: 0.9;">Catatan Evaluator</h6>
                            </div>
                            <p class="mb-0 text-dark ps-5" style="font-size: 0.95rem; line-height: 1.5;">{{ $soal->catatan ?: 'Tidak ada catatan.' }}</p>
                        </div>
                    </div>
                </div>
            </div>`;

const newReviewBox = `                        <div class="review-box status-{{ $colorClass }}">
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
                    <form action="{{ route('gpm.validasi-bank-soal.update', $soal->id) }}" method="POST" style="width: 100%;">
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
            </div>`;

if (content.includes('Catatan Evaluator')) {
    content = content.replace(oldReviewBox, newReviewBox);
    fs.writeFileSync(file, content);
    console.log('Successfully updated view');
} else {
    console.log('Failed to match replacement block');
}
