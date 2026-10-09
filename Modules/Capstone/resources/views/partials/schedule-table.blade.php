<div x-show="view === 'table'" x-cloak>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-[#F6F8FA] text-left text-[13px] font-medium text-slate-500">
                    <th class="px-2 py-3 font-medium">No</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Time</th>
                    <th class="px-4 py-3 font-medium">Type</th>
                    <th class="px-4 py-3 font-medium">Group</th>
                    <th class="px-4 py-3 font-medium">Anggota</th>
                    <th class="px-4 py-3 font-medium">Ruangan</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 text-right font-medium">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(event, i) in visibleRows" :key="event._key">
                    <tr class="border-b border-slate-100 transition-colors last:border-b-0 hover:bg-slate-50">
                        <td class="px-2 py-3 text-slate-900" x-text="(currentPage - 1) * perPage + i + 1"></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-900" x-text="dateId(event.date)"></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-900" x-text="timeShort(event.start_time)"></td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="sched-type inline-block rounded-full px-2.5 py-0.5 text-xs font-medium" :class="typePill(event.type)" x-text="label(event.type)"></span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900" x-text="groupCode(event)"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center" x-show="memberNames(event).length">
                                <template x-for="(nm, ni) in memberNames(event).slice(0, 4)" :key="nm + ni">
                                    <span class="-ml-2 inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-white text-[10px] font-semibold text-white first:ml-0"
                                        :style="{ backgroundColor: avatarBg(nm) }" :title="nm" x-text="initials(nm)"></span>
                                </template>
                                <span x-show="memberNames(event).length > 4" class="-ml-2 inline-flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-slate-200 text-[10px] font-semibold text-slate-600"
                                    x-text="'+' + (memberNames(event).length - 4)"></span>
                            </div>
                            <span x-show="!memberNames(event).length" class="text-slate-400">-</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-900" x-text="event.room || (event.mode === 'online' ? 'Online' : '-')"></td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="sched-status inline-block rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusPill(event.status)" x-text="statusLabel(event.status)"></span>
                        </td>
                        <td class="px-4 py-3 text-right" @click.stop>
                            <div x-data="{ open: false }" class="relative inline-block" @click.outside="open = false" @keydown.escape.window="open = false">
                                <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Schedule actions"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900">
                                    <x-capstone::icon name="Ellipsis" class="h-4 w-4" />
                                </button>
                                <div x-show="open" x-cloak class="absolute right-0 top-full z-30 mt-1 w-44 rounded-xl border border-slate-200 bg-white p-1.5 text-left shadow-lg" role="menu">
                                    <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100" @click="open = false; detail(event)">
                                        <x-capstone::icon name="Eye" class="h-4 w-4 text-slate-400" />Detail
                                    </button>
                                    @if($activeRole === 'admin')
                                    <button type="button" x-show="canApprove(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50" @click="open = false; approve(event)">
                                        <x-capstone::icon name="Check" class="h-4 w-4" />Approve
                                    </button>
                                    <button type="button" x-show="canApprove(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50" @click="open = false; reject(event)">
                                        <x-capstone::icon name="X" class="h-4 w-4" />Reject
                                    </button>
                                    @elseif($activeRole === 'dosen')
                                    <button type="button" x-show="canEdit(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100" @click="open = false; $nextTick(() => edit(event))">
                                        <x-capstone::icon name="SquarePen" class="h-4 w-4 text-slate-400" />Edit
                                    </button>
                                    <button type="button" x-show="canEdit(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50" @click="open = false; confirmDelete(event)">
                                        <x-capstone::icon name="Trash2" class="h-4 w-4" />Delete
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="!tableRows.length">
                    <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400">No results.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <label class="flex items-center gap-2 text-slate-600">Per page
                <select x-model.number="perPage" @change="page = 1" aria-label="Rows per page" class="h-9 rounded-lg border border-slate-200 bg-white px-2 text-sm text-slate-900">
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </label>
            <span class="font-medium text-slate-900" x-text="'Showing ' + showingFrom + ' to ' + showingTo + ' of, ' + tableRows.length + ' results'"></span>
        </div>
        <div class="flex items-center gap-1.5">
            <button type="button" @click="goto(currentPage - 1)" ::disabled="currentPage <= 1" aria-label="Previous page"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50 disabled:opacity-40">
                <x-capstone::icon name="ChevronLeft" class="h-4 w-4" />
            </button>
            <template x-for="p in pageList" :key="'pg' + p">
                <button type="button" x-show="p !== '…'" @click="goto(p)" :aria-current="p === currentPage ? 'page' : null
                    :class="p === currentPage ? 'bg-[#0B266E] text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 px-2 text-sm font-medium transition-colors" x-text="p"></button>
                <span x-show="p === '…'" class="inline-flex h-9 min-w-9 items-center justify-center text-sm text-slate-400">…</span>
            </template>
            <button type="button" @click="goto(currentPage + 1)" ::disabled="currentPage >= totalPages" aria-label="Next page"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50 disabled:opacity-40">
                <x-capstone::icon name="ChevronRight" class="h-4 w-4" />
            </button>
        </div>
    </div>
</div>
