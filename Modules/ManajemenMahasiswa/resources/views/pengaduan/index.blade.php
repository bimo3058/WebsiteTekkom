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
    /* Padding sel 12px (kolom tepi 16px) + kolom yang bisa memanjang dibatasi, supaya
       seluruh kolom — termasuk Tanggal & Aksi — muat di layar laptop 1280px tanpa
       harus menggeser tabel ke kanan. */
    .pgd-table thead th {
        background: #FAFAFA; padding: 11px 12px; font-size: 11px; font-weight: 600;
        color: var(--c-fg-muted); border-bottom: 1px solid var(--c-border); white-space: nowrap;
    }
    .pgd-table tbody tr { transition: background .12s; }
    .pgd-table tbody tr:hover { background: #FAFAFA; }
    .pgd-table tbody td {
        padding: 14px 12px; font-size: 13px; color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6; vertical-align: middle;
    }
    .pgd-table th:first-child, .pgd-table td:first-child { padding-left: 16px; }
    .pgd-table th:last-child, .pgd-table td:last-child { padding-right: 16px; }

    /* Kategori panjang dipotong dengan elipsis; label lengkap ada di tooltip. */
    .pgd-table .pgd-pill.kategori {
        display: inline-block; max-width: 180px; overflow: hidden; text-overflow: ellipsis;
        vertical-align: middle;
    }

    /* Tanggal dua baris: tanggal di atas, jam di bawah. */
    .pgd-tgl { white-space: nowrap; color: var(--c-fg-sec); line-height: 1.35; }
    .pgd-jam { font-size: 11px; color: var(--c-fg-muted); }

    /* Judul boleh dua baris lalu dipotong, alih-alih satu baris panjang yang melebarkan tabel. */
    .pgd-judul {
        font-weight: 600; color: var(--c-fg); text-decoration: none !important;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; max-width: 240px; min-width: 120px; line-height: 1.4; overflow-wrap: anywhere;
    }
    .pgd-judul:hover { color: var(--c-primary); }
    .pgd-id { font-family: monospace; font-size: 11px; color: var(--c-fg-muted); margin-top: 1px; }

    /* Kolom Pelapor, meniru kolom User Name SITKOM (avatar + nama). */
    .pgd-pelapor { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .pgd-pelapor-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
    .pgd-pelapor-name.is-anon { color: var(--c-fg-sec); }
    .pgd-anon-avatar {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #111827; color: #ffffff;
    }

    /* Menu aksi "⋯" memakai posisi fixed (dihitung saat dibuka) supaya tidak terpotong
       pembungkus tabel ber-overflow — dulu saat daftar hanya 1–3 baris, menu tertutup
       footer tabel sehingga "Tandai Tercatat" tidak bisa diklik. */
    .pgd-table .mk-menu.pgd-menu-fixed { position: fixed; z-index: 1050; }

    /* Sengaja tanpa kolom centang & aksi massal: "tercatat" berarti tiket sudah dibaca
       satu per satu, dan aduan (termasuk PPKS) tidak dihapus beramai-ramai. */
</style>

{{-- ── Header ── --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Layanan Pengaduan">
    @if($isStaff)
        Total <span style="color: var(--c-primary); font-weight: 600;">{{ number_format($totalCount) }}</span> pengaduan
        @if($baruCount > 0)
            · <span style="color: var(--c-warning); font-weight: 700;">{{ $baruCount }} baru</span>
        @endif
    @elseif($kuotaPesan)
        <span id="pgdKuotaInfo" style="color: var(--c-warning); font-weight: 600;">{{ $kuotaPesan }}</span>
    @else
        Sampaikan keluhan Anda; tim akan mencatat dan menindaklanjutinya.
    @endif

    <x-slot:actions>
        @if($canCreate)
            @if($kuotaPesan)
                {{-- Kuota harian habis: tombol mati, alasannya di subjudul. --}}
                <button type="button" class="mk-btn mk-btn--primary" disabled aria-describedby="pgdKuotaInfo">
                    <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Buat Pengaduan
                </button>
            @else
                <button type="button" class="mk-btn mk-btn--primary" data-bs-toggle="modal" data-bs-target="#buatPengaduanModal">
                    <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Buat Pengaduan
                </button>
            @endif
        @endif
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

@include('manajemenmahasiswa::pengaduan.partials.alerts')

@php
    $filterPanelAktif = $filters['kategori'] !== '' || $filters['status'] !== '';
    $adaFilter = $filters['q'] !== '' || $filterPanelAktif;
    // Pelapor tidak melihat status tiket (kolom Status & filternya khusus staff).
    $jumlahKolom = $isStaff ? 7 : 4;
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
                            {{-- "semua", bukan nilai kosong: nilai kosong digambar komponen
                                 sebagai placeholder abu, jadi warnanya beda dari kotak lain. --}}
                            <x-manajemenmahasiswa::ui.select name="kategori" id="filterKategori">
                                <option value="semua">Semua Kategori</option>
                                @foreach($kategoriOptions as $value => $meta)
                                    <option value="{{ $value }}" @selected($filters['kategori'] === $value)>{{ $meta['label'] }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        @if($isStaff)
                            <div>
                                <label class="filter-pop-label" for="filterStatus">Status</label>
                                <x-manajemenmahasiswa::ui.select name="status" id="filterStatus">
                                    <option value="semua">Semua Status</option>
                                    <option value="baru" @selected($filters['status'] === 'baru')>Baru</option>
                                    <option value="tercatat" @selected($filters['status'] === 'tercatat')>Tercatat</option>
                                </x-manajemenmahasiswa::ui.select>
                            </div>
                        @endif

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
                    <th style="width: 56px;">No</th>
                    <th>Judul</th>
                    @if($isStaff)
                        <th>Pelapor</th>
                    @endif
                    <th>Kategori</th>
                    @if($isStaff)
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
                    @endphp
                    <tr>
                        <td style="color: var(--c-fg-muted);">{{ $pengaduan->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ $detailUrl }}" class="pgd-judul" title="{{ $judul }}">{{ $judul }}</a>
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
                        <td><span class="pgd-pill kategori" title="{{ $kategoriLabel }}">{{ $kategoriLabel }}</span></td>
                        @if($isStaff)
                            <td>@include('manajemenmahasiswa::pengaduan.partials.status-badge', ['pengaduan' => $item])</td>
                        @endif
                        <td class="pgd-tgl">
                            {{ optional($item->created_at)->translatedFormat('d M Y') }}
                            <div class="pgd-jam">{{ optional($item->created_at)->translatedFormat('H:i') }} WIB</div>
                        </td>
                        @if($isStaff)
                            <td style="text-align: center;">
                                <div style="position: relative; display: inline-block;" x-data="pgdAksiMenu"
                                     @scroll.window.capture="open = false" @resize.window="open = false">
                                    <button type="button" @click="toggle($el)" @click.outside="open = false" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Aksi">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak :style="posisi"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="mk-menu pgd-menu-fixed" style="display: none;">
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
    // Menu aksi "⋯" per baris. Posisinya dihitung dari tombol saat dibuka (position:
    // fixed), jadi tidak terpotong pembungkus tabel; bila ruang di bawah tombol tidak
    // cukup, menu dibuka ke atas. Ditutup saat halaman digulir/diubah ukurannya.
    document.addEventListener('alpine:init', () => {
        Alpine.data('pgdAksiMenu', () => ({
            open: false,
            posisi: {},
            toggle(tombol) {
                this.open = !this.open;
                if (!this.open) return;

                const r = tombol.getBoundingClientRect();
                const layar = document.documentElement;
                const keAtas = layar.clientHeight - r.bottom < 150 && r.top > 150;
                this.posisi = {
                    right: (layar.clientWidth - r.right) + 'px',
                    top: keAtas ? 'auto' : (r.bottom + 5) + 'px',
                    bottom: keAtas ? (layar.clientHeight - r.top + 5) + 'px' : 'auto',
                };
            },
        }));
    });

    document.querySelector('.search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgdFilterForm').submit(); }
    });
</script>
</x-dynamic-component>
