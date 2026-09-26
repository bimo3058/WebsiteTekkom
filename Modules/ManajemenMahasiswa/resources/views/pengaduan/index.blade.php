<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

@include('manajemenmahasiswa::pengaduan.partials.palette')
@include('manajemenmahasiswa::partials.filter-popover')
@if($canDelete)
    @include('manajemenmahasiswa::pengaduan.partials.hapus-script')
@endif

<style>
    /* Pola tabel disamakan dengan User Management SITKOM
       (resources/views/superadmin/users/_table.blade.php). */

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
    .pgd-table tbody tr { transition: background .12s; }
    .pgd-table tbody tr:hover { background: #FAFAFA; }
    .pgd-table tbody td {
        padding: 14px 16px; font-size: 13px; color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6; vertical-align: middle;
    }

    /* Baris "baru" (belum dibuka staff) disorot, seperti baris akun suspend di SITKOM.
       Nada amber mengikuti badge status "Baru". */
    .pgd-table tbody tr.is-baru { background: #FFFCF3; }
    .pgd-table tbody tr.is-baru:hover { background: #FFF7E3; }
    .pgd-new-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--c-warning); flex-shrink: 0; }

    .pgd-judul-row { display: flex; align-items: center; gap: 7px; min-width: 0; }
    .pgd-judul {
        font-weight: 600; color: var(--c-fg); text-decoration: none !important;
        display: block; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .is-baru .pgd-judul { font-weight: 700; }
    .pgd-judul:hover { color: var(--c-primary); }
    .pgd-id { font-family: monospace; font-size: 11px; color: var(--c-fg-muted); margin-top: 1px; }

    /* Kolom Pelapor, meniru kolom User Name SITKOM (avatar + nama). */
    .pgd-pelapor { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .pgd-pelapor-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; }
    .pgd-pelapor-name.is-anon { color: var(--c-fg-sec); }
    .pgd-anon-avatar {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #111827; color: #ffffff;
    }

    .pgd-check { width: 15px; height: 15px; margin: 0; cursor: pointer; accent-color: var(--c-primary); vertical-align: middle; }

    /* ── Bilah aksi massal, meniru #bulkActionBar SITKOM ── */
    .pgd-bulk {
        display: none; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
        background: #1A1A2E; border-radius: 10px; padding: 10px 16px; margin: 0 0 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,.2);
    }
    .pgd-bulk-info { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #ffffff; }
    .pgd-bulk-icon {
        width: 28px; height: 28px; border-radius: 7px; background: var(--c-primary);
        display: flex; align-items: center; justify-content: center;
    }
    .pgd-bulk-count { color: #A5B4FC; font-size: 15px; font-weight: 700; margin-right: 2px; }
    .pgd-bulk-actions { display: flex; align-items: center; gap: 10px; }
    .pgd-bulk-sep { width: 1px; height: 18px; background: rgba(255,255,255,.12); }
    .pgd-bulk-cancel {
        padding: 0 4px; background: none; border: none; cursor: pointer; font-family: inherit;
        font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
        color: rgba(255,255,255,.5); transition: color .15s;
    }
    .pgd-bulk-cancel:hover { color: #ffffff; }
</style>

{{-- ── Header ── --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Layanan Pengaduan">
    @if($isStaff)
        Total <span style="color: var(--c-primary); font-weight: 600;">{{ number_format($pengaduan->total()) }}</span> pengaduan
        @if($baruCount > 0)
            · <span style="color: var(--c-warning); font-weight: 700;">{{ $baruCount }} baru</span>
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

@include('manajemenmahasiswa::pengaduan.partials.alerts')

@php
    $adaFilter = ($filters['q'] ?? '') !== '' || ($filters['kategori'] ?? '') !== '' || ($filters['sort'] ?? 'terbaru') !== 'terbaru';
    $filterPanelAktif = ($filters['kategori'] ?? '') !== '' || ($filters['sort'] ?? 'terbaru') !== 'terbaru';
    $jumlahKolom = $isStaff ? 8 : 5;
@endphp

{{-- ── Bilah aksi massal (staff) ── --}}
@if($isStaff)
    <form id="pgdBulkForm" method="POST" class="pgd-bulk"
          data-url-tercatat="{{ route('manajemenmahasiswa.pengaduan.bulk.tercatat') }}"
          @if($canDelete) data-url-hapus="{{ route('manajemenmahasiswa.pengaduan.bulk.destroy') }}" @endif>
        @csrf
        <input type="hidden" name="_method" value="DELETE" id="pgdBulkMethod" disabled>
        <div class="pgd-bulk-info">
            <span class="pgd-bulk-icon">
                <svg width="13" height="13" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
            </span>
            <span><span class="pgd-bulk-count" id="pgdBulkCount">0</span> pengaduan dipilih</span>
        </div>
        <div class="pgd-bulk-actions">
            <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" data-bulk="tercatat">Tandai Tercatat</button>
            @if($canDelete)
                <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" data-bulk="hapus">Hapus</button>
            @endif
            <span class="pgd-bulk-sep"></span>
            <button type="button" class="pgd-bulk-cancel" data-bulk="batal">Batal</button>
        </div>
    </form>
@endif

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
                        <th style="width: 44px;">
                            <input type="checkbox" class="pgd-check" id="pgdSelectAll" aria-label="Pilih semua pengaduan di halaman ini">
                        </th>
                    @endif
                    <th style="width: 56px;">No</th>
                    <th>Judul</th>
                    @if($isStaff)
                        <th>Pelapor</th>
                    @endif
                    <th>Kategori</th>
                    <th>Status</th>
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
                        $sorotBaru = $isStaff && $item->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_BARU;
                        // Dua baris terbawah membuka menu ke atas supaya tidak terpotong tepi tabel.
                        $menuKeAtas = $loop->count > 3 && $loop->remaining < 2;
                    @endphp
                    <tr class="{{ $sorotBaru ? 'is-baru' : '' }}">
                        @if($isStaff)
                            <td>
                                <input type="checkbox" class="pgd-check pgd-row-check" value="{{ $item->id }}"
                                       aria-label="Pilih pengaduan: {{ $judul }}">
                            </td>
                        @endif
                        <td style="color: var(--c-fg-muted);">{{ $pengaduan->firstItem() + $index }}</td>
                        <td>
                            <div class="pgd-judul-row">
                                @if($sorotBaru)
                                    <span class="pgd-new-dot" title="Baru — belum dibuka"></span>
                                @endif
                                <a href="{{ $detailUrl }}" class="pgd-judul" title="{{ $judul }}">{{ $judul }}</a>
                            </div>
                            <div class="pgd-id">#{{ $item->id }}</div>
                        </td>
                        @if($isStaff)
                            <td>
                                <div class="pgd-pelapor">
                                    @if($item->is_anonim)
                                        <span class="pgd-anon-avatar" title="Identitas dilindungi">
                                            <x-manajemenmahasiswa::ui.icon name="shield-02" size="15" />
                                        </span>
                                        <span class="pgd-pelapor-name is-anon">Konfidensial</span>
                                    @else
                                        <x-ui.user-avatar :user="$item->pelapor" size="sm" />
                                        <span class="pgd-pelapor-name">{{ optional($item->pelapor)->name ?? '—' }}</span>
                                    @endif
                                </div>
                            </td>
                        @endif
                        <td><span class="pgd-pill kategori">{{ $kategoriLabel }}</span></td>
                        <td>@include('manajemenmahasiswa::pengaduan.partials.status-badge', ['pengaduan' => $item])</td>
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
                                         class="mk-menu {{ $menuKeAtas ? 'mk-menu--up' : '' }}" style="display: none;">
                                        <a href="{{ $detailUrl }}" class="mk-menu-item">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            Lihat Detail
                                        </a>
                                        <div class="mk-menu-sep"></div>
                                        <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.toggle.tercatat', $item->id) }}">
                                            @csrf
                                            <button type="submit" class="mk-menu-item">
                                                @if($item->isTercatat())
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                                                    Batalkan Tercatat
                                                @else
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                                    Tandai Tercatat
                                                @endif
                                            </button>
                                        </form>
                                        @if($canDelete)
                                            <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.destroy', $item->id) }}"
                                                  data-judul="{{ $judul }}" onsubmit="return pgdKonfirmasiHapus(this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="mk-menu-item">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
                                                    Hapus Pengaduan
                                                </button>
                                            </form>
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

{{-- Bootstrap JS sudah dimuat layout; tidak dimuat ulang di sini (dulu dobel → latar modal menumpuk). --}}
@if($canCreate)
    @include('manajemenmahasiswa::pengaduan.partials.buat-modal')
@endif

<script>
    document.querySelector('.search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgdFilterForm').submit(); }
    });

    // ── Pilih baris + aksi massal (pola #bulkActionBar SITKOM) ──
    (function () {
        const bar = document.getElementById('pgdBulkForm');
        const semua = document.getElementById('pgdSelectAll');
        if (!bar || !semua) return;

        const hitung = document.getElementById('pgdBulkCount');
        const metode = document.getElementById('pgdBulkMethod');
        const baris = () => Array.from(document.querySelectorAll('.pgd-row-check'));
        const terpilih = () => baris().filter(cb => cb.checked);

        const perbarui = () => {
            const n = terpilih().length;
            hitung.textContent = n;
            bar.style.display = n > 0 ? 'flex' : 'none';
            semua.checked = n > 0 && n === baris().length;
            semua.indeterminate = n > 0 && n < baris().length;
        };

        semua.addEventListener('change', () => {
            baris().forEach(cb => { cb.checked = semua.checked; });
            perbarui();
        });
        baris().forEach(cb => cb.addEventListener('change', perbarui));

        bar.addEventListener('click', (e) => {
            const tombol = e.target.closest('[data-bulk]');
            if (!tombol) return;

            const aksi = tombol.dataset.bulk;
            if (aksi === 'batal') {
                baris().forEach(cb => { cb.checked = false; });
                perbarui();
                return;
            }

            const ids = terpilih().map(cb => cb.value);
            if (!ids.length) return;

            bar.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bar.appendChild(input);
            });

            const hapus = aksi === 'hapus';
            bar.action = hapus ? bar.dataset.urlHapus : bar.dataset.urlTercatat;
            metode.disabled = !hapus;

            mkConfirmSubmit(bar, hapus
                ? ids.length + ' pengaduan yang dipilih akan dihapus dari daftar pengaduan.'
                : ids.length + ' pengaduan yang dipilih akan ditandai tercatat.',
                hapus
                    ? { title: 'Hapus Pengaduan?', variant: 'danger', confirmText: 'Ya, Hapus' }
                    : { title: 'Tandai Tercatat?', variant: 'primary', confirmText: 'Ya, Tandai' });
        });
    })();
</script>
</x-dynamic-component>
