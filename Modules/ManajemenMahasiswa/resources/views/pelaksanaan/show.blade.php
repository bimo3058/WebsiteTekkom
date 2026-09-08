<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-detail._styles')


<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Header with back button -->
<div class="d-flex justify-content-between align-items-start">
    <div class="detail-header">
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" class="btn-back">
            &larr;
        </a>
        <div>
            <h3 class="fw-bold mb-0" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Detail Pelaksanaan Kegiatan</h3>
            <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">Informasi kegiatan pada subbab pelaksanaan</p>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        @if($canManage && $proker->status !== 'selesai')
            <a href="{{ route('manajemenmahasiswa.pelaksanaan.edit', $proker->id) }}"
               class="btn d-flex align-items-center gap-2"
               style="background: #0B266E; color: #fff; font-weight: 600; font-size: 13px; padding: 8px 18px; border-radius: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
        @endif
        @if($canDelete && $proker->status !== 'selesai')
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
        @if($proker->status !== 'selesai')
            @if($canArsip)
                @if($proker->is_pelaksanaan_updated && $proker->banner)
                    <button type="button" class="btn"
                            onclick="document.getElementById('arsipModal').style.display='flex'"
                            style="background:linear-gradient(135deg,#0B266E,#0B266E);color:#fff;font-weight:600;font-size:13px;padding:8px 18px;border-radius:10px;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer;transition:all .2s;box-shadow:0 2px 8px rgba(11,38,110,.25);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Unggah ke Arsip
                    </button>
                @else
                    <button type="button" disabled
                        title="{{ !$proker->is_pelaksanaan_updated ? 'Silakan edit/update data pelaksanaan kegiatan terlebih dahulu sebelum mengunggah ke arsip' : 'Banner kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip' }}"
                        style="background:#DFE1E7;color:#666D80;font-weight:600;font-size:13px;padding:8px 18px;border-radius:10px;display:inline-flex;align-items:center;gap:6px;border:none;cursor:not-allowed;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Unggah ke Arsip
                    </button>
                @endif
            @endif
            {{-- Role tanpa hak arsip (staff_himpunan, gpm) tidak menampilkan tombol sama sekali (view-only) --}}
        @endif
        @if($proker->status === 'selesai')
            <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
               class="btn" style="background:#0B266E;color:#fff;font-weight:600;font-size:13px;padding:8px 18px;border-radius:10px;height:38px;display:inline-flex;align-items:center;">
                Lihat di Arsip &rarr;
            </a>
        @endif
    </div>
</div>

{{-- Badan detail dipakai bersama dengan Rencana Proker; $showDokumentasi = true --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => true])


<!-- Unggah ke Arsip Confirmation Modal -->
@if($canArsip && $proker->status !== 'selesai' && $proker->is_pelaksanaan_updated && $proker->banner)
<div id="arsipModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background: #fff; border-radius: 16px; padding: 32px; max-width: 440px; width: 90%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #eef2ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </div>
        <h5 style="font-weight: 700; color: #0D0D12; margin-bottom: 8px;">Unggah ke Laporan & Arsip?</h5>
        <p style="color: #666D80; font-size: 14px; margin-bottom: 24px;">
            Semua data kegiatan <strong>{{ $proker->judul }}</strong> akan tersinkron ke subbab Laporan &amp; Arsip dan ditandai sebagai <strong>Selesai</strong>.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button"
                    onclick="document.getElementById('arsipModal').style.display='none'"
                    style="padding: 10px 24px; border-radius: 10px; border: 1px solid #DFE1E7; background: #fff; color: #374151; font-weight: 600; font-size: 14px; cursor: pointer;">
                Batal
            </button>
            <form action="{{ route('manajemenmahasiswa.pelaksanaan.publish', $proker->id) }}" method="POST">
                @csrf
                <button type="submit"
                        style="padding: 10px 24px; border-radius: 10px; border: none; background: #0B266E; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">
                    Unggah
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
@if($canDelete)
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 16px; padding: 32px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h5 style="font-weight: 700; color: #0D0D12; margin-bottom: 8px;">Hapus Kegiatan?</h5>
        <p style="color: #666D80; font-size: 14px; margin-bottom: 24px;">
            Kegiatan <strong>{{ $proker->judul }}</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button"
                    onclick="document.getElementById('deleteModal').style.display='none'"
                    style="padding: 10px 24px; border-radius: 10px; border: 1px solid #DFE1E7; background: #fff; color: #374151; font-weight: 600; font-size: 14px; cursor: pointer;">
                Batal
            </button>
            <form action="{{ route('manajemenmahasiswa.pelaksanaan.destroy', $proker->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="padding: 10px 24px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

</x-manajemenmahasiswa::layouts.mahasiswa>
