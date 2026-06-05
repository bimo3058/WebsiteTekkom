@props([
    'currentPage' => 1,
    'totalPages'  => 1,
    'jsCallback'  => 'goToPage',
])

@php
    $currentPage = (int) $currentPage;
    $totalPages  = (int) $totalPages;

    // Tidak render apapun jika hanya 1 halaman atau kurang
    if ($totalPages <= 1) {
        return;
    }

    if ($totalPages < 10) {
        // Tampilkan maks 5 halaman dengan currentPage sebagai anchor tengah
        $start = max(1, $currentPage - 2);
        $end   = min($totalPages, $start + 4);
        $start = max(1, $end - 4); // koreksi jika end terlalu kecil
        $pages = range($start, $end);
    } else {
        // 3 halaman pertama + ellipsis + 2 halaman terakhir
        $pages = [1, 2, 3, '...', $totalPages - 1, $totalPages];
    }
@endphp

<div class="pagination-list">
    {{-- Tombol Previous --}}
    <button
        type="button"
        class="pagination-btn"
        onclick="{{ $jsCallback }}({{ max(1, $currentPage - 1) }})"
        {{ $currentPage === 1 ? 'disabled' : '' }}
    >&lsaquo;</button>

    {{-- Tombol halaman --}}
    @foreach ($pages as $page)
        @if ($page === '...')
            <span class="pagination-ellipsis">...</span>
        @else
            <button
                type="button"
                class="pagination-btn {{ (int) $page === $currentPage ? 'active' : '' }}"
                onclick="{{ $jsCallback }}({{ $page }})"
            >{{ $page }}</button>
        @endif
    @endforeach

    {{-- Tombol Next --}}
    <button
        type="button"
        class="pagination-btn"
        onclick="{{ $jsCallback }}({{ min($totalPages, $currentPage + 1) }})"
        {{ $currentPage === $totalPages ? 'disabled' : '' }}
    >&rsaquo;</button>
</div>
