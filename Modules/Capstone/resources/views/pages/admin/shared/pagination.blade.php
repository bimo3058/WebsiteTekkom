<div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm text-muted-foreground">
    <span x-text="filtered.length+' data'"></span>
    <div class="flex items-center gap-3"><select x-model.number="pageSize" @change="page=1" aria-label="Baris per halaman" class="rounded border bg-background p-1"><option>10</option><option>25</option><option>50</option></select><x-capstone::button variant="outline" size="sm" @click="page=Math.max(1,page-1)" ::disabled="page<=1">Sebelumnya</x-capstone::button><span x-text="Math.min(page,pageCount)+' / '+pageCount"></span><x-capstone::button variant="outline" size="sm" @click="page++" ::disabled="page>=pageCount">Berikutnya</x-capstone::button></div>
</div>
