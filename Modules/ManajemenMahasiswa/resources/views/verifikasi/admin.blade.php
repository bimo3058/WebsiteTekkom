<x-dynamic-component :component="$layout">

<style>
    /* ── Stat Cards (Admin KPI) ── */
    .admin-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 22px; }
    /* Kartu ini murni penunjuk angka — penyaringan status dilakukan lewat dropdown
       di toolbar tabel, sama seperti Direktori Mahasiswa. Karena itu tidak ada
       cursor pointer maupun efek hover: kartu yang terlihat bisa diklik tapi diam
       justru menyesatkan. */
    .admin-stat-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        position: relative; overflow: hidden;
    }
    .admin-stat-card .stat-icon {
        width: 42px; height: 42px; border-radius: 11px; display: flex;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .admin-stat-card .stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 1px; }
    .admin-stat-card .stat-lbl { font-size: .78rem; color: var(--c-fg-muted); font-weight: 500; }
    .admin-stat-card.pending .stat-icon { background: var(--c-warning-subtle); color: #d97706; }
    .admin-stat-card.pending .stat-num { color: #d97706; }
    .admin-stat-card.approved .stat-icon { background: var(--c-success-subtle); color: var(--c-success); }
    .admin-stat-card.approved .stat-num { color: var(--c-success); }
    .admin-stat-card.rejected .stat-icon { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }
    .admin-stat-card.rejected .stat-num { color: var(--c-error, #dc2626); }

    /* ── Status & Buttons ── */
    .status-verif {
        display: inline-flex; align-items: center; padding: 3px 9px;
        border-radius: 50px; font-size: .73rem; font-weight: 600;
    }
    .status-verif.pending { background: #FFFBEB; color: #d97706; }
    .status-verif.approved { background: #ECFDF5; color: #059669; }
    .status-verif.rejected { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }

    .btn-approve {
        width: 30px; height: 30px; border-radius: 8px; border: 1px solid #bbf7d0;
        background: #ECFDF5; color: #059669; cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; justify-content: center; padding: 0;
    }
    .btn-approve:hover { background: #bbf7d0; border-color: #86efac; }
    .btn-reject {
        width: 30px; height: 30px; border-radius: 8px; border: 1px solid #fecaca;
        background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; justify-content: center; padding: 0;
    }
    .btn-reject:hover { background: #fee2e2; border-color: #fca5a5; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 60px 24px; color: var(--c-fg-muted); }
    .empty-state .empty-icon { display: flex; justify-content: center; margin-bottom: 12px; color: #E5E7EB; }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; color: var(--c-fg); }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 14px 22px; }

    /* Kerangka modal Tinjau (.tp-*) tinggal di partials/tinjau-modal-styles.blade.php
       karena dipakai bersama halaman Klaim Reward. */

    .tingkat-badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 50px; font-size: .73rem; font-weight: 600; text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #FFFBEB; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #ECFDF5; color: #059669; }
    .tingkat-badge.prodi { background: #eef2ff; color: var(--c-primary); }

    /* ── Reward Badge & Aksi ── */
    .claim-badge { font-size: .73rem; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-flex; align-items: center; }
    .claim-badge.belum     { background: #f3f4f6; color: var(--c-fg-muted); }
    .claim-badge.diajukan  { background: #dbeafe; color: #1e40af; }
    .claim-badge.disetujui { background: #ECFDF5; color: #059669; }
    .claim-badge.ditolak   { background: var(--c-error-subtle, #fef2f2); color: var(--c-error, #dc2626); }

    .reward-mini { font-size: .72rem; color: var(--c-fg-muted); margin-top: 4px; max-width: 200px; line-height: 1.4; }


    /* ── Pagination (global style) ── */
    .verif-pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: #fff; border-top: 1px solid #e5e7eb;
        flex-wrap: wrap; gap: 10px;
    }

    /* ── Enhanced Table Visibility ── */
    table thead tr {
        background: #eef0f4 !important;
        border-bottom: 2px solid #d1d5db !important;
    }
    table thead th {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #374151 !important;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
    }
    table tbody tr {
        border-bottom: 1px solid #e5e7eb !important;
    }
    table tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    table tbody tr:hover {
        background: #eef2ff !important;
        box-shadow: inset 3px 0 0 0 #0B266E;
    }
    table tbody td {
        font-size: 13px;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
    }
</style>

@include('manajemenmahasiswa::verifikasi.partials.tinjau-modal-styles')

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
    <div>
        @if($tab === 'prestasi')
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Verifikasi Prestasi</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Review & verifikasi prestasi lomba yang diajukan mahasiswa</p>
        @else
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Verifikasi Riwayat Kegiatan</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Review & verifikasi riwayat keikutsertaan kegiatan yang diajukan mahasiswa</p>
        @endif
        @unless($canVerify ?? true)
            <span style="display:inline-flex; align-items:center; gap:6px; margin-top:10px; background:#eef2ff; color:#0B266E; font-size:.72rem; font-weight:700; padding:4px 12px; border-radius:50px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Mode Pemantauan — hanya melihat (tanpa setujui/tolak)
            </span>
        @endunless
    </div>
    @if($tab === 'prestasi' && ($canViewReward ?? ($canVerify ?? true)))
        <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}"
           style="background:#0B266E; color:#fff; font-weight:600; font-size:.85rem; padding:9px 18px; border-radius:8px; text-decoration:none; white-space:nowrap; display:inline-flex; align-items:center; gap:8px; transition:all .15s; border:none;"
           onmouseover="this.style.background='#091958'" onmouseout="this.style.background='#0B266E'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6"/><path d="M9 12l2 2 4-4"/><path d="M16 5h6M19 2v6"/></svg>
            Klaim Reward
            @if($pendingPrestasiReward > 0)
                <span style="background:#fff; color:#0B266E; font-size:.72rem; font-weight:700; padding:2px 8px; border-radius:50px;">{{ $pendingPrestasiReward }}</span>
            @endif
        </a>
    @endif
</div>

<!-- Admin Stat Cards — ringkasan angka saja, bukan tombol filter -->
<div class="admin-stats">
    <div class="admin-stat-card pending">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $adminStats['pending'] }}</div>
            <div class="stat-lbl">Menunggu Verifikasi</div>
        </div>
    </div>
    <div class="admin-stat-card approved">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $adminStats['approved'] }}</div>
            <div class="stat-lbl">Disetujui</div>
        </div>
    </div>
    <div class="admin-stat-card rejected">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $adminStats['rejected'] }}</div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </div>
</div>

<!-- Main Table Card (Global Style) -->
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

    <!-- Table Toolbar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:12px; flex-shrink:0;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">
                @if($tab === 'prestasi') Verifikasi Prestasi @else Verifikasi Riwayat Kegiatan @endif
            </h2>
        </div>

        @php
            // Placeholder menyebut kolom yang benar-benar dicari pada tab ini —
            // fitur pencarian yang tidak disebut tidak akan pernah dipakai admin.
            $searchPlaceholder = $tab === 'prestasi'
                ? 'Cari nama, NIM, prestasi, tingkat...'
                : 'Cari nama, NIM, kegiatan, peran...';
        @endphp

        <form method="GET" action="{{ route('manajemenmahasiswa.verifikasi.index') }}" id="filterForm"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Search -->
            <div style="position:relative; width:min(280px, calc(100vw - 200px)); min-width:120px;">
                <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;"
                     width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ $searchPlaceholder }}"
                       style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(11,38,110,0.08)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>


            <!-- Status Filter — pengganti kartu statistik yang dulu bisa diklik -->
            <select name="status"
                    style="height:34px; padding:0 10px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; font-weight:600; font-family:inherit; color:var(--c-fg-sec); outline:none; background:#fff; cursor:pointer;"
                    onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Status</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <!-- Angkatan Filter -->
            <select name="angkatan"
                    style="height:34px; padding:0 10px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; font-weight:600; font-family:inherit; color:var(--c-fg-sec); outline:none; background:#fff; cursor:pointer;"
                    onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Angkatan</option>
                @foreach($angkatanList as $a)
                    <option value="{{ $a }}" {{ $angkatan == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>

            @if($tab === 'prestasi')
                <!-- Tingkat Filter — hanya tab Prestasi; riwayat kegiatan tidak punya kolom tingkat -->
                <select name="tingkat"
                        style="height:34px; padding:0 10px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; font-weight:600; font-family:inherit; color:var(--c-fg-sec); outline:none; background:#fff; cursor:pointer;"
                        onchange="document.getElementById('filterForm').submit()">
                    <option value="semua">Semua Tingkat</option>
                    @foreach($tingkatList as $t)
                        <option value="{{ $t }}" {{ $tingkat === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            @endif
        </form>
    </div>

<!-- Tab Content: Riwayat Kegiatan -->
@if($tab === 'riwayat')
    @if($riwayatData->count() > 0)
        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:780px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:160px;">Mahasiswa</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">NIM</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">Nama Kegiatan</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Peran</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatData as $i => $rw)
                        @php
                            // Payload modal Tinjau: data klaim + berkas bukti, agar keduanya
                            // bisa dibandingkan dalam satu layar tanpa membuka tab baru.
                            $rwDiputus = $rw->verification_status !== 'pending';
                            $tinjauRiwayatPayload = [
                                'id'       => $rw->id,
                                'jenis'    => 'riwayat',
                                'judul'    => 'Tinjau Pengajuan Kegiatan',
                                'pending'  => ! $rwDiputus,
                                'contoh'   => 'Wajib diisi bila menolak. Contoh: bukti tidak sesuai dengan kegiatan yang diklaim.',
                                'readonly' => $rwDiputus
                                    ? 'Pengajuan ini sudah diverifikasi — tidak ada aksi yang tersedia.'
                                    : 'Anda hanya memiliki akses lihat — verifikasi dilakukan oleh admin kemahasiswaan.',
                                // Verifikator, waktu, & catatannya hanya ada pada baris yang sudah
                                // diputus. Kolom Status di tabel kini memuat badge saja, jadi di
                                // sinilah alasan keputusan dibaca — utuh, tanpa dipotong.
                                'sections' => [[
                                    'judul' => 'Data yang diklaim mahasiswa',
                                    'items' => array_values(array_filter([
                                        ['Mahasiswa', $rw->student->user->name ?? '-'],
                                        ['NIM',       $rw->student->student_number ?? '-'],
                                        ['Kegiatan',  $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui'],
                                        ['Peran',     $rw->peran_manual ?? ucfirst($rw->peran ?? '')],
                                        ['Tanggal',   $rw->tanggal_kegiatan?->translatedFormat('d M Y')],
                                        ['Diajukan',  $rw->created_at?->translatedFormat('d M Y, H:i')],
                                        $rwDiputus && $rw->verifiedBy        ? ['Verifikator',  $rw->verifiedBy->name] : null,
                                        $rwDiputus && $rw->verified_at       ? ['Diverifikasi', $rw->verified_at->translatedFormat('d M Y, H:i')] : null,
                                        $rwDiputus && $rw->verification_note ? ['Catatan',      $rw->verification_note] : null,
                                    ])),
                                ]],
                                'bukti' => ($rw->buktiFiles ?? collect())->map(fn ($b) => [
                                    'url'      => $b->public_url,
                                    'nama'     => $b->nama_file,
                                    'is_image' => $b->isImage(),
                                ])->values()->all(),
                            ];
                        @endphp
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;">{{ ($riwayatData->currentPage() - 1) * $riwayatData->perPage() + $i + 1 }}</td>
                            <td style="padding:14px 16px; min-width:160px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;">{{ $rw->student->user->name ?? '-' }}</p>
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-family:monospace; font-size:12px; font-weight:600; color:var(--c-primary);">{{ $rw->student->student_number ?? '-' }}</span>
                            </td>
                            <td style="padding:14px 16px; min-width:180px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:300px;">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui' }}</p>
                            </td>
                            <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-sec);">{{ $rw->peran_manual ?? ucfirst($rw->peran ?? '') }}</td>
                            <td style="padding:14px 16px;">
                                {{-- Badge saja: tinggi baris jadi seragam & warna statusnya tidak
                                     bersaing dengan apa pun. Catatan verifikasi dibaca utuh di
                                     modal Tinjau, bukan sebagai potongan kalimat di sel ini. --}}
                                <span class="status-verif {{ $rw->verification_status }}">
                                    @if($rw->verification_status === 'pending') Menunggu Persetujuan
                                    @elseif($rw->verification_status === 'approved') Disetujui
                                    @else Ditolak
                                    @endif
                                </span>
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                {{-- Satu pintu masuk untuk semua baris & semua role: bukti hanya dilihat
                                     dari dalam modal. Baris yang sudah diverifikasi dan pengunjung
                                     read-only mendapat modal mode baca-saja. --}}
                                <button type="button" class="btn-tinjau" onclick="openTinjau(@js($tinjauRiwayatPayload))">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Tinjau
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination (Global Style) -->
        @php
            $rFrom = $riwayatData->firstItem() ?? 0;
            $rTo   = $riwayatData->lastItem() ?? 0;
            $rTotal = $riwayatData->total();
            $rCurrentPage = $riwayatData->currentPage();
            $rLastPage = $riwayatData->lastPage();
        @endphp
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--c-fg-sec);">
                Showing <strong style="color:var(--c-fg); font-weight:700;">{{ $rFrom }}</strong>
                to <strong style="color:var(--c-fg); font-weight:700;">{{ $rTo }}</strong>
                of <strong style="color:var(--c-fg); font-weight:700;">{{ number_format($rTotal) }}</strong> results
            </span>
            @if($rLastPage > 1)
            <div style="display:flex; align-items:center; gap:4px;">
                @if($rCurrentPage > 1)
                <a href="{{ $riwayatData->appends(request()->query())->previousPageUrl() }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
                @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg></span>
                @endif

                @php $range = 2; $start = max(1, $rCurrentPage - $range); $end = min($rLastPage, $rCurrentPage + $range); @endphp
                @if($start > 1)
                    <a href="{{ $riwayatData->appends(request()->query())->url(1) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
                    @if($start > 2)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
                @endif
                @for($p = $start; $p <= $end; $p++)
                <a href="{{ $riwayatData->appends(request()->query())->url($p) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:{{ $p === $rCurrentPage ? '700' : '500' }}; text-decoration:none; transition:all .15s; {{ $p === $rCurrentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);' }}">{{ $p }}</a>
                @endfor
                @if($end < $rLastPage)
                    @if($end < $rLastPage - 1)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
                    <a href="{{ $riwayatData->appends(request()->query())->url($rLastPage) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">{{ $rLastPage }}</a>
                @endif

                @if($rCurrentPage < $rLastPage)
                <a href="{{ $riwayatData->appends(request()->query())->nextPageUrl() }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg></span>
                @endif
            </div>
            @endif
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5">
                    <rect x="8" y="2" width="8" height="4" rx="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M9 12h6M9 16h6"></path>
                </svg>
            </div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada data riwayat kegiatan</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Belum ada pengajuan riwayat kegiatan yang sesuai filter</p>
        </div>
    @endif
@endif

<!-- Tab Content: Prestasi Lomba -->
@if($tab === 'prestasi')
    @php $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class; @endphp

    @if($prestasiData->count() > 0)
        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:960px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:160px;">Mahasiswa</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">NIM</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">Nama Prestasi</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tingkat</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:140px;">Reward</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasiData as $i => $p)
                        @php
                            // Payload modal Tinjau: data klaim + berkas bukti, agar keduanya
                            // bisa dibandingkan dalam satu layar tanpa membuka tab baru.
                            $pDiputus = $p->verification_status !== 'pending';
                            $tinjauPayload = [
                                'id'       => $p->id,
                                'jenis'    => 'prestasi',
                                'judul'    => 'Tinjau Pengajuan Prestasi',
                                'pending'  => ! $pDiputus,
                                'contoh'   => 'Wajib diisi bila menolak. Contoh: tingkat pada sertifikat tidak sesuai.',
                                'readonly' => $pDiputus
                                    ? 'Pengajuan ini sudah diverifikasi — tidak ada aksi yang tersedia.'
                                    : 'Anda hanya memiliki akses lihat — verifikasi dilakukan oleh admin kemahasiswaan.',
                                // Verifikator, waktu, & catatannya hanya ada pada baris yang sudah
                                // diputus. Kolom Status di tabel kini memuat badge saja, jadi di
                                // sinilah alasan keputusan dibaca — utuh, tanpa dipotong.
                                'sections' => [[
                                    'judul' => 'Data yang diklaim mahasiswa',
                                    'items' => array_values(array_filter([
                                        ['Mahasiswa', $p->kemahasiswaan?->nama ?? '-'],
                                        ['NIM',       $p->kemahasiswaan?->nim ?? '-'],
                                        ['Prestasi',  $p->nama_prestasi],
                                        ['Tingkat',   ucfirst($p->tingkat)],
                                        ['Tgl Raih',  $p->tanggal?->translatedFormat('d M Y')],
                                        ['Diajukan',  $p->created_at?->translatedFormat('d M Y, H:i')],
                                        $pDiputus && $p->verifiedBy        ? ['Verifikator',  $p->verifiedBy->name] : null,
                                        $pDiputus && $p->verified_at       ? ['Diverifikasi', $p->verified_at->translatedFormat('d M Y, H:i')] : null,
                                        $pDiputus && $p->verification_note ? ['Catatan',      $p->verification_note] : null,
                                    ])),
                                ]],
                                'bukti' => $p->buktiFiles->map(fn ($b) => [
                                    'url'      => $b->public_url,
                                    'nama'     => $b->nama_file,
                                    'is_image' => $b->isImage(),
                                ])->values()->all(),
                            ];
                        @endphp
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;">{{ ($prestasiData->currentPage() - 1) * $prestasiData->perPage() + $i + 1 }}</td>
                            <td style="padding:14px 16px; min-width:160px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;">{{ $p->kemahasiswaan?->nama ?? '-' }}</p>
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-family:monospace; font-size:12px; font-weight:600; color:var(--c-primary);">{{ $p->kemahasiswaan?->nim ?? '-' }}</span>
                            </td>
                            <td style="padding:14px 16px; min-width:180px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:300px;">{{ $p->nama_prestasi }}</p>
                            </td>
                            <td style="padding:14px 16px;"><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                            <td style="padding:14px 16px;">
                                {{-- Badge saja — lihat catatan pada kolom Status tab Kegiatan --}}
                                <span class="status-verif {{ $p->verification_status }}">
                                    @if($p->verification_status === 'pending') Menunggu Persetujuan
                                    @elseif($p->verification_status === 'approved') Disetujui
                                    @else Ditolak
                                    @endif
                                </span>
                            </td>
                            <td style="padding:14px 16px;">
                                @if($p->verification_status === 'approved')
                                    @if($p->reward_status === $P::CLAIM_DIAJUKAN)
                                        <span class="claim-badge diajukan">Menunggu</span>
                                    @elseif($p->reward_status === $P::CLAIM_DISETUJUI)
                                        <span class="claim-badge disetujui">Disetujui</span>
                                    @elseif($p->reward_status === $P::CLAIM_DITOLAK)
                                        <span class="claim-badge ditolak">Ditolak</span>
                                    @else
                                        <span style="font-size:11px; color:var(--c-fg-muted);">Belum diajukan</span>
                                    @endif
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                {{-- Satu pintu masuk untuk semua baris & semua role: bukti hanya dilihat
                                     dari dalam modal. Baris yang sudah diverifikasi dan pengunjung
                                     read-only mendapat modal mode baca-saja. --}}
                                <button type="button" class="btn-tinjau" onclick="openTinjau(@js($tinjauPayload))">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Tinjau
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination (Global Style) -->
        @php
            $pFrom = $prestasiData->firstItem() ?? 0;
            $pTo   = $prestasiData->lastItem() ?? 0;
            $pTotal = $prestasiData->total();
            $pCurrentPage = $prestasiData->currentPage();
            $pLastPage = $prestasiData->lastPage();
        @endphp
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--c-fg-sec);">
                Showing <strong style="color:var(--c-fg); font-weight:700;">{{ $pFrom }}</strong>
                to <strong style="color:var(--c-fg); font-weight:700;">{{ $pTo }}</strong>
                of <strong style="color:var(--c-fg); font-weight:700;">{{ number_format($pTotal) }}</strong> results
            </span>
            @if($pLastPage > 1)
            <div style="display:flex; align-items:center; gap:4px;">
                @if($pCurrentPage > 1)
                <a href="{{ $prestasiData->appends(request()->query())->previousPageUrl() }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
                @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg></span>
                @endif

                @php $range = 2; $start = max(1, $pCurrentPage - $range); $end = min($pLastPage, $pCurrentPage + $range); @endphp
                @if($start > 1)
                    <a href="{{ $prestasiData->appends(request()->query())->url(1) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
                    @if($start > 2)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
                @endif
                @for($p_page = $start; $p_page <= $end; $p_page++)
                <a href="{{ $prestasiData->appends(request()->query())->url($p_page) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:{{ $p_page === $pCurrentPage ? '700' : '500' }}; text-decoration:none; transition:all .15s; {{ $p_page === $pCurrentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);' }}">{{ $p_page }}</a>
                @endfor
                @if($end < $pLastPage)
                    @if($end < $pLastPage - 1)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
                    <a href="{{ $prestasiData->appends(request()->query())->url($pLastPage) }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">{{ $pLastPage }}</a>
                @endif

                @if($pCurrentPage < $pLastPage)
                <a href="{{ $prestasiData->appends(request()->query())->nextPageUrl() }}" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg></span>
                @endif
            </div>
            @endif
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5">
                    <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4z"></path><path d="M5 4H3v2a3 3 0 0 0 3 3M19 4h2v2a3 3 0 0 1-3 3"></path>
                </svg>
            </div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada data prestasi</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Belum ada pengajuan prestasi yang sesuai filter</p>
        </div>
    @endif
@endif

</div> {{-- End Main Table Card --}}

{{-- Modal tersedia untuk semua role — bukti hanya bisa dilihat dari sini sejak
     kolom Bukti dihapus dari tabel. Aksi setujui/tolak tetap khusus verifikator.

     Kerangka & perilaku modalnya tinggal di partial karena dipakai bersama
     halaman pengajuan mahasiswa; halaman ini hanya menyusun daftar fieldnya. --}}
@include('manajemenmahasiswa::verifikasi.partials.tinjau-modal', [
    'tinjauAksi' => $canVerify ?? true,
])

</x-dynamic-component>
