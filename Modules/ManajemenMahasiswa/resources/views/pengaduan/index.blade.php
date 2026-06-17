<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
    <style>
        .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

        /* ── Page Title ────────────────────────────────── */
        .page-title { margin-bottom: 22px; }
        .page-title h4 {
            font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
            margin: 0 0 4px; letter-spacing: -0.02em;
        }
        .page-title p { font-size: 0.95rem; color: #6b7280; margin: 0; }

        /* ── KPI Cards (Dashboard pattern) ────────────── */
        .kpi-row {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 12px; margin-bottom: 20px;
        }
        @media (max-width: 900px) { .kpi-row { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .kpi-row { grid-template-columns: 1fr; } }

        .kpi-mini {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 14px 16px; display: flex; align-items: center; gap: 12px;
            position: relative; overflow: hidden;
        }
        .kpi-mini::before {
            content: ''; position: absolute; top: 0; left: 0;
            width: 4px; height: 100%;
        }
        .kpi-mini-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .kpi-mini-val { font-size: 1.4rem; font-weight: 800; color: #1e1b4b; line-height: 1; margin-bottom: 1px; }
        .kpi-mini-label { font-size: .78rem; color: #9ca3af; font-weight: 500; }
        .kpi-mini-sub { font-size: .68rem; color: #d1d5db; margin-top: 2px; }
        .kpi-urgent::before { background: #ef4444; }
        .kpi-orange::before { background: #f97316; }
        .kpi-blue::before { background: #293C79; }

        /* ── Chart Card (Dashboard pattern) ───────────── */
        .chart-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
            padding: 22px 24px; margin-bottom: 20px;
        }
        .chart-title {
            font-size: .9rem; font-weight: 700; color: #1e1b4b;
            margin-bottom: 16px; display: flex; align-items: center; gap: 7px;
        }
        .chart-title-right {
            margin-left: auto; font-size: .75rem; color: #9ca3af; font-weight: 500;
        }
        .kategori-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .kategori-label {
            font-size: .82rem; font-weight: 600; color: #374151;
            width: 180px; flex-shrink: 0; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }
        .kategori-track {
            flex: 1; height: 22px; background: #f3f4f6;
            border-radius: 6px; overflow: hidden;
        }
        .kategori-fill {
            height: 100%; border-radius: 6px;
            background: linear-gradient(90deg, #293C79, #415086);
            min-width: 4px; transition: width .6s ease;
        }
        .kategori-count {
            font-size: .82rem; font-weight: 700; color: #1e1b4b;
            width: 70px; text-align: right; flex-shrink: 0;
        }

        /* ── Stat Cards (Dashboard pattern) ───────────── */
        .stat-row {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 12px; margin-top: 16px; padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        .stat-mini {
            text-align: center; padding: 10px;
            background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;
        }
        .stat-mini-val { font-size: 1.25rem; font-weight: 800; color: #1e293b; }
        .stat-mini-label {
            font-size: .7rem; font-weight: 600; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .03em;
        }

        /* ── Stat Card Horizontal (Dosen/Mhs) ────────── */
        .stat-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
            padding: 18px 20px; display: flex; align-items: center; gap: 14px;
            transition: box-shadow .2s;
        }
        .stat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.06); }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-value { font-size: 1.6rem; font-weight: 800; color: #1e1b4b; line-height: 1; margin-bottom: 2px; }
        .stat-label { font-size: .8rem; color: #9ca3af; font-weight: 500; }

        /* ── Search & Filter (Forum pattern) ──────────── */
        .toolbar {
            display: flex; gap: 12px; align-items: center;
            margin-bottom: 20px; flex-wrap: wrap;
        }
        .search-wrapper { position: relative; flex: 1; min-width: 200px; }
        .search-icon {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%); color: #9ca3af;
            pointer-events: none;
        }
        .search-input {
            width: 100%; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 12px 18px 12px 46px; font-size: 0.9rem;
            color: #4b5563; outline: none; background: #E7E8F0;
            height: 44px; transition: all 0.25s ease; font-weight: 500;
        }
        .search-input::placeholder { color: #9ca3af; }
        .search-input:focus {
            border-color: #293C79; box-shadow: 0 0 0 3px rgba(41,60,121,.12);
            background: #fff;
        }
        .filter-select {
            height: 44px; padding: 0 14px; border: 1px solid #e5e7eb;
            border-radius: 12px; background: #fff; font-size: .85rem;
            font-weight: 600; color: #374151; cursor: pointer;
            outline: none; transition: all .2s; min-width: 160px;
        }
        .filter-select:hover, .filter-select:focus { border-color: #293C79; }

        /* ── CTA Button (Forum pattern) ───────────────── */
        .btn-post {
            display: inline-flex; align-items: center; gap: 8px;
            background-color: #293C79; color: white; border: none;
            border-radius: 12px; padding: 0 24px; height: 44px;
            font-weight: 600; white-space: nowrap;
            transition: all 0.2s ease; text-decoration: none;
            flex-shrink: 0;
        }
        .btn-post:hover {
            background-color: #415086; color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(41, 60, 121, 0.3);
        }

        /* ── Ticket Cards (Forum pattern) ─────────────── */
        .forum-card {
            background: #fff; border-radius: 12px; border: 1px solid #DDE1E8;
            padding: 22px 26px; margin-bottom: 14px;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(22, 22, 43, 0.06), 0 1px 2px rgba(22, 22, 43, 0.04);
            cursor: pointer; text-decoration: none; display: block; color: inherit;
        }
        .forum-card:hover {
            border-color: #C6CBD2;
            box-shadow: 0 4px 8px -2px rgba(22, 22, 43, 0.06), 0 2px 4px -2px rgba(22, 22, 43, 0.04);
            transform: translateY(-1px); color: inherit; text-decoration: none;
        }
        .ticket-title {
            font-size: 15px; font-weight: 700; color: #111827;
            margin-bottom: 6px; line-height: 1.4;
        }
        .ticket-excerpt {
            font-size: 13px; color: #6b7280; line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 12px;
        }
        .ticket-footer {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding-top: 12px; border-top: 1px solid #f3f4f6;
        }
        .ticket-meta {
            font-size: 12px; color: #9ca3af; font-weight: 500;
            display: flex; align-items: center; gap: 6px;
        }

        /* ── Tags (Forum pattern) ─────────────────────── */
        .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
        .tag-label {
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 20px; display: inline-flex;
            align-items: center; gap: 4px; white-space: nowrap;
        }
        .tag-kategori { background: #e0e7ff; color: #4f46e5; }
        .tag-baru { background: #f3f4f6; color: #4b5563; }
        .tag-dibaca { background: #e0f2fe; color: #0284c7; }
        .tag-didelegasikan { background: #ffedd5; color: #ea580c; }
        .tag-ditanggapi_dosen { background: #e0e7ff; color: #4f46e5; }
        .tag-dijawab { background: #dcfce7; color: #16a34a; }
        .tag-diajukan_ulang { background: #fef3c7; color: #d97706; }
        .tag-selesai { background: #bbf7d0; color: #15803d; }
        .tag-anonim { background: #111827; color: #fff; }

        /* ── Delete Button (Forum pattern) ────────────── */
        .delete-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 12px; border-radius: 6px;
            font-size: 11px; font-weight: 600;
            border: 1px solid #fecaca; background: #fff;
            color: #dc2626; cursor: pointer; transition: all 0.15s;
        }
        .delete-btn:hover { background: #fef2f2; border-color: #ef4444; }

        /* ── Empty State (Forum pattern) ──────────────── */
        .empty-state { text-align: center; padding: 72px 20px; color: #9ca3af; }
        .empty-state .icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: #E7E8F0; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px; color: #293C79;
        }
        .empty-state h5 { font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: 4px; }
        .empty-state p { font-size: .88rem; }

        /* ── Pagination ───────────────────────────────── */
        .pagination-info { font-size: .82rem; color: #9ca3af; font-weight: 500; }
        .pagination .page-link {
            color: #293C79; border-color: #DDE1E8; border-radius: 8px;
            margin: 0 2px; font-size: .875rem; font-weight: 500;
            padding: 7px 13px; transition: all .2s;
        }
        .pagination .page-link:hover { background: #E7E8F0; border-color: #293C79; }
        .pagination .page-item.active .page-link { background: #293C79; border-color: #293C79; color: #fff; }
        .pagination .page-item.disabled .page-link { color: #d1d5db; border-color: #DDE1E8; }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width: 768px) {
            .toolbar { flex-direction: column; }
            .kategori-label { width: 120px; }
            .stat-row { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    {{-- ── Header ───────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="page-title">
            <h4>Layanan Pengaduan</h4>
            <p>Sampaikan keluhan secara terarah dan pantau jawabannya.</p>
        </div>
        @if($canCreate)
            <a href="{{ route('manajemenmahasiswa.pengaduan.jalur') }}" class="btn-post">
                <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Buat Pengaduan
            </a>
        @endif
    </div>

    {{-- ── Flash Message (Staff Only) ───────────────── --}}
    @if ($isStaff && session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ══ SUMMARY STATS ══════════════════════════════ --}}
    @if($isStaff && !($isDosenOnlyView ?? false))

        {{-- ── Admin: KPI Cards ───────────────────────── --}}
        <div class="kpi-row">
            <div class="kpi-mini kpi-urgent">
                <div class="kpi-mini-icon" style="background: #fef2f2; color: #dc2626;">
                    <x-manajemenmahasiswa::ui.icon name="alert-circle" size="18" />
                </div>
                <div>
                    <div class="kpi-mini-val" style="color: #dc2626;">{{ $summaryStats['perluDitindakCount'] ?? 0 }}</div>
                    <div class="kpi-mini-label">Perlu Ditindak</div>
                    <div class="kpi-mini-sub">Baru masuk</div>
                </div>
            </div>
            <div class="kpi-mini kpi-orange">
                <div class="kpi-mini-icon" style="background: #fff7ed; color: #ea580c;">
                    <x-manajemenmahasiswa::ui.icon name="clock-02" size="18" />
                </div>
                <div>
                    <div class="kpi-mini-val" style="color: #ea580c;">{{ $summaryStats['menungguDosenCount'] ?? 0 }}</div>
                    <div class="kpi-mini-label">Menunggu Dosen</div>
                    <div class="kpi-mini-sub">Didelegasikan</div>
                </div>
            </div>
            <div class="kpi-mini kpi-blue">
                <div class="kpi-mini-icon" style="background: #E7E8F0; color: #293C79;">
                    <x-manajemenmahasiswa::ui.icon name="bar-chart-11" size="18" />
                </div>
                <div>
                    <div class="kpi-mini-val" style="color: #293C79;">{{ $summaryStats['responsivitas'] ?? 0 }}%</div>
                    <div class="kpi-mini-label">Responsivitas</div>
                    <div class="kpi-mini-sub">{{ $summaryStats['ditanganiCount'] ?? 0 }}/{{ $summaryStats['totalNonDraft'] ?? 0 }} ditangani</div>
                </div>
            </div>
        </div>

        {{-- ── Admin: Distribusi Kategori ─────────────── --}}
        @if(!empty($summaryStats['kategoriDistribusi']) && $summaryStats['kategoriDistribusi']->count() > 0)
            @php $maxKat = $summaryStats['kategoriDistribusi']->max() ?: 1; @endphp
            <div class="chart-card">
                <div class="chart-title">
                    <x-manajemenmahasiswa::ui.icon name="bar-chart-11" size="15" />
                    Distribusi Kategori
                    <span class="chart-title-right">Total: {{ $summaryStats['totalNonDraft'] ?? 0 }}</span>
                </div>
                @foreach($summaryStats['kategoriDistribusi'] as $kat => $total)
                    @php
                        $katNorm = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kat);
                        $katLabel = data_get($kategoriOptions, $katNorm . '.label') ?? ucwords(str_replace('_', ' ', $katNorm));
                        $pct = round($total / ($summaryStats['totalNonDraft'] ?: 1) * 100);
                    @endphp
                    <div class="kategori-bar">
                        <div class="kategori-label">{{ $katLabel }}</div>
                        <div class="kategori-track">
                            <div class="kategori-fill" style="width: {{ round($total / $maxKat * 100) }}%"></div>
                        </div>
                        <div class="kategori-count">{{ $total }} ({{ $pct }}%)</div>
                    </div>
                @endforeach
                <div class="stat-row">
                    <div class="stat-mini">
                        <div class="stat-mini-val">{{ $summaryStats['totalNonDraft'] ?? 0 }}</div>
                        <div class="stat-mini-label">Total</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-val">{{ $belumDijawabCount }}</div>
                        <div class="stat-mini-label">Pending</div>
                    </div>
                    <div class="stat-mini">
                        <div class="stat-mini-val">{{ $selesaiCount }}</div>
                        <div class="stat-mini-label">Selesai</div>
                    </div>
                </div>
            </div>
        @endif

    @elseif($isDosenOnlyView ?? false)
        {{-- ── Dosen: Stat Cards ──────────────────────── --}}
        <div class="d-flex gap-3 mb-4 flex-wrap">
            <div class="stat-card" style="flex: 1; min-width: 180px;">
                <div class="stat-icon" style="background: #fff7ed; color: #ea580c;">
                    <x-manajemenmahasiswa::ui.icon name="mail-05" size="20" />
                </div>
                <div>
                    <div class="stat-value" style="color: #ea580c;">{{ $belumDijawabCount }}</div>
                    <div class="stat-label">Menunggu Tanggapan</div>
                </div>
            </div>
            <div class="stat-card" style="flex: 1; min-width: 180px;">
                <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
                    <x-manajemenmahasiswa::ui.icon name="check-circle" size="20" />
                </div>
                <div>
                    <div class="stat-value" style="color: #059669;">{{ $selesaiCount }}</div>
                    <div class="stat-label">Sudah Ditanggapi</div>
                </div>
            </div>
        </div>

    @endif

    {{-- ══ TOOLBAR ════════════════════════════════════ --}}
    <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.index') }}" id="pgdFilterForm">
        <div class="toolbar">
            <div class="search-wrapper">
                <span class="search-icon">
                    <x-manajemenmahasiswa::ui.icon name="search-01" size="18" />
                </span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="search-input"
                    placeholder="Cari judul, kronologi, atau ID pengaduan…">
            </div>
            <select name="kategori" class="filter-select" onchange="document.getElementById('pgdFilterForm').submit()">
                <option value="">Semua Kategori</option>
                @foreach($kategoriOptions as $value => $meta)
                    <option value="{{ $value }}" {{ ($filters['kategori'] ?? '') === $value ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <select name="status" class="filter-select" onchange="document.getElementById('pgdFilterForm').submit()">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- ══ TICKET CARDS ══════════════════════════════ --}}
    @forelse($pengaduan as $item)
        @php
            $judul = data_get($item, 'data_template.judul') ?? '-';
            $kronologi = data_get($item, 'data_template.kronologi') ?? data_get($item, 'data_template.hal_aduan') ?? '';
            $kategoriRaw = (string) $item->kategori;
            $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
            $status = strtolower($item->status);
            $kategoriLabel = data_get($kategoriOptions, $kategori . '.label') ?? ucwords(str_replace('_', ' ', $kategori));
            $pelaporLabel = $item->is_anonim ? 'Anonim' : ($isStaff ? (optional($item->pelapor)->name ?? '—') : 'Anda');
            $detailUrl = route('manajemenmahasiswa.pengaduan.show', $item->id);
        @endphp
        <a href="{{ $detailUrl }}" class="forum-card">
            <div class="tags-row">
                <span class="tag-label tag-kategori">{{ $kategoriLabel }}</span>
                <span class="tag-label tag-{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                @if($item->is_anonim)
                    <span class="tag-label tag-anonim">
                        <x-manajemenmahasiswa::ui.icon name="locked-01" size="11" /> Anonim
                    </span>
                @endif
            </div>
            <div class="ticket-title">{{ Str::limit($judul, 90) }}</div>
            <div class="ticket-excerpt">{{ Str::limit(strip_tags($kronologi), 140) }}</div>
            <div class="ticket-footer">
                <div class="ticket-meta">
                    <x-manajemenmahasiswa::ui.icon name="user-01" size="14" />
                    {{ $pelaporLabel }}
                    @if($isStaff && in_array($status, ['didelegasikan']) && optional($item->delegasiAktif)->delegatedTo)
                        <span style="color: #ea580c;">→ {{ $item->delegasiAktif->delegatedTo->name }}</span>
                    @endif
                </div>
                <div class="ticket-meta">
                    <x-manajemenmahasiswa::ui.icon name="clock-02" size="14" />
                    {{ optional($item->created_at)->translatedFormat('j M Y, H:i') }}
                </div>
                @if($canDelete)
                    <button type="button" class="delete-btn"
                        onclick="event.preventDefault();event.stopPropagation();document.getElementById('deleteForm').action='{{ route('manajemenmahasiswa.pengaduan.destroy', $item->id) }}';document.getElementById('deleteModalText').innerText='{{ addslashes($judul) }}';new bootstrap.Modal(document.getElementById('deleteModal')).show()">
                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="12" /> Hapus
                    </button>
                @endif
            </div>
        </a>
    @empty
        <div class="empty-state">
            <div class="icon">
                <x-manajemenmahasiswa::ui.icon name="box-archive" size="32" />
            </div>
            <h5>Belum ada pengaduan</h5>
            <p>Data pengaduan akan muncul di sini.</p>
        </div>
    @endforelse

    {{-- ── Pagination ─────────────────────────────────── --}}
    @if($pengaduan->total() > 0)
        <div class="mb-2">
            <span class="pagination-info">
                Menampilkan {{ $pengaduan->firstItem() }}–{{ $pengaduan->lastItem() }} dari {{ $pengaduan->total() }} pengaduan
            </span>
        </div>
    @endif
    @if($pengaduan->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-4">
            {{ $pengaduan->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- ── Delete Modal ───────────────────────────────── --}}
    @if($canDelete)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18);">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="margin-bottom: 16px; color: #f59e0b;">
                            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="48" />
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Hapus Pengaduan?</h4>
                        <p class="text-muted mb-4" id="deleteModalText" style="font-size: 14px;"></p>
                        <form id="deleteForm" method="POST" action="">
                            @csrf
                            @method('DELETE')
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal"
                                    style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; color: #4b5563;">Batal</button>
                                <button type="submit" class="btn px-4 py-2"
                                    style="background-color: #dc2626; color: white; border-radius: 8px; font-weight: 600;">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.querySelector('.search-input')?.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgdFilterForm').submit(); }
            });
        </script>
    @endpush

</x-dynamic-component>