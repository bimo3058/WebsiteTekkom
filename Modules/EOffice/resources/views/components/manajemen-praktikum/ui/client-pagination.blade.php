@props(['options' => [10, 20, 30], 'separator' => false])
<x-eoffice::manajemen-praktikum.ui.pagination-styles />
<nav class="mp-pagination" @if($separator) style="box-shadow:inset 0 -1px 0 #DFE1E7" @endif aria-label="Navigasi halaman tabel" x-data="{
    get paginationPages() {
        const last = Math.max(1, this.totalPages);
        const numbers = [...new Set([1, last, ...Array.from({ length: 5 }, (_, i) => this.page + i - 2).filter(n => n >= 1 && n <= last)])].sort((a, b) => a - b);
        return numbers.flatMap((n, i) => i > 0 && n - numbers[i - 1] > 1 ? ['gap-' + n, n] : [n]);
    }
}">
    <div class="mp-pagination-info">
        <div class="mp-pagination-size">Per halaman
            <div class="mp-pagination-dropdown" x-data="{ open: false }" @keydown.escape.stop.prevent="open = false" @click.outside="open = false">
                <button type="button" class="mp-pagination-select" @click="open = !open" :aria-expanded="open" aria-label="Jumlah data per halaman">
                    <span x-text="perPage"></span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" :style="open ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="mp-pagination-options" x-show="open" x-cloak x-transition.opacity.duration.100ms>
                    @foreach($options as $size)
                        <button type="button" class="mp-pagination-option" :class="{ 'is-selected': perPage === {{ $size }} }" @click="perPage = {{ $size }}; page = 1; open = false">{{ $size }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <span class="mp-pagination-summary">Menampilkan <strong x-text="visibleRows.length ? (page - 1) * perPage + 1 : 0"></strong> sampai <strong x-text="Math.min(page * perPage, visibleRows.length)"></strong> dari <strong x-text="visibleRows.length"></strong> data</span>
    </div>
    <div class="mp-pagination-pages">
        <button type="button" class="mp-page-button" @click="page = Math.max(1, page - 1)" :disabled="page <= 1" aria-label="Halaman sebelumnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-left" /></button>
        <template x-for="number in paginationPages" :key="number">
            <span>
                <template x-if="typeof number === 'string'"><span class="mp-page-gap" aria-hidden="true">…</span></template>
                <template x-if="typeof number === 'number'"><button type="button" class="mp-page-button" :class="{ 'is-current': page === number }" :aria-current="page === number ? 'page' : null" :aria-label="'Halaman ' + number" @click="page = number" x-text="number"></button></template>
            </span>
        </template>
        <button type="button" class="mp-page-button" @click="page = Math.min(totalPages, page + 1)" :disabled="totalPages === 0 || page >= totalPages" aria-label="Halaman berikutnya"><x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" /></button>
    </div>
</nav>
