<x-dynamic-component :component="$isAdmin ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #4D4DFF 0%, #6b6bff 100%);
            }
            .custom-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 24px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                border: none;
            }
            .btn-custom {
                background-color: #4D4DFF;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 10px 20px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
            }
            .btn-custom:hover {
                background-color: #3b3be5;
                color: white;
                transform: translateY(-1px);
            }
            .table-custom th {
                color: #6b7280;
                font-weight: 600;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 2px solid #f3f4f6;
                padding: 16px;
            }
            .table-custom td {
                padding: 20px 16px;
                vertical-align: middle;
                border-bottom: 1px solid #f3f4f6;
                font-size: 14px;
            }
            .table-custom tbody tr {
                transition: background-color 0.2s;
            }
            .table-custom tbody tr:hover {
                background-color: #f8fafc;
            }
            .pengaduan-row {
                cursor: pointer;
            }
            .pengaduan-row:focus-visible {
                outline: 2px solid #4D4DFF;
                outline-offset: -2px;
            }
            .custom-badge {
                font-size: 12px;
                font-weight: 600;
                padding: 6px 12px;
                border-radius: 20px;
            }
            .badge-success { background: #dcfce7; color: #16a34a; }
            .badge-info { background: #e0f2fe; color: #0284c7; }
            .badge-secondary { background: #f3f4f6; color: #4b5563; }
        </style>
    @endpush

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Layanan Pengaduan</h3>
            <p class="text-muted mb-0 fw-medium">Sampaikan keluhan secara terarah (opsional anonim) dan pantau jawabannya.</p>
        </div>

        <a href="{{ route('manajemenmahasiswa.pengaduan.create') }}" class="btn-custom text-decoration-none shadow-sm">
            <span>⊕</span> Buat Pengaduan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 fw-medium">
                <span>✓</span> {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-borderless table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Judul</th>
                        <th style="width: 200px;">Kategori</th>
                        @if($isAdmin)
                            <th style="width: 220px;">Pelapor</th>
                        @endif
                        <th style="width: 140px;">Status</th>
                        <th style="width: 180px;">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengaduan as $item)
                        @php
                            $judul = data_get($item, 'data_template.judul') ?? '-';
                            $kategori = $item->kategori;
                            $status = strtolower($item->status);

                            $badgeClass = match ($status) {
                                'dijawab' => 'badge-success',
                                'dibaca' => 'badge-info',
                                default => 'badge-secondary',
                            };

                            $pelaporLabel = 'Anonim';
                            if (!$item->is_anonim) {
                                $pelaporLabel = $isAdmin
                                    ? (optional($item->pelapor)->name ?? '—')
                                    : 'Anda';
                            }

                            $detailUrl = route('manajemenmahasiswa.pengaduan.show', $item->id);
                        @endphp

                        <tr class="pengaduan-row" data-href="{{ $detailUrl }}" tabindex="0" role="link">
                            <td class="text-muted fw-bold">#{{ $item->id }}</td>
                            <td>
                                <a class="text-decoration-none fw-bold" style="color: #111827;"
                                    href="{{ route('manajemenmahasiswa.pengaduan.show', $item->id) }}">
                                    {{ Str::limit($judul, 70) }}
                                </a>
                                @if($item->is_anonim)
                                    <div><span class="badge" style="background: #111827; color: white; font-size: 10px; margin-top: 4px;">Anonim</span></div>
                                @endif
                            </td>
                            <td>
                                <span class="custom-badge" style="background: #e0e7ff; color: #4f46e5;">
                                    {{ str_replace('_', ' ', ucfirst($kategori)) }}
                                </span>
                            </td>
                            @if($isAdmin)
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                            👤
                                        </div>
                                        <span class="text-dark fw-medium">{{ $pelaporLabel }}</span>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <span class="custom-badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="text-muted fw-medium" style="font-size: 13px;">
                                {{ optional($item->created_at)->translatedFormat('d F Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 6 : 5 }}" class="text-center text-muted py-5">
                                <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                                <div class="fw-medium">Belum ada pengaduan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengaduan->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $pengaduan->withQueryString()->links() }}
            </div>
        @endif
    </div>

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