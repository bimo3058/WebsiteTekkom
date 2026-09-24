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
    /* Warna status memakai token yang sama dengan Verifikasi Prestasi & Kegiatan
       (admin.blade.php) — jangan diberi hex sendiri, nanti melenceng lagi. */
    .admin-stat-card { background: var(--c-card); border: 1px solid var(--c-border); border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; position: relative; overflow: hidden; }
    .admin-stat-card .stat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .admin-stat-card .stat-num { font-size: 24px; font-weight: 800; line-height: 1; margin-bottom: 1px; }
    .admin-stat-card .stat-lbl { font-size: 12px; color: var(--c-fg-muted); font-weight: 500; }
    .admin-stat-card.pending .stat-icon { background: var(--c-warning-subtle); color: var(--c-warning); }
    .admin-stat-card.pending .stat-num { color: var(--c-warning); }
    .admin-stat-card.approved .stat-icon { background: var(--c-success-subtle); color: var(--c-success); }
    .admin-stat-card.approved .stat-num { color: var(--c-success); }
    .admin-stat-card.rejected .stat-icon { background: var(--c-error-subtle); color: var(--c-error); }
    .admin-stat-card.rejected .stat-num { color: var(--c-error); }

    /* Kolom pencarian — ukuran huruf, warna ikon, & fokus sama dengan
       Verifikasi Prestasi & Kegiatan (admin.blade.php). */
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c-fg-placeholder); pointer-events: none; display: flex; }
    .search-input { width: 100%; height: 34px; padding: 0 12px 0 34px; border: 1px solid var(--c-border); border-radius: 8px; font-size: 12px; color: var(--c-fg); font-family: inherit; background: var(--c-card); box-sizing: border-box; transition: all .15s; }
    .search-input:focus { background: var(--c-card); border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); outline: none; }

    .verif-table { width: 100%; border-collapse: collapse; min-width: 1100px; }
    /* Header, padding, ukuran huruf, dan warna teks sel disamakan dengan tabel
       Verifikasi Prestasi & Kegiatan (admin.blade.php): isi 13px, header 11px. */
    .verif-table thead th { font-size: 11px; font-weight: 600; color: var(--c-fg-muted); padding: 11px 16px; text-align: left; background: var(--c-bg); border-bottom: 1px solid var(--c-border); white-space: nowrap; }
    .verif-table thead th:first-child { padding: 11px 12px; width: 48px; }
    .verif-table tbody td { padding: 14px 16px; font-size: 13px; color: var(--c-fg-sec); border-bottom: 1px solid var(--c-border); vertical-align: middle; }
    .verif-table tbody td:first-child { padding: 14px 12px; color: var(--c-fg-muted); font-weight: 400; }
    .verif-table tbody tr:last-child td { border-bottom: none; }
    .verif-table tbody tr { transition: background .12s; }
    .verif-table tbody tr:hover td { background: #FAFAFA; }
    .verif-table .sel-utama { font-size: 13px; font-weight: 600; color: var(--c-fg); margin: 0; }
    .verif-table .sel-nama { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
    .verif-table .sel-judul { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5; max-width: 300px; }
    .verif-table .sel-nim { font-family: monospace; font-size: 12px; font-weight: 600; color: var(--c-primary); }
    .verif-table .sel-sub { font-size: 11px; color: var(--c-fg-muted); }

    .tingkat-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 50px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    /* Navy solid, bukan kuning: kuning sudah jadi warna status "Menunggu Review"
       di kolom sebelahnya, sehingga keduanya tampak sama. */
    .tingkat-badge.internasional { background: var(--c-primary); color: #fff; }
    .tingkat-badge.nasional { background: var(--c-primary-subtle); color: var(--c-primary); }
    .tingkat-badge.regional { background: var(--c-sky-subtle); color: var(--c-sky); }
    .tingkat-badge.universitas { background: var(--c-success-subtle); color: var(--c-success); }
    .tingkat-badge.prodi { background: var(--c-primary-subtle); color: var(--c-primary); }

    .claim-badge { font-size: 12px; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-flex; align-items: center; }
    .claim-badge.belum { background: var(--c-bg); color: var(--c-fg-muted); }
    .claim-badge.diajukan { background: var(--c-warning-subtle); color: var(--c-warning); }
    .claim-badge.disetujui { background: var(--c-success-subtle); color: var(--c-success); }
    .claim-badge.ditolak { background: var(--c-error-subtle); color: var(--c-error); }
    .reward-mini { font-size: 11px; color: var(--c-fg-muted); margin-top: 4px; max-width: 220px; line-height: 1.4; }

    .detail-status-pill { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; padding: 3px 12px; border-radius: 50px; }
    .detail-status-pill.disetujui { background: var(--c-success-subtle); color: var(--c-success); }
    .detail-status-pill.ditolak { background: var(--c-error-subtle); color: var(--c-error); }
    .detail-status-pill.diajukan { background: var(--c-warning-subtle); color: var(--c-warning); }

    /* MK pilihan mahasiswa (read-only) */
    .mk-readonly { display: flex; flex-wrap: wrap; gap: 6px; background: var(--c-bg); border: 1px solid var(--c-border); border-radius: 10px; padding: 10px 12px; min-height: 42px; }
    .mk-tag { display: inline-flex; align-items: center; font-size: 13px; font-weight: 600; color: var(--c-primary); background: var(--c-primary-subtle); border: 1px solid var(--c-primary-border, #5C78B8); border-radius: 50px; padding: 4px 12px; white-space: nowrap; flex-shrink: 0; }
    /* Versi kecil untuk sel tabel. Lebarnya dibatasi supaya klaim 3 MK tidak
       meregangkan kolom lain; daftar utuhnya tetap ada di modal Tinjau. */
    .mk-tag--sm { font-size: 11px; padding: 2px 8px; }
    .mk-cell { display: flex; flex-wrap: nowrap; gap: 4px; }
    .sel-kosong { color: var(--c-fg-placeholder); }

    .empty-state { text-align: center; padding: 60px 24px; color: var(--c-fg-muted); }
    .empty-state .empty-icon { display: flex; justify-content: center; margin-bottom: 12px; color: var(--c-border-strong); }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid var(--c-border); padding: 18px 22px; }
    .modal-header .modal-title { font-size: 16px; font-weight: 700; color: var(--c-fg); }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid var(--c-border); padding: 14px 22px; }

    /* Kolom data di sini memuat kuota, mata kuliah, dan penjelasan SK,
       jadi butuh lebih lega daripada halaman Verifikasi Prestasi */
    #tinjauRewardModal { --tp-lebar-data: 420px; }

    /* Pilihan cepat alasan Batalkan Persetujuan */
    .alasan-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .alasan-chip { font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 50px; border: 1px solid var(--c-border); background: var(--c-card); color: var(--c-fg-sec); cursor: pointer; transition: all .15s; }
    .alasan-chip:hover { border-color: var(--c-primary); color: var(--c-primary); }
    .alasan-chip.aktif { background: var(--c-primary); border-color: var(--c-primary); color: #fff; }
</style>

@include('manajemenmahasiswa::verifikasi.partials.tinjau-modal-styles')
@include('manajemenmahasiswa::partials.filter-popover')

<!-- Page Header -->
<x-manajemenmahasiswa::ui.page-header bordered title="Klaim Prestasi">
    @if($canReview)
        Tinjau pengajuan klaim prestasi mahasiswa, lalu setujui atau tolak konversinya menjadi nilai mata kuliah sesuai SK FT 774.
    @else
        Pantau pengajuan klaim prestasi mahasiswa untuk dikonversi menjadi nilai mata kuliah sesuai SK FT 774.
    @endif
</x-manajemenmahasiswa::ui.page-header>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-error-subtle); color: var(--c-error); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


<!-- Stat Cards (status klaim) — ringkasan angka saja, bukan tombol filter -->
<div class="admin-stats">
    <div class="admin-stat-card pending">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-num">{{ $rewardStats['menunggu'] }}</div>
            <div class="stat-lbl">Menunggu Review</div>
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

<!-- Main Table Card (Global Style) — disamakan dengan Verifikasi Prestasi & Kegiatan -->
<div class="filter-pop-host" style="background:var(--c-card); border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

    <!-- Table Toolbar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Klaim Prestasi</h2>

        <form method="GET" action="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}" id="filterForm"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
            {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
            @if(request()->filled('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            <div style="position:relative; width:min(280px, calc(100vw - 200px)); min-width:120px;">
                <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                <input type="text" name="search" class="search-input" placeholder="Cari nama, NIM, prestasi, tingkat..." value="{{ request('search') }}">
            </div>
            {{-- Status Klaim & Angkatan dikumpulkan dalam satu panel, sama dengan panel
                 "Advanced Filters" tabel Audit Log global (partials/filter-popover).
                 Titik di tombol Filter menyala bila ada dropdown yang sedang menyaring —
                 termasuk "Menunggu Review" yang jadi tampilan bawaan. --}}
            @php
                $filterRewardAktif   = $reward !== 'semua';
                $filterAngkatanAktif = filled($angkatan) && $angkatan !== 'semua';
                $adaFilterApaPun     = $filterAngkatanAktif || request()->filled('search') || $reward !== 'menunggu';
            @endphp
            <div class="filter-pop" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($filterRewardAktif || $filterAngkatanAktif)
                        <span class="filter-pop-dot"></span>
                    @endif
                </button>

                <div class="filter-pop-backdrop" x-show="filterOpen" x-cloak style="display: none;"
                     @click="filterOpen = false"></div>

                <div class="filter-pop-panel" x-show="filterOpen" x-cloak style="display: none;"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    <p class="filter-pop-title">Advanced Filters</p>

                    <div class="filter-pop-fields">
                        <!-- Status Klaim — pengganti kartu statistik yang dulu bisa diklik -->
                        <div>
                            <label class="filter-pop-label" for="filterReward">Status</label>
                            <x-manajemenmahasiswa::ui.select name="reward" id="filterReward">
                                <option value="semua" {{ $reward === 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="menunggu" {{ $reward === 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                                <option value="disetujui" {{ $reward === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ $reward === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterAngkatan">Angkatan</label>
                            <x-manajemenmahasiswa::ui.select name="angkatan" id="filterAngkatan">
                                <option value="semua">Semua Angkatan</option>
                                @foreach($angkatanList as $a)
                                    <option value="{{ $a }}" {{ $angkatan == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilterApaPun)
                                {{-- Reset kembali ke tampilan bawaan (Menunggu Review), bukan "semua status". --}}
                                <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@if($rewardData->count() > 0)
    <div style="overflow-x: auto;">
        <table class="verif-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Nama Prestasi</th>
                    <th>Tingkat</th>
                    <th>Tahun Ajaran</th>
                    <th>SKS Diklaim</th>
                    <th>Mata Kuliah</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 120px;">Aksi</th>
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
                        $kuotaMaks = $P::KUOTA_MAKS[$grup];   // null = tanpa batas
                        // Batas SK 774 lebih ketat dari kebijakan departemen — dihitung
                        // dari reward lain yang sudah disetujui (klaim ini tidak ikut).
                        $rewardLain = collect($kuotaMap[$p->kemahasiswaan_id] ?? [])
                            ->map(fn ($daftar) => collect($daftar)->where('id', '!=', $p->id)->count());
                        $peringatanSk = $P::peringatanBatasSk(
                            $rewardLain->sum(),
                            $rewardLain[$P::KUOTA_INVENTION] ?? 0,
                            $grup === $P::KUOTA_INVENTION
                        );
                        $mkRingkas = $p->reward_mk_diajukan ?? [];
                        // Satu payload untuk semua baris. Klaim yang masih menunggu membuka
                        // modal dengan panel keputusan; yang sudah diputus membuka modal yang
                        // sama dalam mode baca-saja — jadi tidak ada dua tampilan berbeda.
                        $rewardPayload = [
                            "id"             => $p->id,
                            "pending"        => $canReview && $p->reward_status === $P::CLAIM_DIAJUKAN,
                            // Satu-satunya pembatalan keputusan di bab Verifikasi Data: persetujuan
                            // departemen belum final karena fakultas masih bisa menolak konversinya.
                            // Klaim milik sendiri dikunci — sama dengan guard tolakTinjauKlaimSendiri().
                            "bisa_batal"     => $canReview
                                                && $p->reward_status === $P::CLAIM_DISETUJUI
                                                && $p->kemahasiswaan?->user_id !== auth()->id(),
                            "nama"           => $p->nama_prestasi,
                            "mahasiswa"      => $p->kemahasiswaan->nama ?? "-",
                            "nim"            => $p->kemahasiswaan->nim ?? "-",
                            "penyelenggara"  => $p->reward_penyelenggara_label,
                            "capaian"        => $p->reward_capaian_label,
                            "invention"      => (bool) $p->reward_is_invention,
                            "tahun_ajaran"   => $p->reward_tahun_ajaran_label,
                            "jml_mk_max"     => $p->reward_jml_mk_max,
                            "sks_max"        => $p->reward_sks_max,
                            "sks_diajukan"   => $p->reward_sks_diajukan,
                            "mk_diajukan"    => $mkRingkas,
                            "mk_disetujui"   => $p->reward_mk_disetujui,
                            "kuota_terpakai" => $kuotaTerpakai,
                            "kuota_maks"     => $kuotaMaks,
                            "kuota_dipakai"  => $kuotaDipakai,
                            "grup"           => $grup,
                            "grup_label"     => $P::KUOTA_LABELS[$grup] ?? $grup,
                            "peringatan_sk"  => $peringatanSk,
                            // Dasar aturan yang dicap saat klaim diajukan
                            "sk_ref"         => $p->reward_sk_ref,
                            "sk_lawas"       => $p->rewardSkSudahDiganti(),
                            // SK 774 poin 9 — prestasi sebelum SK berlaku
                            "pra_sk"         => $p->rewardSebelumMasaBerlaku(),
                            "status"         => $p->reward_status,
                            "note"           => $p->reward_note,
                            "reviewer"       => $p->reviewedBy->name ?? null,
                            "reviewed_at"    => $p->reward_reviewed_at ? $p->reward_reviewed_at->translatedFormat('d M Y') : null,
                            "bukti"          => $p->buktiFiles->map(fn ($b) => [
                                "url"      => $b->url_akses,
                                "nama"     => $b->nama_file,
                                "is_image" => $b->isImage(),
                            ])->values()->all(),
                        ];
                    @endphp
                    <tr>
                        <td>{{ ($rewardData->currentPage() - 1) * $rewardData->perPage() + $i + 1 }}</td>
                        <td style="min-width: 160px;"><p class="sel-utama sel-nama">{{ $p->kemahasiswaan->nama ?? '-' }}</p></td>
                        <td><span class="sel-nim">{{ $p->kemahasiswaan->nim ?? '-' }}</span></td>
                        <td style="min-width: 180px;"><p class="sel-utama sel-judul">{{ $p->nama_prestasi }}</p></td>
                        <td><span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span></td>
                        <td style="white-space: nowrap;">
                            @if($p->reward_tahun_ajaran_label)
                                {{ $p->reward_tahun_ajaran_label }}
                            @else
                                <span class="sel-kosong">&ndash;</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            @if($p->reward_sks_diajukan !== null)
                                <span style="font-weight: 600; color: var(--c-fg);">{{ $p->reward_sks_diajukan }} SKS</span>
                                @if($p->reward_sks_max)
                                    <div class="sel-sub">maksimal {{ $p->reward_sks_max }} SKS</div>
                                @endif
                            @else
                                <span class="sel-kosong">&ndash;</span>
                            @endif
                        </td>
                        <td>
                            @if(count($mkRingkas))
                                <div class="mk-cell">
                                    @foreach(array_slice($mkRingkas, 0, 2) as $mkNama)
                                        <span class="mk-tag mk-tag--sm">{{ $mkNama }}</span>
                                    @endforeach
                                    @if(count($mkRingkas) > 2)
                                        <span class="mk-tag mk-tag--sm">+{{ count($mkRingkas) - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="sel-kosong">&ndash;</span>
                            @endif
                        </td>
                        <td>
                            @if($p->reward_status === $P::CLAIM_DISETUJUI)
                                <span class="claim-badge disetujui">Disetujui</span>
                            @elseif($p->reward_status === $P::CLAIM_DIAJUKAN)
                                <span class="claim-badge diajukan">Menunggu Review</span>
                            @elseif($p->reward_status === $P::CLAIM_DITOLAK)
                                <span class="claim-badge ditolak">Ditolak</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div class="d-flex gap-1 flex-wrap justify-content-center">
                                {{-- Semua baris memakai tombol & modal yang sama; klaim yang sudah
                                     diputus tinggal membuka modal itu tanpa panel keputusan. --}}
                                <button type="button" class="mk-btn mk-btn--primary mk-btn--sm" onclick="openTinjauReward(@js($rewardPayload))">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Tinjau
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Di dalam kartu, sama seperti tabel Verifikasi Prestasi & Kegiatan --}}
        @include('manajemenmahasiswa::partials.table-footer', ['paginator' => $rewardData])
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"></rect><path d="M12 8v13M5 12v9h14v-9"></path><path d="M12 8C12 8 11 3 8 3a2.5 2.5 0 0 0 0 5h4zM12 8s1-5 4-5a2.5 2.5 0 0 1 0 5h-4z"></path></svg></div>
        <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada klaim prestasi</p>
        <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Belum ada klaim prestasi yang sesuai filter</p>
    </div>
@endif

</div> {{-- End Main Table Card --}}

{{-- Tinjau Reward Modal — kerangka split-pane yang sama dengan halaman Verifikasi
     Prestasi: bukti di kiri, data & keputusan di kanan. Klaim yang sudah diputus
     memakai modal ini juga, hanya panel keputusannya diganti ringkasan hasil. --}}
<div class="modal fade tinjau-modal" id="tinjauRewardModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="overflow: hidden;">
            <form id="tinjauRewardForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--c-fg);">
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
                        </div>

                        {{-- Kanan: rambu keputusan lebih dulu, baru datanya --}}
                        <div class="tp-pane-data">
                            <div id="trStatusArea" style="display: none; margin-bottom: 12px;"></div>

                            {{-- Hanya muncul bila kuota habis — alasan tombol Setujui terkunci --}}
                            <div id="trKuotaWarn" class="sk-lawas" style="display: none; margin: 0 0 12px;"></div>

                            <p class="tp-pane-heading">Data klaim reward</p>
                            <div class="tinjau-info" id="trInfo"></div>

                            {{-- Muncul bila klaim ini diajukan di bawah SK yang kini
                                 sudah diganti — keputusannya tetap memakai aturan lama --}}
                            <div id="trSkLawas" class="sk-lawas" style="display: none;"></div>
                            <div id="trPraSk" class="sk-lawas" style="display: none;"></div>
                            {{-- Melewati batas SK 774 — diizinkan kebijakan departemen,
                                 tapi fakultas masih mengacu ke SK --}}
                            <div id="trSkBatas" class="sk-lawas" style="display: none;"></div>

                            {{-- Kotak MK & textarea Catatan berbagi sisa tinggi panel
                                 sama rata, supaya tidak ada satu kotak yang terlalu besar --}}
                            <div style="margin-top: 14px; flex: 1 1 0; display: flex; flex-direction: column; min-height: 90px;">
                                <label class="form-label fw-bold mb-1" style="font-size: 13px;">
                                    Mata Kuliah yang Dinaikkan Nilainya
                                </label>
                                <div id="trMkView" class="mk-readonly" style="flex: 1 1 auto; align-content: flex-start;"></div>
                            </div>

                            <div id="trActions" style="flex: 1 1 0; flex-direction: column; padding-top: 18px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold mb-0" style="font-size: 12px;">Catatan <span style="font-weight: 400; color: var(--c-fg-muted);">(wajib untuk menolak)</span></label>
                                    <span class="text-muted" style="font-size: 11px;" id="charCount_tr">0 / 300 huruf</span>
                                </div>
                                <textarea name="reward_note" id="trNote" class="form-control" rows="3" maxlength="300"
                                          placeholder="Catatan persetujuan / alasan penolakan"
                                          style="border-radius: 10px; font-size: 13px; flex: 1 1 auto; min-height: 84px; resize: none;"
                                          oninput="document.getElementById('charCount_tr').innerText = this.value.length + ' / 300 huruf'; document.getElementById('trError').style.display = 'none';"></textarea>
                                <div id="trError" style="display: none; font-size: 12px; font-weight: 600; color: var(--c-error); margin-top: 6px;"></div>
                                <div class="tp-aksi">
                                    <button type="button" id="trTolakBtn" class="mk-btn mk-btn--secondary">Tolak</button>
                                    <button type="button" id="trSetujuiBtn" class="mk-btn mk-btn--primary">Setujui</button>
                                </div>
                            </div>

                            <div id="trReadonly" style="display: none; margin-top: auto; padding-top: 18px;">
                                <div id="trReviewArea"></div>

                                {{-- Batalkan Persetujuan — mis. fakultas menolak menaikkan nilai.
                                     Klaim jadi Ditolak & kuota kembali. Alasannya wajib karena
                                     tampil ke mahasiswa sebagai "Alasan": tanpa itu ia tidak tahu
                                     harus klaim ulang dengan MK lain atau berhenti. --}}
                                <div id="trBatalArea" style="display: none; margin-top: 14px;">
                                    <button type="button" id="trBatalBtn" class="mk-btn mk-btn--secondary mk-btn--block">Batalkan Persetujuan</button>
                                    <div id="trBatalPanel" class="tp-konfirmasi" style="display: none; margin-top: 0;">
                                        <p>Status klaim akan berubah menjadi <strong>Ditolak</strong> dan kuota mahasiswa dikembalikan. Alasan di bawah akan dibaca mahasiswa.</p>
                                        <div class="alasan-chips" id="trBatalChips">
                                            <button type="button" class="alasan-chip" data-alasan="Ditolak Fakultas">Ditolak Fakultas</button>
                                            <button type="button" class="alasan-chip" data-alasan="Mata kuliah tidak sesuai">Mata kuliah tidak sesuai</button>
                                            <button type="button" class="alasan-chip" data-alasan="Salah persetujuan">Salah persetujuan</button>
                                            <button type="button" class="alasan-chip" data-alasan="">Lainnya</button>
                                        </div>
                                        <textarea id="trBatalDetail" class="form-control" rows="2" maxlength="270"
                                                  placeholder="Keterangan tambahan (wajib bila memilih Lainnya)"
                                                  style="border-radius: 10px; font-size: 13px; margin-top: 10px; resize: none;"></textarea>
                                        <div id="trBatalError" style="display: none; font-size: 12px; font-weight: 600; color: var(--c-error); margin-top: 6px;"></div>
                                        <div class="tp-aksi">
                                            <button type="button" id="trBatalKembaliBtn" class="mk-btn mk-btn--secondary">Kembali</button>
                                            <button type="button" id="trBatalKirimBtn" class="mk-btn mk-btn--primary">Ya, Batalkan Persetujuan</button>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="mk-btn mk-btn--secondary mk-btn--block" data-bs-dismiss="modal" style="margin-top: 14px;">Tutup</button>
                            </div>
                        </div>
                    </div>
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

    // kuota_maks null = kelompok tanpa batas, tidak pernah habis.
    // Peringatan hanya tampil saat kuota habis dan klaim masih menunggu.
    const maks    = data.kuota_maks;
    const penuh   = maks !== null && data.kuota_terpakai >= maks;
    const kuotaEl = document.getElementById('trKuotaWarn');
    kuotaEl.textContent = penuh && data.pending
        ? 'Kuota ' + data.grup_label + ' mahasiswa ini sudah habis, klaim tidak dapat disetujui.'
        : '';
    kuotaEl.style.display = penuh && data.pending ? 'block' : 'none';

    // Ringkasan klaim. Dirakit sebagai node, bukan innerHTML — nama prestasi
    // berasal dari input mahasiswa dan tidak boleh dieksekusi sebagai HTML.
    const infoEl = document.getElementById('trInfo');
    infoEl.innerHTML = '';
    [
        ['Mahasiswa',      data.mahasiswa + ' (' + data.nim + ')'],
        ['Prestasi',       data.nama],
        ['Penyelenggara',  data.penyelenggara || '-'],
        ['Capaian',        (data.capaian || '-') + (data.invention ? ' (invention/expo/fair)' : '')],
        ['Tahun ajaran',   data.tahun_ajaran || '-'],
        ['Maks. konversi', data.jml_mk_max + ' mata kuliah (setara ' + data.sks_max + ' SKS)'],
        ['SKS diklaim',    (data.sks_diajukan === null || data.sks_diajukan === undefined)
                               ? '-'
                               : data.sks_diajukan + ' SKS'],
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

    // SK 774 poin 9: aturan berlaku mulai Januari 2025. Prestasi yang lebih tua
    // tetap boleh diajukan, tapi peninjau harus sadar dasar aturannya berbeda.
    const praSkEl = document.getElementById('trPraSk');
    praSkEl.textContent = data.pra_sk
        ? 'Prestasi ini bertanggal sebelum SK 774 berlaku (1 Januari 2025). Menurut poin 9, prestasi tersebut masih diatur SE 176/2020. Periksa sebelum menyetujui.'
        : '';
    praSkEl.style.display = data.pra_sk ? 'block' : 'none';

    // Kebijakan departemen lebih longgar dari SK 774 poin 4 & 5 — tidak
    // menghalangi persetujuan, hanya mengingatkan bahwa fakultas bisa menolak.
    const skBatasEl = document.getElementById('trSkBatas');
    skBatasEl.textContent = data.peringatan_sk || '';
    skBatasEl.style.display = data.peringatan_sk ? 'block' : 'none';

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
        kosong.style.cssText = 'font-size:12px; color:var(--c-fg-muted);';
        kosong.textContent = 'Mahasiswa belum memilih mata kuliah.';
        mkView.appendChild(kosong);
    }

    trRenderBukti(0);

    // Klaim yang sudah diputus hanya bisa dilihat
    document.getElementById('trActions').style.display    = data.pending ? 'flex'  : 'none';
    document.getElementById('trReadonly').style.display   = data.pending ? 'none'  : 'block';
    document.getElementById('trStatusArea').style.display = data.pending ? 'none'  : 'block';

    if (data.pending) {
        document.getElementById('trNote').value = '';
        document.getElementById('charCount_tr').innerText = '0 / 300 huruf';
        document.getElementById('trError').style.display = 'none';

        // Kuota penuh = guard server pasti menolak. Tombolnya dikunci di sini
        // supaya admin tidak menekan tombol yang sudah pasti gagal; Tolak tetap
        // hidup karena menolak justru cara mengosongkan antreannya.
        const setujuiBtn = document.getElementById('trSetujuiBtn');
        setujuiBtn.disabled = penuh;
        setujuiBtn.title = penuh
            ? 'Kuota reward mahasiswa ini sudah habis, klaim tidak dapat disetujui.'
            : '';
    } else {
        trRenderHasil(data);
    }

    // Tombol Batalkan Persetujuan hanya untuk klaim disetujui & peninjau berwenang;
    // panel alasannya selalu mulai tertutup & kosong.
    document.getElementById('trBatalArea').style.display = data.bisa_batal ? 'block' : 'none';
    trTampilBatal(false);

    new bootstrap.Modal(document.getElementById('tinjauRewardModal')).show();
}

// Ringkasan keputusan untuk klaim yang sudah disetujui/ditolak
function trRenderHasil(data) {
    const label = { disetujui: 'Disetujui', ditolak: 'Ditolak', diajukan: 'Menunggu Review' };

    const statusEl = document.getElementById('trStatusArea');
    statusEl.innerHTML = '';
    const pill = document.createElement('span');
    pill.className   = 'detail-status-pill ' + (data.status || '');
    pill.textContent = label[data.status] || data.status || '-';
    statusEl.appendChild(pill);

    const jejak = [data.reviewer ? 'oleh ' + data.reviewer : null, data.reviewed_at].filter(Boolean).join(' • ');
    if (jejak) {
        const ket = document.createElement('span');
        ket.style.cssText = 'font-size:12px; color:var(--c-fg-muted); margin-left:6px;';
        ket.textContent = jejak;
        statusEl.appendChild(ket);
    }

    const reviewEl = document.getElementById('trReviewArea');
    reviewEl.innerHTML = '';

    if (data.mk_disetujui) {
        reviewEl.appendChild(trBarisHasil('MK disetujui: ', data.mk_disetujui, 'var(--c-success)', false));
    }
    if (data.note) {
        reviewEl.appendChild(trBarisHasil('Catatan: ', data.note, data.status === 'ditolak' ? 'var(--c-error)' : 'var(--c-success)', true));
    }
}

function trBarisHasil(label, isi, warna, berkotak) {
    const baris = document.createElement('div');
    baris.style.cssText = berkotak
        ? 'font-size:13px; padding:10px 14px; background:var(--c-bg); border:1px solid var(--c-border); border-radius:10px;'
        : 'font-size:13px; margin-bottom:10px;';

    const lbl = document.createElement('span');
    lbl.style.color   = 'var(--c-fg-muted)';
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
    const bukti   = (trData && trData.bukti) || [];

    pane.innerHTML   = '';
    thumbs.innerHTML = '';

    if (!bukti.length) {
        const kosong = document.createElement('div');
        kosong.style.cssText = 'padding: 70px 0; text-align: center; font-size: 13px; color: var(--c-fg-muted);';
        kosong.textContent = 'Tidak ada bukti dilampirkan';
        pane.appendChild(kosong);
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

    // ── Batalkan Persetujuan ──
    const batalErr = document.getElementById('trBatalError');
    const chips    = document.querySelectorAll('#trBatalChips .alasan-chip');

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) { c.classList.toggle('aktif', c === chip); });
            batalErr.style.display = 'none';
        });
    });
    document.getElementById('trBatalDetail').addEventListener('input', function () {
        batalErr.style.display = 'none';
    });

    document.getElementById('trBatalBtn').addEventListener('click', function () { trTampilBatal(true); });
    document.getElementById('trBatalKembaliBtn').addEventListener('click', function () { trTampilBatal(false); });

    document.getElementById('trBatalKirimBtn').addEventListener('click', function () {
        if (!trData) return;
        const aktif  = document.querySelector('#trBatalChips .alasan-chip.aktif');
        const detail = document.getElementById('trBatalDetail').value.trim();

        if (!aktif) {
            batalErr.textContent = 'Pilih alasan pembatalan lebih dulu.';
            batalErr.style.display = 'block';
            return;
        }
        // "Lainnya" tidak membawa teks sendiri — keterangannya jadi alasan utuh
        const alasan = aktif.dataset.alasan;
        if (!alasan && !detail) {
            batalErr.textContent = 'Tuliskan alasannya bila memilih Lainnya.';
            batalErr.style.display = 'block';
            return;
        }

        // Dikirim lewat textarea reward_note milik form yang sama (validasi server: wajib)
        document.getElementById('trNote').value = alasan ? (detail ? alasan + ': ' + detail : alasan) : detail;
        this.disabled = true;
        form.action = baseUrl + '/prestasi/' + trData.id + '/reward/batalkan-persetujuan';
        form.submit();
    });
})();

// Tukar tombol "Batalkan Persetujuan" dengan panel alasannya (dan sebaliknya)
function trTampilBatal(tampil) {
    document.getElementById('trBatalBtn').style.display   = tampil ? 'none' : '';
    document.getElementById('trBatalPanel').style.display = tampil ? 'block' : 'none';
    if (!tampil) {
        document.querySelectorAll('#trBatalChips .alasan-chip').forEach(function (c) { c.classList.remove('aktif'); });
        document.getElementById('trBatalDetail').value = '';
        document.getElementById('trBatalError').style.display = 'none';
        document.getElementById('trBatalKirimBtn').disabled = false;
    }
}

</script>
</x-dynamic-component>
