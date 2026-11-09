@props(['paginator', 'perPageName' => 'per_page', 'options' => [5, 10, 20, 30, 50], 'fragment' => null])
<x-eoffice::manajemen-praktikum.ui.pagination-styles />
@php
    $pageName = $paginator->getPageName();
    $paginator->appends(request()->except($pageName));
    if ($fragment) $paginator->fragment($fragment);
    $sizes = collect($options)->push($paginator->perPage())->unique()->sort()->values();
    $current = $paginator->currentPage();
    $last = max(1, $paginator->lastPage());
    $windowPage = max(1, min($current, $last));
    $pages = collect([1, $last])->merge(range(max(1, $windowPage - 2), min($last, $windowPage + 2)))->unique()->sort()->values();
@endphp
<nav class="mp-pagination" aria-label="Navigasi halaman tabel">
    <div class="mp-pagination-info">
        <div class="mp-pagination-size">Per halaman
            <div class="mp-pagination-dropdown" x-data="{ open: false }" @keydown.escape.stop.prevent="open = false" @click.outside="open = false">
                <button type="button" class="mp-pagination-select" @click="open = !open" :aria-expanded="open" aria-label="Jumlah data per halaman">
                    <span>{{ $paginator->perPage() }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" :style="open ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="mp-pagination-options" x-show="open" x-cloak x-transition.opacity.duration.100ms>
                    @foreach($sizes as $size)
                        @php
                            $sizeUrl = request()->url() . '?' . http_build_query(array_merge(request()->except([$perPageName, $pageName]), [$perPageName => $size, $pageName => 1]));
                            if ($fragment) $sizeUrl .= '#' . $fragment;
                        @endphp
                        <a href="{{ $sizeUrl }}" @class(['mp-pagination-option', 'is-selected' => $paginator->perPage() == $size])>{{ $size }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <span class="mp-pagination-summary">Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong> sampai <strong>{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong>{{ $paginator->total() }}</strong> data</span>
    </div>
    <div class="mp-pagination-pages">
        @if($paginator->onFirstPage())<span class="mp-page-button" aria-disabled="true" aria-label="Halaman sebelumnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-left" /></span>
        @else<a class="mp-page-button" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-left" /></a>@endif
        @php $previous = 0; @endphp
        @foreach($pages as $number)
            @if($previous && $number - $previous > 1)<span class="mp-page-gap" aria-hidden="true">…</span>@endif
            @if($number === $current)<span class="mp-page-button is-current" aria-current="page" aria-label="Halaman {{ $number }}">{{ $number }}</span>
            @else<a class="mp-page-button" href="{{ $paginator->url($number) }}" aria-label="Halaman {{ $number }}">{{ $number }}</a>@endif
            @php $previous = $number; @endphp
        @endforeach
        @if($paginator->hasMorePages())<a class="mp-page-button" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" /></a>
        @else<span class="mp-page-button" aria-disabled="true" aria-label="Halaman berikutnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" /></span>@endif
    </div>
</nav>
