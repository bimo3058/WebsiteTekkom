@props(['page' => 1, 'pageCount' => 1, 'total' => null])
{{-- Deprecated: use capstone::partials.table-footer for numbered pills (Image 2). Kept for BC. --}}
<nav aria-label="Pagination" {{ $attributes->class(['flex flex-wrap items-center justify-between gap-4 text-sm text-on-surface']) }}>
    @if(!is_null($total))<p class="text-on-surface/60">{{ $total }} data</p>@endif
    <div class="flex items-center gap-2">
        <x-btn style="outline" size="sm" @click="goToPage(1)" x-bind:disabled="page <= 1" aria-label="Halaman Pertama"><x-capstone::icon name="CaretLeft" size="14" /><x-capstone::icon name="CaretLeft" size="14" class="-ml-2.5" /></x-btn>
        <x-btn style="outline" size="sm" @click="goToPage(Math.max(1, page - 1))" x-bind:disabled="page <= 1" aria-label="Halaman Sebelumnya"><x-capstone::icon name="CaretLeft" size="14" /></x-btn>
        <span aria-live="polite" class="px-1">Halaman {{ $page }} dari {{ $pageCount }}</span>
        <x-btn style="outline" size="sm" @click="goToPage(Math.min(pageCount, page + 1))" x-bind:disabled="page >= pageCount" aria-label="Halaman Berikutnya"><x-capstone::icon name="CaretRight" size="14" /></x-btn>
        <x-btn style="outline" size="sm" @click="goToPage(pageCount)" x-bind:disabled="page >= pageCount" aria-label="Halaman Terakhir"><x-capstone::icon name="CaretRight" size="14" /><x-capstone::icon name="CaretRight" size="14" class="-ml-2.5" /></x-btn>
    </div>
</nav>
