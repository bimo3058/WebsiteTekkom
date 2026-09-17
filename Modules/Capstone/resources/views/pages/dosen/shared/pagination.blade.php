<div class="flex flex-wrap items-center justify-between gap-3 text-sm" x-show="filtered.length">
    <span class="text-muted-foreground" x-text="filtered.length+' result(s)'"></span>
    <div class="flex items-center gap-3">
        <select x-model.number="pageSize" @change="page=1" aria-label="Rows per page" class="rounded-md border bg-background p-1"><option>10</option><option>25</option><option>50</option></select>
        <x-capstone::button variant="outline" size="sm" @click="page=Math.max(1,page-1)" ::disabled="page<=1">Previous</x-capstone::button>
        <span x-text="Math.min(page,pageCount)+' / '+pageCount"></span>
        <x-capstone::button variant="outline" size="sm" @click="page++" ::disabled="page>=pageCount">Next</x-capstone::button>
    </div>
</div>
