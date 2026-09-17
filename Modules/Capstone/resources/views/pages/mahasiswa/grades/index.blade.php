@extends('capstone::layouts.app')
@section('title','Nilai Saya')
@section('content')
<div x-data="capstoneGrades">
    @include('capstone::partials.loading')
    <div class="space-y-10" x-show="!loading && !error" x-cloak>
        <div><h1 class="text-3xl font-bold tracking-tight">Nilai Saya</h1><p class="mt-1.5 text-sm text-muted-foreground" x-show="sections.length && result?.period?.name" x-text="result?.period?.name"></p></div>
        <div x-show="!sections.length" class="flex flex-col items-center justify-center rounded-xl border border-dashed px-6 py-20 text-center"><x-capstone::icon name="GraduationCap" class="mb-5 h-12 w-12 text-muted-foreground/40" /><h3 class="text-lg font-semibold">Belum ada nilai</h3><p class="mt-1.5 max-w-sm text-sm text-muted-foreground">Nilai akan muncul setelah pembimbing dan penguji mengirimkan evaluasi.</p></div>
        <template x-if="sections.length"><div class="space-y-10">
            <div class="space-y-2"><template x-for="item in sections" :key="item.key"><div class="group flex items-center gap-4 rounded-lg px-4 py-3 transition-colors hover:bg-muted/30">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-sm font-bold" :class="[palette(item.section.grade).bg,palette(item.section.grade).text]" :aria-label="'Letter grade '+palette(item.section.grade).letter" x-text="palette(item.section.grade).letter"></span>
                <div class="min-w-0 flex-1"><div class="flex items-baseline justify-between gap-3"><div class="flex items-baseline gap-2 min-w-0"><span class="truncate text-sm font-semibold" x-text="item.label"></span><span class="hidden sm:inline truncate text-xs text-muted-foreground" x-text="item.subtitle"></span></div><div class="flex items-center gap-2 shrink-0"><span class="text-lg font-bold tabular-nums" :class="palette(item.section.grade).text" x-text="score(item.section.grade)"></span><span class="inline-flex items-center rounded-md text-[10px] h-5 px-1.5 font-medium" :class="item.section.status==='COMPLETE'?'bg-primary text-primary-foreground':'bg-secondary text-secondary-foreground'" x-text="item.section.status"></span></div></div><div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-muted"><div class="h-full rounded-full transition-all duration-700 ease-out-quart" :class="palette(item.section.grade).bar" :style="{width:Math.min(100,Math.max(0,Number(item.section.grade)))+'%'}"></div></div></div>
            </div></template></div>
            <div role="separator" class="bg-border h-px w-full"></div>
            <div>
                <div role="tablist" aria-label="Komponen nilai" class="inline-flex h-9 items-center justify-center rounded-lg bg-muted p-[3px] text-muted-foreground"><template x-for="item in sections" :key="item.key"><button type="button" role="tab" :id="'grade-tab-'+item.key" :aria-controls="'grade-panel-'+item.key" :aria-selected="tab===item.key" @click="tab=item.key" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-2 py-1 text-sm font-medium whitespace-nowrap transition-all focus-visible:ring-2 focus-visible:ring-ring" :class="tab===item.key ? 'bg-background text-foreground shadow-sm' : ''" x-text="item.label"></button></template></div>
                <div role="tabpanel" :id="'grade-panel-'+tab" :aria-labelledby="'grade-tab-'+tab" class="mt-5 space-y-4"><template x-for="detail in components" :key="tab+':'+detail.type"><div class="rounded-lg border px-4 py-3">
                    <div class="flex items-center justify-between"><h4 class="text-sm font-medium" x-text="componentLabel(detail.type)"></h4><div class="flex items-center gap-2"><span x-show="detail.score!=null" class="text-sm font-semibold tabular-nums" :class="palette(detail.score).text" x-text="score(detail.score)"></span><span class="inline-flex items-center rounded-md text-[10px] h-5 px-1.5 font-medium" :class="detail.score!=null?'bg-primary text-primary-foreground':'bg-secondary text-secondary-foreground'" x-text="detail.score!=null?'Dinilai':'Menunggu'"></span></div></div>
                    <div x-show="detail.score!=null && detail.evaluators?.length" class="mt-3 space-y-1.5"><template x-for="(e,index) in (detail.evaluators || [])" :key="index"><div class="flex items-center justify-between rounded-md bg-muted/40 px-3 py-2"><div class="flex items-center gap-2 min-w-0"><x-capstone::icon name="Users" class="h-3.5 w-3.5 shrink-0 text-muted-foreground" /><span class="truncate text-sm" x-text="e.name"></span><x-capstone::badge variant="outline" class="text-[10px] h-5 px-1.5 shrink-0" x-text="roleLabel(e.role)" /><span x-show="e.component" class="hidden sm:inline truncate text-[11px] text-muted-foreground" x-text="e.component"></span></div><span class="ml-2 text-sm font-semibold tabular-nums shrink-0" :class="e.score!=null?palette(e.score).text:'text-muted-foreground'" x-text="score(e.score)"></span></div></template></div>
                    <p x-show="detail.score==null || !detail.evaluators?.length" class="mt-2 text-xs text-muted-foreground italic">Belum ada evaluasi masuk.</p>
                </div></template></div>
            </div>
            <p class="text-[11px] text-muted-foreground">Skala: A &ge; 85 &middot; B 70&ndash;84 &middot; C 60&ndash;69 &middot; D 50&ndash;59 &middot; E &lt; 50</p>
        </div></template>
    </div>
</div>
@endsection
