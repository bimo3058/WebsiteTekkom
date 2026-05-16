<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    .filter-chip { padding:7px 18px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none !important;display:inline-block; }
    .filter-chip:hover { border-color:#818cf8;color:#4f46e5;background:#eef2ff; }
    .filter-chip.active { background:#4f46e5;color:#fff !important;border-color:#4f46e5; }
    .filter-select-custom { padding:7px 16px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;outline:none;height:38px; }
    .search-wrapper { position:relative;flex-grow:1; }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af; }
    .search-input { background:#f3f4f6;border:none;border-radius:8px;height:42px;padding-left:36px;font-size:13px;width:100%; }
    .search-input:focus { background:#fff;box-shadow:0 0 0 2px #e0e7ff;outline:none; }
    .filter-section { display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;align-items:center; }

    /* Cards */
    .pelaksanaan-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);transition:all 0.25s;text-decoration:none !important;display:flex;flex-direction:column;border:1px solid #f3f4f6; }
    .pelaksanaan-card:hover { transform:translateY(-3px);box-shadow:0 12px 24px -4px rgba(79,70,229,0.12);border-color:#c7d2fe; }
    .card-banner { width:100%;aspect-ratio:16/9;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative; }
    .card-banner img { width:100%;height:100%;object-fit:cover; }
    .card-body { padding:16px 18px 18px;display:flex;flex-direction:column;flex:1; }
    .badge-bidang { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#eef2ff;color:#4f46e5; }
    .card-title { font-weight:700;font-size:15px;color:#1f2937;margin:8px 0 10px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    /* Rencana vs Realisasi compare */
    .compare-row { display:flex;gap:8px;font-size:12px;color:#6b7280;margin-bottom:4px; }
    .compare-label { font-weight:600;width:100px;flex-shrink:0; }
    .compare-rencana { color:#6b7280; }
    .compare-realisasi { color:#16a34a;font-weight:600; }
    .compare-realisasi.missing { color:#f59e0b;font-style:italic; }
    /* Status */
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700; }
    .status-disetujui { background:#dcfce7;color:#166534; }
    .status-berlangsung { background:#dbeafe;color:#1d4ed8; }
    .status-selesai { background:#f3f4f6;color:#374151; }
    .empty-state { text-align:center;padding:50px 20px;color:#9ca3af; }
    .empty-state h5 { color:#6b7280;font-weight:600;margin-bottom:4px; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Pelaksanaan Kegiatan</h3>
        <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">Proker yang sudah disetujui — input data realisasi pelaksanaan di sini</p>
    </div>
</div>


{{-- Filter --}}
<form method="GET" action="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari kegiatan..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-2">
            <select name="tahun" class="filter-select-custom" style="min-width:130px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Tahun</option>
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ request('tahun')==$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-section">
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.index', request()->except(['bidang','page'])) }}"
           class="filter-chip {{ !request('bidang')||request('bidang')==='semua'?'active':'' }}">Semua</a>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.index', array_merge(request()->except('page'),['bidang'=>'prodi'])) }}"
           class="filter-chip {{ request('bidang')==='prodi'?'active':'' }}"
           style="{{ request('bidang')==='prodi'?'background:#7c3aed;border-color:#7c3aed;':'' }}">Prodi</a>
        @foreach($bidangList as $bidang)
            <a href="{{ route('manajemenmahasiswa.pelaksanaan.index', array_merge(request()->except('page'),['bidang'=>$bidang->id])) }}"
               class="filter-chip {{ request('bidang')==$bidang->id?'active':'' }}">{{ $bidang->nama_bidang }}</a>
        @endforeach
    </div>
</form>

@if($pelaksanaanList->count() > 0)
    <div class="row g-4">
        @foreach($pelaksanaanList as $item)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $item->id) }}" class="pelaksanaan-card">
                    <div class="card-banner">
                        @if($item->banner)
                            <img src="{{ $item->banner_url }}" alt="{{ $item->judul }}">
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @if($item->bidangs && $item->bidangs->count() > 0)
                                @foreach($item->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @else
                                <span class="badge-bidang" style="background:#f3e8ff;color:#7c3aed;">Prodi</span>
                            @endif
                        </div>
                        <div class="card-title">{{ $item->judul }}</div>
                        @if($canManage && $item->status === 'disetujui' && !$item->has_realisasi)
                            <div style="margin-top:10px;">
                                <span style="display:inline-flex;align-items:center;gap:6px;background:#fef3c7;color:#92400e;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;">
                                    &#9888; Belum ada data realisasi — klik untuk input
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    @if($pelaksanaanList->hasPages())
        <div class="mt-4 d-flex justify-content-center">{{ $pelaksanaanList->withQueryString()->links() }}</div>
    @endif
@else
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#127939;</div>
        <h5>Belum ada proker yang siap dilaksanakan</h5>
        <p>Proker yang sudah disetujui admin akan muncul di sini</p>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn mt-2"
           style="background:#4f46e5;color:#fff;border-radius:10px;font-weight:600;font-size:14px;">
            Lihat Rencana Proker
        </a>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
