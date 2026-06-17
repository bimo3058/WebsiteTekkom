<x-dynamic-component :component="$layout">
@php $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class; @endphp

<style>
    .filter-section { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; align-items: center; }
    .filter-chip {
        padding: 7px 18px; border-radius: 20px; border: 1.5px solid #e5e7eb;
        background: #ffffff; color: #374151; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; text-decoration: none !important;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-chip:hover { border-color: #818cf8; color: #4f46e5; background: #eef2ff; }
    .filter-chip.active { background: #4f46e5; color: #ffffff !important; border-color: #4f46e5; }
    .tab-badge { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: #fef3c7; color: #d97706; min-width: 20px; text-align: center; }
    .filter-chip.active .tab-badge { background: #fff; color: #4f46e5; }
    .tab-badge.zero { background: #f3f4f6; color: #9ca3af; }

    .filter-select-custom { padding: 7px 16px; border-radius: 20px; border: 1.5px solid #e5e7eb; background: #fff; color: #374151; font-size: 13px; font-weight: 600; outline: none; height: 38px; }
    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    .search-input { background-color: #f3f4f6; border: none; border-radius: 8px; height: 42px; padding-left: 36px; font-size: 13px; font-weight: 500; width: 100%; }
    .search-input:focus { background-color: #fff; box-shadow: 0 0 0 2px #e0e7ff; outline: none; }

    .form-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.05), 0 2px 4px -1px rgba(0,0,0,.03); margin-bottom: 20px; border: 1px solid #f3f4f6; }

    .verif-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .verif-table thead th { background: #f8fafc; padding: 10px 14px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e5e7eb; border-top: 1px solid #e5e7eb; }
    .verif-table thead th:first-child { border-top-left-radius: 8px; border-left: 1px solid #e5e7eb; }
    .verif-table thead th:last-child { border-top-right-radius: 8px; border-right: 1px solid #e5e7eb; }
    .verif-table tbody td { padding: 12px 14px; font-size: 14px; color: #374151; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
    .verif-table tbody td:first-child { border-left: 1px solid #e5e7eb; }
    .verif-table tbody td:last-child { border-right: 1px solid #e5e7eb; }
    .verif-table tbody tr:hover td { background: #f8fafc; }

    .tingkat-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 12px; text-transform: uppercase; }
    .tingkat-badge.internasional { background: #fef3c7; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #dcfce7; color: #166534; }
    .tingkat-badge.prodi { background: #eef2ff; color: #4f46e5; }

    .claim-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; }
    .claim-badge.belum { background: #f3f4f6; color: #6b7280; }
    .claim-badge.diajukan { background: #dbeafe; color: #1e40af; }
    .claim-badge.disetujui { background: #dcfce7; color: #166534; }
    .claim-badge.ditolak { background: #fef2f2; color: #dc2626; }
    .reward-mini { font-size: 11px; color: #6b7280; margin-top: 4px; max-width: 220px; line-height: 1.4; }

    .btn-tinjau { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; }
    .btn-tinjau:hover { background: #e0e7ff; }
    .btn-batal-reward { margin-top: 6px; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; }
    .btn-batal-reward:hover { background: #fee2e2; }

    .tinjau-info { font-size: 13px; color: #1f2937; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; line-height: 1.7; }
    .tinjau-info .lbl { color: #6b7280; }

    /* MK pilihan mahasiswa (read-only) */
    .mk-by-mhs { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #4f46e5; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 2px 8px; margin-left: 4px; }
    .mk-readonly { display: flex; flex-wrap: wrap; gap: 6px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; min-height: 42px; }
    .mk-tag { display: inline-flex; align-items: center; font-size: 12px; font-weight: 600; color: #3730a3; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 20px; padding: 4px 12px; }

    /* Bukti prestasi di modal tinjau */
    .tr-bukti { display: flex; flex-wrap: wrap; gap: 8px; }
    .tr-bukti-item { display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden; background: #f8fafc; text-decoration: none; font-size: 24px; transition: border-color .2s; }
    .tr-bukti-item img { width: 100%; height: 100%; object-fit: cover; }
    .tr-bukti-item:hover { border-color: #818cf8; }
    .kuota-pill { display: inline-block; margin-top: 10px; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 20px; background: #eef2ff; color: #4f46e5; }
    .kuota-pill.penuh { background: #fef2f2; color: #dc2626; }

    .empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; }
    .empty-state-icon { font-size: 48px; margin-bottom: 12px; opacity: .5; }

    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; }

    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #6b7280; text-decoration: none; margin-bottom: 10px; }
    .back-link:hover { color: #4f46e5; }

    /* Kartu kelola dokumen aturan reward */
    .aturan-card { background: #fff; border: 1px solid #eef2ff; border-radius: 12px; padding: 16px 18px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .aturan-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .aturan-title { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 700; color: #1f2937; }
    .aturan-sub { font-size: 12px; color: #9ca3af; margin: 4px 0 0; }
    .btn-aturan-add { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    .btn-aturan-add:hover { background: #e0e7ff; }
    .aturan-upload { display: none; margin-top: 14px; padding: 14px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; }
    .aturan-upload.show { display: block; }
    .aturan-form { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .aturan-input { flex: 1; min-width: 220px; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 12px; font-size: 13px; }
    .aturan-input:focus { border-color: #818cf8; outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
    .aturan-file-input { font-size: 12px; color: #374151; max-width: 260px; }
    .btn-aturan-upload { background: #4f46e5; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .btn-aturan-upload:hover { background: #4338ca; }
    .aturan-list { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; }
    .aturan-row { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; }
    .aturan-ico { width: 38px; height: 38px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 11px; font-weight: 800; color: #fff; }
    .aturan-ico.pdf { background: #dc2626; }
    .aturan-ico.img { background: #0ea5e9; }
    .aturan-row-meta { flex: 1; min-width: 0; }
    .aturan-row-judul { display: block; font-size: 13px; font-weight: 700; color: #1f2937; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .aturan-row-file { display: block; font-size: 11px; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .aturan-act { flex-shrink: 0; font-size: 12px; font-weight: 600; border-radius: 8px; padding: 6px 12px; cursor: pointer; text-decoration: none; border: 1px solid transparent; }
    .aturan-act.lihat { color: #4f46e5; background: #eef2ff; border-color: #c7d2fe; }
    .aturan-act.lihat:hover { background: #e0e7ff; }
    .aturan-act.hapus { color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; }
    .aturan-act.hapus:hover { background: #fee2e2; }
    .aturan-empty { font-size: 13px; color: #9ca3af; margin-top: 12px; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
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
<div class="mb-4">
    <a href="{{ route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi']) }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali ke Verifikasi Prestasi
    </a>
    <h3 class="fw-bold mb-1 text-dark">Klaim Reward Prestasi</h3>
    <p class="text-dark fw-bold mb-0" style="font-size: 14px;">Tinjau &amp; setujui pengajuan reward prestasi mahasiswa (konversi nilai mata kuliah, SK FT 774). Keputusan final ada di Bidang Akademik Fakultas.</p>
</div>

<!-- Dokumen Aturan Reward (SK FT 774) -->
<div class="aturan-card">
    <div class="aturan-head">
        <div class="aturan-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Dokumen Aturan Reward (SK FT 774)
        </div>
        <button type="button" class="btn-aturan-add" onclick="document.getElementById('aturanUploadWrap').classList.toggle('show')">+ Tambah Dokumen</button>
    </div>
    <p class="aturan-sub">Unggah PDF/gambar aturan reward agar mahasiswa &amp; admin bisa membaca sumbernya saat mengajukan / meninjau reward.</p>

    <!-- Form upload (toggle) -->
    <div id="aturanUploadWrap" class="aturan-upload">
        <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.aturan.store') }}" enctype="multipart/form-data" class="aturan-form">
            @csrf
            <input type="text" name="judul" class="aturan-input" maxlength="150" required placeholder="Judul dokumen (mis. SK FT No. 774/UN7.F3/AK/I/2025)">
            <input type="file" name="file" class="aturan-file-input" required accept=".pdf,image/jpeg,image/png,image/webp">
            <button type="submit" class="btn-aturan-upload">Unggah</button>
        </form>
        <small style="font-size:11px; color:#9ca3af;">Format: PDF, JPG, PNG, WEBP. Maks 10MB.</small>
    </div>

    <!-- Daftar dokumen -->
    @if($rewardAturan->count())
        <div class="aturan-list">
            @foreach($rewardAturan as $a)
                <div class="aturan-row">
                    <span class="aturan-ico {{ $a->isImage() ? 'img' : 'pdf' }}">{{ $a->isImage() ? 'IMG' : 'PDF' }}</span>
                    <div class="aturan-row-meta">
                        <span class="aturan-row-judul">{{ $a->judul }}</span>
                        <span class="aturan-row-file">{{ $a->nama_file }}@if($a->uploadedBy) • oleh {{ $a->uploadedBy->name }}@endif</span>
                    </div>
                    <a href="{{ $a->public_url }}" target="_blank" rel="noopener" class="aturan-act lihat">Lihat ↗</a>
                    <form method="POST" action="{{ route('manajemenmahasiswa.verifikasi.aturan.destroy', $a->id) }}" onsubmit="return confirm('Hapus dokumen aturan ini?');" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="aturan-act hapus">Hapus</button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="aturan-empty">Belum ada dokumen aturan diunggah.</div>
    @endif
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

<!-- Chip status klaim -->
<div class="filter-section">
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'menunggu'])) }}"
       class="filter-chip {{ $reward === 'menunggu' ? 'active' : '' }}">
        Menunggu <span class="tab-badge {{ $pendingPrestasiReward == 0 ? 'zero' : '' }}">{{ $pendingPrestasiReward }}</span>
    </a>
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'disetujui'])) }}"
       class="filter-chip {{ $reward === 'disetujui' ? 'active' : '' }}">Disetujui</a>
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index', array_merge(request()->only(['search','angkatan']), ['reward' => 'ditolak'])) }}"
       class="filter-chip {{ $reward === 'ditolak' ? 'active' : '' }}">Ditolak</a>
</div>

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
                    <th>Bukti</th>
                    <th>Penyelenggara</th>
                    <th>Capaian</th>
                    <th>Maks. Konversi</th>
                    <th>Kuota</th>
                    <th>Status</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rewardData as $i => $p)
                    @php
                        $grup = $p->rewardKuotaGrup();
                        $kuotaTerpakai = $kuotaMap[$p->kemahasiswaan_id][$grup] ?? 0;
                        $kuotaMaks = $P::KUOTA_MAKS[$grup];
                    @endphp
                    <tr>
                        <td style="color: #9ca3af;">{{ ($rewardData->currentPage() - 1) * $rewardData->perPage() + $i + 1 }}</td>
                        <td style="font-weight: 600;">{{ $p->kemahasiswaan->nama ?? '-' }}</td>
                        <td style="font-family: monospace; font-size: 13px; color: #4f46e5;">{{ $p->kemahasiswaan->nim ?? '-' }}</td>
                        <td style="font-weight: 600;">{{ $p->nama_prestasi }}</td>
                        <td><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                        <td>
                            @if($p->buktiFiles && $p->buktiFiles->count() > 0)
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($p->buktiFiles as $bukti)
                                        <a href="{{ $bukti->public_url }}" target="_blank" title="{{ $bukti->nama_file }}" style="text-decoration: none;">
                                            @if($bukti->isImage())
                                                <img src="{{ $bukti->public_url }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e5e7eb;">
                                            @else
                                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #eef2ff; border: 1px solid #e5e7eb; font-size: 14px;">📄</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span style="font-size: 11px; color: #9ca3af;">—</span>
                            @endif
                        </td>
                        <td style="font-size: 13px;">{{ $p->reward_penyelenggara_label ?? '-' }}</td>
                        <td style="font-size: 13px;">{{ $p->reward_capaian_label ?? '-' }}@if($p->reward_is_invention)<div class="reward-mini">(invention/expo/fair)</div>@endif</td>
                        <td style="font-size: 13px; white-space: nowrap;">{{ $p->reward_jml_mk_max }} mata kuliah<br><span style="color: #6b7280;">setara {{ $p->reward_sks_max }} SKS</span></td>
                        <td style="font-size: 13px; white-space: nowrap;">{{ $kuotaTerpakai }}/{{ $kuotaMaks }}<div class="reward-mini">{{ $grup }}</div></td>
                        <td>
                            @if($p->reward_status === $P::CLAIM_DISETUJUI)
                                <span class="claim-badge disetujui">✓ Disetujui</span>
                                <div class="reward-mini" title="{{ $p->reward_mk_disetujui }}">MK: {{ Str::limit($p->reward_mk_disetujui, 40) }}</div>
                                @if($p->reward_note)<div class="reward-mini" title="{{ $p->reward_note }}">Catatan: {{ Str::limit($p->reward_note, 40) }}</div>@endif
                            @elseif($p->reward_status === $P::CLAIM_DIAJUKAN)
                                <span class="claim-badge diajukan">● Menunggu</span>
                                @if($p->claimed_at)<div class="reward-mini">{{ $p->claimed_at->translatedFormat('d M Y') }}</div>@endif
                            @elseif($p->reward_status === $P::CLAIM_DITOLAK)
                                <span class="claim-badge ditolak">✗ Ditolak</span>
                                @if($p->reward_note)<div class="reward-mini" title="{{ $p->reward_note }}">"{{ Str::limit($p->reward_note, 40) }}"</div>@endif
                            @endif
                        </td>
                        <td>
                            @if($p->reward_status === $P::CLAIM_DIAJUKAN)
                                <button type="button" class="btn-tinjau" onclick="openTinjauReward(@js([
                                    "id"             => $p->id,
                                    "nama"           => $p->nama_prestasi,
                                    "mahasiswa"      => $p->kemahasiswaan->nama ?? "-",
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
                                ]))">Tinjau</button>
                            @else
                                <span style="font-size: 11px; color: #9ca3af;">
                                    @if($p->reviewedBy) {{ $p->reviewedBy->name }} @endif
                                    @if($p->reward_reviewed_at)<div>{{ $p->reward_reviewed_at->translatedFormat('d M Y') }}</div>@endif
                                </span>
                                @if($p->reward_status === $P::CLAIM_DISETUJUI)
                                    <button type="button" class="btn-batal-reward"
                                            onclick="openBatalReward(@js($p->id), @js($p->nama_prestasi), @js($p->kemahasiswaan->nama ?? '-'))">Batalkan</button>
                                @endif
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
        <div class="empty-state-icon">🎁</div>
        <h6 style="font-weight: 600; color: #6b7280; margin-bottom: 4px;">Tidak ada klaim reward</h6>
        <p style="font-size: 13px; color: #9ca3af; margin: 0;">Belum ada klaim reward yang sesuai filter</p>
    </div>
@endif

<!-- Tinjau Reward Modal -->
<div class="modal fade" id="tinjauRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="tinjauRewardForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #4f46e5;">
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
                        <small style="font-size: 11px; color: #6b7280;">Diisi oleh mahasiswa &amp; tidak dapat diubah admin. Pastikan MK bernilai min. C (SK 774 poin 3 &amp; 4). Keputusan final tetap di Bidang Akademik Fakultas.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Bukti Prestasi</label>
                        <div id="trBukti" class="tr-bukti"></div>
                        <small style="font-size: 11px; color: #6b7280;">Klik untuk membuka bukti di tab baru.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Aturan Reward (rujukan)</label>
                        @include('manajemenmahasiswa::verifikasi._aturan_links', ['items' => $rewardAturan])
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold mb-1" style="font-size: 13px;">Catatan <span style="font-weight: 400; color: #6b7280;">(wajib untuk menolak)</span></label>
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
                    <h5 class="modal-title fw-bold" style="color: #dc2626;">Batalkan Persetujuan Reward</h5>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        mkView.innerHTML = '<span style="font-size:12px; color:#6b7280;">Mahasiswa belum memilih mata kuliah.</span>';
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
                a.textContent = '📄';
            }
            buktiEl.appendChild(a);
        });
    } else {
        buktiEl.innerHTML = '<span style="font-size:12px; color:#6b7280;">Tidak ada bukti terlampir.</span>';
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
</script>
</x-dynamic-component>
