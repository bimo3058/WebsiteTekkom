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
    /* Sama seperti Verifikasi Prestasi: kartu hanya menampilkan angka, filternya
       ada di dropdown status pada baris filter di bawahnya. */
    .admin-stat-card { background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; position: relative; overflow: hidden; }
    .admin-stat-card .stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .admin-stat-card .stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 1px; }
    .admin-stat-card .stat-lbl { font-size: .78rem; color: #666D80; font-weight: 500; }
    .admin-stat-card.pending .stat-icon { background: #FFFBEB; color: #d97706; }
    .admin-stat-card.pending .stat-num { color: #d97706; }
    .admin-stat-card.approved .stat-icon { background: #ECFDF5; color: #059669; }
    .admin-stat-card.approved .stat-num { color: #059669; }
    .admin-stat-card.rejected .stat-icon { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }
    .admin-stat-card.rejected .stat-num { color: var(--c-error, #dc2626); }
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
    .claim-badge.ditolak { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }
    .reward-mini { font-size: .72rem; color: #666D80; margin-top: 4px; max-width: 220px; line-height: 1.4; }

    .btn-batal-reward { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 14px; border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s; }
    .btn-batal-reward:hover { background: #fee2e2; border-color: #fca5a5; }
    .detail-status-pill { display: inline-flex; align-items: center; gap: 4px; font-size: .73rem; font-weight: 600; padding: 3px 12px; border-radius: 50px; }
    .detail-status-pill.disetujui { background: #ECFDF5; color: #059669; }
    .detail-status-pill.ditolak { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }
    .detail-status-pill.diajukan { background: #dbeafe; color: #1e40af; }

    /* MK pilihan mahasiswa (read-only) */
    .mk-by-mhs { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #0B266E; background: rgba(11,38,110,0.06); border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 2px 8px; margin-left: 4px; }
    .mk-readonly { display: flex; flex-wrap: wrap; gap: 6px; background: #fafafa; border: 1px solid #DFE1E7; border-radius: 10px; padding: 10px 12px; min-height: 42px; }
    .mk-tag { display: inline-flex; align-items: center; font-size: .8rem; font-weight: 600; color: #0B266E; background: rgba(11,38,110,0.06); border: 1px solid rgba(11,38,110,0.18); border-radius: 50px; padding: 4px 12px; }

    .empty-state { text-align: center; padding: 50px 20px; color: #666D80; }
    .empty-state-icon { display: flex; justify-content: center; margin-bottom: 12px; color: #C1C7CF; }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; color: #0D0D12; }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 14px 22px; }

    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: .82rem; font-weight: 600; color: #666D80; text-decoration: none; margin-bottom: 10px; transition: color .15s; }
    .back-link:hover { color: #0B266E; }

    /* Kolom data di sini memuat kuota, mata kuliah, dan penjelasan SK,
       jadi butuh lebih lega daripada halaman Verifikasi Prestasi */
    #tinjauRewardModal { --tp-lebar-data: 420px; }
</style>

@include('manajemenmahasiswa::verifikasi.partials.tinjau-modal-styles')

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


<!-- Stat Cards (status klaim) — ringkasan angka saja, bukan tombol filter -->
<div class="admin-stats">
    <div class="admin-stat-card pending">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['menunggu'] }}</div>
            <div class="stat-lbl">Menunggu</div>
        </div>
    </div>
    <div class="admin-stat-card approved">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['disetujui'] }}</div>
            <div class="stat-lbl">Disetujui</div>
        </div>
    </div>
    <div class="admin-stat-card rejected">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['ditolak'] }}</div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </div>
</div>

<!-- Filter Area -->
<form method="GET" action="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" name="search" class="form-control search-input w-100" placeholder="Cari nama, NIM, prestasi, tingkat..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-3">
            <!-- Status Klaim — pengganti kartu statistik yang dulu bisa diklik -->
            <select name="reward" class="form-select border-1 filter-select-custom" onchange="document.getElementById('filterForm').submit()">
                <option value="semua" {{ $reward === 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="menunggu" {{ $reward === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ $reward === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ $reward === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
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
                        // Daftar klaim yang memakan kuota grup ini — jumlahnya dihitung
                        // dari daftar yang sama, jadi angka & rinciannya tidak bisa beda.
                        $kuotaDipakai = $kuotaMap[$p->kemahasiswaan_id][$grup] ?? [];
                        $kuotaTerpakai = count($kuotaDipakai);
                        $kuotaMaks = $P::KUOTA_MAKS[$grup];
                        // Satu payload untuk semua baris. Klaim yang masih menunggu membuka
                        // modal dengan panel keputusan; yang sudah diputus membuka modal yang
                        // sama dalam mode baca-saja — jadi tidak ada dua tampilan berbeda.
                        $rewardPayload = [
                            "id"             => $p->id,
                            "pending"        => $canReview && $p->reward_status === $P::CLAIM_DIAJUKAN,
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
                            "kuota_dipakai"  => $kuotaDipakai,
                            "grup"           => $grup,
                            // Dasar aturan yang dicap saat klaim diajukan
                            "sk_ref"         => $p->reward_sk_ref,
                            "sk_lawas"       => $p->rewardSkSudahDiganti(),
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
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Semua baris memakai tombol & modal yang sama; klaim yang sudah
                                     diputus tinggal membuka modal itu tanpa panel keputusan. --}}
                                <button type="button" class="btn-tinjau" onclick="openTinjauReward(@js($rewardPayload))">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Tinjau
                                </button>
                                @if($canReview && $p->reward_status === $P::CLAIM_DISETUJUI)
                                    <button type="button" class="btn-batal-reward"
                                            onclick="openBatalReward(@js($p->id), @js($p->nama_prestasi), @js($p->kemahasiswaan->nama ?? '-'))">Batalkan</button>
                                @endif
                            </div>
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

{{-- Tinjau Reward Modal — kerangka split-pane yang sama dengan halaman Verifikasi
     Prestasi: bukti di kiri, data & keputusan di kanan. Klaim yang sudah diputus
     memakai modal ini juga, hanya panel keputusannya diganti ringkasan hasil. --}}
<div class="modal fade tinjau-modal" id="tinjauRewardModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="overflow: hidden;">
            <form id="tinjauRewardForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        Tinjau Pengajuan Reward
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 0;">
                    <div class="tp-grid">
                        {{-- Kiri: sertifikat yang jadi dasar konversi nilai --}}
                        <div class="tp-pane-bukti">
                            <div id="trPreview" style="width: 100%; display: flex; align-items: center; justify-content: center;"></div>
                            <div id="trThumbs" class="tp-thumbs"></div>
                            <a id="trOpenTab" href="#" target="_blank" rel="noopener"
                               style="font-size: 11.5px; font-weight: 600; color: var(--c-primary, #0B266E); text-decoration: none;">
                                Buka bukti di tab baru &#8599;
                            </a>
                        </div>

                        {{-- Kanan: rambu keputusan lebih dulu, baru datanya --}}
                        <div class="tp-pane-data">
                            <div id="trStatusArea" style="display: none; margin-bottom: 12px;"></div>

                            {{-- Kuota sengaja paling atas: inilah yang menentukan boleh
                                 tidaknya klaim disetujui, jadi harus terbaca lebih dulu --}}
                            <div id="trKuotaWarn" style="margin-bottom: 12px;"></div>

                            {{-- Rincian klaim yang memakan kuota tersebut, supaya angkanya
                                 bisa diperiksa dan admin tahu mana yang harus dibatalkan
                                 bila ternyata keliru --}}
                            <div id="trKuotaDipakai"></div>

                            <p class="tp-pane-heading">Data klaim reward</p>
                            <div class="tinjau-info" id="trInfo"></div>

                            {{-- Muncul bila klaim ini diajukan di bawah SK yang kini
                                 sudah diganti — keputusannya tetap memakai aturan lama --}}
                            <div id="trSkLawas" class="sk-lawas" style="display: none;"></div>

                            <div style="margin-top: 14px;">
                                <label class="form-label fw-bold mb-1" style="font-size: 13px;">
                                    Mata Kuliah yang Dinaikkan Nilainya
                                    <span class="mk-by-mhs">pilihan mahasiswa</span>
                                </label>
                                <div id="trMkView" class="mk-readonly"></div>
                                <small style="font-size: 11px; color: #666D80;">Diisi oleh mahasiswa &amp; tidak dapat diubah admin. Pastikan MK bernilai min. C (SK 774 poin 3 &amp; 4). Keputusan final tetap di Bidang Akademik Fakultas.</small>
                            </div>

                            <div id="trActions" style="margin-top: auto; padding-top: 18px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold mb-0" style="font-size: 12.5px;">Catatan <span style="font-weight: 400; color: #666D80;">(wajib untuk menolak)</span></label>
                                    <span class="text-muted" style="font-size: 11px;" id="charCount_tr">0 / 300 huruf</span>
                                </div>
                                <textarea name="reward_note" id="trNote" class="form-control" rows="3" maxlength="300"
                                          placeholder="Catatan persetujuan / alasan penolakan"
                                          style="border-radius: 10px; font-size: 13.5px;"
                                          oninput="document.getElementById('charCount_tr').innerText = this.value.length + ' / 300 huruf'; document.getElementById('trError').style.display = 'none';"></textarea>
                                <div id="trError" style="display: none; font-size: 12px; font-weight: 600; color: #dc2626; margin-top: 6px;"></div>
                                <div class="tp-aksi">
                                    <button type="button" id="trTolakBtn" class="tp-btn-tolak">Tolak</button>
                                    <button type="button" id="trSetujuiBtn" class="tp-btn-setujui">Setujui</button>
                                </div>
                            </div>

                            <div id="trReadonly" style="display: none; margin-top: auto; padding-top: 18px;">
                                <div id="trReviewArea"></div>
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal"
                                        style="border-radius: 10px; margin-top: 14px; font-weight: 600; font-size: 13.5px;">Tutup</button>
                            </div>
                        </div>
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

<script>
// ── Modal Tinjau Reward ────────────────────────────────────────────────
// Satu modal untuk klaim yang masih menunggu maupun yang sudah diputus:
// mode baca-saja hanya menyembunyikan panel keputusan dan menggantinya
// dengan ringkasan hasil, jadi tidak ada dua tampilan yang bisa melenceng.
let trData = null;

function openTinjauReward(data) {
    trData = data;

    // Kuota tampil paling atas — inilah yang menentukan boleh tidaknya disetujui
    const penuh   = data.kuota_terpakai >= data.kuota_maks;
    const kuotaEl = document.getElementById('trKuotaWarn');
    kuotaEl.innerHTML = '';
    const pill = document.createElement('span');
    pill.className = 'kuota-pill' + (penuh ? ' penuh' : '');
    pill.textContent = 'Kuota ' + data.grup + ': ' + data.kuota_terpakai + '/' + data.kuota_maks
                     + (penuh && data.pending ? ' — PENUH, tidak bisa disetujui' : '');
    kuotaEl.appendChild(pill);

    trRenderKuotaDipakai(data);

    // Ringkasan klaim. Dirakit sebagai node, bukan innerHTML — nama prestasi
    // berasal dari input mahasiswa dan tidak boleh dieksekusi sebagai HTML.
    const infoEl = document.getElementById('trInfo');
    infoEl.innerHTML = '';
    [
        ['Mahasiswa',      data.mahasiswa + ' (' + data.nim + ')'],
        ['Prestasi',       data.nama],
        ['Penyelenggara',  data.penyelenggara || '-'],
        ['Capaian',        (data.capaian || '-') + (data.invention ? ' (invention/expo/fair)' : '')],
        ['Maks. konversi', data.jml_mk_max + ' mata kuliah (setara ' + data.sks_max + ' SKS)'],
        ['Dasar aturan',   data.sk_ref || '-'],
    ].forEach(function (pair) {
        const baris = document.createElement('div');
        const lbl   = document.createElement('span');
        lbl.className   = 'lbl';
        lbl.textContent = pair[0] + ': ';
        baris.appendChild(lbl);
        baris.appendChild(document.createTextNode(pair[1]));
        infoEl.appendChild(baris);
    });

    // Peringatan bila SK yang jadi dasar klaim ini sudah diganti. Penting saat
    // klaim lama baru sempat ditinjau setelah aturan baru turun: keputusannya
    // tetap harus memakai aturan yang berlaku ketika mahasiswa mengajukan.
    const skEl = document.getElementById('trSkLawas');
    skEl.textContent = data.sk_lawas
        ? 'SK ini sudah diganti. Klaim tetap dinilai dengan aturan yang berlaku saat diajukan.'
        : '';
    skEl.style.display = data.sk_lawas ? 'block' : 'none';

    // MK pilihan mahasiswa — tampil read-only sebagai chip
    const mkView = document.getElementById('trMkView');
    mkView.innerHTML = '';
    const mks = data.mk_diajukan || [];
    if (mks.length) {
        mks.forEach(function (nama) {
            const tag = document.createElement('span');
            tag.className = 'mk-tag';
            tag.textContent = nama;
            mkView.appendChild(tag);
        });
    } else {
        const kosong = document.createElement('span');
        kosong.style.cssText = 'font-size:12px; color:#666D80;';
        kosong.textContent = 'Mahasiswa belum memilih mata kuliah.';
        mkView.appendChild(kosong);
    }

    trRenderBukti(0);

    // Klaim yang sudah diputus hanya bisa dilihat
    document.getElementById('trActions').style.display    = data.pending ? 'block' : 'none';
    document.getElementById('trReadonly').style.display   = data.pending ? 'none'  : 'block';
    document.getElementById('trStatusArea').style.display = data.pending ? 'none'  : 'block';

    if (data.pending) {
        document.getElementById('trNote').value = '';
        document.getElementById('charCount_tr').innerText = '0 / 300 huruf';
        document.getElementById('trError').style.display = 'none';
    } else {
        trRenderHasil(data);
    }

    new bootstrap.Modal(document.getElementById('tinjauRewardModal')).show();
}

// Rincian klaim yang sudah memakan kuota grup ini. Tanpa ini, angka "2/2"
// hanyalah vonis yang tidak bisa diperiksa — dan admin tidak punya cara tahu
// klaim mana yang harus dibatalkan bila salah satunya keliru.
function trRenderKuotaDipakai(data) {
    const wadah = document.getElementById('trKuotaDipakai');
    wadah.innerHTML = '';
    wadah.className = '';

    const dipakai = data.kuota_dipakai || [];
    if (!dipakai.length) return;

    wadah.className = 'kuota-dipakai-blok';

    const judul = document.createElement('p');
    judul.className = 'tp-pane-heading';
    judul.textContent = 'Kuota terpakai untuk';
    wadah.appendChild(judul);

    // Kotak yang sama persis dengan blok "Data klaim reward" di bawahnya
    const kotak = document.createElement('div');
    kotak.className = 'tinjau-info';

    dipakai.forEach(function (k) {
        const baris = document.createElement('div');
        baris.className = 'kuota-dipakai-item';

        // textContent — nama prestasi berasal dari input mahasiswa
        const nama = document.createElement('div');
        nama.className = 'kuota-dipakai-nama';
        nama.textContent = k.nama;
        baris.appendChild(nama);

        // Klaim yang sedang dibuka bisa ikut terhitung bila sudah disetujui,
        // jadi ditandai agar tidak terbaca sebagai klaim lain
        const ket = [
            k.id === data.id ? 'klaim ini' : null,
            k.mk ? 'MK: ' + k.mk : null,
            k.tanggal,
        ].filter(Boolean).join(' • ');

        if (ket) {
            const sub = document.createElement('div');
            sub.className = 'kuota-dipakai-ket';
            sub.textContent = ket;
            baris.appendChild(sub);
        }

        kotak.appendChild(baris);
    });

    wadah.appendChild(kotak);
}

// Ringkasan keputusan untuk klaim yang sudah disetujui/ditolak
function trRenderHasil(data) {
    const label = { disetujui: 'Disetujui', ditolak: 'Ditolak', diajukan: 'Menunggu' };

    const statusEl = document.getElementById('trStatusArea');
    statusEl.innerHTML = '';
    const pill = document.createElement('span');
    pill.className   = 'detail-status-pill ' + (data.status || '');
    pill.textContent = label[data.status] || data.status || '-';
    statusEl.appendChild(pill);

    const jejak = [data.reviewer ? 'oleh ' + data.reviewer : null, data.reviewed_at].filter(Boolean).join(' • ');
    if (jejak) {
        const ket = document.createElement('span');
        ket.style.cssText = 'font-size:12px; color:#666D80; margin-left:6px;';
        ket.textContent = jejak;
        statusEl.appendChild(ket);
    }

    const reviewEl = document.getElementById('trReviewArea');
    reviewEl.innerHTML = '';

    if (data.mk_disetujui) {
        reviewEl.appendChild(trBarisHasil('MK disetujui: ', data.mk_disetujui, '#059669', false));
    }
    if (data.note) {
        reviewEl.appendChild(trBarisHasil('Catatan: ', data.note, data.status === 'ditolak' ? '#dc2626' : '#059669', true));
    }
}

function trBarisHasil(label, isi, warna, berkotak) {
    const baris = document.createElement('div');
    baris.style.cssText = berkotak
        ? 'font-size:13px; padding:10px 14px; background:#f8fafc; border:1px solid #DFE1E7; border-radius:10px;'
        : 'font-size:13px; margin-bottom:10px;';

    const lbl = document.createElement('span');
    lbl.style.color   = '#666D80';
    lbl.textContent   = label;

    const val = document.createElement('span');
    val.style.cssText = 'font-weight:600; color:' + warna + ';';
    val.textContent   = isi;

    baris.appendChild(lbl);
    baris.appendChild(val);
    return baris;
}

function trRenderBukti(idx) {
    const pane    = document.getElementById('trPreview');
    const thumbs  = document.getElementById('trThumbs');
    const openTab = document.getElementById('trOpenTab');
    const bukti   = (trData && trData.bukti) || [];

    pane.innerHTML   = '';
    thumbs.innerHTML = '';

    if (!bukti.length) {
        const kosong = document.createElement('div');
        kosong.style.cssText = 'padding: 70px 0; text-align: center; font-size: 13px; color: #666D80;';
        kosong.textContent = 'Tidak ada bukti dilampirkan';
        pane.appendChild(kosong);
        openTab.style.display = 'none';
        return;
    }

    const b = bukti[idx] || bukti[0];
    if (b.is_image) {
        const img = document.createElement('img');
        img.src = b.url;
        img.alt = b.nama || 'Bukti';
        img.className = 'tp-viewer-img';
        pane.appendChild(img);
    } else {
        const frame = document.createElement('iframe');
        frame.src = b.url + '#view=FitH';
        frame.title = b.nama || 'Bukti';
        frame.className = 'tp-viewer';
        pane.appendChild(frame);
    }

    openTab.href = b.url;
    openTab.style.display = 'inline';

    // Selector hanya perlu bila klaim punya lebih dari satu berkas
    if (bukti.length > 1) {
        bukti.forEach(function (f, i) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tp-thumb' + (i === idx ? ' active' : '');
            btn.title = f.nama || 'Bukti';
            btn.onclick = function () { trRenderBukti(i); };
            if (f.is_image) {
                const th = document.createElement('img');
                th.src = f.url;
                th.alt = '';
                btn.appendChild(th);
            } else {
                btn.textContent = 'PDF';
            }
            thumbs.appendChild(btn);
        });
    }
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

</script>
</x-dynamic-component>
