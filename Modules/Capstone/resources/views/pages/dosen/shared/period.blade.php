<x-capstone::select-menu x-model="selectedPeriod" @select-menu-change.window="page=1" :options="['all' => 'Semua Periode']" placeholder="Periode Akademik" aria-label="Periode Akademik" class="w-[200px] max-w-full">
    <template x-for="period in periods" :key="period.id">
        <button type="button" role="option" :aria-selected="(String(period.id) === selectedValue).toString()" @click="pick(period.id, period.name)" class="flex w-full items-center justify-between gap-2 rounded-radius px-2 py-1.5 text-sm hover:bg-surface-alt" :class="String(period.id) === selectedValue ? 'bg-primary/10 font-medium' : ''">
            <span class="truncate" x-text="period.name"></span>
            <span x-show="String(period.id) === selectedValue" class="text-primary"><x-capstone::icon name="Check" size="14" /></span>
        </button>
    </template>
</x-capstone::select-menu>
