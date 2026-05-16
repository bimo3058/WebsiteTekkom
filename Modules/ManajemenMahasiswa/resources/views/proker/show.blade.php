<x-manajemenmahasiswa::layouts.mahasiswa>
<style>
.detail-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
.btn-back{width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:18px;transition:all 0.2s;flex-shrink:0}
.btn-back:hover{background:#f3f4f6;border-color:#d1d5db;color:#1f2937}
.detail-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);margin-bottom:20px}
.detail-card-title{font-weight:700;font-size:16px;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.badge-bidang{font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;background:#eef2ff;color:#4f46e5}
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700}
.status-draft{background:#f3f4f6;color:#6b7280}
.status-diajukan{background:#fef3c7;color:#92400e}
.status-ditolak{background:#fee2e2;color:#dc2626}
/* Buttons */
.btn-ajukan{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-weight:600;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;font-size:14px;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;}
.btn-ajukan:hover{background:linear-gradient(135deg,#4338ca,#6d28d9);transform:translateY(-1px)}
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
<div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
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
            <h3 class="fw-bold mb-0 text-dark">Detail Rencana Proker</h3>
            <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">{{ $proker->judul }}</p>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-start">
        @if(($isCreator || $isAdmin) && in_array($proker->status, ['draft','ditolak']))
            <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
               class="btn d-flex align-items-center gap-2"
               style="background: #4f46e5; color: #fff; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
        @endif
        @if($isAdmin && in_array($proker->status, ['draft','ditolak']))
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
        @if($proker->status === 'draft' && ($isCreator || $isPengurus))
            <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="btn-ajukan" style="height:38px;padding:0 20px;font-size:13px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                    Ajukan Proker
                </button>
            </form>
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
            &#128247; Banner Proker &bull; Klik untuk memperbesar
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
                    <span class="badge-bidang" style="background:#f3e8ff;color:#7c3aed;">Prodi</span>
                @endif
                @foreach(($proker->kategoris ?? collect()) as $kat)
                    <span class="badge-bidang" style="background:#fef3c7;color:#92400e;">{{ $kat->nama_kategori }}</span>
                @endforeach
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $proker->judul }}</h4>
            <div style="font-size:13px;color:#6b7280;font-weight:500;">
                Dibuat oleh: <span style="color:#374151;font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span> &bull; 
                {{ $proker->created_at->translatedFormat('d M Y') }}
            </div>
        </div>

        <div class="d-flex flex-column align-items-end gap-2">
            <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>
        </div>
    </div>
</div>

{{-- Catatan Penolakan --}}
@if($proker->status === 'ditolak' && $proker->catatan_penolakan)
<div style="background:#fff5f5;border:1.5px solid #fecaca;border-radius:12px;padding:20px;margin-bottom:20px;">
    <div style="font-weight:700;color:#dc2626;margin-bottom:8px;">&#9888; Proker Ditolak</div>
    <p style="color:#374151;font-size:14px;margin:0;">{{ $proker->catatan_penolakan }}</p>
    @if($isCreator || $isPengurus)
        <div style="margin-top:12px;">
            <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
               class="btn d-flex align-items-center gap-2"
               style="background: #4f46e5; color: #fff; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px; width: fit-content;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Revisi &amp; Ajukan Ulang
            </a>
        </div>
    @endif
</div>
@endif

{{-- Banner link ke Pelaksanaan Kegiatan --}}
@if(in_array($proker->status, ['diajukan','menunggu_ttd_ketua','menunggu_ttd_dpm','menunggu_ttd_dept','disetujui']))
<div class="detail-card" style="background:linear-gradient(135deg,#fef3c7,#fffbeb);border:1.5px solid #fde68a;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#92400e;margin-bottom:4px;">&#9998; Proker Telah Diajukan</div>
            <div style="font-size:14px;color:#78350f;">Proker ini sedang diproses. Pantau progres di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="btn" style="background:#f59e0b;color:#fff;font-weight:600;padding:10px 22px;border-radius:10px;font-size:14px;white-space:nowrap;">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

{{-- Deskripsi --}}
<div class="detail-card">
    <div class="detail-card-title">Deskripsi &amp; Latar Belakang</div>
    <div style="font-size:14px;color:#374151;line-height:1.75;white-space:pre-line;">{{ $proker->deskripsi }}</div>
</div>

{{-- Link ke Pelaksanaan --}}
@if(in_array($proker->status, ['disetujui','akan_datang','berlangsung','selesai']))
<div class="detail-card" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);border:1.5px solid #c7d2fe;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#4338ca;margin-bottom:4px;">&#128640; Proker Disetujui!</div>
            <div style="font-size:14px;color:#4f46e5;font-weight:500;">Input data realisasi di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="btn" style="background:#4f46e5;color:#fff;font-weight:600;padding:10px 22px;border-radius:10px;font-size:14px;white-space:nowrap;">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

{{-- Modals --}}
@if($isAdmin && in_array($proker->status, ['draft','ditolak']))
<div id="deleteModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">&#128465;</div>
        <h5 class="fw-bold mb-2">Hapus Proker?</h5>
        <p style="color:#6b7280;font-size:14px;">Data proker "<strong>{{ $proker->judul }}</strong>" akan dihapus permanen.</p>
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
