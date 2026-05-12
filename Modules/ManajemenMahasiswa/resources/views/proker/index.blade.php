<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    /* ── Status Badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
    }
    .status-draft     { background: #f3f4f6; color: #6b7280; }
    .status-diajukan  { background: #fef3c7; color: #92400e; }
    .status-disetujui { background: #dcfce7; color: #166534; }
    .status-ditolak   { background: #fee2e2; color: #dc2626; }

    /* ── Stats Cards ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    .stat-card {
        background: #fff; border-radius: 12px; padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #f3f4f6;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .stat-value { font-size: 22px; font-weight: 800; color: #1f2937; line-height: 1; }
    .stat-label { font-size: 11px; color: #9ca3af; font-weight: 600; margin-top: 2px; }

    /* ── Filter Bar ── */
    .filter-section { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; align-items: center; }
    .filter-chip {
        padding: 7px 18px; border-radius: 20px; border: 1.5px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; text-decoration: none !important;
        display: inline-block;
    }
    .filter-chip:hover { border-color: #818cf8; color: #4f46e5; background: #eef2ff; }
    .filter-chip.active { background: #4f46e5; color: #fff !important; border-color: #4f46e5; }
    .filter-select-custom {
        padding: 7px 16px; border-radius: 20px; border: 1.5px solid #e5e7eb;
        background: #fff; color: #374151; font-size: 13px; font-weight: 600;
        outline: none; transition: all 0.2s; height: 38px;
    }
    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    .search-input {
        background: #f3f4f6; border: none; border-radius: 8px;
        height: 42px; padding-left: 36px; font-size: 13px; width: 100%;
    }
    .search-input:focus { background: #fff; box-shadow: 0 0 0 2px #e0e7ff; outline: none; }

    /* ── Proker Cards ── */
    .proker-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: all 0.25s ease; text-decoration: none !important;
        display: flex; flex-direction: column; border: 1px solid #f3f4f6;
    }
    .proker-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px rgba(79,70,229,0.12);
        border-color: #c7d2fe;
    }
    .proker-card-image {
        width: 100%; aspect-ratio: 16/9;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .proker-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .proker-card-body { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; }
    .badge-bidang {
        font-size: 11px; font-weight: 700; padding: 3px 10px;
        border-radius: 20px; background: #eef2ff; color: #4f46e5;
    }
    .proker-card-title {
        font-weight: 700; font-size: 15px; color: #1f2937;
        margin: 8px 0 6px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    .proker-card-meta {
        display: flex; flex-wrap: wrap; gap: 10px;
        font-size: 12px; color: #9ca3af; font-weight: 500;
        padding-top: 10px; border-top: 1px solid #f3f4f6; margin-top: auto;
    }
    .proker-card-meta span { display: inline-flex; align-items: center; gap: 4px; }
    .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; }
    .empty-state h5 { color: #6b7280; font-weight: 600; margin-bottom: 4px; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:#fee2e2;color:#dc2626;font-weight:500;font-size:14px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Rencana Program Kerja</h3>
        <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">Daftar rencana proker dari 8 bidang himpunan &amp; prodi</p>
    </div>
    @if($canManage)
        <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="btn d-flex align-items-center gap-2"
           style="background:#4f46e5;color:#fff;font-weight:600;font-size:14px;padding:10px 20px;border-radius:10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Proker
        </a>
    @endif
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;">&#128221;</div>
        <div><div class="stat-value">{{ $stats['draft'] }}</div><div class="stat-label">Draft</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">&#8987;</div>
        <div><div class="stat-value">{{ $stats['diajukan'] }}</div><div class="stat-label">Menunggu Persetujuan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;">&#10003;</div>
        <div><div class="stat-value">{{ $stats['disetujui'] }}</div><div class="stat-label">Disetujui</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">&#10005;</div>
        <div><div class="stat-value">{{ $stats['ditolak'] }}</div><div class="stat-label">Ditolak</div></div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('manajemenmahasiswa.proker.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari rencana proker..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-2">
            <select name="status" class="filter-select-custom" style="min-width:160px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Status</option>
                <option value="draft" {{ request('status')==='draft'?'selected':'' }}>Draft</option>
                <option value="diajukan" {{ request('status')==='diajukan'?'selected':'' }}>Diajukan</option>
                <option value="disetujui" {{ request('status')==='disetujui'?'selected':'' }}>Disetujui</option>
                <option value="ditolak" {{ request('status')==='ditolak'?'selected':'' }}>Ditolak</option>
            </select>
            <select name="tahun" class="filter-select-custom" style="min-width:130px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Tahun</option>
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ request('tahun')==$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-section">
        <a href="{{ route('manajemenmahasiswa.proker.index', request()->except(['bidang','page'])) }}"
           class="filter-chip {{ !request('bidang')||request('bidang')==='semua'?'active':'' }}">Semua</a>
        <a href="{{ route('manajemenmahasiswa.proker.index', array_merge(request()->except('page'),['bidang'=>'prodi'])) }}"
           class="filter-chip {{ request('bidang')==='prodi'?'active':'' }}"
           style="{{ request('bidang')==='prodi'?'background:#7c3aed;border-color:#7c3aed;':'' }}">Prodi</a>
        @foreach($bidangList as $bidang)
            <a href="{{ route('manajemenmahasiswa.proker.index', array_merge(request()->except('page'),['bidang'=>$bidang->id])) }}"
               class="filter-chip {{ request('bidang')==$bidang->id?'active':'' }}">{{ $bidang->nama_bidang }}</a>
        @endforeach
    </div>
</form>

@if($prokerList->count() > 0)
    <div class="row g-4">
        @foreach($prokerList as $proker)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <a href="{{ route('manajemenmahasiswa.proker.show', $proker->id) }}" class="proker-card">
                    <div class="proker-card-image">
                        @if($proker->banner)
                            <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}">
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="proker-card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>
                            @if($proker->bidangs && $proker->bidangs->count() > 0)
                                @foreach($proker->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @else
                                <span class="badge-bidang" style="background:#f3e8ff;color:#7c3aed;">Prodi</span>
                            @endif
                        </div>
                        <div class="proker-card-title">{{ $proker->judul }}</div>
                        <div class="proker-card-meta">
                            <span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ $proker->tanggal_mulai ? $proker->tanggal_mulai->translatedFormat('d M Y') : 'Belum ditentukan' }}
                            </span>
                            @if($proker->tahun)<span>{{ $proker->tahun }}</span>@endif
                            @if($proker->ketuaPelaksana && $proker->ketuaPelaksana->user)
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ Str::limit($proker->ketuaPelaksana->user->name, 18) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    @if($prokerList->hasPages())
        <div class="mt-4 d-flex justify-content-center">{{ $prokerList->withQueryString()->links() }}</div>
    @endif
@else
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#128203;</div>
        <h5>Belum ada rencana proker</h5>
        <p>Rencana program kerja yang dibuat akan muncul di sini</p>
        @if($canManage)
            <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="btn mt-2"
               style="background:#4f46e5;color:#fff;border-radius:10px;font-weight:600;font-size:14px;">+ Buat Proker Pertama</a>
        @endif
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
