<x-manajemenmahasiswa::layouts.mahasiswa>
<style>
.detail-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
.btn-back{width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #DFE1E7;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:18px;transition:all 0.2s;flex-shrink:0}
.btn-back:hover{background:#f3f4f6;border-color:#C1C7CF;color:#0D0D12}
.detail-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);margin-bottom:20px}
.detail-card-title{font-weight:700;font-size:16px;color:#0D0D12;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.badge-bidang{font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;background:#eef2ff;color:#0B266E}
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700}
.status-draft{background:#f3f4f6;color:#666D80}
.status-diajukan{background:#FFFBEB;color:#92400e}
.status-disetujui{background:#eef2ff;color:#0B266E}
.status-selesai{background:#ECFDF5;color:#059669}
.status-ditolak{background:#fee2e2;color:#dc2626}
/* Buttons */
.btn-ajukan{background:linear-gradient(135deg,#0B266E,#0B266E);color:#fff;font-weight:600;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;font-size:14px;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;}
.btn-ajukan:hover{background:linear-gradient(135deg,#091958,#091958);transform:translateY(-1px)}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
.modal-box{background:#fff;border-radius:16px;padding:32px;max-width:440px;width:90%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.15)}

/* ── Lightbox Modal ── */
.lightbox-modal { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(0, 0, 0, 0.92); align-items: center; justify-content: center; animation: lightboxFadeIn 0.25s ease; }
.lightbox-modal.active { display: flex; }
@keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
.lightbox-content { position: relative; max-width: 90vw; max-height: 85vh; display: flex; align-items: center; justify-content: center; }
.lightbox-content img { max-width: 90vw; max-height: 82vh; object-fit: contain; border-radius: 8px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4); animation: lightboxZoomIn 0.3s ease; }
@keyframes lightboxZoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.lightbox-close { position: fixed; top: 20px; right: 24px; width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10001; }
.lightbox-close:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
.lightbox-info { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); text-align: center; z-index: 10001; }
.lightbox-info .lightbox-title { color: #fff; font-size: 14px; font-weight: 600; margin-bottom: 4px; }
</style>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#ECFDF5;color:#059669;font-weight:500;font-size:14px;">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;border:none;background:#fee2e2;color:#dc2626;font-weight:500;font-size:14px;">
    {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start">
    <div class="detail-header">
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-back">&larr;</a>
        <div>
            <h3 class="fw-bold mb-0" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Detail Rencana Proker</h3>
            <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">{{ $proker->judul }}</p>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-start">
        @if($canEdit && $proker->status === 'draft')
            <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
               class="btn d-flex align-items-center gap-2"
               style="background: #0B266E; color: #fff; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
        @endif
        @if($canDelete && $proker->status === 'draft')
            <button type="button" class="btn d-flex align-items-center gap-2"
                    style="background: #fee2e2; color: #dc2626; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px; border: none;"
                    onclick="document.getElementById('deleteModal').style.display='flex'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Hapus
            </button>
        @endif
        @if($proker->status === 'draft' && !$isPengawas)
            @if($canAjukan)
                @if($proker->banner)
                    <button type="button" class="btn-ajukan" style="height:38px;padding:0 20px;font-size:13px;"
                            onclick="document.getElementById('ajukanModal').style.display='flex'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                        Ajukan Proker
                    </button>
                @else
                    <button type="button" disabled
                        title="Banner proker wajib diunggah terlebih dahulu sebelum mengajukan proker"
                        style="height:38px;padding:0 20px;font-size:13px;font-weight:600;border-radius:10px;border:none;display:inline-flex;align-items:center;gap:8px;background:#DFE1E7;color:#666D80;cursor:not-allowed;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                        Ajukan Proker
                    </button>
                @endif
            @else
                <button type="button" disabled
                    title="Hanya Ketua / Ketua Bidang / Ketua Unit yang dapat mengajukan proker"
                    style="height:38px;padding:0 20px;font-size:13px;font-weight:600;border-radius:10px;border:none;display:inline-flex;align-items:center;gap:8px;background:#DFE1E7;color:#666D80;cursor:not-allowed;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                    Ajukan Proker
                </button>
            @endif
        @endif
    </div>
</div>

@if($proker->banner)
<div style="position:relative;width:100%;max-height:340px;border-radius:18px;overflow:hidden;margin-bottom:28px;box-shadow:0 10px 30px -10px rgba(0,0,0,0.15);cursor:pointer;transition:transform 0.2s;" 
     onclick="openLightbox()"
     onmouseover="this.style.transform='scale(1.005)'"
     onmouseout="this.style.transform='scale(1)'">
    <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 45%);z-index:1;transition:background 0.2s;" onmouseover="this.style.background='linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 50%)'" onmouseout="this.style.background='linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 45%)'"></div>
    <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}" style="width:100%;height:340px;object-fit:cover;display:block;">
    <div style="position:absolute;bottom:24px;left:28px;z-index:2;display:flex;align-items:center;gap:12px;">
        <span style="background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:0.5px;border:1px solid rgba(255,255,255,0.4);text-shadow:0 1px 2px rgba(0,0,0,0.2);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>Banner Proker &bull; Klik untuk memperbesar
        </span>
    </div>
</div>
@endif

{{-- Info Utama & Aksi --}}
<div class="detail-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-4 mb-3">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($proker->bidangs && $proker->bidangs->count() > 0)
                    @foreach($proker->bidangs as $b)
                        <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                    @endforeach
                @else
                    <span class="badge-bidang" style="background:#eef2ff;color:#0B266E;">Prodi</span>
                @endif
                @foreach(($proker->kategoris ?? collect()) as $kat)
                    <span class="badge-bidang" style="background:#FFFBEB;color:#92400e;">{{ $kat->nama_kategori }}</span>
                @endforeach
            </div>
            <h4 class="fw-bold mb-1" style="color:#0D0D12;">{{ $proker->judul }}</h4>
            <div style="font-size:13px;color:#666D80;font-weight:500;">
                Dibuat oleh: <span style="color:#374151;font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span> &bull; 
                {{ $proker->created_at->translatedFormat('d M Y') }}
            </div>
        </div>

        <div class="d-flex flex-column align-items-end gap-2">
            <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>
        </div>
    </div>
</div>



{{-- Deskripsi --}}
<div class="detail-card">
    <div class="detail-card-title">Deskripsi dan Tujuan Kegiatan</div>
    <div style="font-size:14px;color:#374151;line-height:1.75;white-space:pre-line;">{{ $proker->deskripsi }}</div>
</div>

{{-- Link ke Pelaksanaan / Arsip --}}
@if($proker->status === 'disetujui')
<div class="detail-card" style="background:rgba(11,38,110,0.05);border:1px solid rgba(11,38,110,0.18);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#091958;margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>Proker Diajukan!</div>
            <div style="font-size:14px;color:#0B266E;font-weight:500;">Input data realisasi di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="btn" style="background:#0B266E;color:#fff;font-weight:600;padding:10px 22px;border-radius:8px;font-size:14px;white-space:nowrap;">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

@if($proker->status === 'selesai')
<div class="detail-card" style="background:#ECFDF5;border:1px solid rgba(5,150,105,0.25);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#047857;margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Kegiatan Selesai &amp; Diarsipkan</div>
            <div style="font-size:14px;color:#059669;font-weight:500;">Laporan akhir kegiatan ini ada di halaman Laporan &amp; Arsip.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
           class="btn" style="background:#059669;color:#fff;font-weight:600;padding:10px 22px;border-radius:8px;font-size:14px;white-space:nowrap;">
            Lihat di Arsip &rarr;
        </a>
    </div>
</div>
@endif

{{-- Modals --}}
@if($canAjukan && $proker->status === 'draft' && !$isPengawas)
<div id="ajukanModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:#eef2ff;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
        </div>
        <h5 class="fw-bold mb-2">Ajukan Proker?</h5>
        <p style="color:#666D80;font-size:14px;">Rencana proker "<strong>{{ $proker->judul }}</strong>" akan diajukan dan masuk ke tahap <strong>Pelaksanaan Kegiatan</strong>.</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <button type="button" class="btn" style="background:#f3f4f6;color:#374151;font-weight:600;border-radius:10px;" onclick="document.getElementById('ajukanModal').style.display='none'">Batal</button>
            <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-ajukan" style="border-radius:10px;">Ajukan</button>
            </form>
        </div>
    </div>
</div>
@endif

@if($canDelete && $proker->status === 'draft')
<div id="deleteModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">&#128465;</div>
        <h5 class="fw-bold mb-2">Hapus Proker?</h5>
        <p style="color:#666D80;font-size:14px;">Data proker "<strong>{{ $proker->judul }}</strong>" akan dihapus permanen.</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <button class="btn" style="background:#f3f4f6;color:#374151;font-weight:600;border-radius:10px;" onclick="document.getElementById('deleteModal').style.display='none'">Batal</button>
            <form action="{{ route('manajemenmahasiswa.proker.destroy', $proker->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="border-radius:10px;font-weight:600;">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Lightbox Modal --}}
@if($proker->banner)
<div class="lightbox-modal" id="lightboxModal">
    <button class="lightbox-close" onclick="closeLightbox()" title="Tutup">&#10005;</button>
    <div class="lightbox-content">
        <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}">
    </div>
    <div class="lightbox-info">
        <div class="lightbox-title">{{ $proker->judul }}</div>
    </div>
</div>
@endif

<script>
// Lightbox
function openLightbox() {
    document.getElementById('lightboxModal').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('active');
}
document.getElementById('lightboxModal')?.addEventListener('click', e => {
    if (e.target.id === 'lightboxModal' || e.target.classList.contains('lightbox-content')) {
        closeLightbox();
    }
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});

// Close general modals on backdrop click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
</script>
</x-manajemenmahasiswa::layouts.mahasiswa>
