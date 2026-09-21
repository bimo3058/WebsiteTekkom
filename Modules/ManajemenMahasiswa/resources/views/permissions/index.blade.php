<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
        @include('manajemenmahasiswa::permissions._styles')
        <style>
            /* ── Stat cards: pola _stats dashboard Super Admin ───────── */
            .mp-stat-row {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 12px; margin-bottom: 10px;
            }
            .mp-stat-card {
                background: #fff; border: 1px solid var(--c-border); border-radius: 12px;
                padding: 12px 14px; text-decoration: none; display: block;
                box-shadow: var(--shadow-card);
                transition: border-color .15s, box-shadow .15s;
            }
            .mp-stat-card:hover { border-color: var(--c-primary-border); box-shadow: 0 4px 14px rgba(11,38,110,.07); }
            .mp-stat-top { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
            .mp-stat-icon {
                width: 28px; height: 28px; border-radius: 8px;
                background: var(--c-primary-subtle); color: var(--c-primary);
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .mp-stat-label { font-size: 12px; font-weight: 500; color: var(--c-fg-muted); margin: 0; }
            .mp-stat-count {
                font-size: 24px; font-weight: 700; color: var(--c-fg);
                line-height: 1; letter-spacing: -.02em; margin: 0;
            }

            /* ── Section per kategori ────────────────────────────────── */
            .mp-section { margin-bottom: 10px; }
            .mp-section-header { display: flex; align-items: center; gap: 8px; margin-bottom:8px; }
            .mp-section-header::before {
                content: ''; display: inline-block; width: 3px; height: 14px;
                border-radius: 2px; background: var(--c-primary);
            }
            .mp-section-header h2 { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; }
            .mp-view-all {
                margin-left: auto; font-size: 12px; font-weight: 600;
                color: var(--c-primary); text-decoration: none;
                display: inline-flex; align-items: center; gap: 4px;
            }
            .mp-view-all:hover { text-decoration: underline; color: var(--c-primary); }
        </style>
    @endpush

    @php
        $categoryRoutes = [
            'Mahasiswa Aktif'   => route('manajemenmahasiswa.pengguna.category', 'Mahasiswa Aktif'),
            'Pengurus Himpunan' => route('manajemenmahasiswa.pengguna.category', 'Pengurus Himpunan'),
            'Alumni'            => route('manajemenmahasiswa.pengguna.category', 'Alumni'),
        ];
        $categorySvg = [
            'Mahasiswa Aktif'   => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
            'Pengurus Himpunan' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
            'Alumni'            => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path>',
        ];
        $totalUsers = collect($categories)->sum(fn ($list) => count($list));
        $isAdmin    = auth()->user()->hasAnyRole(['admin_kemahasiswaan','admin','superadmin']);
    @endphp

    <div class="user-wrap">
        <div class="user-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="user-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0;">Manajemen Pengguna</h1>
                        <p style="font-size:12px; color:var(--c-fg-muted); margin:3px 0 0;">
                            Total <span style="color:var(--c-primary); font-weight:600;">{{ number_format($totalUsers) }}</span> pengguna dalam modul ini
                        </p>
                    </div>

                    @if($isAdmin)
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <button type="button" onclick="openResetPengurusModal()" class="mk-btn mk-btn--secondary mk-btn--sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/>
                                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>
                                </svg>
                                Reset Pengurus
                            </button>
                            <button type="button" onclick="openAlumniModal()" class="mp-btn-primary mk-btn mk-btn--primary mk-btn--sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                                Cek Alumni Otomatis
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="user-box-body">

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mp-flash success">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mp-flash error">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- ── Search ───────────────────────────────── --}}
                <form action="{{ route('manajemenmahasiswa.pengguna.index') }}" method="GET" class="mp-filter-bar">
                    <div class="mp-filter-row">
                        <div class="mp-field mp-field-grow">
                            <label class="mp-label">Cari</label>
                            <div style="position:relative;">
                                <svg class="mp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" name="search" value="{{ $search }}" class="mp-input" placeholder="Nama, NIM, atau email...">
                            </div>
                        </div>

                        <div class="mp-field">
                            <button type="submit" class="mp-btn-primary mk-btn mk-btn--primary mk-btn--sm" style="height:32px;padding:0 16px;">Filter</button>
                        </div>

                        @if($search)
                            <div class="mp-field">
                                <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm" style="height:32px;padding:0 14px;">Reset</a>
                            </div>
                        @endif
                    </div>
                </form>

                {{-- ── Stat Cards ───────────────────────────── --}}
                <div class="mp-stat-row">
                    @foreach($categories as $title => $sectionUsers)
                        <a href="{{ $categoryRoutes[$title] }}" class="mp-stat-card">
                            <div class="mp-stat-top">
                                <div class="mp-stat-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $categorySvg[$title] !!}
                                    </svg>
                                </div>
                                <p class="mp-stat-label">{{ $title }}</p>
                            </div>
                            <p class="mp-stat-count">{{ number_format(count($sectionUsers)) }}</p>
                        </a>
                    @endforeach
                </div>

                {{-- ── Category Sections ────────────────────── --}}
                @foreach($categories as $title => $sectionUsers)
                    <section class="mp-section">
                        <div class="mp-section-header">
                            <h2>{{ $title }}</h2>
                            <a href="{{ $categoryRoutes[$title] }}" class="mp-view-all">
                                Lihat Semua
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>

                        <div class="mp-table-card">
                            @include('manajemenmahasiswa::permissions._table', [
                                'users'           => collect($sectionUsers)->values(),
                                'assignableRoles' => $assignableRoles,
                                'emptyText'       => 'Tidak ada pengguna',
                            ])
                        </div>
                    </section>
                @endforeach

            </div> <!-- end user-box-body -->
        </div> <!-- end user-box -->
    </div> <!-- end user-wrap -->

    {{-- ── Modal Cek Alumni ───────────────────────────── --}}
    <div id="alumniModal" class="mp-modal">
        <div class="mp-modal-box" style="max-width:460px;">
            <div class="mp-modal-head">
                <div class="mp-modal-icon" style="background:var(--c-success-subtle);color:var(--c-success);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                </div>
                <div>
                    <h3>Cek Alumni Otomatis</h3>
                    <p>Mahasiswa angkatan ≤ {{ now()->year - 5 }} akan dijadikan alumni</p>
                </div>
            </div>

            <div class="mp-modal-body">
                <div id="alumniPreview" class="mp-preview-box">
                    <span>Klik "Preview" untuk melihat data...</span>
                </div>
            </div>

            <div class="mp-modal-foot">
                <button type="button" onclick="closeAlumniModal()" class="mk-btn mk-btn--secondary mk-btn--sm">Batal</button>
                <button type="button" onclick="previewAlumni()" class="mk-btn mk-btn--secondary mk-btn--sm">Preview</button>
                <button type="button" id="btnExecAlumni" onclick="execAlumni()" disabled class="mp-btn-primary mk-btn mk-btn--primary mk-btn--sm">Jalankan</button>
            </div>
        </div>
    </div>

    {{-- ── Modal Reset Pengurus Himpunan ──────────────── --}}
    <div id="resetPengurusModal" class="mp-modal">
        <div class="mp-modal-box" style="max-width:500px;">
            <div class="mp-modal-head">
                <div class="mp-modal-icon" style="background:var(--c-error-subtle);color:var(--c-error);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                </div>
                <div>
                    <h3>Reset Seluruh Role Pengurus Himpunan</h3>
                    <p>Semua pengurus akan dikembalikan ke role mahasiswa biasa</p>
                </div>
            </div>

            <div class="mp-modal-body">
                <div class="mp-warning-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>Aksi ini akan menghapus role Ketua Himpunan, Ketua Bidang, Ketua Unit, Staff Himpunan, dan Pengurus Himpunan dari seluruh anggota. Gunakan setiap pergantian periode kepengurusan.</span>
                </div>

                <div id="resetPengurusPreview" class="mp-preview-box">
                    <span>Klik "Preview" untuk melihat daftar pengurus yang akan direset...</span>
                </div>
            </div>

            <div class="mp-modal-foot">
                <button type="button" onclick="closeResetPengurusModal()" class="mk-btn mk-btn--secondary mk-btn--sm">Batal</button>
                <button type="button" onclick="previewResetPengurus()" class="mk-btn mk-btn--secondary mk-btn--sm">Preview</button>
                <button type="button" id="btnExecResetPengurus" onclick="execResetPengurus()" disabled class="mp-btn-primary mk-btn mk-btn--primary mk-btn--sm">Reset Sekarang</button>
            </div>
        </div>
    </div>

    @include('manajemenmahasiswa::permissions._scripts')

    @push('scripts')
    <script>
    // ── Reset Pengurus Modal ──────────────────────────────────────────────────
    function openResetPengurusModal() {
        document.getElementById('resetPengurusModal').classList.add('open');
    }
    function closeResetPengurusModal() {
        document.getElementById('resetPengurusModal').classList.remove('open');
        document.getElementById('resetPengurusPreview').innerHTML =
            '<span>Klik "Preview" untuk melihat daftar pengurus yang akan direset...</span>';
        const btn = document.getElementById('btnExecResetPengurus');
        btn.disabled = true;
        btn.classList.remove('mp-btn-danger');
        btn.classList.add('mp-btn-primary');
    }
    function previewResetPengurus() {
        const preview = document.getElementById('resetPengurusPreview');
        preview.innerHTML = '<span>Memuat...</span>';

        fetch('{{ route('manajemenmahasiswa.pengguna.reset-pengurus') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: true }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                preview.innerHTML = `<span style="color:var(--c-error);">${data.error}</span>`;
                return;
            }
            if (data.count === 0) {
                preview.innerHTML = '<span style="color:var(--c-success);font-weight:600;">Tidak ada pengurus himpunan yang perlu direset.</span>';
                return;
            }
            const rows = data.preview.map(p =>
                `<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;padding:5px 0;border-bottom:1px solid var(--c-border);">
                    <span style="font-size:12px;color:var(--c-fg-sec);font-weight:600;">${p.name}</span>
                    <span style="font-size:11px;color:var(--c-fg-muted);background:#fff;border:1px solid var(--c-border);padding:2px 8px;border-radius:6px;">${p.roles}</span>
                </div>`
            ).join('');
            const more = data.count > 10
                ? `<div style="font-size:11px;color:var(--c-fg-placeholder);padding-top:8px;text-align:center;">...dan ${data.count - 10} pengurus lainnya</div>`
                : '';
            preview.innerHTML = `
                <div style="width:100%;">
                    <p style="font-size:12px;font-weight:700;color:var(--c-error);margin:0 0 10px;">${data.message}</p>
                    <div>${rows}${more}</div>
                </div>`;

            const btn = document.getElementById('btnExecResetPengurus');
            btn.disabled = false;
            btn.classList.remove('mp-btn-primary');
            btn.classList.add('mp-btn-danger');
        })
        .catch(() => {
            preview.innerHTML = '<span style="color:var(--c-error);">Gagal mengambil data.</span>';
        });
    }
    /** Menghapus seluruh role pengurus himpunan. Tidak dapat dibatalkan. */
    async function execResetPengurus() {
        const lanjut = await mkConfirm({
            title: 'Reset Seluruh Pengurus',
            subtitle: 'Aksi ini tidak dapat dibatalkan',
            message: 'Seluruh role pengurus himpunan akan dihapus dan mereka kembali menjadi mahasiswa biasa.\n\nLanjutkan?',
            confirmText: 'Ya, Reset Semua',
        });

        if (!lanjut) return;

        fetch('{{ route('manajemenmahasiswa.pengguna.reset-pengurus') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: false }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeResetPengurusModal();
                location.reload();
            } else {
                mkNotify({ title: 'Gagal', message: data.error || 'Unknown error', variant: 'danger' });
            }
        })
        .catch(() => mkNotify({ title: 'Gagal', message: 'Terjadi kesalahan jaringan.', variant: 'danger' }));
    }

    // ── Alumni Modal ──────────────────────────────────────────────────────────
    function openAlumniModal() {
        document.getElementById('alumniModal').classList.add('open');
    }
    function closeAlumniModal() {
        document.getElementById('alumniModal').classList.remove('open');
        document.getElementById('alumniPreview').innerHTML = '<span>Klik "Preview" untuk melihat data...</span>';
        document.getElementById('btnExecAlumni').disabled = true;
    }
    function previewAlumni() {
        const preview = document.getElementById('alumniPreview');
        preview.innerHTML = '<span>Memuat...</span>';

        fetch('{{ route('manajemenmahasiswa.pengguna.check-alumni') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: true }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                preview.innerHTML = `<span style="color:var(--c-error);">${data.error}</span>`;
                return;
            }
            const names = data.preview.map(p =>
                `<div style="font-size:12px;color:var(--c-fg-sec);padding:3px 0;">${p.name} <span style="color:var(--c-fg-muted);">(${p.nim}, angkatan ${p.cohort_year})</span></div>`
            ).join('');
            const more  = data.count > 10
                ? `<div style="font-size:11px;color:var(--c-fg-placeholder);padding-top:6px;">...dan ${data.count - 10} lainnya</div>`
                : '';
            preview.innerHTML = `
                <div style="width:100%;">
                    <p style="font-size:12px;font-weight:700;color:var(--c-fg);margin:0 0 8px;">${data.message}</p>
                    <div>${names}${more}</div>
                </div>`;

            if (data.count > 0) {
                document.getElementById('btnExecAlumni').disabled = false;
            }
        })
        .catch(() => {
            preview.innerHTML = '<span style="color:var(--c-error);">Gagal mengambil data.</span>';
        });
    }
    /** Mengubah seluruh mahasiswa yang terdeteksi lulus menjadi alumni. */
    async function execAlumni() {
        const lanjut = await mkConfirm({
            title: 'Jadikan Alumni',
            message: 'Yakin ingin mengubah semua mahasiswa yang terdeteksi menjadi alumni?',
            variant: 'primary',
            confirmText: 'Ya, Ubah Semua',
        });

        if (!lanjut) return;

        fetch('{{ route('manajemenmahasiswa.pengguna.check-alumni') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ dry_run: false }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeAlumniModal();
                location.reload();
            } else {
                mkNotify({ title: 'Gagal', message: data.error || 'Unknown error', variant: 'danger' });
            }
        })
        .catch(() => mkNotify({ title: 'Gagal', message: 'Terjadi kesalahan jaringan.', variant: 'danger' }));
    }

    // Tutup modal dengan Escape / klik area gelap
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeAlumniModal(); closeResetPengurusModal(); }
    });
    document.querySelectorAll('.mp-modal').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
    });
    </script>
    @endpush
</x-manajemenmahasiswa::layouts.admin>
