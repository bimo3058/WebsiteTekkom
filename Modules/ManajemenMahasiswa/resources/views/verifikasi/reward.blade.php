<x-dynamic-component :component="$layout">
@php
    $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class;
    $canReview = $canReview ?? true;
@endphp

<style>
    /* ── Dashboard Analitik Style ── */
    .filter-section { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; align-items: center; }

    /* ── Stat Cards (status klaim) ── */
    .admin-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 22px; }
    .admin-stat-card { background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; cursor: pointer; transition: all .18s; text-decoration: none !important; position: relative; overflow: hidden; }
    .admin-stat-card:hover { border-color: rgba(11,38,110,0.18); box-shadow: 0 4px 14px rgba(0,0,0,.06); transform: translateY(-1px); }
    .admin-stat-card.active { border-color: #0B266E; box-shadow: 0 0 0 2px rgba(11,38,110,.12); }
    .admin-stat-card .stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .admin-stat-card .stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 1px; }
    .admin-stat-card .stat-lbl { font-size: .78rem; color: #666D80; font-weight: 500; }
    .admin-stat-card.pending .stat-icon { background: #FFFBEB; color: #d97706; }
    .admin-stat-card.pending .stat-num { color: #d97706; }
    .admin-stat-card.approved .stat-icon { background: #ECFDF5; color: #059669; }
    .admin-stat-card.approved .stat-num { color: #059669; }
    .admin-stat-card.rejected .stat-icon { background: #fef2f2; color: #dc2626; }
    .admin-stat-card.rejected .stat-num { color: #dc2626; }
    .filter-chip {
        padding: 7px 16px; border-radius: 8px; border: 1px solid #DFE1E7;
        background: #fff; color: #666D80; font-size: .82rem; font-weight: 600;
        cursor: pointer; transition: all .15s; text-decoration: none !important;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-chip:hover { border-color: #0B266E; color: #0B266E; background: rgba(11,38,110,0.06); }
    .filter-chip.active { background: #0B266E; color: #fff !important; border-color: #0B266E; }
    .tab-badge { font-size: .72rem; font-weight: 700; padding: 2px 8px; border-radius: 50px; background: #FFFBEB; color: #d97706; min-width: 20px; text-align: center; }
    .filter-chip.active .tab-badge { background: #fff; color: #0B266E; }
    .tab-badge.zero { background: #f3f4f6; color: #666D80; }

    .filter-select-custom { padding: 0 14px; border-radius: 8px; border: 1px solid #DFE1E7; background: #fff; color: #374151; font-size: .82rem; font-weight: 600; outline: none; height: 34px; transition: all .2s; }
    .filter-select-custom:focus { border-color: #0B266E; box-shadow: 0 0 0 3px rgba(11,38,110,.1); }
    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666D80; }
    .search-input { background-color: #fff; border: 1px solid #DFE1E7; border-radius: 8px; height: 34px; padding-left: 36px; font-size: .85rem; font-weight: 500; width: 100%; color: #374151; }
    .search-input:focus { background-color: #fff; border-color: #0B266E; box-shadow: 0 0 0 3px rgba(11,38,110,.1); outline: none; }

    .form-card { background: #fff; border-radius: 14px; padding: 22px 24px; border: 1px solid #DFE1E7; margin-bottom: 18px; }

    .verif-table { width: 100%; border-collapse: collapse; }
    .verif-table thead th { font-size: 11px; font-weight: 600; color: #666D80; padding: 11px 12px; text-align: left; background: #FAFAFA; border-bottom: 1px solid #DFE1E7; white-space: nowrap; }
    .verif-table tbody td { padding: 11px 12px; font-size: .87rem; color: #374151; border-bottom: 1px solid #f9fafb; vertical-align: middle; }
    .verif-table tbody tr:last-child td { border-bottom: none; }
    .verif-table tbody tr:hover td { background: #fafafa; }

    .tingkat-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 50px; font-size: .73rem; font-weight: 600; text-transform: uppercase; }
    .tingkat-badge.internasional { background: #FFFBEB; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #ECFDF5; color: #059669; }
    .tingkat-badge.prodi { background: #eef2ff; color: #0B266E; }

    .claim-badge { font-size: .73rem; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-flex; align-items: center; }
    .claim-badge.belum { background: #f3f4f6; color: #666D80; }
    .claim-badge.diajukan { background: #dbeafe; color: #1e40af; }
    .claim-badge.disetujui { background: #ECFDF5; color: #059669; }
    .claim-badge.ditolak { background: #fef2f2; color: #dc2626; }
    .reward-mini { font-size: .72rem; color: #666D80; margin-top: 4px; max-width: 220px; line-height: 1.4; }

    .btn-tinjau { background: rgba(11,38,110,0.06); color: #0B266E; border: 1px solid rgba(11,38,110,0.18); padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-tinjau:hover { background: rgba(11,38,110,0.12); border-color: #3C518B; }
    .btn-batal-reward { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; }
    .btn-batal-reward:hover { background: #fee2e2; border-color: #fca5a5; }
    .btn-detail { background: #f3f4f6; color: #374151; border: 1px solid #DFE1E7; padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-detail:hover { background: #DFE1E7; color: #0D0D12; }
    .detail-info-grid { display: grid; grid-template-columns: 130px 1fr; gap: 8px 12px; font-size: .87rem; }
    .detail-info-grid .dlbl { color: #666D80; font-weight: 500; }
    .detail-info-grid .dval { color: #0D0D12; font-weight: 600; }
    .detail-divider { border: none; border-top: 1px solid #f3f4f6; margin: 14px 0; }
    .detail-status-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .73rem; font-weight: 600; padding: 3px 12px; border-radius: 50px; }
    .detail-status-pill.disetujui { background: #ECFDF5; color: #059669; }
    .detail-status-pill.ditolak { background: #fef2f2; color: #dc2626; }
    .detail-status-pill.diajukan { background: #dbeafe; color: #1e40af; }

    .tinjau-info { font-size: .87rem; color: #374151; background: #fafafa; border: 1px solid #DFE1E7; border-radius: 10px; padding: 12px 14px; line-height: 1.7; }
    .tinjau-info .lbl { color: #666D80; }

    /* MK pilihan mahasiswa (read-only) */
    .mk-by-mhs { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #0B266E; background: rgba(11,38,110,0.06); border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 2px 8px; margin-left: 4px; }
    .mk-readonly { display: flex; flex-wrap: wrap; gap: 6px; background: #fafafa; border: 1px solid #DFE1E7; border-radius: 10px; padding: 10px 12px; min-height: 42px; }
    .mk-tag { display: inline-flex; align-items: center; font-size: .8rem; font-weight: 600; color: #0B266E; background: rgba(11,38,110,0.06); border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 4px 12px; }

    /* Bukti prestasi di modal tinjau */
    .tr-bukti { display: flex; flex-wrap: wrap; gap: 8px; }
    .tr-bukti-item { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 10px; border: 1px solid #DFE1E7; overflow: hidden; background: #fafafa; text-decoration: none; font-size: 24px; transition: border-color .15s; }
    .tr-bukti-item img { width: 100%; height: 100%; object-fit: cover; }
    .tr-bukti-item:hover { border-color: #0B266E; }
    .kuota-pill { display: inline-block; margin-top: 10px; font-size: .8rem; font-weight: 600; padding: 5px 12px; border-radius: 50px; background: rgba(11,38,110,0.06); color: #0B266E; }
    .kuota-pill.penuh { background: #fef2f2; color: #dc2626; }

    .empty-state { text-align: center; padding: 50px 20px; color: #666D80; }
    .empty-state-icon { display: flex; justify-content: center; margin-bottom: 12px; color: #C1C7CF; }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; color: #0D0D12; }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 14px 22px; }

    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: .82rem; font-weight: 600; color: #666D80; text-decoration: none; margin-bottom: 10px; transition: color .15s; }
    .back-link:hover { color: #0B266E; }

</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div style="margin-bottom:24px;">
    <a href="{{ route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi']) }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali ke Verifikasi Prestasi
    </a>
    <h4 style="font-size:1.45rem; font-weight:700; color:#0D0D12; margin-bottom:2px; letter-spacing:-.02em;">Klaim Reward Prestasi</h4>
    @if($canReview)
        <p style="font-size:.82rem; color:#666D80; margin:0;">Tinjau & setujui pengajuan reward prestasi mahasiswa (konversi nilai mata kuliah, SK FT 774). Keputusan final ada di Bidang Akademik Fakultas.</p>
    @else
        <p style="font-size:.82rem; color:#666D80; margin:0;">Pantau pengajuan reward prestasi mahasiswa (konversi nilai mata kuliah, SK FT 774). Keputusan final ada di Bidang Akademik Fakultas.</p>
        <span style="display:inline-flex; align-items:center; gap:6px; margin-top:10px; background:#eef2ff; color:#0B266E; font-size:.72rem; font-weight:700; padding:4px 12px; border-radius:50px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Mode Pemantauan — hanya melihat (tanpa tinjau/setujui/tolak)
        </span>
    @endif
</div>


<!-- Stat Cards (status klaim) -->
<div class="admin-stats">
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'menunggu'])) }}"
       class="admin-stat-card pending {{ $reward === 'menunggu' ? 'active' : '' }}">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['menunggu'] }}</div>
            <div class="stat-lbl">Menunggu</div>
        </div>
    </a>
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'disetujui'])) }}"
       class="admin-stat-card approved {{ $reward === 'disetujui' ? 'active' : '' }}">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['disetujui'] }}</div>
            <div class="stat-lbl">Disetujui</div>
        </div>
    </a>
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'ditolak'])) }}"
       class="admin-stat-card rejected {{ $reward === 'ditolak' ? 'active' : '' }}">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['ditolak'] }}</div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </a>
</div>

<!-- Filter Area -->
<form method="GET" action="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}" id="filterForm">
    <input type="hidden" name="reward" value="{{ $reward }}">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" name="search" class="form-control search-input w-100" placeholder="Cari nama / NIM mahasiswa..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-3">
            <select name="angkatan" class="form-select border-1 filter-select-custom" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Angkatan</option>
                @foreach($angkatanList as $a)
                    <option value="{{ $a }}" {{ $angkatan == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

@if($rewardData->count() > 0)
    <div class="form-card p-0" style="overflow-x: auto;">
        <table class="verif-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Nama Prestasi</th>
                    <th>Tingkat</th>
                    <th>Status</th>
                    <th style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rewardData as $i => $p)
                    @php
                        $grup = $p->rewardKuotaGrup();
                        $kuotaTerpakai = $kuotaMap[$p->kemahasiswaan_id][$grup] ?? 0;
                        $kuotaMaks = $P::KUOTA_MAKS[$grup];
                        // Payload detail read-only — dipakai tombol "Detail" (admin & pengawas).
                        $detailPayload = [
                            "nama"           => $p->nama_prestasi,
                            "mahasiswa"      => $p->kemahasiswaan->nama ?? "-",
                            "nim"            => $p->kemahasiswaan->nim ?? "-",
                            "penyelenggara"  => $p->reward_penyelenggara_label,
                            "capaian"        => $p->reward_capaian_label,
                            "invention"      => (bool) $p->reward_is_invention,
                            "jml_mk_max"     => $p->reward_jml_mk_max,
                            "sks_max"        => $p->reward_sks_max,
                            "mk_diajukan"    => $p->reward_mk_diajukan ?? [],
                            "mk_disetujui"   => $p->reward_mk_disetujui,
                            "kuota_terpakai" => $kuotaTerpakai,
                            "kuota_maks"     => $kuotaMaks,
                            "grup"           => $grup,
                            "status"         => $p->reward_status,
                            "note"           => $p->reward_note,
                            "reviewer"       => $p->reviewedBy->name ?? null,
                            "reviewed_at"    => $p->reward_reviewed_at ? $p->reward_reviewed_at->translatedFormat('d M Y') : null,
                            "bukti"          => $p->buktiFiles->map(fn ($b) => [
                                "url"      => $b->public_url,
                                "nama"     => $b->nama_file,
                                "is_image" => $b->isImage(),
                            ])->values()->all(),
                        ];
                    @endphp
                    <tr>
                        <td style="color: #666D80;">{{ ($rewardData->currentPage() - 1) * $rewardData->perPage() + $i + 1 }}</td>
                        <td style="font-weight: 600;">{{ $p->kemahasiswaan->nama ?? '-' }}</td>
                        <td style="font-family: monospace; font-size: 13px; color: #0B266E;">{{ $p->kemahasiswaan->nim ?? '-' }}</td>
                        <td style="font-weight: 600;">{{ $p->nama_prestasi }}</td>
                        <td><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                        <td>
                            @if($p->reward_status === $P::CLAIM_DISETUJUI)
                                <span class="claim-badge disetujui">Disetujui</span>
                            @elseif($p->reward_status === $P::CLAIM_DIAJUKAN)
                                <span class="claim-badge diajukan">Menunggu</span>
                            @elseif($p->reward_status === $P::CLAIM_DITOLAK)
                                <span class="claim-badge ditolak">Ditolak</span>
                            @endif
                        </td>
                        <td>
                            @if($canReview && $p->reward_status === $P::CLAIM_DIAJUKAN)
                                <button type="button" class="btn-tinjau" onclick="openTinjauReward(@js([
                                    "id"             => $p->id,
                                    "nama"           => $p->nama_prestasi,
                                    "mahasiswa"      => $p->kemahasiswaan->nama ?? "-",
                                    "nim"            => $p->kemahasiswaan->nim ?? "-",
                                    "penyelenggara"  => $p->reward_penyelenggara_label,
                                    "capaian"        => $p->reward_capaian_label,
                                    "invention"      => (bool) $p->reward_is_invention,
                                    "jml_mk_max"     => $p->reward_jml_mk_max,
                                    "sks_max"        => $p->reward_sks_max,
                                    "mk_diajukan"    => $p->reward_mk_diajukan ?? [],
                                    "kuota_terpakai" => $kuotaTerpakai,
                                    "kuota_maks"     => $kuotaMaks,
                                    "grup"           => $grup,
                                    "bukti"          => $p->buktiFiles->map(fn ($b) => [
                                        "url"      => $b->public_url,
                                        "nama"     => $b->nama_file,
                                        "is_image" => $b->isImage(),
                                    ])->values()->all(),
                                ]))">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px; margin-right: 2px;"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                                    Tinjau
                                </button>
                            @else
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="btn-detail" onclick="openDetailReward(@js($detailPayload))">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px; margin-right: 2px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Detail
                                    </button>
                                    @if($canReview && $p->reward_status === $P::CLAIM_DISETUJUI)
                                        <button type="button" class="btn-batal-reward"
                                                onclick="openBatalReward(@js($p->id), @js($p->nama_prestasi), @js($p->kemahasiswaan->nama ?? '-'))">Batalkan</button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $rewardData->appends(request()->query())->links() }}</div>
@else
    <div class="empty-state">
        <div class="empty-state-icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"></rect><path d="M12 8v13M5 12v9h14v-9"></path><path d="M12 8C12 8 11 3 8 3a2.5 2.5 0 0 0 0 5h4zM12 8s1-5 4-5a2.5 2.5 0 0 1 0 5h-4z"></path></svg></div>
        <h6 style="font-weight: 600; color: #666D80; margin-bottom: 4px;">Tidak ada klaim reward</h6>
        <p style="font-size: 13px; color: #666D80; margin: 0;">Belum ada klaim reward yang sesuai filter</p>
    </div>
@endif

<!-- Tinjau Reward Modal -->
<div class="modal fade" id="tinjauRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="tinjauRewardForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        Tinjau Pengajuan Reward
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="tinjau-info" id="trInfo"></div>
                    <div id="trKuotaWarn"></div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">
                            Mata Kuliah yang Dinaikkan Nilainya
                            <span class="mk-by-mhs">pilihan mahasiswa</span>
                        </label>
                        <div id="trMkView" class="mk-readonly"></div>
                        <small style="font-size: 11px; color: #666D80;">Diisi oleh mahasiswa &amp; tidak dapat diubah admin. Pastikan MK bernilai min. C (SK 774 poin 3 &amp; 4). Keputusan final tetap di Bidang Akademik Fakultas.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Bukti Prestasi</label>
                        <div id="trBukti" class="tr-bukti"></div>
                        <small style="font-size: 11px; color: #666D80;">Klik untuk membuka bukti di tab baru.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Catatan <span style="font-weight: 400; color: #666D80;">(wajib untuk menolak)</span></label>
                        <textarea name="reward_note" id="trNote" class="form-control" rows="2" maxlength="300"
                                  placeholder="Catatan persetujuan / alasan penolakan"
                                  style="border-radius: 10px; font-size: 14px;"></textarea>
                    </div>
                    <div id="trError" style="display: none; font-size: 12px; color: #dc2626; margin-top: 6px;"></div>
                </div>
                <div class="modal-footer" style="justify-content: space-between;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                    <div class="d-flex gap-2">
                        <button type="button" id="trTolakBtn" style="padding: 8px 18px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">Tolak</button>
                        <button type="button" id="trSetujuiBtn" style="padding: 8px 18px; border-radius: 10px; border: none; background: #16a34a; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">Setujui</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Batalkan Persetujuan Reward Modal -->
<div class="modal fade" id="batalRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="batalRewardForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0D0D12;">Batalkan Persetujuan Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 13px; color: #374151;">Reward untuk <strong id="brNama"></strong> (<span id="brMhs"></span>) akan diubah menjadi <b style="color:#dc2626;">Ditolak</b> dan kuota mahasiswa dikembalikan. Gunakan ini bila Fakultas menolak peningkatan nilai.</p>
                    <div class="mb-1">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Alasan pembatalan <span style="color:#dc2626;">*</span></label>
                        <textarea name="reward_note" id="brNote" class="form-control" rows="3" maxlength="300"
                                  placeholder="Contoh: Ditolak Bidang Akademik Fakultas — nilai mata kuliah tidak dapat dinaikkan."
                                  style="border-radius: 10px; font-size: 14px;"></textarea>
                    </div>
                    <div id="brError" style="display: none; font-size: 12px; color: #dc2626; margin-top: 6px;"></div>
                </div>
                <div class="modal-footer" style="justify-content: space-between;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                    <button type="button" id="brConfirmBtn" style="padding: 8px 18px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">Ya, Batalkan Persetujuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Reward Modal (read-only) -->
<div class="modal fade" id="detailRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #374151;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    Detail Klaim Reward
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="drStatusArea" style="margin-bottom: 14px;"></div>
                <div class="tinjau-info" id="drInfo"></div>
                <div id="drKuotaWarn" style="margin-top: 10px;"></div>
                <hr class="detail-divider">
                <div class="mb-3">
                    <label class="form-label fw-bold mb-1" style="font-size: 13px;">Mata Kuliah yang Diajukan</label>
                    <div id="drMkView" class="mk-readonly"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold mb-1" style="font-size: 13px;">Bukti Prestasi</label>
                    <div id="drBukti" class="tr-bukti"></div>
                </div>
                <div id="drReviewArea"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
let trData = null;
function openTinjauReward(data) {
    trData = data;
    document.getElementById('trNote').value = '';
    document.getElementById('trError').style.display = 'none';

    const inv = data.invention ? ' (invention/expo/fair)' : '';
    document.getElementById('trInfo').innerHTML =
        '<div><span class="lbl">Mahasiswa:</span> <b>' + data.mahasiswa + '</b></div>' +
        '<div><span class="lbl">Prestasi:</span> ' + data.nama + '</div>' +
        '<div><span class="lbl">Penyelenggara:</span> ' + (data.penyelenggara || '-') + '</div>' +
        '<div><span class="lbl">Capaian:</span> ' + (data.capaian || '-') + inv + '</div>' +
        '<div><span class="lbl">Maks. konversi:</span> ' + data.jml_mk_max + ' mata kuliah (setara ' + data.sks_max + ' SKS)</div>';

    // MK pilihan mahasiswa — tampil read-only sebagai chip
    const mkView = document.getElementById('trMkView');
    mkView.innerHTML = '';
    const mks = data.mk_diajukan || [];
    if (mks.length) {
        mks.forEach(function(nama) {
            const tag = document.createElement('span');
            tag.className = 'mk-tag';
            tag.textContent = nama;
            mkView.appendChild(tag);
        });
    } else {
        mkView.innerHTML = '<span style="font-size:12px; color:#666D80;">Mahasiswa belum memilih mata kuliah.</span>';
    }

    // Bukti prestasi — thumbnail gambar / ikon dokumen, klik buka tab baru
    const buktiEl = document.getElementById('trBukti');
    buktiEl.innerHTML = '';
    const bukti = data.bukti || [];
    if (bukti.length) {
        bukti.forEach(function(b) {
            const a = document.createElement('a');
            a.href = b.url;
            a.target = '_blank';
            a.rel = 'noopener';
            a.title = b.nama || 'Bukti';
            a.className = 'tr-bukti-item';
            if (b.is_image) {
                const img = document.createElement('img');
                img.src = b.url;
                img.alt = b.nama || 'Bukti';
                a.appendChild(img);
            } else {
                a.classList.add('doc');
                a.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
            }
            buktiEl.appendChild(a);
        });
    } else {
        buktiEl.innerHTML = '<span style="font-size:12px; color:#666D80;">Tidak ada bukti terlampir.</span>';
    }

    const penuh = data.kuota_terpakai >= data.kuota_maks;
    document.getElementById('trKuotaWarn').innerHTML =
        '<span class="kuota-pill' + (penuh ? ' penuh' : '') + '">Kuota ' + data.grup + ': ' + data.kuota_terpakai + '/' + data.kuota_maks + (penuh ? ' — PENUH, tidak bisa disetujui' : '') + '</span>';

    new bootstrap.Modal(document.getElementById('tinjauRewardModal')).show();
}

(function() {
    const baseUrl = '{{ url("manajemen-mahasiswa/verifikasi") }}';
    const form = document.getElementById('tinjauRewardForm');
    if (!form) return;
    const errEl = document.getElementById('trError');

    document.getElementById('trSetujuiBtn').addEventListener('click', function() {
        if (!trData) return;
        if (!trData.mk_diajukan || !trData.mk_diajukan.length) {
            errEl.textContent = 'Mahasiswa belum memilih mata kuliah, tidak dapat disetujui.';
            errEl.style.display = 'block';
            return;
        }
        form.action = baseUrl + '/prestasi/' + trData.id + '/reward/setujui';
        form.submit();
    });

    document.getElementById('trTolakBtn').addEventListener('click', function() {
        if (!trData) return;
        const note = document.getElementById('trNote').value.trim();
        if (!note) { errEl.textContent = 'Catatan/alasan wajib diisi untuk menolak.'; errEl.style.display = 'block'; return; }
        form.action = baseUrl + '/prestasi/' + trData.id + '/reward/tolak';
        form.submit();
    });
})();

// Batalkan persetujuan reward (yg sudah disetujui) -> jadi ditolak + alasan
let brId = null;
function openBatalReward(id, nama, mahasiswa) {
    brId = id;
    document.getElementById('brNama').textContent = nama;
    document.getElementById('brMhs').textContent = mahasiswa;
    document.getElementById('brNote').value = '';
    document.getElementById('brError').style.display = 'none';
    new bootstrap.Modal(document.getElementById('batalRewardModal')).show();
}
(function() {
    const baseUrl = '{{ url("manajemen-mahasiswa/verifikasi") }}';
    const form = document.getElementById('batalRewardForm');
    if (!form) return;
    const errEl = document.getElementById('brError');
    document.getElementById('brConfirmBtn').addEventListener('click', function() {
        if (!brId) return;
        const note = document.getElementById('brNote').value.trim();
        if (!note) { errEl.textContent = 'Alasan pembatalan wajib diisi.'; errEl.style.display = 'block'; return; }
        form.action = baseUrl + '/prestasi/' + brId + '/reward/batalkan-persetujuan';
        form.submit();
    });
})();

// Detail Reward Modal (read-only, untuk reward yang sudah disetujui/ditolak)
function openDetailReward(data) {
    const inv = data.invention ? ' (invention/expo/fair)' : '';
    const statusMap = { 'disetujui': ['disetujui', 'Disetujui'], 'ditolak': ['ditolak', 'Ditolak'], 'diajukan': ['diajukan', 'Menunggu'] };
    const st = statusMap[data.status] || ['', data.status];

    // Status area
    document.getElementById('drStatusArea').innerHTML =
        '<span class="detail-status-pill ' + st[0] + '">' + st[1] + '</span>' +
        (data.reviewer ? ' <span style="font-size:12px; color:#666D80;">oleh ' + data.reviewer + '</span>' : '') +
        (data.reviewed_at ? ' <span style="font-size:12px; color:#666D80;">• ' + data.reviewed_at + '</span>' : '');

    // Info grid
    document.getElementById('drInfo').innerHTML =
        '<div class="detail-info-grid">' +
        '<span class="dlbl">Mahasiswa</span><span class="dval">' + data.mahasiswa + ' (' + data.nim + ')</span>' +
        '<span class="dlbl">Prestasi</span><span class="dval">' + data.nama + '</span>' +
        '<span class="dlbl">Penyelenggara</span><span class="dval">' + (data.penyelenggara || '-') + '</span>' +
        '<span class="dlbl">Capaian</span><span class="dval">' + (data.capaian || '-') + inv + '</span>' +
        '<span class="dlbl">Maks. Konversi</span><span class="dval">' + data.jml_mk_max + ' mata kuliah (setara ' + data.sks_max + ' SKS)</span>' +
        '</div>';

    // Kuota
    var penuh = data.kuota_terpakai >= data.kuota_maks;
    document.getElementById('drKuotaWarn').innerHTML =
        '<span class="kuota-pill' + (penuh ? ' penuh' : '') + '">Kuota ' + data.grup + ': ' + data.kuota_terpakai + '/' + data.kuota_maks + '</span>';

    // MK
    var mkView = document.getElementById('drMkView');
    mkView.innerHTML = '';
    var mks = data.mk_diajukan || [];
    if (mks.length) {
        mks.forEach(function(nama) {
            var tag = document.createElement('span');
            tag.className = 'mk-tag';
            tag.textContent = nama;
            mkView.appendChild(tag);
        });
    } else {
        mkView.innerHTML = '<span style="font-size:12px; color:#666D80;">Tidak ada MK diajukan.</span>';
    }

    // Bukti
    var buktiEl = document.getElementById('drBukti');
    buktiEl.innerHTML = '';
    var bukti = data.bukti || [];
    if (bukti.length) {
        bukti.forEach(function(b) {
            var a = document.createElement('a');
            a.href = b.url; a.target = '_blank'; a.rel = 'noopener';
            a.title = b.nama || 'Bukti'; a.className = 'tr-bukti-item';
            if (b.is_image) {
                var img = document.createElement('img');
                img.src = b.url; img.alt = b.nama || 'Bukti';
                a.appendChild(img);
            } else {
                a.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
            }
            buktiEl.appendChild(a);
        });
    } else {
        buktiEl.innerHTML = '<span style="font-size:12px; color:#666D80;">Tidak ada bukti terlampir.</span>';
    }

    // Review area (catatan, MK disetujui)
    var reviewHtml = '';
    if (data.mk_disetujui) {
        reviewHtml += '<div style="margin-bottom:10px;"><span class="dlbl" style="font-size:13px; font-weight:600;">MK Disetujui:</span> <span style="font-size:13px; font-weight:600; color:#059669;">' + data.mk_disetujui + '</span></div>';
    }
    if (data.note) {
        var noteColor = data.status === 'ditolak' ? '#dc2626' : '#059669';
        reviewHtml += '<div style="font-size:13px; padding:10px 14px; background:#f8fafc; border:1px solid #DFE1E7; border-radius:10px;"><span style="color:#666D80;">Catatan:</span> <span style="color:' + noteColor + '; font-weight:600;">' + data.note + '</span></div>';
    }
    document.getElementById('drReviewArea').innerHTML = reviewHtml;

    new bootstrap.Modal(document.getElementById('detailRewardModal')).show();
}
</script>
</x-dynamic-component>
