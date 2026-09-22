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

            /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
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

            /* ── Penghitung pending di header ───────────────────────────── */
            .pending-counter {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 6px 12px;
                background: var(--c-warning-subtle, #F9ECCB);
                border: 1px solid var(--c-warning, #956321);
                border-radius: 8px;
                font-size: 12px; font-weight: 600;
                color: var(--c-warning, #956321);
            }
            .pending-counter .counter-num {
                background: var(--c-warning, #956321); color: #fff;
                min-width: 20px; height: 20px; padding: 0 6px;
                border-radius: 9999px;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px; font-weight: 700;
            }

            /* ── Tab filter ─────────────────────────────────────────────── */
            .verif-tabs {
                display: flex; gap: 4px; flex-wrap: wrap;
                margin-bottom: 10px;
                border-bottom: 1px solid var(--c-border, #DFE1E7);
            }
            .verif-tab {
                padding: 8px 14px;
                font-size: 12px; font-weight: 600;
                color: var(--c-fg-muted, #666D80);
                text-decoration: none;
                border-bottom: 2px solid transparent;
                margin-bottom: -1px;
                transition: color .15s, border-color .15s;
                display: inline-flex; align-items: center; gap: 6px;
            }
            .verif-tab:hover { color: var(--c-primary, #0B266E); }
            .verif-tab.active {
                color: var(--c-primary, #0B266E);
                border-bottom-color: var(--c-primary, #0B266E);
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
            .request-card-header {
                display: flex; align-items: flex-start; justify-content: space-between;
                gap: 14px; margin-bottom: 10px;
            }
            .request-card-title {
                font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12);
                margin-bottom: 6px; line-height: 1.3;
            }
            .request-card-title a { color: inherit; text-decoration: none; transition: color .15s; }
            .request-card-title a:hover { color: var(--c-primary, #0B266E); }

            .badge-baru {
                display: inline-flex; align-items: center;
                padding: 3px 8px; border-radius: 8px;
                font-size: 10px; font-weight: 700; letter-spacing: .03em;
                background: var(--c-success-subtle, #DDF2EE); color: var(--c-success, #287F6E);
                margin-left: 6px; vertical-align: middle;
            }

            .request-meta {
                display: flex; flex-wrap: wrap; gap: 12px;
                font-size: 11px; color: var(--c-fg-muted, #666D80);
            }
            .request-meta-item { display: flex; align-items: center; gap: 5px; }
            .request-meta-item svg { color: var(--c-fg-placeholder, #808897); flex-shrink: 0; }

            .request-pesan {
                background: var(--c-bg, #F6F8FA);
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 12px; color: var(--c-fg-sec, #353849);
                margin-bottom: 10px; line-height: 1.5;
            }
            .request-pesan .pesan-label {
                font-weight: 700; color: var(--c-fg, #0D0D12);
                font-size: 11px; margin-bottom: 4px;
                display: flex; align-items: center; gap: 5px;
            }

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

            /* ── Area aksi ──────────────────────────────────────────────── */
            .request-actions {
                display: flex; align-items: flex-end; gap: 10px;
                padding-top: 12px;
                border-top: 1px solid var(--c-border, #DFE1E7);
            }
            .catatan-input-group { flex: 1; }
            .catatan-input-group label {
                font-size: 12px; font-weight: 600; color: var(--c-fg-sec, #353849);
                display: block; margin-bottom: 6px;
            }
            .catatan-input-group label .hint { color: var(--c-fg-placeholder, #808897); font-weight: 400; }
            .catatan-input {
                width: 100%;
                padding: 9px 12px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                font-size: 13px; color: var(--c-fg-sec, #353849);
                background: #fff; outline: none; font-family: inherit;
                resize: vertical; min-height: 38px;
                transition: border-color .15s, box-shadow .15s;
            }
            .catatan-input::placeholder { color: var(--c-fg-placeholder, #808897); }
            .catatan-input:focus {
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }

            .action-buttons { display: flex; gap: 8px; flex-shrink: 0; }
            .btn-approve {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 16px; border-radius: 8px;
                border: 1px solid var(--c-success, #287F6E);
                background: var(--c-success, #287F6E); color: #fff;
                font-size: 12px; font-weight: 600; cursor: pointer;
                transition: all .15s; font-family: inherit;
                box-shadow: 0 2px 6px rgba(40, 127, 110, 0.25);
            }
            .btn-approve:hover { background: #1F6657; border-color: #1F6657; box-shadow: 0 4px 12px rgba(40, 127, 110, 0.35); }
            .btn-reject {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 16px; border-radius: 8px;
                border: 1px solid var(--c-border, #DFE1E7);
                background: #fff; color: var(--c-error, #DF1C41);
                font-size: 12px; font-weight: 600; cursor: pointer;
                transition: all .15s; font-family: inherit;
                box-shadow: 0 1px 2px rgba(0,0,0,.04);
            }
            .btn-reject:hover {
                background: var(--c-error-subtle, #FADAE1);
                border-color: var(--c-error, #DF1C41);
            }

            /* ── Catatan verifikator ────────────────────────────────────── */
            .verifikator-catatan {
                background: var(--c-warning-subtle, #F9ECCB);
                border: 1px solid var(--c-warning, #956321);
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 12px; color: var(--c-warning, #956321);
                margin-top: 12px; line-height: 1.5;
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
            .empty-state p  { font-size: 12px; margin-bottom: 0; }

            @media (max-width: 640px) {
                .request-card-header { flex-direction: column; }
                .request-actions { flex-direction: column; align-items: stretch; }
                .action-buttons { justify-content: flex-end; }
            }
        </style>
    @endpush

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="Verifikasi Pengumuman"
                    badge="Modul Mahasiswa"
                    subtitle="Kelola pengajuan verifikasi pengumuman dari staff himpunan">
                    <x-slot:actions>
                        @if($pendingCount > 0)
                            <div class="pending-counter">
                                <div class="counter-num">{{ $pendingCount }}</div>
                                <span>Menunggu Verifikasi</span>
                            </div>
                        @endif
                    </x-slot:actions>
                </x-manajemenmahasiswa::ui.page-header>
            </div>

            <div class="dash-box-body">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert-error">{{ session('error') }}</div>
                @endif

                {{-- ── Tab Filter ───────────────────────────── --}}
                <div class="verif-tabs">
                    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'pending']) }}"
                       class="verif-tab {{ $statusFilter === 'pending' ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Menunggu
                    </a>
                    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'approved']) }}"
                       class="verif-tab {{ $statusFilter === 'approved' ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Disetujui
                    </a>
                    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'rejected']) }}"
                       class="verif-tab {{ $statusFilter === 'rejected' ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Ditolak
                    </a>
                    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'all']) }}"
                       class="verif-tab {{ $statusFilter === 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                </div>

                {{-- ── Request List ─────────────────────────── --}}
                @forelse($requests as $req)
                    @php $isNewRequest = $req->created_at?->gt(now()->subHours(24)); @endphp
                    <div class="request-card">
                        <div class="request-card-header">
                            <div style="min-width:0; flex:1;">
                                <div class="request-card-title">
                                    @if($req->pengumuman)
                                        <a href="{{ route('manajemenmahasiswa.pengumuman.show', $req->pengumuman_id) }}">
                                            {{ $req->pengumuman->judul }}
                                        </a>
                                    @else
                                        <span style="color:var(--c-fg-placeholder, #808897);">(Pengumuman telah dihapus)</span>
                                    @endif
                                    {{-- Penanda request yang masuk dalam 24 jam terakhir --}}
                                    @if($isNewRequest && $req->status === 'pending')
                                        <span class="badge-baru">BARU</span>
                                    @endif
                                </div>
                                <div class="request-meta">
                                    <span class="request-meta-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        {{ $req->requester?->name ?? 'Unknown' }}
                                    </span>
                                    <span class="request-meta-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $req->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <span class="status-badge
                                @if($req->status === 'pending') badge-pending
                                @elseif($req->status === 'approved') badge-approved
                                @elseif($req->status === 'rejected') badge-rejected
                                @else badge-cancelled @endif">
                                @if($req->status === 'pending') Menunggu
                                @elseif($req->status === 'approved') Disetujui
                                @elseif($req->status === 'rejected') Ditolak
                                @else Dibatalkan @endif
                            </span>
                        </div>

                        {{-- Pesan dari staff --}}
                        @if($req->pesan_pengaju)
                            <div class="request-pesan">
                                <div class="pesan-label">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    Pesan dari pengaju:
                                </div>
                                {{ $req->pesan_pengaju }}
                            </div>
                        @endif

                        {{-- Catatan verifikator (jika sudah diproses) --}}
                        @if($req->catatan_verifikator)
                            <div class="verifikator-catatan">
                                <strong>Catatan Anda:</strong> {{ $req->catatan_verifikator }}
                                <br><small>Diproses: {{ $req->verified_at?->diffForHumans() }}</small>
                            </div>
                        @endif

                        {{-- Action Area (hanya untuk pending) --}}
                        @if($req->isPending())
                            <div class="request-actions">
                                <div class="catatan-input-group">
                                    <label for="catatan-{{ $req->id }}">Catatan <span class="hint">(wajib jika menolak)</span></label>
                                    <textarea class="catatan-input" id="catatan-{{ $req->id }}" placeholder="Tulis catatan verifikasi..." rows="1"></textarea>
                                </div>
                                <div class="action-buttons">
                                    <form action="{{ route('manajemenmahasiswa.pengumuman.verifikasi.reject', $req->id) }}" method="POST" class="d-inline"
                                        onsubmit="return handleReject(this, {{ $req->id }})">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="catatan" class="catatan-hidden-{{ $req->id }}">
                                        <button type="submit" class="mk-btn mk-btn--secondary">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Tolak
                                        </button>
                                    </form>
                                    <form action="{{ route('manajemenmahasiswa.pengumuman.verifikasi.approve', $req->id) }}" method="POST" class="d-inline"
                                        onsubmit="return handleApprove(this, {{ $req->id }})">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="catatan" class="catatan-hidden-{{ $req->id }}">
                                        <button type="submit" class="mk-btn mk-btn--primary">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <h6>Tidak ada pengajuan</h6>
                        <p>
                            @if($statusFilter === 'pending')
                                Belum ada pengumuman yang menunggu verifikasi Anda.
                            @elseif($statusFilter === 'approved')
                                Belum ada pengumuman yang Anda setujui.
                            @elseif($statusFilter === 'rejected')
                                Belum ada pengumuman yang Anda tolak.
                            @else
                                Belum ada pengajuan verifikasi yang masuk.
                            @endif
                        </p>
                    </div>
                @endforelse

                @if($isAdmin && $requests->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    @push('scripts')
    <script>
        /**
         * Sync textarea value to hidden input before approve/reject submit.
         */
        function syncCatatan(reqId) {
            const textarea = document.getElementById('catatan-' + reqId);
            const hiddenInputs = document.querySelectorAll('.catatan-hidden-' + reqId);
            hiddenInputs.forEach(input => {
                input.value = textarea ? textarea.value : '';
            });
        }

        /**
         * Menyetujui pengumuman: meminta konfirmasi lewat dialog modul, lalu mengirim form.
         *
         * Selalu mengembalikan false supaya submit bawaan tertahan — mkConfirmSubmit yang
         * mengirim formnya sendiri setelah pengguna menyetujui.
         */
        function handleApprove(form, reqId) {
            syncCatatan(reqId);

            return mkConfirmSubmit(form, 'Setujui pengumuman ini? Pengumuman akan langsung dipublikasikan.', {
                title: 'Setujui Pengumuman',
                variant: 'success',
                confirmText: 'Ya, Setujui',
            });
        }

        /**
         * Menolak pengumuman: catatan wajib diisi lebih dulu, lalu konfirmasi.
         */
        function handleReject(form, reqId) {
            syncCatatan(reqId);
            const textarea = document.getElementById('catatan-' + reqId);
            if (!textarea || !textarea.value.trim()) {
                textarea.focus();
                textarea.style.borderColor = '#DF1C41';
                textarea.style.boxShadow = '0 0 0 3px rgba(223,28,65,0.12)';
                mkNotify({
                    title: 'Catatan Belum Diisi',
                    message: 'Catatan wajib diisi saat menolak pengumuman.',
                    variant: 'warning',
                });
                return false;
            }

            return mkConfirmSubmit(form, 'Tolak pengumuman ini? Pengumuman akan dikembalikan ke status draft.', {
                title: 'Tolak Pengumuman',
                confirmText: 'Ya, Tolak',
            });
        }
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
