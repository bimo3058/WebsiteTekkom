<div class="flex flex-wrap items-center gap-2.5 rounded-xl border border-border bg-white p-4 shadow-xs">
    <select x-model="periodId" @change="page=1;load()" :disabled="saving" aria-label="Filter periode" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm text-muted-foreground outline-none"><option value="">Semua periode</option><template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template></select>
    <div class="relative min-w-48 flex-1">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
        <input type="search" x-model="search" @input="page=1" @keydown.enter="load()" placeholder="Cari..." aria-label="Cari data" class="h-9 w-full rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
    </div>
    <x-capstone::button variant="outline" @click="load()" ::disabled="loading || saving"><x-capstone::icon name="RefreshCw" size="15" />Refresh</x-capstone::button>
</div>
