<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.filter-popover')

<style>
    /* Pola tabel disamakan dengan Direktori Mahasiswa & User Management global. */

    /* ── Tombol utama ── */
    .btn-post {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--c-primary); color: #ffffff !important; border: none;
        border-radius: 8px; padding: 0 16px; height: 36px;
        font-size: 13px; font-weight: 600; white-space: nowrap;
        text-decoration: none !important; transition: background .15s;
    }
    .btn-post:hover { background: var(--c-primary-hover); }

    /* ── Search ── */
    .search-wrapper { position: relative; width: min(220px, calc(100vw - 200px)); min-width: 120px; }
    .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: var(--c-fg-placeholder);
    }
    .search-input {
        background: #ffffff; border: 1px solid var(--c-border); border-radius: 8px;
        height: 34px; padding-left: 34px; font-size: 12px; font-weight: 500;
        width: 100%; color: var(--c-fg);
    }
    .search-input:focus {
        border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); outline: none;
    }

    .btn-reset {
        height: 34px; padding: 0 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer; white-space: nowrap;
        text-decoration: none !important; border: 1px solid var(--c-border);
        background: #ffffff; color: var(--c-fg-sec); box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .btn-reset:hover { background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg); }

    /* ── Kartu tabel ── */
    .table-card {
        background: #ffffff; border: 1px solid var(--c-border);
        border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .table-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px; border-bottom: 1px solid var(--c-border);
        gap: 10px; flex-wrap: wrap;
    }
    .table-toolbar-title { font-size: 14px; font-weight: 700; color: var(--c-fg); margin: 0; flex-shrink: 0; }
    .table-toolbar-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 0; }

    .pgd-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .pgd-table thead th {
        background: #FAFAFA; padding: 11px 16px; font-size: 11px; font-weight: 600;
        color: var(--c-fg-muted); border-bottom: 1px solid var(--c-border); white-space: nowrap;
    }
    .pgd-table tbody tr { transition: background .15s; }
    .pgd-table tbody tr:hover { background: #FAFAFA; }
    .pgd-table tbody td {
        padding: 14px 16px; font-size: 13px; color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6; vertical-align: middle;
    }

    .pgd-judul {
        font-weight: 600; color: var(--c-fg); text-decoration: none !important;
        display: block; max-width: 340px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .pgd-judul:hover { color: var(--c-primary); }
    .pgd-id { font-family: monospace; font-size: 11px; color: var(--c-fg-muted); margin-top: 1px; }

    /* ── Badge ── */
    .pill {
        font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
        display: inline-block; white-space: nowrap;
    }
    .pill-kategori { background: var(--c-primary-subtle); color: var(--c-primary); }
    .pill-baru { background: var(--c-warning-subtle); color: var(--c-warning); }
    .pill-tercatat { background: var(--c-success-subtle); color: var(--c-success); }
    .pill-anonim { background: #111827; color: #ffffff; }

    /* Dot "baru" (belum dibuka staff) */
    .baru-dot {
        display: inline-block; width: 9px; height: 9px; border-radius: 50%;
        background: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.15);
    }

    /* Checkbox tercatat */
    .tercatat-check {
        width: 17px; height: 17px; cursor: pointer; accent-color: var(--c-primary); margin: 0;
    }
    .tercatat-check:disabled { opacity: .5; cursor: progress; }

    /* ── Menu aksi ── */
</style>

{{-- ── Header ── --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Layanan Pengaduan">
    @if($isStaff)
        {{ number_format($pengaduan->total()) }} pengaduan
        @if($baruCount > 0)
            · <span style="color:#2563eb;font-weight:700;">{{ $baruCount }} baru</span>
        @endif
    @else
        Sampaikan keluhan Anda; tim akan mencatat dan menindaklanjutinya.
    @endif

    <x-slot:actions>
        @if($canCreate)
            <button type="button" class="mk-btn mk-btn--primary" data-bs-toggle="modal" data-bs-target="#buatPengaduanModal">
                <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Buat Pengaduan
            </button>
        @endif
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

{{-- ── Flash ── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $adaFilter = ($filters['q'] ?? '') !== '' || ($filters['kategori'] ?? '') !== '' || ($filters['sort'] ?? 'terbaru') !== 'terbaru';
    $filterPanelAktif = ($filters['kategori'] ?? '') !== '' || ($filters['sort'] ?? 'terbaru') !== 'terbaru';
    $jumlahKolom = $isStaff ? 8 : 5;
@endphp

<div class="table-card filter-pop-host">
    <div class="table-toolbar">
        <h2 class="table-toolbar-title">Daftar Pengaduan</h2>
        <form method="GET" action="{{ route('manajemenmahasiswa.pengaduan.index') }}" id="pgdFilterForm" class="table-toolbar-form">
            @if(request()->filled('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" name="q" class="search-input" value="{{ $filters['q'] }}"
                       placeholder="Cari judul, kronologi, atau ID…">
            </div>

            <div class="filter-pop" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($filterPanelAktif)
                        <span class="filter-pop-dot"></span>
                    @endif
                </button>

                <div class="filter-pop-backdrop" x-show="filterOpen" x-cloak style="display: none;"
                     @click="filterOpen = false"></div>

                <div class="filter-pop-panel" x-show="filterOpen" x-cloak style="display: none;"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    <p class="filter-pop-title">Advanced Filters</p>

                    <div class="filter-pop-fields">
                        <div>
                            <label class="filter-pop-label" for="filterKategori">Kategori</label>
                            <x-manajemenmahasiswa::ui.select name="kategori" id="filterKategori">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoriOptions as $value => $meta)
                                    <option value="{{ $value }}" {{ $filters['kategori'] === $value ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterSort">Urutkan</label>
                            <x-manajemenmahasiswa::ui.select name="sort" id="filterSort">
                                <option value="terbaru" {{ $filters['sort'] === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="terlama" {{ $filters['sort'] === 'terlama' ? 'selected' : '' }}>Terlama</option>
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilter)
                                <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="pgd-table">
            <thead>
                <tr>
                    @if($isStaff)
                        <th style="width: 44px; text-align: center;" title="Tercatat">✓</th>
                    @endif
                    <th style="width: 56px;">No</th>
                    <th>Judul</th>
                    @if($isStaff)
                        <th>Pelapor</th>
                    @endif
                    <th>Kategori</th>
                    @if($isStaff)
                        <th style="width: 28px;"></th>
                    @else
                        <th>Status</th>
                    @endif
                    <th>Tanggal</th>
                    @if($isStaff)
                        <th style="width: 72px; text-align: center;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduan as $index => $item)
                    @php
                        $judul = data_get($item, 'data_template.judul') ?: '-';
                        $kategoriUtama = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori((string) $item->kategori);
                        $kategoriLabel = data_get($kategoriOptions, $kategoriUtama . '.label') ?? ucwords(str_replace('_', ' ', $kategoriUtama));
                        $detailUrl = route('manajemenmahasiswa.pengaduan.show', $item->id);
                        $tercatat = in_array($item->status, ['tercatat', 'selesai', 'didelegasikan'], true);
                    @endphp
                    <tr data-row="{{ $item->id }}">
                        @if($isStaff)
                            <td style="text-align: center;">
                                <input type="checkbox" class="tercatat-check"
                                       data-url="{{ route('manajemenmahasiswa.pengaduan.toggle.tercatat', $item->id) }}"
                                       aria-label="Tandai tercatat: {{ $judul }}"
                                       {{ $tercatat ? 'checked' : '' }}>
                            </td>
                        @endif
                        <td style="color: var(--c-fg-muted); font-weight: 500;">{{ $pengaduan->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ $detailUrl }}" class="pgd-judul" title="{{ $judul }}">{{ $judul }}</a>
                            <div class="pgd-id">#{{ $item->id }}</div>
                        </td>
                        @if($isStaff)
                            <td>
                                @if($item->is_anonim)
                                    <span class="pill pill-anonim">Konfidensial</span>
                                @else
                                    {{ optional($item->pelapor)->name ?? '—' }}
                                @endif
                            </td>
                        @endif
                        <td><span class="pill pill-kategori">{{ $kategoriLabel }}</span></td>
                        @if($isStaff)
                            <td style="text-align: center;">
                                <span class="baru-dot js-baru-dot" title="Baru — belum dibuka"
                                      @if($item->status !== 'baru') style="display: none;" @endif></span>
                            </td>
                        @else
                            <td>
                                @if($item->status === 'baru')
                                    <span class="pill pill-baru">Baru</span>
                                @elseif($tercatat)
                                    <span class="pill pill-tercatat">Tercatat</span>
                                @endif
                            </td>
                        @endif
                        <td style="white-space: nowrap; color: var(--c-fg-sec);">{{ optional($item->created_at)->translatedFormat('d M Y, H:i') }}</td>
                        @if($isStaff)
                            <td style="text-align: center;">
                                <div style="position: relative; display: inline-block;" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" @click.outside="open = false" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Aksi">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="mk-menu" style="display: none;">
                                        <a href="{{ $detailUrl }}" class="mk-menu-item">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Lihat Detail
                                        </a>
                                        @if($canDelete)
                                            <div class="mk-menu-sep"></div>
                                            <button type="button" class="mk-menu-item js-hapus"
                                                    data-action="{{ route('manajemenmahasiswa.pengaduan.destroy', $item->id) }}"
                                                    data-judul="{{ $judul }}">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $jumlahKolom }}" style="padding: 60px 24px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #E5E7EB;">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                @if($adaFilter)
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted); margin: 0;">Tidak ada pengaduan yang cocok</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder); margin: 0;">Coba kata kunci lain, atau kosongkan filternya.</p>
                                    <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm mt-1">Reset pencarian &amp; filter</a>
                                @else
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted); margin: 0;">Belum ada pengaduan</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder); margin: 0;">Data pengaduan akan muncul di sini.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('manajemenmahasiswa::partials.table-footer', ['paginator' => $pengaduan])
</div>

{{-- ── Delete Modal ── --}}
@if($canDelete)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18);">
                <div class="modal-body text-center p-4 p-md-5">
                    <div style="margin-bottom: 16px; color: #f59e0b;">
                        <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="48" />
                    </div>
                    <h4 class="fw-bold text-dark mb-3">Hapus Pengaduan?</h4>
                    <p class="text-muted mb-4" id="deleteModalText" style="font-size: 14px;"></p>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="mk-btn mk-btn--primary">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@if($canCreate)
    @include('manajemenmahasiswa::pengaduan.partials.buat-modal')
@endif

<script>
    document.querySelector('.search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgdFilterForm').submit(); }
    });

    // Hapus: isi modal konfirmasi
    document.querySelectorAll('.js-hapus').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deleteForm').action = btn.dataset.action;
            document.getElementById('deleteModalText').textContent = btn.dataset.judul;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        });
    });

    // Centang "tercatat" — AJAX, dikembalikan bila gagal
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    document.querySelectorAll('.tercatat-check').forEach(cb => {
        cb.addEventListener('change', async () => {
            const dicentang = cb.checked;
            cb.disabled = true;
            try {
                const res = await fetch(cb.dataset.url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error(res.status);
                const data = await res.json();
                cb.checked = data.status === 'tercatat';
                // Tercatat = sudah ditangani, dot "baru" ikut hilang
                cb.closest('tr').querySelector('.js-baru-dot').style.display = data.status === 'baru' ? '' : 'none';
            } catch (e) {
                cb.checked = !dicentang;
                mkNotify({ title: 'Gagal Memperbarui', message: 'Gagal memperbarui status pengaduan. Silakan coba lagi.', variant: 'danger' });
            } finally {
                cb.disabled = false;
            }
        });
    });
</script>
</x-dynamic-component>
