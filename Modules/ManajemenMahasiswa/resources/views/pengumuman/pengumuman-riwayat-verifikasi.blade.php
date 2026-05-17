<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* Header */
        .rv-header {
            margin-bottom: 28px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .rv-header-text h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 4px;
        }
        .rv-header-text p {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 28px;
        }
        @media (max-width: 768px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .stat-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.2s;
        }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon-pending  { background: #fef3c7; color: #d97706; }
        .stat-icon-approved { background: #d1fae5; color: #059669; }
        .stat-icon-rejected { background: #fee2e2; color: #dc2626; }
        .stat-icon-all      { background: #f3f0ff; color: #6B4FF4; }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e1b4b;
            line-height: 1;
            margin-bottom: 2px;
        }
        .stat-label { font-size: 0.8rem; color: #9ca3af; font-weight: 500; }

        /* Tab Filter */
        .rv-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 24px;
            border-bottom: 2px solid #f3f4f6;
        }
        .rv-tab {
            padding: 10px 20px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .rv-tab:hover { color: #6B4FF4; }
        .rv-tab.active { color: #6B4FF4; border-bottom-color: #6B4FF4; }
        .tab-badge {
            background: #f3f4f6;
            color: #6b7280;
            padding: 2px 8px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .rv-tab.active .tab-badge { background: #F5F3FF; color: #6B4FF4; }

        /* Flash messages */
        .alert-success {
            background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;
            border-radius: 12px; padding: 14px 18px; font-size: 0.9rem;
            font-weight: 500; margin-bottom: 20px;
        }
        .alert-error {
            background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
            border-radius: 12px; padding: 14px 18px; font-size: 0.9rem;
            font-weight: 500; margin-bottom: 20px;
        }

        /* Request Cards */
        .request-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 22px 24px;
            margin-bottom: 14px;
            transition: all 0.2s;
        }
        .request-card:hover { border-color: #d1d5db; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }

        /* Garis kiri berwarna sesuai status */
        .request-card.status-pending  { border-left: 4px solid #fbbf24; }
        .request-card.status-approved { border-left: 4px solid #10b981; }
        .request-card.status-rejected { border-left: 4px solid #ef4444; }
        .request-card.status-cancelled { border-left: 4px solid #9ca3af; }

        .request-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }
        .request-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .request-title a { color: inherit; text-decoration: none; transition: color 0.15s; }
        .request-title a:hover { color: #6B4FF4; }

        .request-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.84rem;
            color: #6b7280;
        }
        .request-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .request-meta-item svg { color: #9ca3af; flex-shrink: 0; }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            flex-shrink: 0;
        }
        .badge-pending   { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .badge-approved  { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-rejected  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-cancelled { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

        /* Pesan & catatan boxes */
        .info-box {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        .info-box-pesan {
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            color: #4b5563;
        }
        .info-box-catatan-approved {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .info-box-catatan-rejected {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .info-box-label {
            font-weight: 700;
            font-size: 0.8rem;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Action area — cancel button untuk pending */
        .request-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid #f3f4f6;
            margin-top: 14px;
        }
        .footer-hint { font-size: 0.82rem; color: #9ca3af; }

        .btn-cancel-req {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            background: #fff;
            color: #dc2626;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cancel-req:hover { background: #fef2f2; border-color: #ef4444; }

        .btn-create-post {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: #6B4FF4;
            color: #fff;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-create-post:hover {
            background: #8266F5;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(107, 79, 244, 0.3);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        .empty-state-icon {
            width: 64px; height: 64px;
            background: #f3f4f6; border-radius: 50%;
            display: flex; align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: #d1d5db;
        }
        .empty-state h6 { font-size: 1rem; font-weight: 600; color: #6b7280; margin-bottom: 6px; }
        .empty-state p  { font-size: 0.88rem; margin-bottom: 16px; }

        @media (max-width: 640px) {
            .request-header { flex-direction: column; }
            .rv-header { flex-direction: column; }
        }
    </style>
    @endpush

    @php
        $totalAll = $stats['pending'] + $stats['approved'] + $stats['rejected'] + $stats['cancelled'];
    @endphp

    <!-- Header -->
    <div class="rv-header">
        <div class="rv-header-text">
            <h4>Status Verifikasi Pengumuman</h4>
            <p>Pantau status pengajuan verifikasi pengumuman yang pernah Anda kirimkan</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-pending">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Menunggu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-approved">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">Disetujui</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-rejected">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-all">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $totalAll }}</div>
                <div class="stat-label">Total Pengajuan</div>
            </div>
        </div>
    </div>

    <!-- Tab Filter -->
    <div class="rv-tabs">
        @php
            $tabs = [
                'all'       => ['label' => 'Semua',      'count' => $totalAll],
                'pending'   => ['label' => 'Menunggu',   'count' => $stats['pending']],
                'approved'  => ['label' => 'Disetujui',  'count' => $stats['approved']],
                'rejected'  => ['label' => 'Ditolak',    'count' => $stats['rejected']],
                'cancelled' => ['label' => 'Dibatalkan', 'count' => $stats['cancelled']],
            ];
        @endphp
        @foreach($tabs as $value => $tab)
            <a href="{{ route('manajemenmahasiswa.pengumuman.riwayat.verifikasi', ['status' => $value]) }}"
               class="rv-tab {{ $statusFilter === $value ? 'active' : '' }}">
                {{ $tab['label'] }}
                <span class="tab-badge">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Request List -->
    @forelse($requests as $req)
        @php
            $statusClass = 'status-' . $req->status;
            // Fix #7: deteksi request yang baru diproses dalam 24 jam terakhir
            $recentlyProcessed = in_array($req->status, ['approved','rejected'])
                && $req->verified_at?->gt(now()->subHours(24));
        @endphp
        <div class="request-card {{ $statusClass }}">
            <div class="request-header">
                <div style="min-width:0; flex:1;">
                    <div class="request-title">
                        @if($req->pengumuman)
                            <a href="{{ route('manajemenmahasiswa.pengumuman.show', $req->pengumuman_id) }}">
                                📢 {{ $req->pengumuman->judul }}
                            </a>
                        @else
                            <span style="color:#9ca3af;">(Pengumuman telah dihapus)</span>
                        @endif
                        {{-- Fix #7: notifikasi visual untuk status yang baru berubah dalam 24 jam --}}
                        @if($recentlyProcessed)
                            <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:800;letter-spacing:.04em;margin-left:6px;vertical-align:middle;{{ $req->status === 'approved' ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#dc2626;' }}">
                                {{ $req->status === 'approved' ? '✓ BARU DISETUJUI' : '✗ BARU DITOLAK' }}
                            </span>
                        @endif
                    </div>
                    <div class="request-meta">
                        <span class="request-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            Verifikator: <strong>{{ $req->verifier?->name ?? 'N/A' }}</strong>
                        </span>
                        <span class="request-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            Diajukan {{ $req->created_at->diffForHumans() }}
                        </span>
                        @if($req->verified_at)
                            <span class="request-meta-item">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Diproses {{ $req->verified_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>

                <span class="status-badge
                    @if($req->status === 'pending')   badge-pending
                    @elseif($req->status === 'approved') badge-approved
                    @elseif($req->status === 'rejected') badge-rejected
                    @else badge-cancelled @endif">
                    @if($req->status === 'pending')    ⏳ Menunggu
                    @elseif($req->status === 'approved') ✅ Disetujui
                    @elseif($req->status === 'rejected') ❌ Ditolak
                    @else 🚫 Dibatalkan @endif
                </span>
            </div>

            {{-- Pesan yang dikirim ke verifikator --}}
            @if($req->pesan_pengaju)
                <div class="info-box info-box-pesan">
                    <div class="info-box-label">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Pesan Anda ke verifikator:
                    </div>
                    {{ $req->pesan_pengaju }}
                </div>
            @endif

            {{-- Fix #12: Catatan dari verifikator — label jelas bahwa ini dari pengajuan terakhir --}}
            @if($req->catatan_verifikator)
                <div class="info-box {{ $req->status === 'approved' ? 'info-box-catatan-approved' : 'info-box-catatan-rejected' }}">
                    <div class="info-box-label">
                        @if($req->status === 'approved')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Catatan persetujuan dari {{ $req->verifier?->name ?? 'verifikator' }}:
                        @else
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Alasan penolakan dari {{ $req->verifier?->name ?? 'verifikator' }}
                            <span style="font-size:.72rem;font-weight:400;opacity:.7;">(pengajuan ini)</span>:
                        @endif
                    </div>
                    {{ $req->catatan_verifikator }}
                </div>
            @endif

            {{-- Footer: cancel button untuk pending, hint untuk rejected --}}
            <div class="request-footer">
                <span class="footer-hint">
                    @if($req->status === 'pending')
                        Menunggu persetujuan dari <strong>{{ $req->verifier?->name ?? 'verifikator' }}</strong>
                    @elseif($req->status === 'approved')
                        Pengumuman telah dipublikasikan secara otomatis
                    @elseif($req->status === 'rejected')
                        Pengumuman dikembalikan ke draft. Edit terlebih dahulu jika perlu, lalu klik <strong>Ajukan Kembali</strong>.
                    @else
                        Pengajuan dibatalkan. Klik <strong>Ajukan Kembali</strong> jika ingin mengajukan ulang.
                    @endif
                </span>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- Fix #11: Pesan informatif ketika pengumuman dihapus --}}
                    @if(in_array($req->status, ['rejected', 'cancelled']) && !$req->pengumuman)
                        <span style="font-size:.82rem;color:#9ca3af;font-style:italic;">
                            Pengumuman telah dihapus — tidak dapat diajukan ulang
                        </span>
                    @elseif(in_array($req->status, ['rejected', 'cancelled']) && $req->pengumuman)
                        {{-- Edit dulu, lalu ajukan ulang dari halaman ini --}}
                        <a href="{{ route('manajemenmahasiswa.pengumuman.edit', $req->pengumuman_id) }}"
                            style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;border:1px solid #d1d5db;border-radius:10px;background:#f9fafb;color:#374151;font-size:0.83rem;font-weight:600;text-decoration:none;transition:all .2s;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Edit Pengumuman
                        </a>

                        {{-- Ajukan kembali langsung tanpa edit --}}
                        <form action="{{ route('manajemenmahasiswa.pengumuman.publish', $req->pengumuman_id) }}"
                              method="POST" style="margin:0;"
                              onsubmit="return confirm('Ajukan kembali pengumuman ini untuk diverifikasi?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;border:1px solid #6B4FF4;border-radius:10px;background:#f5f3ff;color:#6B4FF4;font-size:0.83rem;font-weight:600;cursor:pointer;transition:all .2s;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                Ajukan Kembali
                            </button>
                        </form>
                    @endif

                    @if($req->status === 'pending' && $req->pengumuman)
                        <form action="{{ route('manajemenmahasiswa.pengumuman.verification.cancel', $req->pengumuman_id) }}"
                            method="POST"
                            onsubmit="return confirm('Batalkan pengajuan ini? Pengumuman akan kembali ke status draft.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-cancel-req">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                Batalkan Pengajuan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                </svg>
            </div>
            <h6>Belum ada pengajuan verifikasi</h6>
            <p>
                @if($statusFilter === 'all')
                    Anda belum pernah mengajukan verifikasi pengumuman.
                @elseif($statusFilter === 'pending')
                    Tidak ada pengajuan yang sedang menunggu verifikasi.
                @elseif($statusFilter === 'approved')
                    Belum ada pengajuan yang disetujui.
                @elseif($statusFilter === 'rejected')
                    Belum ada pengajuan yang ditolak.
                @else
                    Tidak ada pengajuan yang dibatalkan.
                @endif
            </p>
            <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}"
                style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;color:#6b7280;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all .2s;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Kembali ke Daftar Pengumuman
            </a>
        </div>
    @endforelse

</x-manajemenmahasiswa::layouts.admin>
