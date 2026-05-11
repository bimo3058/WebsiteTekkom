<x-manajemenmahasiswa::layouts.mahasiswa>

<style>
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:0.3px; }
    .status-draft { background:#f3f4f6;color:#6b7280; }
    .status-diajukan,.status-menunggu_ttd_ketua,.status-menunggu_ttd_dpm,.status-menunggu_ttd_dept { background:#fef3c7;color:#92400e; }
    .status-disetujui { background:#dcfce7;color:#166534; }
    .status-ditolak { background:#fee2e2;color:#dc2626; }
    .stats-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px; }
    @media(max-width:768px){ .stats-grid{grid-template-columns:repeat(2,1fr);} }
    .stat-card { background:#fff;border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border:1px solid #f3f4f6;display:flex;align-items:center;gap:14px; }
    .stat-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
    .stat-value { font-size:22px;font-weight:800;color:#1f2937;line-height:1; }
    .stat-label { font-size:11px;color:#9ca3af;font-weight:600;margin-top:2px; }
    .filter-chip { padding:7px 18px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none!important;display:inline-block; }
    .filter-chip:hover { border-color:#818cf8;color:#4f46e5;background:#eef2ff; }
    .filter-chip.active { background:#4f46e5;color:#fff!important;border-color:#4f46e5; }
    .filter-select-custom { padding:7px 16px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:13px;font-weight:600;outline:none;height:38px; }
    .search-wrapper { position:relative;flex-grow:1; }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af; }
    .search-input { background:#f3f4f6;border:none;border-radius:8px;height:42px;padding-left:36px;font-size:13px;width:100%; }
    .search-input:focus { background:#fff;box-shadow:0 0 0 2px #e0e7ff;outline:none; }
    .proker-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);transition:all 0.25s ease;text-decoration:none!important;display:flex;flex-direction:column;border:1px solid #f3f4f6; }
    .proker-card:hover { transform:translateY(-3px);box-shadow:0 12px 24px -4px rgba(79,70,229,0.12);border-color:#c7d2fe; }
    .proker-card-image { width:100%;aspect-ratio:16/9;background:linear-gradient(135deg,#e0e7ff 0%,#c7d2fe 100%);display:flex;align-items:center;justify-content:center;overflow:hidden; }
    .proker-card-image img { width:100%;height:100%;object-fit:cover; }
    .proker-card-body { padding:16px 18px 18px;display:flex;flex-direction:column;flex:1; }
    .badge-bidang { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#eef2ff;color:#4f46e5; }
    .proker-card-title { font-weight:700;font-size:15px;color:#1f2937;margin:8px 0 6px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    .proker-card-meta { display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#9ca3af;font-weight:500;padding-top:10px;border-top:1px solid #f3f4f6;margin-top:auto; }
    .proker-card-meta span { display:inline-flex;align-items:center;gap:4px; }
    .empty-state { text-align:center;padding:50px 20px;color:#9ca3af; }
    .empty-state h5 { color:#6b7280;font-weight:600;margin-bottom:4px; }
    /* TTD indicator badge */
    .ttd-indicator { display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#fef3c7;color:#92400e;border:1px solid #fde68a; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
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
        <h3 class="fw-bold mb-1 text-dark">Persuratan</h3>
        <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">Upload surat proker dan proses tanda tangan digital</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;">&#128221;</div>
        <div><div class="stat-value">{{ $stats['draft'] }}</div><div class="stat-label">Menunggu Surat</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">&#8987;</div>
        <div><div class="stat-value">{{ $stats['diajukan'] }}</div><div class="stat-label">Proses Tanda Tangan</div></div>
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
<form method="GET" action="{{ route('manajemenmahasiswa.persuratan.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari proker..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-2">
            <select name="status" class="filter-select-custom" style="min-width:180px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Status</option>
                <option value="draft" {{ request('status')==='draft'?'selected':'' }}>Menunggu Surat</option>
                <option value="menunggu_ttd_ketua" {{ request('status')==='menunggu_ttd_ketua'?'selected':'' }}>TTD Ketua & Bendahara</option>
                <option value="menunggu_ttd_dpm" {{ request('status')==='menunggu_ttd_dpm'?'selected':'' }}>TTD DPM</option>
                <option value="menunggu_ttd_dept" {{ request('status')==='menunggu_ttd_dept'?'selected':'' }}>TTD Ketua Departemen</option>
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
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('manajemenmahasiswa.persuratan.index', request()->except(['bidang','page'])) }}"
           class="filter-chip {{ !request('bidang')||request('bidang')==='semua'?'active':'' }}">Semua</a>
        <a href="{{ route('manajemenmahasiswa.persuratan.index', array_merge(request()->except('page'),['bidang'=>'prodi'])) }}"
           class="filter-chip {{ request('bidang')==='prodi'?'active':'' }}"
           style="{{ request('bidang')==='prodi'?'background:#7c3aed;border-color:#7c3aed;':'' }}">Prodi</a>
        @foreach($bidangList as $bidang)
            <a href="{{ route('manajemenmahasiswa.persuratan.index', array_merge(request()->except('page'),['bidang'=>$bidang->id])) }}"
               class="filter-chip {{ request('bidang')==$bidang->id?'active':'' }}">{{ $bidang->nama_bidang }}</a>
        @endforeach
    </div>
</form>

@if($prokerList->count() > 0)
    <div class="row g-4">
        @foreach($prokerList as $proker)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <a href="{{ route('manajemenmahasiswa.persuratan.show', $proker->id) }}" class="proker-card">
                    <div class="proker-card-image">
                        @if($proker->banner)
                            <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}">
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#a5b4fc" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="proker-card-body">
                        <div class="d-flex flex-wrap gap-2">
                            {{-- Status badge --}}
                            @php
                                $statusLabel = match($proker->status) {
                                    'draft'              => '📄 Belum Ada Surat',
                                    'menunggu_ttd_ketua' => '✍ TTD Ketua & Bendahara',
                                    'menunggu_ttd_dpm'   => '✍ TTD DPM',
                                    'menunggu_ttd_dept'  => '✍ TTD Ketua Dept',
                                    'disetujui'          => '✓ Disetujui',
                                    'ditolak'            => '✕ Ditolak',
                                    default              => $proker->status_label,
                                };
                            @endphp
                            <span class="status-badge status-{{ $proker->status }}">{{ $statusLabel }}</span>
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
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">📋</div>
        <h5>Belum ada proker di fase persuratan</h5>
        <p>Buat proker baru di <strong>Rencana Proker</strong> terlebih dahulu</p>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="btn mt-2"
           style="background:#4f46e5;color:#fff;border-radius:10px;font-weight:600;font-size:14px;">
            → Ke Rencana Proker
        </a>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
