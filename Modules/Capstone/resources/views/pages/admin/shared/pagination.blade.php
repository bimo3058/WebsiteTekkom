<div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm text-muted-foreground">
    <span x-text="filtered.length+' data'"></span>
    <div class="flex items-center gap-3"><x-capstone::select-menu x-model.number="pageSize" @select-menu-change.window="page=1" :options="[10,25,50]" size="sm" dropup aria-label="Baris per halaman" class="min-w-20" /><x-btn style="outline" size="sm" @click="page=Math.max(1,page-1)" x-bind:disabled="page<=1">Sebelumnya</x-btn><span x-text="Math.min(page,pageCount)+' / '+pageCount"></span><x-btn style="outline" size="sm" @click="page++" x-bind:disabled="page>=pageCount">Berikutnya</x-btn></div>
</div>
