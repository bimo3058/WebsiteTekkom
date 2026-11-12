<div class="flex flex-wrap items-center gap-2.5 rounded-xl border border-border bg-white p-4 shadow-xs">
    <x-capstone::select-menu x-model="periodId" @select-menu-change.window="page=1;load()" :options="['all'=>'Semua periode']" placeholder="Semua periode" aria-label="Filter periode" />
    <div class="relative min-w-48 flex-1">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
        <input type="search" x-model="search" @input.debounce.500ms="page=1;load()" @keydown.enter="load()" placeholder="Cari..." aria-label="Cari data" class="h-9 w-full rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
    </div>
    <x-btn style="outline" @click="load()" x-bind:disabled="loading || saving"><x-capstone::icon name="RefreshCw" size="15" />Muat Ulang</x-btn>
</div>
