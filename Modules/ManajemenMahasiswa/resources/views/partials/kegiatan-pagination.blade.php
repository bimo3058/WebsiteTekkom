@if($paginator->hasPages())
    <div class="mk-pagination mt-4">
        <nav class="mk-pagination__nav" aria-label="Navigasi halaman">
            @if($paginator->onFirstPage())
                <span class="mk-page-btn mk-page-btn--nav is-disabled" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <a href="{{ $paginator->withQueryString()->previousPageUrl() }}" class="mk-page-btn mk-page-btn--nav" rel="prev" aria-label="Halaman sebelumnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            @foreach($paginator->withQueryString()->links()->offsetGet('elements') as $element)
                @if(is_string($element))
                    <span class="mk-page-btn mk-page-btn--dots" aria-hidden="true">…</span>
                @elseif(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $paginator->currentPage())
                            <span class="mk-page-btn is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="mk-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->withQueryString()->nextPageUrl() }}" class="mk-page-btn mk-page-btn--nav" rel="next" aria-label="Halaman berikutnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span class="mk-page-btn mk-page-btn--nav is-disabled" aria-disabled="true" aria-label="Halaman berikutnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </nav>
        <div class="mk-pagination__info">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </div>
    </div>

    <style>
        .mk-pagination { display:flex; flex-direction:column; align-items:center; gap:8px; }
        .mk-pagination__nav { display:flex; align-items:center; gap:4px; }
        .mk-page-btn {
            display:inline-flex; align-items:center; justify-content:center;
            min-width:34px; height:34px; padding:0 10px; border-radius:8px;
            font-size:13px; font-weight:600; color:var(--c-fg-sec);
            background:#fff; border:1px solid var(--c-border);
            text-decoration:none !important; transition:all .15s; cursor:pointer;
        }
        .mk-page-btn:hover:not(.is-disabled):not(.is-active) {
            background:var(--c-bg); border-color:var(--c-primary); color:var(--c-primary);
        }
        .mk-page-btn.is-active { background:var(--c-primary); border-color:var(--c-primary); color:#fff !important; cursor:default; }
        .mk-page-btn--nav { color:var(--c-fg-muted); }
        .mk-page-btn--nav.is-disabled { opacity:.35; cursor:not-allowed; }
        .mk-page-btn--dots { min-width:24px; padding:0; border-color:transparent; background:transparent; color:var(--c-fg-muted); cursor:default; }
        .mk-pagination__info { font-size:12px; color:var(--c-fg-muted); font-weight:500; }
    </style>
@endif
