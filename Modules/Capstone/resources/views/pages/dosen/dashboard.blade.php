@extends('capstone::layouts.app')
@section('title','Dashboard')
@section('content')
<div x-data="capstoneDashboard" class="space-y-6" @mouseleave="hoverIdx=-1">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Dashboard</h1>
        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm">
            <span class="text-slate-400"><x-capstone::icon name="Calendar" size="16" /></span>
            <select x-model="selectedPeriod" @change="load()" class="bg-transparent font-semibold outline-none" aria-label="Semester">
                <option value="all">Semua Semester</option>
                <template x-for="period in periods" :key="period.id">
                    <option :value="String(period.id)" x-text="period.name"></option>
                </template>
            </select>
        </label>
    </div>

    @include('capstone::partials.loading')

    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>

    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="Users" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Group Bimbingan</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="data.active_groups ?? 0"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="Star" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Pending Evaluasi</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="pending"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="ChartColumn" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Pending Bids</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="data.pending_bids ?? 0"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="CalendarCheck" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Jadwal Mendatang</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="data.upcoming_schedules ?? 0"></p>
            </div>
        </div>

        <div class="grid gap-3 xl:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 p-3">
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Akses Sistem</h2>
                    <div class="flex items-center gap-2">
                        <select x-model="activityRange" @change="hoverIdx=-1" class="rounded-lg border border-slate-200 bg-transparent px-2.5 py-1.5 text-[13px] text-slate-500 outline-none" aria-label="Rentang waktu">
                            <option value="7">Weekly</option>
                            <option value="30">Monthly</option>
                        </select>
                        <button type="button" @click="load()" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50" aria-label="Muat ulang"><x-capstone::icon name="RefreshCw" size="15" /></button>
                    </div>
                </div>
                <div class="p-3">
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="flex w-8 flex-col justify-between py-1 text-right text-[11px] text-slate-400">
                                <template x-for="t in activityTicks" :key="t"><span x-text="t"></span></template>
                            </div>
                            <div class="relative min-w-0 flex-1">
                                <svg viewBox="0 0 600 200" class="block h-52 w-full" @mousemove="chartHover($event)" @mouseleave="hoverIdx=-1" role="img" aria-label="Grafik aktivitas">
                                    <defs>
                                        <linearGradient id="dosenActivityFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0" stop-color="#2f3d8a" stop-opacity="0.18" />
                                            <stop offset="1" stop-color="#2f3d8a" stop-opacity="0.02" />
                                        </linearGradient>
                                    </defs>
                                    <line x1="10" :y1="activityAvgY" x2="590" :y2="activityAvgY" stroke="#94a3b8" stroke-width="1" stroke-dasharray="5 4" />
                                    <path :d="activityArea" fill="url(#dosenActivityFill)" />
                                    <path :d="activityLine" fill="none" stroke="#2f3d8a" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                                    <circle x-show="activityHover" :cx="activityHover?.x/100*600" :cy="activityHover?.y/100*200" r="4.5" fill="#2f3d8a" stroke="#fff" stroke-width="2" />
                                </svg>
                                <div x-show="activityHover" x-cloak class="pointer-events-none absolute z-10 -translate-x-1/2 -translate-y-full rounded-lg bg-slate-900 px-2.5 py-1.5 text-center shadow-lg" :style="'left:'+activityHover?.x+'%;top:'+activityHover?.y+'%'">
                                    <p class="whitespace-nowrap text-[11px] text-slate-300" x-text="activityHover?.label"></p>
                                    <p class="whitespace-nowrap text-xs font-bold text-white" x-text="activityHover?.value+' aktivitas'"></p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between pl-10 text-[11px] text-slate-400"><span x-text="activityFirstLabel"></span><span x-text="activityLastLabel"></span></div>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 p-3">
                    <h2 class="text-sm font-bold text-slate-900">Judul Tersedia</h2>
                    <button type="button" @click="load()" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50" aria-label="Muat ulang"><x-capstone::icon name="RefreshCw" size="15" /></button>
                </div>
                <div class="flex flex-col items-center p-4">
                    <div class="relative h-44 w-44">
                        <svg viewBox="0 0 176 176" class="h-full w-full -rotate-90" role="img" aria-label="Keterseidaan judul">
                            <circle cx="88" cy="88" r="70" fill="none" :stroke="donutTotal ? '#f0b429' : '#e2e8f0'" stroke-width="24" />
                            <circle cx="88" cy="88" r="70" fill="none" stroke="#8b8bd4" stroke-width="24" stroke-linecap="round" :stroke-dasharray="donutDash" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-[11px] text-slate-400">Total Judul</p>
                            <p class="text-2xl font-bold text-slate-900" x-text="donutTotal"></p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-4 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#8b8bd4]"></span>Tersedia (<span x-text="data.titles_available ?? 0"></span>)</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#f0b429]"></span>Tidak Tersedia (<span x-text="data.titles_full ?? 0"></span>)</span>
                    </div>
                </div>
            </div>
        </div>

        <section class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="h-5 w-1 rounded-full bg-[#2f3d8a]"></span>
                <h2 class="text-[15px] font-bold text-slate-900">Group Bimbingan</h2>
                <span class="h-px flex-1 bg-slate-200"></span>
            </div>
            <div class="grid gap-3 xl:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <h3 class="px-1 pb-2.5 text-sm font-bold text-slate-900">Akses Cepat</h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        <x-capstone::feature-link href="/dosen/ta-review" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-emerald-500"><x-capstone::icon name="CircleCheck" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Review TA</p>
                            <p class="mt-0.5 text-xs text-slate-400">Finalisasi Kelompok</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/dosen/supervisor-evaluation" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-amber-300 hover:bg-amber-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-amber-400"><x-capstone::icon name="Users" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Evaluasi</p>
                            <p class="mt-0.5 text-xs text-slate-400">Setting Group</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/dosen/titles" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-slate-400 hover:bg-slate-50">
                            <span class="flex h-7 w-7 items-center justify-center text-slate-700"><x-capstone::icon name="Briefcase" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Judul</p>
                            <p class="mt-0.5 text-xs text-slate-400">Periode</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/dosen/bids" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-blue-300 hover:bg-blue-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-blue-500"><x-capstone::icon name="CalendarCheck" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Bids</p>
                            <p class="mt-0.5 text-xs text-slate-400">Lihat Jadwal</p>
                        </x-capstone::feature-link>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
                        <h3 class="text-sm font-bold text-slate-900">Tabel group</h3>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                                <x-capstone::icon name="Search" size="15" />
                                <input x-model.debounce.300ms="groupSearch" @input="groupPage=1" type="search" placeholder="Search" class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-36" aria-label="Search groups">
                            </label>
                            <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Filter status">
                                <x-capstone::icon name="ListFilter" size="15" />
                                <select x-model="groupStatus" @change="groupPage=1" class="max-w-24 bg-transparent outline-none" aria-label="Filter status group">
                                    <option value="">Filter</option>
                                    <option value="FORMING">Forming</option>
                                    <option value="FORMING_SOLO">Forming Solo</option>
                                    <option value="READY_FOR_BIDDING">Bidding</option>
                                    <option value="READY_FOR_FINALIZATION">Finalisasi</option>
                                    <option value="PDC1_ACTIVE">PDC1</option>
                                    <option value="PDC2_ACTIVE">PDC2</option>
                                </select>
                            </label>
                            <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Urutkan">
                                <x-capstone::icon name="ArrowUpDown" size="15" />
                                <select x-model="groupSort" @change="groupPage=1" class="max-w-24 bg-transparent outline-none" aria-label="Sort groups">
                                    <option value="newest">Sort by</option>
                                    <option value="newest">Terbaru</option>
                                    <option value="code">Kode</option>
                                    <option value="status">Status</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] text-left text-[13px]">
                            <thead class="border-b border-slate-100 text-xs text-slate-500">
                                <tr>
                                    <th class="px-4 py-2.5 font-medium">No</th>
                                    <th class="px-4 py-2.5 font-medium">Kode</th>
                                    <th class="px-4 py-2.5 font-medium">Ketua</th>
                                    <th class="px-4 py-2.5 font-medium">Dosen Pembimbing</th>
                                    <th class="px-4 py-2.5 font-medium">Status</th>
                                    <th class="px-4 py-2.5 text-right font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item,idx) in pagedGroups" :key="item.id">
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-4 py-2.5 text-slate-500" x-text="(groupPage-1)*groupPerPage+idx+1"></td>
                                        <td class="whitespace-nowrap px-4 py-2.5 font-semibold text-slate-700" x-text="item.code || ('Group '+item.id)"></td>
                                        <td class="px-4 py-2.5">
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(ketuaName(item))"></span>
                                                <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="ketuaName(item)"></span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="flex max-w-44 flex-col items-start gap-1">
                                                <template x-for="name in dosenSupervisors(item)" :key="name">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(name)"></span>
                                                        <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="name"></span>
                                                    </span>
                                                </template>
                                                <span x-show="!dosenSupervisors(item).length" class="text-xs text-slate-300">—</span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="groupStatusClass(item.status)" x-text="groupStatusLabel(item)"></span></td>
                                        <td class="px-4 py-2.5 text-right">
                                            <span x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative inline-block text-left">
                                                <button type="button" @click="menu=!menu" :aria-expanded="menu" aria-label="Aksi group" class="rounded px-1 font-bold tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">...</button>
                                                <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-44 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=pagedGroups.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                                    <a :href="url('/dosen/bimbingan?group_id='+item.id)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="FileText" size="15" />Lihat Bimbingan</a>
                                                    <a :href="url('/dosen/supervisor-evaluation/'+item.id)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Star" size="15" />Evaluasi</a>
                                                </span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!pagedGroups.length"><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Belum ada group bimbingan.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
                        <div class="flex items-center gap-2 text-slate-500">
                            <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                                <select x-model.number="groupPerPage" @change="groupPage=1" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>2</option><option>10</option><option>25</option><option>50</option></select>
                            </label>
                            <span x-text="'Showing '+groupFrom+' to '+groupTo+' of, '+groupTotal+' results'"></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="groupPage=Math.max(1,groupPage-1)" :disabled="groupPage<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                            <template x-for="(p,i) in groupPageList" :key="i+'-'+p">
                                <button type="button" x-show="p!=='…'" @click="groupPage=p" class="min-w-8 rounded-lg border px-2 py-1.5 text-xs font-semibold" :class="p===groupPage ? 'border-[#2f3d8a] bg-[#2f3d8a] text-white' : 'border-slate-200 text-slate-500 hover:bg-slate-50'" x-text="p"></button>
                                <span x-show="p==='…'" class="px-1 text-xs text-slate-400">...</span>
                            </template>
                            <button type="button" @click="groupPage=Math.min(groupLastPage,groupPage+1)" :disabled="groupPage>=groupLastPage" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" size="15" /></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
