<select x-model="selectedPeriod" @change="page=1" aria-label="Academic Period" class="h-9 w-[200px] max-w-full rounded-md border bg-background px-3 text-sm">
    <option value="all">All Periods</option>
    <template x-for="period in periods" :key="period.id"><option :value="String(period.id)" x-text="period.name+(period.is_active?' (Active)':'')"></option></template>
</select>
