<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
    <style>
        .main-wrapper{background:transparent!important;box-shadow:none!important;padding:0!important}
        /* ── Action Banner (gradient) ── */
        .pgd-action-banner{background:linear-gradient(135deg,#293C79 0%,#415086 50%,#6F7DA4 100%);border-radius:12px;padding:24px;margin-bottom:20px;color:#fff}
        .pgd-action-cards{display:flex;gap:14px;flex-wrap:wrap}
        .pgd-action-card{background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border-radius:10px;padding:16px 20px;text-align:center;border:1px solid rgba(255,255,255,.1);flex:1;min-width:140px;transition:transform .2s}
        .pgd-action-card:hover{transform:translateY(-2px)}
        .pgd-action-card.urgent{background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.3);animation:urgentPulse 2s ease-in-out infinite}
        @keyframes urgentPulse{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}50%{box-shadow:0 0 0 4px rgba(239,68,68,.15)}}
        .pgd-action-value{font-size:28px;font-weight:800;line-height:1;letter-spacing:-.02em}
        .pgd-action-label{font-size:12px;font-weight:600;opacity:.85;text-transform:uppercase;letter-spacing:.04em;margin-top:4px}
        .pgd-action-sub{font-size:11px;opacity:.6;margin-top:6px;line-height:1.3}
        .pgd-responsivitas{margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,.15);display:flex;align-items:center;gap:14px}
        .pgd-progress-track{height:8px;background:rgba(255,255,255,.15);border-radius:4px;overflow:hidden;flex:1}
        .pgd-progress-fill{height:100%;border-radius:4px;background:rgba(255,255,255,.7);transition:width .8s ease}
        /* ── Insight Panel ── */
        .pgd-insight-panel{background:#fff;border:1px solid #DDE1E8;border-radius:12px;padding:22px 26px;margin-bottom:20px;box-shadow:0 1px 3px rgba(22,22,43,.06)}
        .pgd-insight-title{font-size:13px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:16px}
        .pgd-kategori-bar{display:flex;align-items:center;gap:12px;margin-bottom:10px}
        .pgd-kategori-label{font-size:13px;font-weight:600;color:#374151;min-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .pgd-kategori-track{flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden}
        .pgd-kategori-fill{height:100%;border-radius:4px;background:#293C79;transition:width .6s ease}
        .pgd-kategori-count{font-size:12px;font-weight:700;color:#6b7280;min-width:60px;text-align:right}
        .pgd-stat-row{display:flex;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6}
        .pgd-mini-stat{flex:1;text-align:center;padding:10px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0}
        .pgd-mini-stat-value{font-size:20px;font-weight:800;color:#1e293b}
        .pgd-mini-stat-label{font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.03em}
        /* ── Mahasiswa Stats ── */
        .pgd-mhs-stats{display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap}
        .pgd-mhs-card{flex:1;min-width:140px;background:#fff;border:1px solid #DDE1E8;border-radius:12px;padding:20px;text-align:center;box-shadow:0 1px 3px rgba(22,22,43,.06)}
        .pgd-mhs-card-value{font-size:28px;font-weight:800;color:#1e293b;line-height:1}
        .pgd-mhs-card-label{font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-top:6px}
        .pgd-mhs-card-icon{font-size:20px;margin-bottom:8px}
        .pgd-nudge{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:600;color:#92400e;display:flex;align-items:center;gap:8px;margin-bottom:20px}
        /* ── Search & Filter ── */
        .pgd-toolbar{display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap}
        .pgd-search-wrap{flex:1;min-width:200px;position:relative}
        .pgd-search-wrap svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none}
        .pgd-search-input{width:100%;padding:10px 16px 10px 42px;border:1px solid #DDE1E8;border-radius:12px;background:#E7E8F0;font-size:.88rem;color:#374151;outline:none;transition:all .2s;height:44px;font-weight:500}
        .pgd-search-input:focus{border-color:#293C79;background:#fff;box-shadow:0 0 0 3px rgba(41,60,121,.12)}
        .pgd-filter-select{height:44px;padding:0 14px;border:1px solid #DDE1E8;border-radius:12px;background:#fff;font-size:.85rem;font-weight:600;color:#374151;cursor:pointer;outline:none;transition:all .2s;min-width:160px}
        .pgd-filter-select:hover,.pgd-filter-select:focus{border-color:#293C79}
        .pgd-btn-cta{display:inline-flex;align-items:center;gap:7px;padding:0 24px;background:#293C79;color:#fff;border-radius:12px;font-size:.88rem;font-weight:700;text-decoration:none;transition:all .2s;white-space:nowrap;flex-shrink:0;height:44px}
        .pgd-btn-cta:hover{background:#415086;color:#fff;box-shadow:0 4px 14px rgba(41,60,121,.35);transform:translateY(-1px)}
        /* ── Ticket Cards ── */
        .pgd-ticket-card{background:#fff;border-radius:12px;border:1px solid #DDE1E8;padding:22px 26px;margin-bottom:14px;transition:all .25s ease;box-shadow:0 1px 3px rgba(22,22,43,.06);cursor:pointer;text-decoration:none;display:block;color:inherit}
        .pgd-ticket-card:hover{border-color:#C6CBD2;box-shadow:0 4px 8px -2px rgba(22,22,43,.08);transform:translateY(-1px);color:inherit;text-decoration:none}
        .pgd-ticket-title{font-size:15px;font-weight:700;color:#111827;margin-bottom:6px;line-height:1.4}
        .pgd-ticket-excerpt{font-size:13px;color:#6b7280;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:12px}
        .pgd-ticket-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:12px;border-top:1px solid #f3f4f6}
        .pgd-ticket-meta{font-size:12px;color:#9ca3af;font-weight:500;display:flex;align-items:center;gap:6px}
        .pgd-tag{font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
        .pgd-tags{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px}
        /* ── Tag Colors ── */
        .pgd-tag-baru{background:#f3f4f6;color:#4b5563}.pgd-tag-dibaca{background:#e0f2fe;color:#0284c7}
        .pgd-tag-didelegasikan{background:#ffedd5;color:#ea580c}.pgd-tag-ditanggapi_dosen{background:#e0e7ff;color:#4f46e5}
        .pgd-tag-dijawab{background:#dcfce7;color:#16a34a}.pgd-tag-diajukan_ulang{background:#fef3c7;color:#d97706}
        .pgd-tag-selesai{background:#bbf7d0;color:#15803d}.pgd-tag-anonim{background:#111827;color:#fff}
        .pgd-tag-kategori{background:#e0e7ff;color:#4f46e5}
        /* ── Empty ── */
        .pgd-empty{padding:72px 20px;text-align:center;color:#9ca3af}
        .pgd-empty-icon{width:72px;height:72px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#293C79}
        /* ── Pagination ── */
        .pgd-pagination-info{font-size:.82rem;color:#9ca3af;font-weight:500}
        .pagination .page-link{color:#293C79;border-color:#DDE1E8;border-radius:8px;margin:0 2px;font-size:.875rem;font-weight:500;padding:7px 13px;transition:all .2s}
        .pagination .page-link:hover{background:#E7E8F0;border-color:#293C79}
        .pagination .page-item.active .page-link{background:#293C79;border-color:#293C79;color:#fff}
        .pagination .page-item.disabled .page-link{color:#d1d5db;border-color:#DDE1E8}
        /* ── Delete Btn ── */
        .pgd-delete-btn{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;border:1px solid #fecaca;background:#fff;font-size:12px;font-weight:600;color:#ef4444;cursor:pointer;transition:all .15s}
        .pgd-delete-btn:hover{background:#fef2f2;border-color:#ef4444}
        @media(max-width:768px){.pgd-action-cards{flex-direction:column}.pgd-mhs-stats{flex-direction:column}.pgd-toolbar{flex-direction:column}.pgd-kategori-label{min-width:120px}}
    </style>
    @endpush

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 style="font-size:1.5rem;font-weight:700;color:#1e1b4b;letter-spacing:-.02em;margin-bottom:4px">Layanan Pengaduan</h4>
            <p style="font-size:.95rem;color:#6b7280;margin:0">Sampaikan keluhan secara terarah dan pantau jawabannya.</p>
        </div>
        @if($canCreate)
            <a href="{{ route('manajemenmahasiswa.pengaduan.jalur') }}" class="pgd-btn-cta">
                <span class="material-symbols-outlined" style="font-size:18px">add</span> Buat Pengaduan
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0" style="background:#dcfce7;color:#16a34a;border-radius:12px;font-weight:500;font-size:14px">
            <div class="d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="font-size:16px">check_circle</span> {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ══ SUMMARY STATS ══ --}}
    @if($isStaff && !($isDosenOnlyView ?? false))
        {{-- ── Admin: Action Banner ── --}}
        <div class="pgd-action-banner">
            <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;opacity:.7;margin-bottom:14px">Ringkasan Layanan</div>
            <div class="pgd-action-cards">
                <div class="pgd-action-card {{ ($summaryStats['perluDitindakCount'] ?? 0) > 0 ? 'urgent' : '' }}">
                    <div class="pgd-action-value">{{ $summaryStats['perluDitindakCount'] ?? 0 }}</div>
                    <div class="pgd-action-label">Perlu Ditindak</div>
                    <div class="pgd-action-sub">Baru</div>
                </div>
                <div class="pgd-action-card">
                    <div class="pgd-action-value">{{ $summaryStats['menungguDosenCount'] ?? 0 }}</div>
                    <div class="pgd-action-label">Menunggu Dosen</div>
                    <div class="pgd-action-sub">Didelegasikan</div>
                </div>
            </div>
            <div class="pgd-responsivitas">
                <span style="font-size:12px;font-weight:600;opacity:.8;white-space:nowrap">Responsivitas</span>
                <div class="pgd-progress-track">
                    <div class="pgd-progress-fill" style="width:{{ $summaryStats['responsivitas'] ?? 0 }}%"></div>
                </div>
                <span style="font-size:13px;font-weight:800">{{ $summaryStats['responsivitas'] ?? 0 }}%</span>
                <span style="font-size:11px;opacity:.6">· {{ $summaryStats['ditanganiCount'] ?? 0 }}/{{ $summaryStats['totalNonDraft'] ?? 0 }} ditangani</span>
            </div>
        </div>

        {{-- ── Admin: Distribusi Kategori ── --}}
        @if(!empty($summaryStats['kategoriDistribusi']) && $summaryStats['kategoriDistribusi']->count() > 0)
            @php $maxKat = $summaryStats['kategoriDistribusi']->max() ?: 1; @endphp
            <div class="pgd-insight-panel">
                <div class="pgd-insight-title">Distribusi Kategori</div>
                @foreach($summaryStats['kategoriDistribusi'] as $kat => $total)
                    @php
                        $katNorm = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kat);
                        $katLabel = data_get($kategoriOptions, $katNorm . '.label') ?? ucwords(str_replace('_', ' ', $katNorm));
                        $pct = round($total / ($summaryStats['totalNonDraft'] ?: 1) * 100);
                    @endphp
                    <div class="pgd-kategori-bar">
                        <div class="pgd-kategori-label">{{ $katLabel }}</div>
                        <div class="pgd-kategori-track"><div class="pgd-kategori-fill" style="width:{{ round($total / $maxKat * 100) }}%"></div></div>
                        <div class="pgd-kategori-count">{{ $total }} ({{ $pct }}%)</div>
                    </div>
                @endforeach
                <div class="pgd-stat-row">
                    <div class="pgd-mini-stat"><div class="pgd-mini-stat-value">{{ $summaryStats['totalNonDraft'] ?? 0 }}</div><div class="pgd-mini-stat-label">Total</div></div>
                    <div class="pgd-mini-stat"><div class="pgd-mini-stat-value">{{ $belumDijawabCount }}</div><div class="pgd-mini-stat-label">Pending</div></div>
                    <div class="pgd-mini-stat"><div class="pgd-mini-stat-value">{{ $selesaiCount }}</div><div class="pgd-mini-stat-label">Selesai</div></div>
                </div>
            </div>
        @endif

    @elseif($isDosenOnlyView ?? false)
        {{-- ── Dosen: Delegasi Stats ── --}}
        <div class="pgd-mhs-stats">
            <div class="pgd-mhs-card">
                <div class="pgd-mhs-card-icon" style="color:#ea580c">📨</div>
                <div class="pgd-mhs-card-value">{{ $belumDijawabCount }}</div>
                <div class="pgd-mhs-card-label">Menunggu Tanggapan</div>
            </div>
            <div class="pgd-mhs-card">
                <div class="pgd-mhs-card-icon" style="color:#16a34a">✅</div>
                <div class="pgd-mhs-card-value">{{ $selesaiCount }}</div>
                <div class="pgd-mhs-card-label">Sudah Ditanggapi</div>
            </div>
        </div>

    @endif

    {{-- ══ TOOLBAR ══ --}}
    <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.index') }}" id="pgdFilterForm">
        <div class="pgd-toolbar">
            <div class="pgd-search-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="pgd-search-input" placeholder="Cari judul, kronologi, atau ID pengaduan…">
            </div>
            <select name="kategori" class="pgd-filter-select" onchange="document.getElementById('pgdFilterForm').submit()">
                <option value="">Semua Kategori</option>
                @foreach($kategoriOptions as $value => $meta)
                    <option value="{{ $value }}" {{ ($filters['kategori'] ?? '') === $value ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <select name="status" class="pgd-filter-select" onchange="document.getElementById('pgdFilterForm').submit()">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- ══ TICKET CARDS ══ --}}
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
        <a href="{{ $detailUrl }}" class="pgd-ticket-card">
            <div class="pgd-tags">
                <span class="pgd-tag pgd-tag-kategori">{{ $kategoriLabel }}</span>
                <span class="pgd-tag pgd-tag-{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                @if($item->is_anonim)<span class="pgd-tag pgd-tag-anonim"><span class="material-symbols-outlined" style="font-size:12px">lock</span> Anonim</span>@endif
            </div>
            <div class="pgd-ticket-title">{{ Str::limit($judul, 90) }}</div>
            <div class="pgd-ticket-excerpt">{{ Str::limit(strip_tags($kronologi), 140) }}</div>
            <div class="pgd-ticket-footer">
                <div class="pgd-ticket-meta">
                    <span class="material-symbols-outlined" style="font-size:15px">person</span>
                    {{ $pelaporLabel }}
                    @if($isStaff && in_array($status, ['didelegasikan']) && optional($item->delegasiAktif)->delegatedTo)
                        <span style="color:#ea580c">→ {{ $item->delegasiAktif->delegatedTo->name }}</span>
                    @endif
                </div>
                <div class="pgd-ticket-meta">
                    <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
                    {{ optional($item->created_at)->translatedFormat('j M Y, H:i') }}
                </div>
                @if($canDelete)
                    <button type="button" class="pgd-delete-btn" onclick="event.preventDefault();event.stopPropagation();document.getElementById('deleteForm').action='{{ route('manajemenmahasiswa.pengaduan.destroy', $item->id) }}';document.getElementById('deleteModalText').innerText='{{ addslashes($judul) }}';new bootstrap.Modal(document.getElementById('deleteModal')).show()">
                        <span class="material-symbols-outlined" style="font-size:14px">delete</span> Hapus
                    </button>
                @endif
            </div>
        </a>
    @empty
        <div class="pgd-empty">
            <div class="pgd-empty-icon"><span class="material-symbols-outlined" style="font-size:32px">inbox</span></div>
            <h5 style="font-size:1rem;font-weight:700;color:#374151;margin-bottom:4px">Belum ada pengaduan</h5>
            <p style="font-size:.88rem">Data pengaduan akan muncul di sini.</p>
        </div>
    @endforelse

    {{-- ── Pagination ── --}}
    @if($pengaduan->total() > 0)
        <div class="mb-2"><span class="pgd-pagination-info">Menampilkan {{ $pengaduan->firstItem() }}–{{ $pengaduan->lastItem() }} dari {{ $pengaduan->total() }} pengaduan</span></div>
    @endif
    @if($pengaduan->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-4">{{ $pengaduan->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
    @endif

    {{-- ── Delete Modal ────────────────────────────────────────── --}}
    @if($canDelete)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="margin-bottom: 16px;"><span class="material-symbols-outlined" style="font-size: 48px; color: #f59e0b;">warning</span></div>
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
            document.querySelector('.pgd-search-input')?.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgdFilterForm').submit(); }
            });
        </script>
    @endpush

</x-dynamic-component>