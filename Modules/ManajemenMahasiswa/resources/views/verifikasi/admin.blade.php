<x-dynamic-component :component="$layout">

<style>
    /* ── Filter Bar & Search (Manajemen Kegiatan Style) ── */
    .filter-section {
        display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; align-items: center;
    }
    .filter-chip {
        padding: 7px 18px; border-radius: 20px; border: 1.5px solid #e5e7eb;
        background: #ffffff; color: #374151; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; text-decoration: none !important;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-chip:hover { border-color: #818cf8; color: #4f46e5; background: #eef2ff; }
    .filter-chip.active { background: #4f46e5; color: #ffffff !important; border-color: #4f46e5; }
    
    .tab-badge {
        font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
        background: #fef3c7; color: #d97706; min-width: 20px; text-align: center;
    }
    .filter-chip.active .tab-badge { background: #fff; color: #4f46e5; }
    .tab-badge.zero { background: #f3f4f6; color: #9ca3af; }

    .filter-select-custom {
        padding: 7px 16px; border-radius: 20px; border: 1.5px solid #e5e7eb;
        background: #ffffff; color: #374151; font-size: 13px; font-weight: 600;
        outline: none; transition: all 0.2s; height: 38px;
    }
    .filter-select-custom:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }

    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 14px;
    }
    .search-input {
        background-color: #f3f4f6; border: none; border-radius: 8px; height: 42px;
        padding-left: 36px; font-size: 13px; font-weight: 500; width: 100%;
    }
    .search-input:focus {
        background-color: #ffffff; box-shadow: 0 0 0 2px #e0e7ff; outline: none;
    }

    /* ── Table & Cards ── */
    .form-card {
        background: #ffffff; border-radius: 12px; padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px; border: 1px solid #f3f4f6;
    }
    
    .verif-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .verif-table thead th {
        background: #f8fafc; padding: 10px 14px; font-size: 12px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb; border-top: 1px solid #e5e7eb;
    }
    .verif-table thead th:first-child { border-top-left-radius: 8px; border-left: 1px solid #e5e7eb; }
    .verif-table thead th:last-child { border-top-right-radius: 8px; border-right: 1px solid #e5e7eb; }
    
    .verif-table tbody td {
        padding: 12px 14px; font-size: 14px; color: #374151;
        border-bottom: 1px solid #e5e7eb; vertical-align: middle;
    }
    .verif-table tbody td:first-child { border-left: 1px solid #e5e7eb; }
    .verif-table tbody td:last-child { border-right: 1px solid #e5e7eb; }
    .verif-table tbody tr:hover td { background: #f8fafc; }
    .verif-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
    .verif-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }

    /* ── Status & Buttons ── */
    .status-verif { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; }
    .status-verif.pending { background: #fef3c7; color: #d97706; }
    .status-verif.approved { background: #dcfce7; color: #166534; }
    .status-verif.rejected { background: #fef2f2; color: #dc2626; }

    .btn-approve {
        background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 5px 12px;
        border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-approve:hover { background: #bbf7d0; }
    .btn-reject {
        background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 12px;
        border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-reject:hover { background: #fee2e2; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
    .empty-state h5 { color: #6b7280; font-weight: 600; margin-bottom: 4px; }
    .empty-state p { font-size: 14px; color: #9ca3af; }

    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; }

    .tingkat-badge {
        font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px; text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #fef3c7; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #dcfce7; color: #166534; }
    .tingkat-badge.prodi { background: #eef2ff; color: #4f46e5; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Verifikasi Data Mahasiswa</h3>
        <p class="text-dark fw-bold mb-0" style="font-size: 14px;">Review dan verifikasi data riwayat kegiatan serta prestasi lomba</p>
    </div>
</div>

<!-- Tabs & Filter Area -->
<form method="GET" action="{{ route('manajemenmahasiswa.verifikasi.index') }}" id="filterForm">
    <input type="hidden" name="tab" value="{{ $tab }}">
    
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <!-- Search -->
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" name="search" class="form-control search-input w-100"
                   placeholder="Cari nama / NIM mahasiswa..." value="{{ request('search') }}">
        </div>

        <!-- Filter Dropdowns -->
        <div class="d-flex gap-3">
            <select name="status" class="form-select border-1 filter-select-custom" onchange="document.getElementById('filterForm').submit()">
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status</option>
            </select>
            <select name="angkatan" class="form-select border-1 filter-select-custom" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Angkatan</option>
                @foreach($angkatanList as $a)
                    <option value="{{ $a }}" {{ $angkatan == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabs (Filter Chips) -->
    <div class="filter-section">
        <a href="{{ route('manajemenmahasiswa.verifikasi.index', array_merge(request()->only(['status','search','angkatan']), ['tab' => 'riwayat'])) }}"
           class="filter-chip {{ $tab === 'riwayat' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Riwayat Kegiatan
            <span class="tab-badge {{ $pendingRiwayat == 0 ? 'zero' : '' }}">{{ $pendingRiwayat }}</span>
        </a>
        <a href="{{ route('manajemenmahasiswa.verifikasi.index', array_merge(request()->only(['status','search','angkatan']), ['tab' => 'prestasi'])) }}"
           class="filter-chip {{ $tab === 'prestasi' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            Prestasi Lomba
            <span class="tab-badge {{ $pendingPrestasi == 0 ? 'zero' : '' }}">{{ $pendingPrestasi }}</span>
        </a>
    </div>
</form>

<!-- Tab Content: Riwayat Kegiatan -->
@if($tab === 'riwayat')
    @if($riwayatData->count() > 0)
        <div class="form-card p-0" style="overflow-x: auto;">
            <table class="verif-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatData as $i => $rw)
                        <tr>
                            <td style="color: #9ca3af;">{{ ($riwayatData->currentPage() - 1) * $riwayatData->perPage() + $i + 1 }}</td>
                            <td style="font-weight: 600;">{{ $rw->student->user->name ?? '-' }}</td>
                            <td style="font-family: monospace; font-size: 13px; color: #4f46e5;">{{ $rw->student->student_number ?? '-' }}</td>
                            <td>
                                <span style="font-weight: 600;">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui' }}</span>
                            </td>
                            <td>{{ $rw->peran_manual ?? ucfirst($rw->peran ?? '') }}</td>
                            <td style="font-size: 13px; color: #6b7280;">
                                @if($rw->tanggal_kegiatan)
                                    {{ $rw->tanggal_kegiatan->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($rw->buktiFiles && $rw->buktiFiles->count() > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach($rw->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb; cursor: pointer; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background: #eef2ff; border: 1px solid #e5e7eb; font-size: 16px; cursor: pointer;">📄</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-verif {{ $rw->verification_status }}">
                                    @if($rw->verification_status === 'pending') ● Pending
                                    @elseif($rw->verification_status === 'approved') ✓ Disetujui
                                    @else ✗ Ditolak
                                    @endif
                                </span>
                                @if($rw->verification_status === 'rejected' && $rw->verification_note)
                                    <div style="font-size: 11px; color: #dc2626; margin-top: 4px; max-width: 180px;" title="{{ $rw->verification_note }}">
                                        "{{ Str::limit($rw->verification_note, 40) }}"
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($rw->verification_status === 'pending')
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.riwayat.approve', $rw->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-approve" title="Setujui">✓</button>
                                        </form>
                                        <button type="button" class="btn-reject" title="Tolak"
                                                onclick="openRejectModal('riwayat', {{ $rw->id }}, '{{ addslashes($rw->nama_kegiatan_manual ?? '') }}')">✗</button>
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">
                                        @if($rw->verifiedBy) {{ $rw->verifiedBy->name }} @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $riwayatData->appends(request()->query())->links() }}</div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h6 style="font-weight: 600; color: #6b7280; margin-bottom: 4px;">Tidak ada data riwayat kegiatan</h6>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">Belum ada pengajuan riwayat kegiatan yang sesuai filter</p>
        </div>
    @endif
@endif

<!-- Tab Content: Prestasi Lomba -->
@if($tab === 'prestasi')
    @if($prestasiData->count() > 0)
        <div class="form-card p-0" style="overflow-x: auto;">
            <table class="verif-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Nama Prestasi</th>
                        <th>Tingkat</th>
                        <th>Tahun</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiData as $i => $p)
                        <tr>
                            <td style="color: #9ca3af;">{{ ($prestasiData->currentPage() - 1) * $prestasiData->perPage() + $i + 1 }}</td>
                            <td style="font-weight: 600;">{{ $p->kemahasiswaan->nama ?? '-' }}</td>
                            <td style="font-family: monospace; font-size: 13px; color: #4f46e5;">{{ $p->kemahasiswaan->nim ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $p->nama_prestasi }}</td>
                            <td><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                            <td>{{ $p->tahun }}</td>
                            <td>
                                @if($p->buktiFiles && $p->buktiFiles->count() > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach($p->buktiFiles as $bukti)
                                            <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                                @if($bukti->isImage())
                                                    <img src="{{ $bukti->public_url }}" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb; cursor: pointer; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                @else
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background: #eef2ff; border: 1px solid #e5e7eb; font-size: 16px; cursor: pointer;">📄</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-verif {{ $p->verification_status }}">
                                    @if($p->verification_status === 'pending') ● Pending
                                    @elseif($p->verification_status === 'approved') ✓ Disetujui
                                    @else ✗ Ditolak
                                    @endif
                                </span>
                                @if($p->verification_status === 'rejected' && $p->verification_note)
                                    <div style="font-size: 11px; color: #dc2626; margin-top: 4px; max-width: 180px;" title="{{ $p->verification_note }}">
                                        "{{ Str::limit($p->verification_note, 40) }}"
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($p->verification_status === 'pending')
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.prestasi.approve', $p->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-approve" title="Setujui">✓</button>
                                        </form>
                                        <button type="button" class="btn-reject" title="Tolak"
                                                onclick="openRejectModal('prestasi', {{ $p->id }}, '{{ addslashes($p->nama_prestasi) }}')">✗</button>
                                    </div>
                                @else
                                    <span style="font-size: 11px; color: #9ca3af;">
                                        @if($p->verifiedBy) {{ $p->verifiedBy->name }} @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $prestasiData->appends(request()->query())->links() }}</div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🏆</div>
            <h6 style="font-weight: 600; color: #6b7280; margin-bottom: 4px;">Tidak ada data prestasi</h6>
            <p style="font-size: 13px; color: #9ca3af; margin: 0;">Belum ada pengajuan prestasi yang sesuai filter</p>
        </div>
    @endif
@endif

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #dc2626;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Tolak Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px;">
                        Anda akan menolak: <strong id="rejectItemName" style="color: #1f2937;"></strong>
                    </p>
                    <label class="form-label fw-bold" style="font-size: 13px;">Alasan Penolakan <span style="color: #dc2626;">*</span></label>
                    <textarea name="verification_note" class="form-control" rows="3" required
                              placeholder="Contoh: Data kurang lengkap, bukti tidak valid, dll."
                              style="border-radius: 10px; font-size: 14px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" style="padding: 8px 20px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(type, id, itemName) {
    const baseUrl = '{{ url("manajemen-mahasiswa/verifikasi") }}';
    document.getElementById('rejectForm').action = `${baseUrl}/${type}/${id}/reject`;
    document.getElementById('rejectItemName').textContent = itemName;
    document.getElementById('rejectForm').querySelector('textarea').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
