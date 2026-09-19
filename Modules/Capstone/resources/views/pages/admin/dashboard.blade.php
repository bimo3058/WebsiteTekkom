@extends('capstone::layouts.app')
@section('title','Dashboard')
@section('content')
<div x-data="capstoneDashboard" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Dashboard</h1>
        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm">
            <span class="text-slate-400"><x-capstone::icon name="Calendar" size="16" /></span>
            <select x-model="selectedPeriod" @change="reloadAdmin()" class="bg-transparent font-semibold outline-none" aria-label="Semester">
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
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="User" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Periode Aktif</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="activePeriodsCount"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="Users" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Dosen</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="data.total_lecturers ?? 0"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="UserMinus" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Mahasiswa</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="data.total_students ?? 0"></p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" :title="'Finalisasi: '+(data.pending_breakdown?.finalization ?? 0)+', Judul: '+(data.pending_breakdown?.titles ?? 0)+', Dokumen: '+(data.pending_breakdown?.documents ?? 0)+', Join Request: '+(data.pending_breakdown?.join_requests ?? 0)">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500"><x-capstone::icon name="Users" size="18" /></span>
                    <p class="text-[13px] font-medium text-slate-600">Butuh Persetujuan</p>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900" x-text="pendingApproval"></p>
            </div>
        </div>

        <section class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="h-5 w-1 rounded-full bg-[#2f3d8a]"></span>
                <h2 class="text-[15px] font-bold text-slate-900">Jadwal</h2>
                <span class="h-px flex-1 bg-slate-200"></span>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-3">
                    <div class="inline-flex items-center gap-1 rounded-lg bg-slate-100 p-1 text-[13px] font-medium">
                        <button type="button" @click="jadwalView='kanban'" class="rounded-md px-4 py-1.5 text-slate-500" :class="jadwalView==='kanban' && 'bg-white text-slate-900 shadow-sm font-semibold'">Kanban</button>
                        <button type="button" @click="jadwalView='table'" class="rounded-md px-4 py-1.5 text-slate-500" :class="jadwalView==='table' && 'bg-white text-slate-900 shadow-sm font-semibold'">Table</button>
                        <button type="button" @click="jadwalView='calendar'" class="rounded-md px-4 py-1.5 text-slate-500" :class="jadwalView==='calendar' && 'bg-white text-slate-900 shadow-sm font-semibold'">Calendar</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex min-w-0 items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                            <x-capstone::icon name="Search" size="15" />
                            <input x-model.debounce.300ms="jadwalSearch" @input="tablePage=1" type="search" placeholder="Search" class="w-36 bg-transparent outline-none placeholder:text-slate-400" aria-label="Search jadwal">
                        </label>
                        <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="Filter tipe">
                            <x-capstone::icon name="ListFilter" size="15" />
                            <select x-model="typeFilter" @change="tablePage=1" class="bg-transparent text-slate-600 outline-none" aria-label="Filter tipe jadwal">
                                <option value="all">Semua</option>
                                <option value="BIMBINGAN">Bimbingan</option>
                                <option value="SEMPRO">Sempro</option>
                                <option value="EXPO">Expo</option>
                                <option value="TA_DEFENSE">TA Defense</option>
                            </select>
                        </label>
                        <label class="hidden items-center gap-1.5 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500 sm:inline-flex" title="Filter status">
                            <select x-model="statusFilter" @change="tablePage=1" class="bg-transparent text-slate-600 outline-none" aria-label="Filter status jadwal">
                                <option value="all">Semua Status</option>
                                <option value="PENDING">Pending</option>
                                <option value="SCHEDULED">Scheduled</option>
                                <option value="APPROVED">Approved</option>
                                <option value="COMPLETED">Completed</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="CANCELLED">Cancelled</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div x-show="jadwalView==='calendar'" class="p-3">
                    <div class="rounded-xl border border-slate-200">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-3 py-2.5">
                            <div class="flex items-center gap-1">
                                <button type="button" @click="move(-1)" class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100" aria-label="Bulan sebelumnya"><x-capstone::icon name="ChevronLeft" size="16" /></button>
                                <h3 class="min-w-36 text-center text-sm font-semibold text-slate-800" x-text="monthLabel"></h3>
                                <button type="button" @click="move(1)" class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100" aria-label="Bulan berikutnya"><x-capstone::icon name="ChevronRight" size="16" /></button>
                            </div>
                            <button type="button" @click="today()" class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Today</button>
                        </div>
                        <div class="grid grid-cols-7 border-b border-slate-100">
                            <template x-for="d in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']" :key="d">
                                <div class="py-2 text-center text-xs font-medium text-slate-500" x-text="d"></div>
                            </template>
                        </div>
                        <div class="grid grid-cols-7">
                            <template x-for="(day,index) in days" :key="day.key">
                                <div @click="selectedDate=day.key" tabindex="0" role="button" @keydown.enter="selectedDate=day.key"
                                    class="min-h-[92px] min-w-0 cursor-pointer border-b border-r border-slate-100 p-1.5 align-top transition-colors hover:bg-slate-50 sm:min-h-[118px] sm:p-2"
                                    :class="[!day.current && 'bg-slate-50/70', day.key===selectedDate && 'bg-blue-50/40', (index+1)%7===0 && 'border-r-0', index>=days.length-7 && 'border-b-0']">
                                    <div class="mb-1 flex justify-end">
                                        <span class="inline-flex h-6 min-w-6 items-center justify-center px-1 text-xs" :class="day.key===selectedDate ? 'rounded-md bg-[#2f3d8a] font-bold text-white' : day.current ? 'font-medium text-slate-700' : 'text-slate-300'" x-text="day.number"></span>
                                    </div>
                                    <div class="space-y-1">
                                        <template x-for="event in eventsFor(day).slice(0,2)" :key="event._key">
                                            <button type="button" @click.stop="detail(event)" class="w-full truncate rounded border-l-4 px-1.5 py-1 text-left text-[11px] font-medium leading-tight" :class="dashboardEventPill(event.type)" :title="time(event)+' — '+title(event)" x-text="title(event)"></button>
                                        </template>
                                        <div x-show="eventsFor(day).length>2" class="px-1 text-[11px] font-bold text-slate-700" x-text="'+'+(eventsFor(day).length-2)+' more'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="dayEvents.length" class="mt-3 rounded-xl border border-slate-200">
                        <p class="border-b border-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-800" x-text="selectedLabel+' — '+dayEvents.length+' kegiatan'"></p>
                        <div class="divide-y divide-slate-100">
                            <template x-for="event in dayEvents" :key="event._key">
                                <button type="button" @click="detail(event)" class="flex w-full items-center gap-3 px-4 py-2.5 text-left hover:bg-slate-50">
                                    <span class="rounded px-2 py-0.5 text-[11px] font-semibold" :class="color(event.type)" x-text="label(event.type)"></span>
                                    <span class="min-w-0 flex-1 truncate text-[13px] font-medium text-slate-800" x-text="title(event)"></span>
                                    <span class="shrink-0 text-xs text-slate-400" x-text="(event.start_time || '').slice(0,5)"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div x-show="jadwalView==='table'" x-cloak class="p-3">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-[13px]">
                            <thead class="border-b border-slate-100 bg-slate-50/60 text-xs text-slate-500">
                                <tr><th class="px-3 py-2.5 font-medium">Tipe</th><th class="px-3 py-2.5 font-medium"><button type="button" @click="sortDirection*=-1" class="inline-flex items-center gap-1">Tanggal &amp; Waktu <x-capstone::icon name="ArrowUpDown" size="13" /></button></th><th class="px-3 py-2.5 font-medium">Kelompok</th><th class="px-3 py-2.5 font-medium">Ruangan</th><th class="px-3 py-2.5 font-medium">Status</th><th class="px-3 py-2.5 font-medium">Aksi</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="event in jadwalVisible" :key="event._key">
                                    <tr class="cursor-pointer hover:bg-slate-50" @click="detail(event)">
                                        <td class="px-3 py-2.5"><span class="rounded px-2 py-0.5 text-[11px] font-semibold" :class="color(event.type)" x-text="label(event.type)"></span></td>
                                        <td class="whitespace-nowrap px-3 py-2.5"><p class="font-medium text-slate-700" x-text="date(event.date)"></p><p class="text-xs text-slate-400" x-text="time(event)"></p></td>
                                        <td class="max-w-52 truncate px-3 py-2.5 font-medium text-slate-700" x-text="event.group?.title?.title || event.group?.code || '-'"></td>
                                        <td class="px-3 py-2.5 text-slate-500" x-text="event.room || (event.mode==='online' ? 'Online' : '-')"></td>
                                        <td class="px-3 py-2.5"><span class="rounded-full px-2 py-0.5 text-[11px] font-medium capitalize" :class="statusColor(event.status)" x-text="(event.status || 'SCHEDULED').toLowerCase()"></span></td>
                                        <td class="px-3 py-2.5"><span class="inline-flex items-center gap-1" @click.stop>@include('capstone::partials.schedule-actions')</span></td>
                                    </tr>
                                </template>
                                <tr x-show="!jadwalRows.length"><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Tidak ada jadwal.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 px-1 pt-3 text-[13px] text-slate-500">
                        <span x-text="jadwalRows.length+' jadwal'"></span>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center gap-1.5">Rows <select x-model.number="tablePerPage" @change="tablePage=1" class="rounded-md border border-slate-200 px-1.5 py-1"><option>10</option><option>20</option><option>50</option></select></label>
                            <span x-text="'Page '+jadwalCurrentPage+' of '+jadwalTotalPages"></span>
                            <button type="button" @click="tablePage=Math.max(1,jadwalCurrentPage-1)" :disabled="jadwalCurrentPage<=1" class="rounded-md border border-slate-200 p-1.5 disabled:opacity-40" aria-label="Previous page"><x-capstone::icon name="ChevronLeft" size="15" /></button>
                            <button type="button" @click="tablePage=Math.min(jadwalTotalPages,jadwalCurrentPage+1)" :disabled="jadwalCurrentPage>=jadwalTotalPages" class="rounded-md border border-slate-200 p-1.5 disabled:opacity-40" aria-label="Next page"><x-capstone::icon name="ChevronRight" size="15" /></button>
                        </div>
                    </div>
                </div>

                <div x-show="jadwalView==='kanban'" x-cloak class="grid gap-3 p-3 sm:grid-cols-2 xl:grid-cols-4">
                    <template x-for="col in kanbanGroups" :key="col.type">
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-2.5">
                            <div class="mb-2.5 flex items-center justify-between px-1">
                                <p class="text-[13px] font-bold text-slate-700" x-text="label(col.type)"></p>
                                <span class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-slate-500 shadow-sm" x-text="col.items.length"></span>
                            </div>
                            <div class="max-h-96 space-y-2 overflow-y-auto">
                                <template x-for="event in col.items.slice(0,20)" :key="event._key">
                                    <button type="button" @click="detail(event)" class="w-full rounded-lg border border-slate-200 bg-white p-2.5 text-left shadow-sm transition-shadow hover:shadow">
                                        <span class="mb-1.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium capitalize" :class="statusColor(event.status)" x-text="(event.status || 'SCHEDULED').toLowerCase()"></span>
                                        <p class="line-clamp-2 text-[13px] font-semibold text-slate-800" x-text="title(event)"></p>
                                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-slate-400"><x-capstone::icon name="Clock" size="13" /><span x-text="date(event.date)+' '+(event.start_time || '').slice(0,5)"></span></p>
                                        <p class="mt-1 flex items-center gap-1.5 truncate text-xs text-slate-400"><x-capstone::icon name="MapPin" size="13" /><span class="truncate" x-text="event.room || (event.mode==='online' ? 'Online' : '-')"></span></p>
                                    </button>
                                </template>
                                <p x-show="!col.items.length" class="rounded-lg border border-dashed border-slate-200 bg-white px-3 py-6 text-center text-xs text-slate-400">Belum ada jadwal</p>
                                <p x-show="col.items.length>20" class="px-1 text-center text-xs text-slate-400" x-text="'+'+(col.items.length-20)+' lainnya'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="h-5 w-1 rounded-full bg-[#2f3d8a]"></span>
                <h2 class="text-[15px] font-bold text-slate-900">Group</h2>
                <span class="h-px flex-1 bg-slate-200"></span>
            </div>
            <div class="grid gap-3 xl:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
                        <h3 class="text-sm font-bold text-slate-900">Tabel Groups</h3>
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
                                    <th class="px-2 py-2.5 font-medium">No</th>
                                    <th class="px-2 py-2.5 font-medium">Kode</th>
                                    <th class="px-2 py-2.5 font-medium">Ketua</th>
                                    <th class="px-2 py-2.5 font-medium">Dosen Pembimbing</th>
                                    <th class="px-2 py-2.5 font-medium">Status</th>
                                    <th class="px-3 py-2.5 text-right font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item,idx) in pagedGroups" :key="item.id">
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-2 py-2.5 text-slate-500" x-text="(groupPage-1)*groupPerPage+idx+1"></td>
                                        <td class="whitespace-nowrap px-2 py-2.5 font-semibold text-slate-700" x-text="item.code || ('Group '+item.id)"></td>
                                        <td class="px-2 py-2.5">
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(ketuaName(item))"></span>
                                                <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="ketuaName(item)"></span>
                                            </span>
                                        </td>
                                        <td class="px-2 py-2.5">
                                            <span class="flex max-w-44 flex-col items-start gap-1">
                                                <template x-for="name in supervisorNames(item)" :key="name">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 py-0.5 pl-0.5 pr-2.5">
                                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-500" x-text="initials(name)"></span>
                                                        <span class="max-w-28 truncate text-xs font-medium text-slate-700" x-text="name"></span>
                                                    </span>
                                                </template>
                                                <span x-show="!supervisorNames(item).length" class="text-xs text-slate-300">—</span>
                                            </span>
                                        </td>
                                        <td class="px-2 py-2.5"><span class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="groupStatusClass(item.status)" x-text="groupStatusLabel(item)"></span></td>
                                        <td class="px-3 py-2.5 text-right">
                                            <span x-data="{menu:false}" @click.outside="menu=false" @keydown.escape.window="menu=false" class="relative inline-block text-left">
                                                <button type="button" @click="menu=!menu" :aria-expanded="menu" aria-label="Aksi group" class="rounded px-1 font-bold tracking-widest text-slate-400 hover:bg-slate-100 hover:text-slate-700">...</button>
                                                <span x-show="menu" x-cloak class="absolute right-0 z-20 min-w-40 rounded-lg border border-slate-200 bg-white p-1 shadow-lg" :class="idx>=pagedGroups.length-2 ? 'bottom-full mb-1' : 'top-full mt-1'">
                                                    <a :href="url('/admin/groups/'+item.id)" @click="menu=false" class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-[13px] font-medium text-slate-700 hover:bg-slate-100"><x-capstone::icon name="Eye" size="15" />Lihat Detail</a>
                                                </span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!pagedGroups.length && !groupLoading"><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Belum ada group.</td></tr>
                                <tr x-show="groupLoading"><td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-3 text-[13px]">
                        <div class="flex items-center gap-2 text-slate-500">
                            <label class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-1">Per page
                                <select x-model.number="groupPerPage" @change="groupPage=1" class="bg-transparent font-semibold text-slate-700 outline-none" aria-label="Baris per halaman"><option>3</option><option>10</option><option>25</option><option>50</option></select>
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

                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <h3 class="px-1 pb-2.5 text-sm font-bold text-slate-900">Akses Cepat</h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        <x-capstone::feature-link href="/admin/finalization" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-emerald-500"><x-capstone::icon name="CircleCheck" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Finalisasi</p>
                            <p class="mt-0.5 text-xs text-slate-400">Finalisasi Kelompok</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/admin/groups" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-amber-300 hover:bg-amber-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-amber-400"><x-capstone::icon name="Users" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Manage Group</p>
                            <p class="mt-0.5 text-xs text-slate-400">Setting Group</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/admin/periods" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-slate-400 hover:bg-slate-50">
                            <span class="flex h-7 w-7 items-center justify-center text-slate-700"><x-capstone::icon name="LayoutDashboard" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Manage Periode</p>
                            <p class="mt-0.5 text-xs text-slate-400">Periode</p>
                        </x-capstone::feature-link>
                        <x-capstone::feature-link href="/admin/schedule" class="rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-blue-300 hover:bg-blue-50/40">
                            <span class="flex h-7 w-7 items-center justify-center text-blue-500"><x-capstone::icon name="Calendar" size="22" /></span>
                            <p class="mt-2.5 text-[13px] font-bold text-slate-800">Jadwal</p>
                            <p class="mt-0.5 text-xs text-slate-400">Lihat Jadwal</p>
                        </x-capstone::feature-link>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('capstone::partials.schedule-detail')
    <x-capstone::dialog id="schedule-reject" title="Reject Schedule">
        <form @submit.prevent="submitRejection" class="mt-4 space-y-4">
            <label for="dashboard-reason" class="text-sm font-medium">Rejection Reason</label>
            <textarea id="dashboard-reason" x-model="reason" required maxlength="1000" class="min-h-24 w-full rounded-md border p-3 text-sm" placeholder="Reason..."></textarea>
            <div class="flex justify-end gap-2">
                <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button>
                <x-capstone::button type="submit" variant="destructive" ::disabled="saving || !reason.trim()">Reject</x-capstone::button>
            </div>
        </form>
    </x-capstone::dialog>
</div>
@endsection
