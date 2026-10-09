@extends('capstone::layouts.app')
@section('title','TA Defense Schedules')
@section('content')
<div x-data="capstoneAdminTaDefense" @keydown.escape.window="closeMenu()" @scroll.window="closeMenu()" @resize.window="closeMenu()" class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">TA Defense Schedules</h1>
            <p class="mt-1 text-sm text-muted-foreground">Manage individual TA defense schedules for students.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <label class="inline-flex items-center gap-2 rounded-lg border border-input bg-white px-4 py-2 text-sm font-semibold shadow-xs">
                <span class="sr-only">Period</span>
                <select x-model="selectedPeriod" @change="page=1;load()" aria-label="Pilih periode" class="bg-transparent font-semibold outline-none">
                    <option value="all">Semua periode</option>
                    <template x-for="period in periods" :key="period.id">
                        <option :value="String(period.id)" x-text="period.name+(period.is_active?' (active)':'')"></option>
                    </template>
                </select>
                <x-capstone::icon name="ChevronDown" class="size-4 text-muted-foreground" />
            </label>
            <button type="button" @click="open()" ::disabled="!selectedPeriod || loading" class="inline-flex h-10 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Plus" class="size-4" />Schedule TA Defense</button>
        </div>
    </div>

    @include('capstone::partials.loading')

    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h2 class="text-base font-semibold">Tabel TA Defense</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"><x-capstone::icon name="Search" class="size-4" /></span>
                    <input type="search" x-model="search" @input="page=1" placeholder="Search student, NIM, group..." aria-label="Search schedules" class="h-9 w-64 rounded-lg border border-input bg-white pl-9 pr-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring">
                </div>
                <div class="relative">
                    <button type="button" @click="filterMenu=!filterMenu;sortMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ListFilter" class="size-4" />Filter
                    </button>
                    <div x-show="filterMenu" @click.away="filterMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-44 rounded-lg border bg-white p-1.5 shadow-md">
                        <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Status</p>
                        <template x-for="opt in [{v:'ALL',l:'Semua'},{v:'SCHEDULED',l:'Scheduled'},{v:'DONE',l:'Completed'},{v:'CANCELLED',l:'Cancelled'}]" :key="opt.v">
                            <button type="button" @click="statusFilter=opt.v;page=1;filterMenu=false" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="String(statusFilter)===String(opt.v)?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="String(statusFilter)===String(opt.v)">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="relative">
                    <button type="button" @click="sortMenu=!sortMenu;filterMenu=false" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-3.5 text-sm text-muted-foreground shadow-xs hover:bg-accent">
                        <x-capstone::icon name="ArrowUpDown" class="size-4" />Sort by
                    </button>
                    <div x-show="sortMenu" @click.away="sortMenu=false" x-cloak class="absolute right-0 z-20 mt-2 w-48 rounded-lg border bg-white p-1.5 shadow-md">
                        <template x-for="opt in [{v:'date-asc',l:'Tanggal terlama'},{v:'date-desc',l:'Tanggal terbaru'},{v:'name-az',l:'Nama A-Z'},{v:'name-za',l:'Nama Z-A'},{v:'status',l:'Status'}]" :key="opt.v">
                            <button type="button" @click="setSort(opt.v)" class="flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm hover:bg-accent" :class="sortOption===opt.v?'font-semibold text-foreground':'text-muted-foreground'">
                                <span x-text="opt.l"></span><span x-show="sortOption===opt.v">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="!filtered.length" class="px-5 py-8 text-center text-muted-foreground">
            <x-capstone::icon name="GraduationCap" class="h-8 w-8 mx-auto mb-3 text-muted-foreground/40" />
            <p class="text-sm font-medium">No TA defense schedules found</p>
            <p class="text-[13px] text-muted-foreground/60 mt-1" x-text="statusFilter!=='ALL'?'No '+statusFilter.toLowerCase()+' schedules. Try changing the status filter.':'Create a new schedule to get started.'"></p>
        </div>
        <div x-show="filtered.length">@include('capstone::pages.admin.ta-defense.table')</div>
    </div>
    <div x-show="openMenuItem" @click.away="closeMenu()" x-cloak :style="menuStyle" style="position: fixed; z-index: 50;" role="menu" class="w-48 rounded-lg border border-border bg-white p-1.5 text-left shadow-md">
        <a x-show="openMenuItem.status!=='CANCELLED'" :href="url('/admin/evaluation-summary/'+openMenuItem.id)" class="block rounded-md px-2 py-1.5 text-sm text-foreground hover:bg-accent">Evaluasi</a>
        <button type="button" x-show="openMenuItem.status==='SCHEDULED'" @click="open(openMenuItem);closeMenu()" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent">Edit</button>
        <button type="button" x-show="openMenuItem.status==='SCHEDULED'" @click="confirmCancel(openMenuItem);closeMenu()" class="block w-full rounded-md px-2 py-1.5 text-left text-sm text-red-600 hover:bg-red-50">Batalkan</button>
    </div>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
    @include('capstone::pages.admin.ta-defense.form')
    <x-capstone::dialog id="admin-ta-cancel" title="Cancel Schedule" description="Are you sure you want to cancel this TA defense schedule? This action cannot be undone."><div class="py-4 text-sm space-y-1"><p x-text="students(cancelling || {})[0]?.name || ''"></p><p class="text-muted-foreground" x-text="date(cancelling?.date)"></p></div><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="document.getElementById('admin-ta-cancel').close()">Keep Schedule</x-capstone::button><x-capstone::button variant="destructive" @click="cancel" ::disabled="saving">Cancel Schedule</x-capstone::button></div></x-capstone::dialog>
</div>
@endsection
