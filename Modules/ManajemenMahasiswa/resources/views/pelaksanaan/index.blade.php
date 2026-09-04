<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    .filter-chip { padding:7px 16px;border-radius:8px;border:1px solid #DFE1E7;background:#fff;color:#666D80;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;text-decoration:none !important;display:inline-block; }
    .filter-chip:hover { border-color:#0B266E;color:#0B266E;background:rgba(11,38,110,0.06); }
    .filter-chip.active { background:#0B266E;color:#fff !important;border-color:#0B266E; }
    .filter-select-custom { padding:0 14px;border-radius:8px;border:1px solid #DFE1E7;background:#fff;color:#374151;font-size:13px;font-weight:600;outline:none;height:38px;transition:all 0.15s; }
    .filter-select-custom:focus { border-color:#0B266E;box-shadow:0 0 0 3px rgba(11,38,110,0.1); }
    .search-wrapper { position:relative;flex-grow:1; }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#666D80; }
    .search-input { background:#fff;border:1px solid #DFE1E7;border-radius:8px;height:38px;padding-left:36px;font-size:13px;width:100%;color:#374151; }
    .search-input:focus { background:#fff;border-color:#0B266E;box-shadow:0 0 0 3px rgba(11,38,110,0.1);outline:none; }
    .filter-section { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;align-items:center; }

    /* Cards */
    .pelaksanaan-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,0.04);transition:all 0.2s;text-decoration:none !important;display:flex;flex-direction:column;border:1px solid #DFE1E7; }
    .pelaksanaan-card:hover { transform:translateY(-3px);box-shadow:0 12px 24px -4px rgba(11,38,110,0.12);border-color:rgba(11,38,110,0.25); }
    .card-banner { width:100%;aspect-ratio:16/9;background:linear-gradient(135deg,rgba(11,38,110,0.06),rgba(11,38,110,0.12));display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative; }
    .card-banner img { width:100%;height:100%;object-fit:cover; }
    .card-body { padding:16px 18px 18px;display:flex;flex-direction:column;flex:1; }
    .badge-bidang { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#eef2ff;color:#0B266E; }
    .card-title { font-weight:700;font-size:15px;color:#0D0D12;margin:8px 0 10px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    .card-meta { display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#666D80;font-weight:500;padding-top:10px;border-top:1px solid #f3f4f6;margin-top:auto; }
    .card-meta span { display:inline-flex;align-items:center;gap:4px; }

    /* Status */
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700; }
    .status-disetujui { background:#ECFDF5;color:#059669; }
    .status-berlangsung { background:#dbeafe;color:#1d4ed8; }
    .status-selesai { background:#f3f4f6;color:#374151; }
    .empty-state { text-align:center;padding:50px 20px;color:#666D80; }
    .empty-state h5 { color:#666D80;font-weight:600;margin-bottom:4px; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#ECFDF5;color:#059669;font-weight:500;font-size:14px;">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Pelaksanaan Kegiatan</h3>
        <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">Proker yang sudah disetujui — lengkapi data pelaksanaan di sini</p>
    </div>
</div>


{{-- Filter --}}
<form method="GET" action="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari judul atau deskripsi kegiatan..." value="{{ request('search') }}">
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
           style="{{ request('bidang')==='prodi'?'background:#0B266E;border-color:#0B266E;':'' }}">Prodi</a>
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
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#5C78B8" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @if($item->bidangs && $item->bidangs->count() > 0)
                                @foreach($item->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @else
                                <span class="badge-bidang" style="background:#eef2ff;color:#0B266E;">Prodi</span>
                            @endif
                        </div>
                        <div class="card-title">{{ $item->judul }}</div>

                        <div class="card-meta">
                            <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> {{ $item->tanggal_mulai ? $item->tanggal_mulai->translatedFormat('d M Y') : 'Belum ditentukan' }}</span>
                            @if($item->jam_mulai)
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $item->jam_mulai_formatted }}{{ $item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : '' }} WIB</span>
                            @endif
                            @if($item->lokasi)
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ $item->lokasi }}</span>
                            @endif
                        </div>
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
           style="background:#0B266E;color:#fff;border-radius:8px;font-weight:600;font-size:14px;">
            Lihat Rencana Proker
        </a>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
