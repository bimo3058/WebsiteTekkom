<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Validasi RPS')
    @section('page-subtitle', 'Pantau riwayat dokumen RPS yang telah direview')

    <style>
        .nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 1rem 0; margin-right: 2rem; background: transparent; font-size: 0.95rem; }
        .nav-tabs-custom .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
        .badge-count { background-color: #dbeafe; color: #1e40af; border-radius: 9999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700;}
        
        .search-container { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        .search-input { border: none; background: transparent; box-shadow: none !important; }
        .search-input:focus { outline: none; box-shadow: none; background: transparent; }
        .btn-filter { border: 1px solid #e2e8f0; background-color: white; color: #475569; font-weight: 500; font-size: 0.9rem;}
        
        .table-container { background-color: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
        .table-rps th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 600; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: white; letter-spacing: 0.5px;}
        .table-rps td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-rps tr:last-child td { border-bottom: none; }
        
        .badge-menunggu { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; font-weight: 600; padding: 0.35rem 0.75rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .badge-revisi { background-color: #fed7aa; color: #b45309; border: 1px solid #fdba74; font-weight: 600; padding: 0.35rem 0.75rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .badge-disetujui { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-weight: 600; padding: 0.35rem 0.75rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .avatar-text { width: 32px; height: 32px; border-radius: 50%; background-color: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; margin-right: 0.75rem; }
        
        .btn-review { background-color: #2563eb; color: white; border-radius: 0.375rem; font-weight: 500; font-size: 0.85rem; padding: 0.5rem 1.25rem; border: none; transition: 0.2s; }
        .btn-review:hover { background-color: #1d4ed8; color: white;}
        
        .pagination-custom .page-link { color: #475569; border: 1px solid #e2e8f0; margin: 0 0.25rem; border-radius: 0.375rem; font-size: 0.875rem;}
        .pagination-custom .page-item.active .page-link { background-color: #2563eb; border-color: #2563eb; color: white; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Validasi RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pantau riwayat dokumen RPS yang telah direview";

                // Flash messages auto close
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert-dismissible');
                    alerts.forEach(alert => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);

                // Debounce function
                function debounce(func, delay) {
                    let timeoutId;
                    return function(...args) {
                        clearTimeout(timeoutId);
                        timeoutId = setTimeout(() => func.apply(this, args), delay);
                    };
                }

                // Enhanced search table function
                function searchTable(searchInput, tabId) {
                    const searchValue = searchInput.value.toLowerCase().trim();
                    const tabContent = document.getElementById(tabId);
                    
                    if (!tabContent) {
                        console.error(`Tab ${tabId} tidak ditemukan`);
                        return;
                    }

                    // Cari semua rows dalam table tbody
                    const rows = tabContent.querySelectorAll('table tbody tr');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        // Ambil text dari cell mata kuliah dan dosen
                        const cells = row.querySelectorAll('td');
                        let rowText = '';
                        
                        // Gabung text dari cell 1 (mata kuliah) dan cell 2 (dosen)
                        if (cells.length >= 2) {
                            rowText = (cells[0].textContent + ' ' + cells[1].textContent).toLowerCase();
                        } else {
                            rowText = row.textContent.toLowerCase();
                        }

                        // Cari apakah searchValue ada di dalam rowText
                        if (rowText.includes(searchValue) || searchValue === '') {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Tampilkan/sembunyikan no results message
                    const noResultsMsg = tabContent.querySelector('.no-results-message');
                    if (noResultsMsg) {
                        noResultsMsg.style.display = visibleCount === 0 ? '' : 'none';
                    }

                    console.log(`Tab ${tabId}: menampilkan ${visibleCount} dari ${rows.length} results`);
                }

                // Initialize search inputs dengan debounce
                const searchInputs = document.querySelectorAll('.search-input');
                console.log(`Ditemukan ${searchInputs.length} search input`);

                searchInputs.forEach((input, index) => {
                    const tabId = input.getAttribute('data-search-tab');
                    console.log(`Input ${index}: target tab = ${tabId}`);

                    input.addEventListener('input', debounce(function() {
                        console.log(`Searching in ${tabId}: "${this.value}"`);
                        searchTable(this, tabId);
                    }, 300));
                });
            });
        </script>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($activePeriode)
            <div class="card border-0 mb-4 rounded-4" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); background: #f8fafc; border-left: 4px solid {{ $isPeriodeRunning ? '#10b981' : '#94a3b8' }} !important;">
                <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: {{ $isPeriodeRunning ? '#d1fae5' : '#e2e8f0' }}; color: {{ $isPeriodeRunning ? '#059669' : '#64748b' }};">
                            <i class="fas fa-calendar-check fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 1rem;">{{ $activePeriode->judul }}</h6>
                            <div class="text-muted" style="font-size: 0.85rem;">
                                Tenggat: {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="flex-wrap: wrap; justify-content: flex-end;">
                        <div>
                            @if($isPeriodeRunning)
                                <span class="badge bg-success py-2 px-3 rounded-pill" style="background-color: #10b981 !important; color: white !important; font-weight: 500; font-size: 0.85rem;">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Sesi Dibuka
                                </span>
                            @else
                                <span class="badge bg-secondary py-2 px-3 rounded-pill" style="background-color: #64748b !important; color: white !important; font-weight: 500; font-size: 0.85rem;">
                                    <i class="fas fa-times-circle me-1" style="font-size: 0.8rem; vertical-align: middle;"></i> Sesi Berakhir
                                </span>
                            @endif
                        </div>
                        @if($isPeriodeRunning)
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalCloseSession" style="font-weight: 500; font-size: 0.85rem;">
                                <i class="fas fa-power-off me-1" style="font-size: 0.75rem;"></i> Matikan Sesi
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 mb-4 rounded-4" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); background: #f8fafc; border-left: 4px solid #f59e0b !important;">
                <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #fef3c7; color: #d97706;">
                            <i class="fas fa-calendar-times fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 1rem;">Belum Ada Jadwal Pengajuan</h6>
                            <div class="text-muted" style="font-size: 0.85rem;">
                                Tidak ada sesi pengajuan RPS yang ditambahkan saat ini
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="badge py-2 px-3 rounded-pill" style="background-color: #fef3c7 !important; color: #d97706 !important; font-weight: 500; font-size: 0.85rem;">
                            <i class="fas fa-exclamation-circle me-1" style="font-size: 0.75rem; vertical-align: middle;"></i> Belum Aktif
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom" style="border-color: #e2e8f0 !important;">
            <div class="nav-tabs-custom mb-0" style="border-bottom: none;">
                <ul class="nav nav-tabs border-0" id="rpsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-decoration-none" id="menunggu-tab" data-bs-toggle="tab" data-bs-target="#menunggu" type="button" role="tab">
                            Menunggu Validasi <span class="badge-count">{{ $rpsDiajukan->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-decoration-none" id="revisi-tab" data-bs-toggle="tab" data-bs-target="#revisi" type="button" role="tab">
                            Menunggu Revisi <span class="badge-count">{{ $rpsRevisi->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-decoration-none" id="disetujui-tab" data-bs-toggle="tab" data-bs-target="#disetujui" type="button" role="tab">
                            Disetujui <span class="badge-count">{{ $rpsDisetujui->total() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
            
            <button type="button" class="btn btn-primary rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-calendar-plus me-2"></i> Buat Periode
            </button>
        </div>

        <div class="tab-content" id="rpsTabContent">
            <!-- Tab Menunggu Validasi -->
            <div class="tab-pane fade show active" id="menunggu" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" 
                                style="max-width: 500px;">

                            <i class="fas fa-search text-muted me-2" 
                            style="font-size: 0.9rem;"></i>

                            <input type="text" class="form-control search-input p-0" data-search-tab="menunggu"
                            placeholder="Cari mata kuliah atau dosen...">
                        </div>
                        
                        <button class="btn btn-filter px-4 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-muted" 
                                style="font-size: 0.85rem;"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="table-container shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-rps mb-0">
                            <thead>
                                <tr>
                                    <th width="35%">MATA KULIAH</th>
                                    <th width="20%">DOSEN PENGAMPU</th>
                                    <th width="15%">TANGGAL DIAJUKAN</th>
                                    <th width="15%">STATUS</th>
                                    <th width="15%" class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rpsDiajukan as $rps)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $rps->mk_nama }} ({{ $rps->kode }})</div>
                                        <div class="text-muted" style="font-size: 0.8rem;">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            @php
                                                $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                                            @endphp
                                            @forelse($dosensList as $dosenItem)
                                                @php
                                                    [$initials, $dosenName] = explode('|', $dosenItem, 2);
                                                @endphp
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="avatar-text">{{ strtoupper($initials) }}</div>
                                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">{{ $dosenName }}</span>
                                                </div>
                                            @empty
                                                <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td><span class="text-muted" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($rps->tanggal_diajukan)->format('d M Y') }}</span></td>
                                    <td><span class="badge-menunggu">MENUNGGU</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->rps_id) }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                            <i class="fas fa-comment-dots me-2" style="font-size: 0.8rem;"></i> Review Sekarang
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Tidak ada RPS yang menunggu validasi</p>
                                    </td>
                                </tr>
                                @endforelse
                                <tr class="no-results-message" style="display: none;">
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Tidak ada hasil pencarian</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-2 px-2">
                    <span class="text-muted mb-3 mb-sm-0" style="font-size: 0.875rem;">Menampilkan {{ $rpsDiajukan->count() }} dari {{ $rpsDiajukan->total() }} entri</span>
                    <nav>
                        {{ $rpsDiajukan->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>

            <!-- Tab Menunggu Revisi Dosen -->
            <div class="tab-pane fade" id="revisi" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" 
                                style="max-width: 500px;">

                            <i class="fas fa-search text-muted me-2" 
                            style="font-size: 0.9rem;"></i>

                            <input type="text" class="form-control search-input p-0" data-search-tab="revisi"
                            placeholder="Cari mata kuliah atau dosen...">
                        </div>
                        
                        <button class="btn btn-filter px-4 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-muted" 
                                style="font-size: 0.85rem;"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="table-container shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-rps mb-0">
                            <thead>
                                <tr>
                                    <th width="35%">MATA KULIAH</th>
                                    <th width="20%">DOSEN PENGAMPU</th>
                                    <th width="15%">TANGGAL REVIEW</th>
                                    <th width="15%">STATUS</th>
                                    <th width="15%" class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rpsRevisi as $rps)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $rps->mk_nama }} ({{ $rps->kode }})</div>
                                        <div class="text-muted" style="font-size: 0.8rem;">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            @php
                                                $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                                            @endphp
                                            @forelse($dosensList as $dosenItem)
                                                @php
                                                    [$initials, $dosenName] = explode('|', $dosenItem, 2);
                                                @endphp
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="avatar-text">{{ strtoupper($initials) }}</div>
                                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">{{ $dosenName }}</span>
                                                </div>
                                            @empty
                                                <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td><span class="text-muted" style="font-size: 0.9rem;">{{ isset($rps->tanggal_review) ? \Carbon\Carbon::parse($rps->tanggal_review)->format('d M Y') : '-' }}</span></td>
                                    <td><span class="badge-revisi">REVISI</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->rps_id) }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                            <i class="fas fa-edit me-2" style="font-size: 0.8rem;"></i> Lihat Catatan
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Tidak ada RPS yang menunggu revisi</p>
                                    </td>
                                </tr>
                                @endforelse
                                <tr class="no-results-message" style="display: none;">
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Tidak ada hasil pencarian</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-2 px-2">
                    <span class="text-muted mb-3 mb-sm-0" style="font-size: 0.875rem;">Menampilkan {{ $rpsRevisi->count() }} dari {{ $rpsRevisi->total() }} entri</span>
                    <nav>
                        {{ $rpsRevisi->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>

            <!-- Tab Disetujui -->
            <div class="tab-pane fade" id="disetujui" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" 
                                style="max-width: 500px;">

                            <i class="fas fa-search text-muted me-2" 
                            style="font-size: 0.9rem;"></i>

                            <input type="text" class="form-control search-input p-0" data-search-tab="disetujui"
                            placeholder="Cari mata kuliah atau dosen...">
                        </div>
                        
                        <button class="btn btn-filter px-4 py-2 rounded-3 d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-muted" 
                                style="font-size: 0.85rem;"></i> Filter
                        </button>
                    </div>
                </div>

                <div class="table-container shadow-sm mb-4">
                    <div class="table-responsive">
                        <table class="table table-rps mb-0">
                            <thead>
                                <tr>
                                    <th width="35%">MATA KULIAH</th>
                                    <th width="20%">DOSEN PENGAMPU</th>
                                    <th width="15%">TANGGAL DISETUJUI</th>
                                    <th width="15%">STATUS</th>
                                    <th width="15%" class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rpsDisetujui as $rps)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $rps->mk_nama }} ({{ $rps->kode }})</div>
                                        <div class="text-muted" style="font-size: 0.8rem;">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            @php
                                                $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                                            @endphp
                                            @forelse($dosensList as $dosenItem)
                                                @php
                                                    [$initials, $dosenName] = explode('|', $dosenItem, 2);
                                                @endphp
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div class="avatar-text">{{ strtoupper($initials) }}</div>
                                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">{{ $dosenName }}</span>
                                                </div>
                                            @empty
                                                <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td><span class="text-muted" style="font-size: 0.9rem;">{{ isset($rps->tanggal_disetujui) ? \Carbon\Carbon::parse($rps->tanggal_disetujui)->format('d M Y') : '-' }}</span></td>
                                    <td><span class="badge-disetujui">DISETUJUI</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->rps_id) }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                            <i class="fas fa-eye me-2" style="font-size: 0.8rem;"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Belum ada RPS yang disetujui</p>
                                    </td>
                                </tr>
                                @endforelse
                                <tr class="no-results-message" style="display: none;">
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Tidak ada hasil pencarian</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-2 px-2">
                    <span class="text-muted mb-3 mb-sm-0" style="font-size: 0.875rem;">Menampilkan {{ $rpsDisetujui->count() }} dari {{ $rpsDisetujui->total() }} entri</span>
                    <nav>
                        {{ $rpsDisetujui->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Tambah Periode -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <form action="{{ route('banksoal.rps.gpm.periode-rps.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Buat Jadwal RPS Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Judul Periode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" required placeholder="Contoh: Pengajuan RPS Genap 2025/2026">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" name="semester" required>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tahun_ajaran" required placeholder="Contoh: 2025/2026">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium text-dark">Tanggal Selesai (Tenggat) <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_selesai" required>
                        </div>
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveAdd" value="1" checked>
                            <label class="form-check-label ms-2" for="isActiveAdd">Otomatis aktifkan jadwal ini</label>
                            <div class="form-text mt-1 text-muted" style="font-size: 0.8rem;">GPM hanya bisa membuka 1 sesi pengajuan dalam satu waktu. Mencentang ini akan membatalkan sesi lain yang masih aktif.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Buat & Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Matikan Sesi -->
    <div class="modal fade" id="modalCloseSession" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0">
                <form action="{{ route('banksoal.rps.gpm.periode-rps.close-session') }}" method="POST">
                    @csrf
                    <div class="modal-body text-center p-4">
                        <div class="text-warning mb-3">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                        </div>
                        <h5 class="mb-2 fw-bold text-dark">Matikan Sesi Pengajuan?</h5>
                        <p class="text-muted mb-4" style="font-size: 0.9rem;">Sesi pengajuan <strong>{{ $activePeriode->judul ?? 'RPS' }}</strong> akan ditutup. Dosen tidak akan bisa lagi mengajukan RPS sampai periode baru diaktifkan.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger w-50">Matikan Sesi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

</x-banksoal::layouts.gpm-master>