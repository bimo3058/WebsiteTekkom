<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            /* Halaman ini menggambar kotak kontennya sendiri (.dash-wrap/.dash-box),
               jadi kotak bawaan .main-wrapper dari layout dimatikan. */
            .main-wrapper {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid #DFE1E7; flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: #C1C7CF; border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; padding: 0; }
                .dash-box { flex: none !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Filter Tabs ── */
            .filter-tab {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 6px 14px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                border: 1px solid #DFE1E7;
                background: #fff;
                color: #666D80;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.15s;
            }

            .filter-tab:hover {
                border-color: #0B266E;
                color: #0B266E;
                background: rgba(11,38,110,0.04);
            }

            .filter-tab.active {
                background: #0B266E;
                color: #fff;
                border-color: #0B266E;
            }

            .filter-tab .badge-count {
                background: rgba(255,255,255,0.25);
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                padding: 1px 6px;
                border-radius: 20px;
            }

            .filter-tab:not(.active) .badge-count {
                background: #f1f5f9;
                color: #64748b;
            }

            /* ── Report Card ── */
            .report-card {
                background: #fff;
                border: 1px solid #DFE1E7;
                border-radius: 12px;
                margin-bottom: 10px;
                overflow: hidden;
                box-shadow: 0px 1px 2px 0px rgba(228,229,231,0.5);
                transition: border-color 0.15s;
            }

            .report-card:hover { border-color: #C1C7CF; }

            .report-card-header {
                padding: 14px 18px;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .report-thread-title {
                font-size: 14px;
                font-weight: 700;
                color: #111827;
                text-decoration: none;
                transition: color 0.15s;
                line-height: 1.4;
            }

            .report-thread-title:hover { color: #0B266E; }

            .report-meta {
                font-size: 11px;
                color: #6b7280;
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                margin-top: 4px;
            }

            .report-reason {
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 12px;
                color: #991b1b;
                line-height: 1.5;
                margin: 0 18px 14px;
                display: flex;
                gap: 8px;
                align-items: flex-start;
            }

            .report-actions {
                padding: 10px 18px 14px;
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
                border-top: 1px solid #f3f4f6;
                background: #fafafa;
            }

            .report-btn {
                padding: 5px 12px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #374151;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                transition: all 0.15s;
                text-decoration: none;
            }

            .report-btn:hover { border-color: #0B266E; color: #0B266E; }

            .report-btn.danger { border-color: #fecaca; color: #dc2626; }
            .report-btn.danger:hover { background: #fef2f2; }

            .report-btn.warning { border-color: #fde68a; color: #d97706; }
            .report-btn.warning:hover { background: #fffbeb; }

            .status-badge {
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 20px;
                white-space: nowrap;
            }

            .status-pending  { background: #fef3c7; color: #d97706; }
            .status-disetujui { background: #dcfce7; color: #16a34a; }
            .status-ditolak  { background: #fee2e2; color: #dc2626; }

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #9ca3af;
            }

            .pagination-container nav>.d-sm-flex {
                flex-direction: column-reverse;
                align-items: center !important;
                gap: 0.75rem;
            }

            .pagination-container .pagination { margin-bottom: 0; }
        </style>
    @endpush

    <div class="dash-wrap">
    <div class="dash-box">
    <div class="dash-box-header">
        <x-manajemenmahasiswa::ui.page-header
            title="Laporan Forum"
            subtitle="Inbox laporan thread dari pengguna" />
    </div>
    <div class="dash-box-body">

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius:8px; border:none; background:#dcfce7; color:#16a34a; font-weight:600; font-size:13px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Strip --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <div style="background:#fff; border:1px solid #DFE1E7; border-radius:10px; padding:12px 16px; box-shadow:0px 1px 2px 0px rgba(228,229,231,0.5); display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:7px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" style="color:#0B266E;" />
                </div>
                <div>
                    <div style="font-size:10px; font-weight:500; color:#666D80; line-height:1; margin-bottom:3px;">Total Laporan</div>
                    <div style="font-size:20px; font-weight:800; color:#0D0D12; letter-spacing:-0.02em; line-height:1;">{{ $totalCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div style="background:#fff; border:1px solid #DFE1E7; border-radius:10px; padding:12px 16px; box-shadow:0px 1px 2px 0px rgba(228,229,231,0.5); display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:7px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <x-manajemenmahasiswa::ui.icon name="clock-02" size="14" style="color:#d97706;" />
                </div>
                <div>
                    <div style="font-size:10px; font-weight:500; color:#666D80; line-height:1; margin-bottom:3px;">Menunggu</div>
                    <div style="font-size:20px; font-weight:800; color:#d97706; letter-spacing:-0.02em; line-height:1;">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('manajemenmahasiswa.forum.reports', ['status' => 'pending']) }}"
            class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
            Menunggu
            @if($pendingCount > 0)
                <span class="badge-count">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('manajemenmahasiswa.forum.reports', ['status' => 'semua']) }}"
            class="filter-tab {{ $status === 'semua' ? 'active' : '' }}">
            Semua
        </a>
        <a href="{{ route('manajemenmahasiswa.forum.reports', ['status' => 'disetujui']) }}"
            class="filter-tab {{ $status === 'disetujui' ? 'active' : '' }}">
            Disetujui
        </a>
        <a href="{{ route('manajemenmahasiswa.forum.reports', ['status' => 'ditolak']) }}"
            class="filter-tab {{ $status === 'ditolak' ? 'active' : '' }}">
            Ditolak
        </a>
    </div>

    {{-- Report List --}}
    @forelse($forumReports as $report)
        <div class="report-card">
            <div class="report-card-header">
                <div style="flex:1; min-width:0;">
                    @if($report->thread)
                        <a href="{{ route('manajemenmahasiswa.forum.show', $report->thread_id) }}"
                            class="report-thread-title">
                            {{ $report->thread->judul }}
                        </a>
                    @else
                        <span class="report-thread-title" style="color:#9ca3af; text-decoration:line-through;">
                            Thread telah dihapus
                        </span>
                    @endif
                    <div class="report-meta">
                        <span>Dilaporkan oleh <strong>{{ $report->reporter->name ?? 'Unknown' }}</strong></span>
                        @if($report->thread && $report->thread->author)
                            <span style="color:#d1d5db;">•</span>
                            <span>Thread oleh <strong>{{ $report->thread->author->name }}</strong></span>
                        @endif
                        <span style="color:#d1d5db;">•</span>
                        <span>{{ $report->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <span class="status-badge status-{{ $report->status }}">
                    {{ ucfirst($report->status) }}
                </span>
            </div>

            <div class="report-reason">
                <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="13" style="flex-shrink:0; margin-top:1px;" />
                <span>{{ $report->alasan }}</span>
            </div>

            @if($report->status === 'pending')
                <div class="report-actions">
                    @if($report->thread)
                        <a href="{{ route('manajemenmahasiswa.forum.show', $report->thread_id) }}"
                            class="report-btn">
                            <x-manajemenmahasiswa::ui.icon name="eye" size="12" /> Lihat Thread
                        </a>
                        @if(!($report->thread->is_locked ?? false))
                            <form method="POST"
                                action="{{ route('manajemenmahasiswa.forum.reports.lock_thread', $report->id) }}"
                                style="display:inline;" onsubmit="return mkConfirmSubmit(this, 'Kunci thread ini?', { title: 'Kunci Thread', variant: 'warning', confirmText: 'Ya, Kunci' })">
                                @csrf @method('PATCH')
                                <button type="submit" class="report-btn warning">
                                    <x-manajemenmahasiswa::ui.icon name="locked-01" size="12" /> Kunci Thread
                                </button>
                            </form>
                        @endif
                        <form method="POST"
                            action="{{ route('manajemenmahasiswa.forum.reports.delete_thread', $report->id) }}"
                            style="display:inline;" onsubmit="return mkConfirmSubmit(this, 'HAPUS thread ini secara permanen?', { title: 'Hapus Permanen', confirmText: 'Ya, Hapus' })">
                            @csrf @method('DELETE')
                            <button type="submit" class="report-btn danger">
                                <x-manajemenmahasiswa::ui.icon name="minus-circle" size="12" /> Hapus Thread
                            </button>
                        </form>
                    @endif
                    <form method="POST"
                        action="{{ route('manajemenmahasiswa.forum.reports.dismiss', $report->id) }}"
                        style="display:inline;" onsubmit="return mkConfirmSubmit(this, 'Abaikan laporan ini?', { title: 'Abaikan Laporan', variant: 'primary', confirmText: 'Ya, Abaikan' })">
                        @csrf @method('DELETE')
                        <button type="submit" class="report-btn">
                            <x-manajemenmahasiswa::ui.icon name="minus" size="12" /> Abaikan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <div style="margin-bottom:16px; color:#d1d5db;">
                <x-manajemenmahasiswa::ui.icon name="check-circle" size="52" />
            </div>
            <h5 style="color:#374151; font-weight:700;">Tidak ada laporan</h5>
            <p style="font-size:14px;">
                @if($status === 'pending') Tidak ada laporan yang menunggu tindakan.
                @elseif($status === 'disetujui') Belum ada laporan yang disetujui.
                @elseif($status === 'ditolak') Belum ada laporan yang ditolak.
                @else Belum ada laporan sama sekali.
                @endif
            </p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($forumReports->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-4 pagination-container">
            {{ $forumReports->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

</x-manajemenmahasiswa::layouts.forum-layout>
