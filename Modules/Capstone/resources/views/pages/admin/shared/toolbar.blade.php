<div class="flex flex-wrap items-center gap-3 rounded-xl border bg-card p-4">
    <label class="text-sm">Periode <select x-model="periodId" @change="page=1;load()" :disabled="saving" class="ml-2 rounded-md border bg-background px-3 py-2"><option value="">Semua periode</option><template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name"></option></template></select></label>
    <input type="search" x-model="search" @input="page=1" @keydown.enter="load()" placeholder="Cari..." aria-label="Cari data" class="min-w-48 flex-1 rounded-md border bg-background px-3 py-2 text-sm">
    <x-capstone::button variant="outline" @click="load()" ::disabled="loading || saving"><x-capstone::icon name="RefreshCw" />Refresh</x-capstone::button>
</div>
