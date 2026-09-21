{{--
    Footer tabel/daftar: "Per page [10 ⌄] | Showing X to Y of Z results" + nomor halaman.

    Disamakan dengan footer tabel Audit Log global
    (resources/views/superadmin/audit-logs/_table.blade.php). Ditulis sebagai CSS biasa
    karena layout modul ini tidak memuat Tailwind. Semua navigasi memakai link biasa
    (bukan JS), jadi filter/pencarian/tab yang sedang aktif ikut terbawa lewat query
    string. Footer hanya tampil bila ada data — saat kosong, keterangannya sudah ada
    di keadaan kosong milik halamannya.

    Variabel include:
      $paginator       LengthAwarePaginator (wajib)
      $perPageOptions  daftar pilihan "Per page"; bawaan PerPage::TABEL,
                       untuk grid kartu pakai PerPage::KARTU
      $perPageParam    nama parameter query; bawaan 'per_page'
      $standalone      true = bilah berdiri sendiri (bergaris + membulat) untuk daftar kartu
                       yang tidak dibungkus kartu tabel; bawaan false

    Controller wajib memakai PerPage::resolve() dengan daftar yang sama.
--}}
@if($paginator->total() > 0)
    @php
        $tfOptions    = $perPageOptions ?? \Modules\ManajemenMahasiswa\Support\PerPage::TABEL;
        $tfParam      = $perPageParam ?? 'per_page';
        $tfStandalone = $standalone ?? false;

        $tfPager   = $paginator->withQueryString();
        $tfPerPage = $paginator->perPage();
        $tfCurrent = $paginator->currentPage();
        $tfLast    = $paginator->lastPage();

        // Nomor halaman: halaman aktif ±2, halaman pertama & terakhir selalu tampil
        $tfStart = max(1, $tfCurrent - 2);
        $tfEnd   = min($tfLast, $tfCurrent + 2);
    @endphp

    {{-- Gaya ditaruh SEBELUM footer supaya footer tetap elemen terakhir di kartunya
         (aturan :last-child di bawah bergantung pada itu). --}}
    @once
        <style>
            .tbl-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                padding: 12px 16px;
                background: #ffffff;
                border-top: 1px solid var(--c-border);
            }
            /* Footer paling bawah di dalam kartu tabel: ikut lengkung sudut kartunya */
            .tbl-foot:last-child:not(.tbl-foot--standalone) { border-radius: 0 0 13px 13px; }
            /* Bilah mandiri di bawah grid kartu / daftar kartu (tanpa kartu tabel pembungkus) */
            .tbl-foot--standalone {
                margin: 20px 0 8px;
                border: 1px solid var(--c-border);
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            }
            .tbl-foot-info {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }
            .tbl-foot-perpage {
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .tbl-foot-label {
                font-size: 12px;
                font-weight: 500;
                color: var(--c-fg-muted);
            }
            .tbl-foot-divider {
                width: 1px;
                height: 14px;
                background: var(--c-border);
            }
            .tbl-foot-showing {
                font-size: 12px;
                color: var(--c-fg-sec);
            }
            .tbl-foot-showing strong {
                font-weight: 700;
                color: var(--c-fg);
            }

            /* Dropdown "Per page" — terbuka ke atas supaya tidak keluar dari kartu */
            .perpage-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                height: 28px;
                padding: 0 8px;
                background: #ffffff;
                border: 1px solid var(--c-border);
                border-radius: 6px;
                color: var(--c-fg);
                font-family: inherit;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                cursor: pointer;
                box-sizing: border-box;
                transition: border-color 0.15s;
            }
            .perpage-btn.is-open { border-color: var(--c-primary); }
            .perpage-btn svg { color: var(--c-fg-muted); transition: transform 0.15s; }
            .perpage-btn.is-open svg { transform: rotate(180deg); }
            .perpage-menu {
                position: absolute;
                left: 0;
                bottom: calc(100% + 5px);
                z-index: 50;
                min-width: 80px;
                background: #ffffff;
                border: 1px solid var(--c-border);
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            .perpage-opt {
                display: block;
                padding: 7px 14px;
                font-size: 12px;
                font-weight: 500;
                color: var(--c-fg-sec);
                text-decoration: none !important;
                transition: background 0.12s;
            }
            .perpage-opt:hover { background: var(--c-bg); color: var(--c-fg-sec); }
            .perpage-opt.is-current {
                font-weight: 700;
                color: var(--c-primary);
                background: var(--c-primary-subtle);
            }

            /* Tombol nomor halaman & panah prev/next */
            .tbl-pager {
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .pg-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border: 1px solid var(--c-border);
                border-radius: 6px;
                background: #ffffff;
                color: var(--c-fg-sec);
                font-size: 12px;
                font-weight: 500;
                text-decoration: none !important;
                transition: all 0.15s;
            }
            a.pg-btn:hover { background: var(--c-bg); color: var(--c-fg-sec); }
            .pg-btn.is-active {
                background: var(--c-primary);
                border-color: var(--c-primary);
                color: #ffffff;
                font-weight: 700;
                box-shadow: 0 2px 6px rgba(11, 38, 110, 0.25);
            }
            .pg-btn.is-disabled {
                border-color: #F3F4F6;
                background: #FAFAFA;
                color: #D1D5DB;
                cursor: not-allowed;
            }
            .pg-dots {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                font-size: 12px;
                color: var(--c-fg-muted);
            }
        </style>
    @endonce

    <div class="tbl-foot {{ $tfStandalone ? 'tbl-foot--standalone' : '' }}">
        <div class="tbl-foot-info">
            <div class="tbl-foot-perpage" x-data="{ open: false }">
                <span class="tbl-foot-label">Per page</span>
                <div style="position: relative;" @keydown.escape.window="open = false">
                    <button type="button" class="perpage-btn"
                            @click="open = !open" @click.outside="open = false"
                            :class="{ 'is-open': open }">
                        <span>{{ $tfPerPage }}</span>
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="perpage-menu" x-show="open" x-cloak style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100">
                        @foreach($tfOptions as $opt)
                            <a href="{{ request()->fullUrlWithQuery([$tfParam => $opt, $paginator->getPageName() => null]) }}"
                               class="perpage-opt {{ $tfPerPage === $opt ? 'is-current' : '' }}">{{ $opt }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="tbl-foot-divider"></div>

            <span class="tbl-foot-showing">
                Showing <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
                to <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                of <strong>{{ number_format($paginator->total()) }}</strong> results
            </span>
        </div>

        @if($tfLast > 1)
            <nav class="tbl-pager" aria-label="Navigasi halaman">
                {{-- Prev --}}
                @if($tfCurrent > 1)
                    <a href="{{ $tfPager->url($tfCurrent - 1) }}" class="pg-btn" aria-label="Halaman sebelumnya">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @else
                    <span class="pg-btn is-disabled" aria-disabled="true">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </span>
                @endif

                {{-- Halaman pertama + titik-titik --}}
                @if($tfStart > 1)
                    <a href="{{ $tfPager->url(1) }}" class="pg-btn">1</a>
                    @if($tfStart > 2)<span class="pg-dots">…</span>@endif
                @endif

                {{-- Jendela di sekitar halaman aktif --}}
                @for($p = $tfStart; $p <= $tfEnd; $p++)
                    @if($p === $tfCurrent)
                        <span class="pg-btn is-active" aria-current="page">{{ $p }}</span>
                    @else
                        <a href="{{ $tfPager->url($p) }}" class="pg-btn">{{ $p }}</a>
                    @endif
                @endfor

                {{-- Titik-titik + halaman terakhir --}}
                @if($tfEnd < $tfLast)
                    @if($tfEnd < $tfLast - 1)<span class="pg-dots">…</span>@endif
                    <a href="{{ $tfPager->url($tfLast) }}" class="pg-btn">{{ $tfLast }}</a>
                @endif

                {{-- Next --}}
                @if($tfCurrent < $tfLast)
                    <a href="{{ $tfPager->url($tfCurrent + 1) }}" class="pg-btn" aria-label="Halaman berikutnya">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                @else
                    <span class="pg-btn is-disabled" aria-disabled="true">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                @endif
            </nav>
        @endif
    </div>
@endif
