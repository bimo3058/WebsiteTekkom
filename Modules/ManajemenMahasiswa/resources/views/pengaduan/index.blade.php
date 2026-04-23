<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            /* ── Gradient Header ───────────────────────────────────── */
            .page-header {
                background: linear-gradient(135deg, #4D4DFF 0%, #6b6bff 60%, #8484ff 100%);
                border-radius: 16px;
                padding: 28px 32px;
                color: white;
                margin-bottom: 24px;
                position: relative;
                overflow: hidden;
            }
            .page-header::after {
                content: '';
                position: absolute;
                top: -40%; right: -5%;
                width: 200px; height: 200px;
                background: rgba(255,255,255,0.08);
                border-radius: 50%;
            }
            .page-header h3 { font-weight: 800; margin-bottom: 4px; position: relative; z-index: 1; }
            .page-header p { opacity: 0.85; margin-bottom: 0; font-weight: 500; position: relative; z-index: 1; }
            .btn-header {
                background: rgba(255,255,255,0.2);
                backdrop-filter: blur(4px);
                color: white;
                border: 1.5px solid rgba(255,255,255,0.35);
                border-radius: 10px;
                padding: 10px 22px;
                font-weight: 700;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
                position: relative;
                z-index: 1;
            }
            .btn-header:hover {
                background: rgba(255,255,255,0.35);
                color: white;
                transform: translateY(-1px);
            }

            /* ── Stats Strip ───────────────────────────────────────── */
            .stats-strip {
                display: flex;
                gap: 16px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }
            .stat-chip {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            }
            .stat-chip-icon {
                width: 36px; height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
            }
            .stat-chip-value {
                font-size: 18px;
                font-weight: 800;
                color: #111827;
                line-height: 1;
            }
            .stat-chip-label {
                font-size: 11px;
                font-weight: 600;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            /* ── Filters ───────────────────────────────────────────── */
            .filter-bar {
                background: #ffffff;
                border-radius: 12px;
                padding: 16px 20px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                border: 1px solid #f1f5f9;
                margin-bottom: 20px;
            }
            .filter-input {
                background: #f8fafc;
                border: 2px solid #eef2f7;
                border-radius: 10px;
                padding: 10px 14px;
                width: 100%;
                font-weight: 600;
                color: #111827;
                font-size: 14px;
            }
            .filter-input:focus {
                outline: none;
                border-color: #c7d2fe;
                box-shadow: 0 0 0 3px rgba(77, 77, 255, 0.08);
                background: #ffffff;
            }
            .filter-select {
                background: #ffffff;
                border: 2px solid #eef2f7;
                border-radius: 10px;
                padding: 10px 12px;
                width: 100%;
                font-weight: 600;
                color: #6b7280;
                font-size: 14px;
            }

            /* ── Table ─────────────────────────────────────────────── */
            .custom-card {
                background: #ffffff;
                border-radius: 14px;
                padding: 0;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
                border: none;
                overflow: hidden;
            }
            .table-custom th {
                color: #6b7280;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                border-bottom: 2px solid #f1f5f9;
                padding: 16px 20px;
                background: #fafbfc;
            }
            .table-custom td {
                padding: 18px 20px;
                vertical-align: middle;
                border-bottom: 1px solid #f3f4f6;
                font-size: 14px;
            }
            .table-custom tbody tr {
                transition: all 0.15s;
            }
            .table-custom tbody tr:hover {
                background-color: #f8faff;
            }
            .pengaduan-row { cursor: pointer; }

            /* ── Badges ────────────────────────────────────────────── */
            .custom-badge {
                font-size: 12px;
                font-weight: 600;
                padding: 5px 12px;
                border-radius: 20px;
                white-space: nowrap;
            }
            .badge-success { background: #dcfce7; color: #16a34a; }
            .badge-info { background: #e0f2fe; color: #0284c7; }
            .badge-secondary { background: #f3f4f6; color: #4b5563; }
        </style>
    @endpush

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h3>Layanan Pengaduan</h3>
            <p>Sampaikan keluhan secara terarah (opsional anonim) dan pantau jawabannya.</p>
        </div>
        @if($canCreate)
            <a href="{{ route('manajemenmahasiswa.pengaduan.create') }}" class="btn-header text-decoration-none">
                <span>＋</span> Buat Pengaduan
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 fw-medium">
                <span>✓</span> {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ── Stats ───────────────────────────────────────────────── --}}
    @php
        $totalPengaduan = $pengaduan->total();
        $dijawabCount = $totalPengaduan - $belumDijawabCount;
    @endphp
    <div class="stats-strip">
        <div class="stat-chip">
            <div class="stat-chip-icon" style="background: #eef2ff; color: #4f46e5;">📋</div>
            <div>
                <div class="stat-chip-value">{{ $totalPengaduan }}</div>
                <div class="stat-chip-label">Total</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon" style="background: #fef3c7; color: #d97706;">⏳</div>
            <div>
                <div class="stat-chip-value">{{ $belumDijawabCount }}</div>
                <div class="stat-chip-label">Belum Dijawab</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-chip-icon" style="background: #dcfce7; color: #16a34a;">✅</div>
            <div>
                <div class="stat-chip-value">{{ $dijawabCount }}</div>
                <div class="stat-chip-label">Dijawab</div>
            </div>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.index') }}">
        <div class="filter-bar">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="filter-input"
                        placeholder="🔍 Cari judul, kronologi, atau ID pengaduan…">
                </div>
                <div class="col-lg-3">
                    <select name="kategori" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriOptions as $value => $meta)
                            <option value="{{ $value }}" {{ ($filters['kategori'] ?? '') === $value ? 'selected' : '' }}>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="btn w-100" style="background: #4D4DFF; color: white; border-radius: 10px; font-weight: 600; padding: 10px;">
                        Cari
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Table ────────────────────────────────────────────────── --}}
    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-borderless table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Judul</th>
                        <th style="width: 200px;">Kategori</th>
                        <th style="width: 200px;">Pelapor</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 160px;">Dibuat</th>
                        @if($canDelete)
                            <th style="width: 70px; text-align: right;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengaduan as $item)
                        @php
                            $judul = data_get($item, 'data_template.judul') ?? '-';
                            $kategoriRaw = (string) $item->kategori;
                            $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
                            $status = strtolower($item->status);
                            $kategoriLabel = data_get($kategoriOptions, $kategori . '.label')
                                ?? ucwords(str_replace('_', ' ', $kategori));
                            $badgeClass = match ($status) {
                                'dijawab' => 'badge-success',
                                'dibaca' => 'badge-info',
                                default => 'badge-secondary',
                            };
                            $pelaporLabel = 'Anonim';
                            if (!$item->is_anonim) {
                                $pelaporLabel = $isStaff
                                    ? (optional($item->pelapor)->name ?? '—')
                                    : 'Anda';
                            }
                            $detailUrl = route('manajemenmahasiswa.pengaduan.show', $item->id);
                        @endphp

                        <tr class="pengaduan-row" data-href="{{ $detailUrl }}" tabindex="0" role="link">
                            <td class="text-muted fw-bold">#{{ $item->id }}</td>
                            <td>
                                <a class="text-decoration-none fw-bold" style="color: #111827;" href="{{ $detailUrl }}">
                                    {{ Str::limit($judul, 70) }}
                                </a>
                                @if($item->is_anonim)
                                    <div><span class="badge" style="background: #111827; color: white; font-size: 10px; margin-top: 4px;">Anonim</span></div>
                                @endif
                            </td>
                            <td>
                                <span class="custom-badge" style="background: #e0e7ff; color: #4f46e5;">
                                    {{ $kategoriLabel }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                        👤
                                    </div>
                                    <span class="text-dark fw-medium">{{ $pelaporLabel }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="custom-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="text-muted fw-medium" style="font-size: 13px;">
                                <div>{{ optional($item->created_at)->translatedFormat('j F Y') }}</div>
                                <div style="color: #b0b8c4;">{{ optional($item->created_at)->format('H:i') }}</div>
                            </td>
                            @if($canDelete)
                                <td style="text-align: right;">
                                    <button type="button" class="btn p-0" style="color: #ef4444;" aria-label="Hapus"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        onclick="document.getElementById('deleteForm').action = '{{ route('manajemenmahasiswa.pengaduan.destroy', $item->id) }}'; document.getElementById('deleteModalText').innerText = '{{ addslashes($judul) }}';">
                                        🗑️
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canDelete ? 7 : 6 }}" class="text-center text-muted py-5">
                                <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada pengaduan</div>
                                <div class="text-muted" style="font-size: 13px;">Data pengaduan akan muncul di sini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengaduan->hasPages())
            <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #f1f5f9;">
                <div class="text-muted fw-medium" style="font-size: 12px;">
                    Menampilkan {{ $pengaduan->count() }} dari {{ $pengaduan->total() }} pengaduan
                </div>
                <div>
                    {{ $pengaduan->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ── Delete Modal ────────────────────────────────────────── --}}
    @if($canDelete)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
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
            document.addEventListener('click', function (e) {
                const row = e.target.closest('tr[data-href]');
                if (!row) return;
                if (e.target.closest('a, button, input, textarea, select, label')) return;
                window.location.href = row.dataset.href;
            });
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                if (e.target.closest('a, button, input, textarea, select, label')) return;
                const row = e.target.closest('tr[data-href]');
                if (!row) return;
                e.preventDefault();
                window.location.href = row.dataset.href;
            });
        </script>
    @endpush

</x-dynamic-component>