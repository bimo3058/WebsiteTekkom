<x-manajemenmahasiswa::layouts.mahasiswa>

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Pelaksanaan --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

<style>
/* Khusus Rencana Proker: badge status, tombol ajukan & modal */
.status-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700}
.status-draft{background:#f3f4f6;color:#666D80}
.status-diajukan{background:#FFFBEB;color:#92400e}
.status-disetujui{background:#eef2ff;color:#0B266E}
.status-selesai{background:#ECFDF5;color:#059669}
.status-ditolak{background:#fee2e2;color:#dc2626}
.btn-ajukan{background:linear-gradient(135deg,#0B266E,#0B266E);color:#fff;font-weight:600;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;font-size:14px;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;}
.btn-ajukan:hover{background:linear-gradient(135deg,#091958,#091958);transform:translateY(-1px)}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
.modal-box{background:#fff;border-radius:16px;padding:32px;max-width:440px;width:90%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.15)}
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

@php
    // Field yang masih kosong — dipakai untuk tooltip tombol Ajukan
    $kelengkapanKurang = collect($kelengkapan ?? [])->reject(fn($i) => $i['terisi'])->pluck('label');
    $rencanaLengkap    = $kelengkapanKurang->isEmpty();
@endphp

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start">
    <div class="detail-header">
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn-back">&larr;</a>
        <div>
            <h3 class="fw-bold mb-0" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Detail Rencana Proker</h3>
            <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">
                Dibuat oleh <span style="color:#374151;font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span>
                &bull; {{ $proker->created_at->translatedFormat('d M Y') }}
            </p>
        </div>
    </div>

    <div class="d-flex flex-column align-items-end gap-2">
        <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>

        <div class="d-flex gap-2 flex-wrap align-items-start justify-content-end">
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
            {{-- staff_himpunan & role pengawas tidak melihat tombol ini sama sekali --}}
            @if($proker->status === 'draft' && $canSeeAjukan)
                @if($canAjukan)
                    @if($rencanaLengkap)
                        <button type="button" class="btn-ajukan" style="height:38px;padding:0 20px;font-size:13px;"
                                onclick="document.getElementById('ajukanModal').style.display='flex'">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                            Ajukan Proker
                        </button>
                    @else
                        <button type="button" disabled
                            title="Lengkapi dulu: {{ $kelengkapanKurang->implode(', ') }}"
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
</div>

{{-- Badan detail dipakai bersama dengan Pelaksanaan; $showDokumentasi = false --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => false])

{{-- Link ke Pelaksanaan / Arsip --}}
@if($proker->status === 'disetujui')
<div class="detail-card" style="background:rgba(11,38,110,0.05);border:1px solid rgba(11,38,110,0.18);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:#091958;margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>Proker Diajukan!</div>
            <div style="font-size:14px;color:#0B266E;font-weight:500;">Cek kesesuaian data dengan realisasi di halaman Pelaksanaan Kegiatan.</div>
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
@if($canAjukan && $canSeeAjukan && $proker->status === 'draft' && $rencanaLengkap)
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

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

<script>
// Tutup modal konfirmasi saat klik area gelap
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
</script>
</x-manajemenmahasiswa::layouts.mahasiswa>
