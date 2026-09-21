<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
           (resources/views/components/sidebar.blade.php). Layout modul ini tidak
           mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
        :root {
            --c-primary: #0B266E;
            --c-primary-hover: #091958;
            --c-primary-subtle: rgba(11, 38, 110, 0.08);
            --c-primary-border: #5C78B8;
            --c-bg: #F6F8FA;
            --c-fg: #0D0D12;
            --c-fg-sec: #353849;
            --c-fg-muted: #666D80;
            --c-fg-placeholder: #808897;
            --c-border: #DFE1E7;
            --c-border-strong: #C1C7CF;
            --c-success: #287F6E;
            --c-success-subtle: #DDF2EE;
            --c-error: #DF1C41;
            --c-error-subtle: #FADAE1;
            --c-warning: #956321;
            --c-warning-subtle: #F9ECCB;
            --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
        }

        .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

        /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
        .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5)); overflow: hidden; width: 100%; box-sizing: border-box; }
        .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border, #DFE1E7); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
        .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong, #C1C7CF); border-radius: 10px; }
        @media (max-width: 767px) {
            .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
            .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
            .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
            .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
            .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
        }

        /* ── Stat cards: pola _stats dashboard Super Admin ──────────── */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap:10px; margin-bottom: 10px; }
        @media (max-width: 768px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .stat-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: #fff;
            border: 1px solid var(--c-border, #DFE1E7);
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
            transition: border-color .15s, box-shadow .15s;
        }
        .stat-card:hover {
            border-color: var(--c-primary-border, #5C78B8);
            box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
        }
        .stat-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .stat-icon {
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--c-primary-subtle, #EEF1F8); color: var(--c-primary, #0B266E);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-label { font-size: 12px; font-weight: 500; color: var(--c-fg-muted, #666D80); margin: 0; }
        .stat-value {
            font-size: 24px; font-weight: 700; color: var(--c-fg, #0D0D12);
            line-height: 1; letter-spacing: -.02em; margin: 0;
        }

        /* ── Tab filter ─────────────────────────────────────────────── */
        .rv-tabs {
            display: flex; gap: 4px; flex-wrap: wrap;
            margin-bottom: 10px;
            border-bottom: 1px solid var(--c-border, #DFE1E7);
        }
        .rv-tab {
            padding: 8px 14px;
            font-size: 12px; font-weight: 600;
            color: var(--c-fg-muted, #666D80);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color .15s, border-color .15s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .rv-tab:hover { color: var(--c-primary, #0B266E); }
        .rv-tab.active { color: var(--c-primary, #0B266E); border-bottom-color: var(--c-primary, #0B266E); }
        .tab-badge {
            background: var(--c-bg, #F6F8FA);
            color: var(--c-fg-muted, #666D80);
            padding: 2px 7px; border-radius: 9999px;
            font-size: 11px; font-weight: 700;
        }
        .rv-tab.active .tab-badge {
            background: var(--c-primary-subtle, #EEF1F8);
            color: var(--c-primary, #0B266E);
        }

        /* ── Flash messages ─────────────────────────────────────────── */
        .alert-success,
        .alert-error {
            border-radius: 10px; padding: 12px 16px;
            font-size: 12px; font-weight: 500; margin-bottom: 10px;
        }
        .alert-success {
            background: var(--c-success-subtle, #DDF2EE);
            color: var(--c-success, #287F6E);
            border: 1px solid var(--c-success, #287F6E);
        }
        .alert-error {
            background: var(--c-error-subtle, #FADAE1);
            color: var(--c-error, #DF1C41);
            border: 1px solid var(--c-error, #DF1C41);
        }

        /* ── Kartu pengajuan ────────────────────────────────────────── */
        .request-card {
            background: #fff;
            border: 1px solid var(--c-border, #DFE1E7);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 8px;
            box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
            transition: border-color .15s, box-shadow .15s;
        }
        .request-card:hover {
            border-color: var(--c-primary-border, #5C78B8);
            box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
        }

        /* Garis kiri berwarna sesuai status */
        .request-card.status-pending   { border-left: 3px solid var(--c-warning, #956321); }
        .request-card.status-approved  { border-left: 3px solid var(--c-success, #287F6E); }
        .request-card.status-rejected  { border-left: 3px solid var(--c-error, #DF1C41); }
        .request-card.status-cancelled { border-left: 3px solid var(--c-border-strong, #C1C7CF); }

        .request-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 14px; margin-bottom: 10px;
        }
        .request-title {
            font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12);
            margin-bottom: 6px; line-height: 1.3;
        }
        .request-title a { color: inherit; text-decoration: none; transition: color .15s; }
        .request-title a:hover { color: var(--c-primary, #0B266E); }

        .badge-baru {
            display: inline-flex; align-items: center;
            padding: 3px 8px; border-radius: 8px;
            font-size: 10px; font-weight: 700; letter-spacing: .03em;
            margin-left: 6px; vertical-align: middle;
        }
        .badge-baru-approved { background: var(--c-success-subtle, #DDF2EE); color: var(--c-success, #287F6E); }
        .badge-baru-rejected { background: var(--c-error-subtle, #FADAE1);   color: var(--c-error, #DF1C41); }

        .request-meta {
            display: flex; flex-wrap: wrap; gap: 12px;
            font-size: 11.5px; color: var(--c-fg-muted, #666D80);
        }
        .request-meta-item { display: flex; align-items: center; gap: 5px; }
        .request-meta-item svg { color: var(--c-fg-placeholder, #808897); flex-shrink: 0; }

        /* ── Status badge ───────────────────────────────────────────── */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 9px; border-radius: 8px;
            font-size: 11px; font-weight: 700; letter-spacing: .02em;
            flex-shrink: 0;
        }
        .badge-pending   { background: var(--c-warning-subtle, #F9ECCB); color: var(--c-warning, #956321); }
        .badge-approved  { background: var(--c-success-subtle, #DDF2EE); color: var(--c-success, #287F6E); }
        .badge-rejected  { background: var(--c-error-subtle, #FADAE1);   color: var(--c-error, #DF1C41); }
        .badge-cancelled { background: var(--c-bg, #F6F8FA);             color: var(--c-fg-muted, #666D80); }

        /* ── Kotak pesan & catatan ──────────────────────────────────── */
        .info-box {
            border-radius: 8px; padding: 10px 14px;
            font-size: 12px; line-height: 1.5; margin-bottom: 10px;
        }
        .info-box-pesan {
            background: var(--c-bg, #F6F8FA);
            border: 1px solid var(--c-border, #DFE1E7);
            color: var(--c-fg-sec, #353849);
        }
        .info-box-catatan-approved {
            background: var(--c-success-subtle, #DDF2EE);
            border: 1px solid var(--c-success, #287F6E);
            color: var(--c-success, #287F6E);
        }
        .info-box-catatan-rejected {
            background: var(--c-error-subtle, #FADAE1);
            border: 1px solid var(--c-error, #DF1C41);
            color: var(--c-error, #DF1C41);
        }
        .info-box-label {
            font-weight: 700; font-size: 11.5px; margin-bottom: 4px;
            display: flex; align-items: center; gap: 5px;
        }

        /* ── Footer kartu ───────────────────────────────────────────── */
        .request-footer {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
            padding-top: 12px; margin-top: 12px;
            border-top: 1px solid var(--c-border, #DFE1E7);
        }
        .footer-hint { font-size: 11.5px; color: var(--c-fg-muted, #666D80); }
        .footer-note { font-size: 11.5px; color: var(--c-fg-placeholder, #808897); font-style: italic; }
        .footer-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

        .btn-sm-action {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 12px; border-radius: 8px;
            border: 1px solid var(--c-border, #DFE1E7);
            background: #fff; color: var(--c-fg-sec, #353849);
            font-size: 12px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: all .15s; font-family: inherit;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .btn-sm-action:hover {
            background: var(--c-bg, #F6F8FA);
            border-color: var(--c-border-strong, #C1C7CF);
            color: var(--c-fg-sec, #353849);
        }
        .btn-sm-primary { color: var(--c-primary, #0B266E); }
        .btn-sm-primary:hover {
            background: var(--c-primary-subtle, #EEF1F8);
            border-color: var(--c-primary-border, #5C78B8);
            color: var(--c-primary, #0B266E);
        }
        .btn-sm-danger { color: var(--c-error, #DF1C41); }
        .btn-sm-danger:hover {
            background: var(--c-error-subtle, #FADAE1);
            border-color: var(--c-error, #DF1C41);
            color: var(--c-error, #DF1C41);
        }

        /* ── Empty state ────────────────────────────────────────────── */
        .empty-state { text-align: center; padding: 44px 20px; color: var(--c-fg-muted, #666D80); }
        .empty-state-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--c-primary-subtle, #EEF1F8); color: var(--c-primary, #0B266E);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-state h6 { font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12); margin-bottom: 4px; }
        .empty-state p  { font-size: 12px; margin-bottom: 10px; }

        @media (max-width: 640px) {
            .request-header { flex-direction: column; }
        }
    </style>
    @endpush

    @php
        $totalAll = $stats['pending'] + $stats['approved'] + $stats['rejected'] + $stats['cancelled'];

        $statCards = [
            ['label' => 'Menunggu',        'value' => $stats['pending'],  'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
            ['label' => 'Disetujui',       'value' => $stats['approved'], 'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
            ['label' => 'Ditolak',         'value' => $stats['rejected'], 'icon' => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'],
            ['label' => 'Total Pengajuan', 'value' => $totalAll,          'icon' => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>'],
        ];

        $tabs = [
            'all'       => ['label' => 'Semua',      'count' => $totalAll],
            'pending'   => ['label' => 'Menunggu',   'count' => $stats['pending']],
            'approved'  => ['label' => 'Disetujui',  'count' => $stats['approved']],
            'rejected'  => ['label' => 'Ditolak',    'count' => $stats['rejected']],
            'cancelled' => ['label' => 'Dibatalkan', 'count' => $stats['cancelled']],
        ];
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">Status Verifikasi</h1>
                            <span style="font-size:10px; font-weight:600; color:var(--c-primary, #0B266E); background:rgba(11,38,110,0.09); border:1px solid rgba(11,38,110,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Modul Mahasiswa</span>
                        </div>
                        <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                            Pantau status pengajuan verifikasi pengumuman yang pernah Anda kirimkan
                        </p>
                    </div>

                    <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="dash-box-body">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif

                {{-- ── Stat Cards ───────────────────────────── --}}
                <div class="stat-grid">
                    @foreach($statCards as $card)
                        <div class="stat-card">
                            <div class="stat-card-top">
                                <div class="stat-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $card['icon'] !!}
                                    </svg>
                                </div>
                                <p class="stat-label">{{ $card['label'] }}</p>
                            </div>
                            <p class="stat-value">{{ number_format($card['value']) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Tab Filter ───────────────────────────── --}}
                <div class="rv-tabs">
                    @foreach($tabs as $value => $tab)
                        <a href="{{ route('manajemenmahasiswa.pengumuman.riwayat.verifikasi', ['status' => $value]) }}"
                           class="rv-tab {{ $statusFilter === $value ? 'active' : '' }}">
                            {{ $tab['label'] }}
                            <span class="tab-badge">{{ $tab['count'] }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- ── Request List ─────────────────────────── --}}
                @forelse($requests as $req)
                    @php
                        $statusClass = 'status-' . $req->status;
                        // Deteksi request yang baru diproses dalam 24 jam terakhir
                        $recentlyProcessed = in_array($req->status, ['approved','rejected'])
                            && $req->verified_at?->gt(now()->subHours(24));
                    @endphp
                    <div class="request-card {{ $statusClass }}">
                        <div class="request-header">
                            <div style="min-width:0; flex:1;">
                                <div class="request-title">
                                    @if($req->pengumuman)
                                        <a href="{{ route('manajemenmahasiswa.pengumuman.show', $req->pengumuman_id) }}">
                                            {{ $req->pengumuman->judul }}
                                        </a>
                                    @else
                                        <span style="color:var(--c-fg-placeholder, #808897);">(Pengumuman telah dihapus)</span>
                                    @endif
                                    @if($recentlyProcessed)
                                        <span class="badge-baru {{ $req->status === 'approved' ? 'badge-baru-approved' : 'badge-baru-rejected' }}">
                                            {{ $req->status === 'approved' ? 'BARU DISETUJUI' : 'BARU DITOLAK' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="request-meta">
                                    <span class="request-meta-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        Verifikator: <strong>{{ $req->verifier?->name ?? 'N/A' }}</strong>
                                    </span>
                                    <span class="request-meta-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        Diajukan {{ $req->created_at->diffForHumans() }}
                                    </span>
                                    @if($req->verified_at)
                                        <span class="request-meta-item">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
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
                                @if($req->status === 'pending')    Menunggu
                                @elseif($req->status === 'approved') Disetujui
                                @elseif($req->status === 'rejected') Ditolak
                                @else Dibatalkan @endif
                            </span>
                        </div>

                        {{-- Pesan yang dikirim ke verifikator --}}
                        @if($req->pesan_pengaju)
                            <div class="info-box info-box-pesan">
                                <div class="info-box-label">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    Pesan Anda ke verifikator:
                                </div>
                                {{ $req->pesan_pengaju }}
                            </div>
                        @endif

                        {{-- Catatan dari verifikator — berlaku untuk pengajuan ini saja --}}
                        @if($req->catatan_verifikator)
                            <div class="info-box {{ $req->status === 'approved' ? 'info-box-catatan-approved' : 'info-box-catatan-rejected' }}">
                                <div class="info-box-label">
                                    @if($req->status === 'approved')
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        Catatan persetujuan dari {{ $req->verifier?->name ?? 'verifikator' }}:
                                    @else
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                        Alasan penolakan dari {{ $req->verifier?->name ?? 'verifikator' }}
                                        <span style="font-size:11px;font-weight:400;opacity:.75;">(pengajuan ini)</span>:
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

                            <div class="footer-actions">
                                @if(in_array($req->status, ['rejected', 'cancelled']) && !$req->pengumuman)
                                    <span class="footer-note">
                                        Pengumuman telah dihapus — tidak dapat diajukan ulang
                                    </span>
                                @elseif(in_array($req->status, ['rejected', 'cancelled']) && $req->pengumuman)
                                    {{-- Edit dulu, lalu ajukan ulang dari halaman ini --}}
                                    <a href="{{ route('manajemenmahasiswa.pengumuman.edit', $req->pengumuman_id) }}"
                                        class="mk-btn mk-btn--secondary mk-btn--sm">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit Pengumuman
                                    </a>

                                    {{-- Ajukan kembali langsung tanpa edit --}}
                                    <form action="{{ route('manajemenmahasiswa.pengumuman.publish', $req->pengumuman_id) }}"
                                          method="POST" style="margin:0;"
                                          onsubmit="return mkConfirmSubmit(this, 'Ajukan kembali pengumuman ini untuk diverifikasi?', { title: 'Ajukan Ulang', variant: 'primary', confirmText: 'Ya, Ajukan' })">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                            </svg>
                                            Ajukan Kembali
                                        </button>
                                    </form>
                                @endif

                                @if($req->status === 'pending' && $req->pengumuman)
                                    <form action="{{ route('manajemenmahasiswa.pengumuman.verification.cancel', $req->pengumuman_id) }}"
                                        method="POST" style="margin:0;"
                                        onsubmit="return mkConfirmSubmit(this, 'Batalkan pengajuan ini? Pengumuman akan kembali ke status draft.', { title: 'Batalkan Pengajuan', variant: 'warning', confirmText: 'Ya, Batalkan' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mk-btn mk-btn--secondary mk-btn--sm">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
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
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
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
                        <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                            </svg>
                            Kembali ke Daftar Pengumuman
                        </a>
                    </div>
                @endforelse

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

</x-manajemenmahasiswa::layouts.admin>
