<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        <style>
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
                cursor: pointer;
            }
            .table-custom tbody tr:hover {
                background-color: #f8faff;
            }

            .custom-badge {
                font-size: 12px;
                font-weight: 600;
                padding: 5px 12px;
                border-radius: 20px;
                white-space: nowrap;
            }
            .badge-aktif { background: #fef3c7; color: #d97706; }
            .badge-ditanggapi { background: #dcfce7; color: #16a34a; }
            .badge-ditolak { background: #fee2e2; color: #dc2626; }
        </style>
    @endpush

    <div class="page-header">
        <h3>Delegasi Pengaduan</h3>
        <p>Daftar tiket pengaduan yang didelegasikan oleh Admin kepada Anda untuk ditanggapi.</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px;">
            <div class="d-flex align-items-center gap-2 fw-medium">
                <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span> {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-borderless table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 100px;">Tiket #</th>
                        <th>Judul Pengaduan</th>
                        <th style="width: 250px;">Didelegasikan Oleh</th>
                        <th style="width: 160px;">Tanggal</th>
                        <th style="width: 150px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($delegasi as $item)
                        @php
                            $pengaduan = $item->pengaduan;
                            $judul = data_get($pengaduan, 'data_template.judul', '-');
                            $detailUrl = route('manajemenmahasiswa.pengaduan.delegasi.show', $item->id);
                            
                            $badgeClass = match($item->status) {
                                'aktif' => 'badge-aktif',
                                'ditanggapi' => 'badge-ditanggapi',
                                'ditolak' => 'badge-ditolak',
                                default => 'bg-secondary text-white'
                            };
                            $statusLabel = match($item->status) {
                                'aktif' => 'Menunggu Tanggapan',
                                'ditanggapi' => 'Selesai Ditanggapi',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($item->status)
                            };
                        @endphp
                        <tr onclick="window.location='{{ $detailUrl }}'">
                            <td class="text-muted fw-bold">#{{ $pengaduan->id }}</td>
                            <td>
                                <a class="text-decoration-none fw-bold" style="color: #111827;" href="{{ $detailUrl }}">
                                    {{ Str::limit($judul, 60) }}
                                </a>
                                @if($pengaduan->is_anonim)
                                    <div><span class="badge" style="background: #111827; color: white; font-size: 10px; margin-top: 4px;">Konfidensial</span></div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #64748b;">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">admin_panel_settings</span>
                                    </div>
                                    <span class="text-dark fw-medium">{{ optional($item->delegatedBy)->name ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td class="text-muted fw-medium" style="font-size: 13px;">
                                <div>{{ $item->delegated_at->translatedFormat('d M Y') }}</div>
                                <div style="color: #b0b8c4;">{{ $item->delegated_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <span class="custom-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <div style="margin-bottom: 12px;"><span class="material-symbols-outlined" style="font-size: 48px; color: #cbd5e1;">inbox</span></div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada delegasi</div>
                                <div class="text-muted" style="font-size: 13px;">Belum ada pengaduan yang didelegasikan kepada Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($delegasi->hasPages())
            <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid #f1f5f9;">
                <div class="text-muted fw-medium" style="font-size: 12px;">
                    Menampilkan {{ $delegasi->count() }} dari {{ $delegasi->total() }} delegasi
                </div>
                <div>
                    {{ $delegasi->links() }}
                </div>
            </div>
        @endif
    </div>

</x-manajemenmahasiswa::layouts.admin>
