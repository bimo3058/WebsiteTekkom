<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
        @include('manajemenmahasiswa::permissions._styles')
        {{-- Gaya tombol + panel "Filter" (dipakai bersama Direktori, Kegiatan, Pengaduan) --}}
        @include('manajemenmahasiswa::partials.filter-popover')
        <style>
            /* ── Stat cards: pola _stats dashboard Super Admin ───────── */
            .mp-stat-row {
                display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 12px; margin-bottom: 10px;
            }
            /* Kartu status saja (bukan tombol/filter) — tanpa efek hover & kursor tangan */
            .mp-stat-card {
                background: #fff; border: 1px solid var(--c-border); border-radius: 12px;
                padding: 12px 14px; display: block;
                box-shadow: var(--shadow-card);
            }
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

            .mp-table-head {
                display: flex; align-items: center; gap: 8px; margin: 4px 0 8px;
            }
            .mp-table-head::before {
                content: ''; display: inline-block; width: 3px; height: 14px;
                border-radius: 2px; background: var(--c-primary);
            }
            .mp-table-head h2 { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; }
            .mp-table-head span { font-size: 12px; color: var(--c-fg-muted); }
        </style>
    @endpush

    @php
        // Kartu status: jumlah pengguna per kategori (mengikuti filter), bukan filter.
        $kartuKategori = ['semua' => 'Semua Pengguna'] + $kategoriLabels;
        $categorySvg = [
            'semua'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
            'mahasiswa' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
            'pengurus'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
            'alumni'    => '<path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path>',
        ];
        $totalUsers = $categoryTotals['semua'] ?? 0;
        $isAdmin    = auth()->user()->hasAnyRole(['admin_kemahasiswaan','admin','superadmin']);
        $filterPanelAktif = filled($angkatan) || $himpunanFilter !== 'semua' || $alumniFilter !== 'semua';
    @endphp

    <div class="user-wrap">
        <div class="user-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="user-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="Manajemen Pengguna">
                    Total <span style="color:var(--c-primary); font-weight:600;">{{ number_format($totalUsers) }}</span> pengguna dalam modul ini
                    <x-slot:actions>
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
                    </x-slot:actions>
                </x-manajemenmahasiswa::ui.page-header>
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

                {{-- ── Search + Filter ──────────────────────── --}}
                <form action="{{ route('manajemenmahasiswa.pengguna.index') }}" method="GET" class="mp-filter-bar">
                    {{-- Jumlah baris yang sedang dipilih ikut terkirim --}}
                    @if(request()->filled('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
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

                        {{-- Tombol Filter membuka panel angkatan; pola & gaya sama dengan
                             Direktori/Pengaduan (partials/filter-popover). --}}
                        <div class="mp-field">
                            <div class="filter-pop" x-data="{ filterOpen: false }"
                                 @keydown.escape.window="filterOpen = false">
                                <button type="button" class="filter-pop-btn"
                                        @click="filterOpen = !filterOpen"
                                        :class="{ 'is-open': filterOpen }">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
                                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                                    </svg>
                                    <span style="line-height:1;">Filter</span>
                                    @if($filterPanelAktif)
                                        <span class="filter-pop-dot"></span>
                                    @endif
                                </button>

                                <div class="filter-pop-backdrop" x-show="filterOpen" x-cloak style="display:none;"
                                     @click="filterOpen = false"></div>

                                <div class="filter-pop-panel" x-show="filterOpen" x-cloak style="display:none;"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95">

                                    <p class="filter-pop-title">Advanced Filters</p>

                                    <div class="filter-pop-fields">
                                        <div>
                                            <label class="filter-pop-label" for="filterAngkatan">Angkatan</label>
                                            <x-manajemenmahasiswa::ui.select name="angkatan" id="filterAngkatan">
                                                <option value="">Semua Angkatan</option>
                                                @foreach($angkatanList as $ank)
                                                    <option value="{{ $ank }}" @selected((string) $angkatan === (string) $ank)>
                                                        Angkatan {{ $ank }}
                                                    </option>
                                                @endforeach
                                            </x-manajemenmahasiswa::ui.select>
                                        </div>

                                        <div>
                                            <label class="filter-pop-label" for="filterHimpunan">Himpunan</label>
                                            <x-manajemenmahasiswa::ui.select name="himpunan" id="filterHimpunan">
                                                <option value="semua">Semua</option>
                                                <option value="pengurus" @selected($himpunanFilter === 'pengurus')>Pengurus Himpunan</option>
                                                <option value="bukan" @selected($himpunanFilter === 'bukan')>Bukan Pengurus</option>
                                            </x-manajemenmahasiswa::ui.select>
                                        </div>

                                        {{-- Hanya admin — role himpunan tidak mengelola alumni --}}
                                        @if(isset($kategoriLabels['alumni']))
                                            <div>
                                                <label class="filter-pop-label" for="filterAlumni">Alumni</label>
                                                <x-manajemenmahasiswa::ui.select name="alumni" id="filterAlumni">
                                                    <option value="semua">Semua</option>
                                                    <option value="alumni" @selected($alumniFilter === 'alumni')>Alumni</option>
                                                    <option value="bukan" @selected($alumniFilter === 'bukan')>Bukan Alumni</option>
                                                </x-manajemenmahasiswa::ui.select>
                                            </div>
                                        @endif

                                        <div class="filter-pop-actions">
                                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isFiltered)
                            <div class="mp-field">
                                <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm" style="height:32px;padding:0 14px;">Reset</a>
                            </div>
                        @endif
                    </div>
                </form>

                {{-- ── Kartu Status (informasi saja, bukan filter) ──── --}}
                <div class="mp-stat-row">
                    @foreach($kartuKategori as $key => $title)
                        <div class="mp-stat-card">
                            <div class="mp-stat-top">
                                <div class="mp-stat-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $categorySvg[$key] !!}
                                    </svg>
                                </div>
                                <p class="mp-stat-label">{{ $title }}</p>
                            </div>
                            <p class="mp-stat-count">{{ number_format($categoryTotals[$key] ?? 0) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Tabel Pengguna (satu tabel untuk semua kategori) ── --}}
                <div class="mp-table-head">
                    <h2>Daftar Pengguna</h2>
                    <span>{{ number_format($users->total()) }} pengguna</span>
                </div>
                <div class="mp-table-card">
                    @include('manajemenmahasiswa::permissions._table', [
                        'users'           => $users->values(),
                        'assignableRoles' => $assignableRoles,
                        'startIndex'      => ($users->currentPage() - 1) * $users->perPage(),
                        'emptyText'       => 'Tidak ada pengguna yang cocok',
                    ])
                </div>
                {{-- Footer bersama: Per page + Showing X to Y of Z results + nomor halaman --}}
                @include('manajemenmahasiswa::partials.table-footer', [
                    'paginator'  => $users,
                    'standalone' => true,
                ])

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
                    <span>Aksi ini akan menghapus role KAHIM, WAKAHIM, KABID, WAKABID, KANIT, WAKANIT, Staff Himpunan, dan Pengurus Himpunan dari seluruh anggota, sekaligus melepas penempatan bidang/unitnya. Gunakan setiap pergantian periode kepengurusan.</span>
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
        .catch((err) => {
            // Tampilkan penyebab sebenarnya — sebelumnya semua error (termasuk bug di
            // pengolahan data) tampil sebagai "Gagal mengambil data." yang menyesatkan.
            console.error('Preview gagal:', err);
            const span = document.createElement('span');
            span.style.color = 'var(--c-error)';
            span.textContent = 'Gagal memuat preview: ' + (err && err.message ? err.message : 'kesalahan tidak diketahui') + '.';
            preview.replaceChildren(span);
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
            // Seluruh calon alumni ditampilkan (bernomor) dalam daftar yang bisa di-scroll,
            // supaya modal tidak memanjang melebihi layar. Disusun lewat textContent —
            // nama berasal dari data pengguna, jadi tidak disisipkan sebagai HTML.
            const wrap = document.createElement('div');
            wrap.style.width = '100%';

            const judul = document.createElement('p');
            judul.style.cssText = 'font-size:12px;font-weight:700;color:var(--c-fg);margin:0 0 8px;';
            judul.textContent = data.message;
            wrap.appendChild(judul);

            const daftar = document.createElement('ol');
            daftar.style.cssText = 'margin:0;padding:0 4px 0 22px;max-height:300px;overflow-y:auto;';
            data.preview.forEach(p => {
                const li = document.createElement('li');
                li.style.cssText = 'font-size:12px;color:var(--c-fg-sec);padding:3px 0;';
                li.append(p.name + ' ');
                const info = document.createElement('span');
                info.style.color = 'var(--c-fg-muted)';
                info.textContent = `(${p.nim}, angkatan ${p.cohort_year})`;
                li.appendChild(info);
                daftar.appendChild(li);
            });
            wrap.appendChild(daftar);

            preview.replaceChildren(wrap);

            if (data.count > 0) {
                document.getElementById('btnExecAlumni').disabled = false;
            }
        })
        .catch((err) => {
            // Tampilkan penyebab sebenarnya — sebelumnya semua error (termasuk bug di
            // pengolahan data) tampil sebagai "Gagal mengambil data." yang menyesatkan.
            console.error('Preview gagal:', err);
            const span = document.createElement('span');
            span.style.color = 'var(--c-error)';
            span.textContent = 'Gagal memuat preview: ' + (err && err.message ? err.message : 'kesalahan tidak diketahui') + '.';
            preview.replaceChildren(span);
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
