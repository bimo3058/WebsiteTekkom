@props([
    'page' => 'page',
    'pageSize' => 'pageSize',
    'pageCount' => 'pageCount',
    'total' => 'total',
    'from' => 'from',
    'to' => 'to',
    'pageList' => 'pageList',
    'lastPage' => null,
    'prevAction' => null,
    'nextAction' => null,
    'goAction' => null,
])
{{-- Canonical numbered-pills footer (Image 2). Must sit OUTSIDE overflow-x-auto with flex-wrap. Active: border-primary bg-primary text-white. --}}
@php
    $lastPageExpr = $lastPage ?? $pageCount;
    $prevExpr = $prevAction ?? $page . ' = Math.max(1, ' . $page . ' - 1)';
    $nextExpr = $nextAction ?? $page . ' = Math.min(' . $lastPageExpr . ', ' . $page . ' + 1)';
    $goExpr = $goAction ?? $page . ' = p';
@endphp
<div class="relative z-30 flex flex-wrap items-center justify-between gap-3 overflow-visible border-t border-slate-100 p-3 text-[13px] text-slate-500">
    <div class="flex items-center gap-2 overflow-visible">
        <label class="inline-flex items-center gap-1.5 overflow-visible rounded-lg border border-slate-200 px-2 py-1">Per halaman
            <x-capstone::select-menu x-model.number="{{ $pageSize }}" :options="[10,25,50]" size="sm" borderless dropup aria-label="Baris per halaman" class="min-w-20" />
        </label>
        <span x-text="'Menampilkan ' + {{ $from }} + ' sampai ' + {{ $to }} + ' dari ' + {{ $total }} + ' data'"></span>
    </div>
    <div class="flex items-center gap-1">
        <button type="button" @click="{{ $prevExpr }}" :disabled="{{ $page }} <= 1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
        <template x-for="(p, i) in {{ $pageList }}" :key="i + '-' + p">
            <button type="button" x-show="p !== '…'" @click="{{ $goExpr }}" class="min-w-8 rounded-lg border px-2 py-1.5 text-xs font-semibold" :class="p === {{ $page }} ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-500 hover:bg-slate-50'" x-text="p"></button>
            <span x-show="p === '…'" class="px-1 text-xs text-slate-400">...</span>
        </template>
        <button type="button" @click="{{ $nextExpr }}" :disabled="{{ $page }} >= {{ $lastPageExpr }}" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
    </div>
</div>
