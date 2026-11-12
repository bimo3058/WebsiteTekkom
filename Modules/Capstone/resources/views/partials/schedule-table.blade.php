<div x-show="view === 'table'" x-cloak>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-muted text-left text-[13px] font-medium text-slate-500">
                    <th class="px-2 py-3 font-medium">No</th>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Waktu</th>
                    <th class="px-4 py-3 font-medium">Tipe</th>
                    <th class="px-4 py-3 font-medium">Kelompok</th>
                    <th class="px-4 py-3 font-medium">Anggota</th>
                    <th class="px-4 py-3 font-medium">Ruangan</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
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
                        <td class="whitespace-nowrap px-4 py-3 text-slate-900" x-text="event.room || (event.mode === 'online' ? 'Daring' : '-')"></td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="sched-status inline-block rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusPill(event.status)" x-text="statusLabel(event.status)"></span>
                        </td>
                        <td class="px-4 py-3 text-right" @click.stop>
                            <div x-data="{ open: false }" class="relative inline-block" @click.outside="open = false" @keydown.escape.window="open = false">
                                <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Aksi jadwal"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900">
                                    <x-capstone::icon name="Ellipsis" class="h-4 w-4" />
                                </button>
                                <div x-show="open" x-cloak class="absolute right-0 top-full z-30 mt-1 w-44 rounded-xl border border-slate-200 bg-white p-1.5 text-left shadow-lg" role="menu">
                                    <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100" @click="open = false; detail(event)">
                                        <x-capstone::icon name="Eye" class="h-4 w-4 text-slate-400" />Detail
                                    </button>
                                    @if($activeRole === 'admin')
                                    <button type="button" x-show="canApprove(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50" @click="open = false; approve(event)">
                                        <x-capstone::icon name="Check" class="h-4 w-4" />Setujui
                                    </button>
                                    <button type="button" x-show="canApprove(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50" @click="open = false; reject(event)">
                                        <x-capstone::icon name="X" class="h-4 w-4" />Tolak
                                    </button>
                                    @elseif($activeRole === 'dosen')
                                    <button type="button" x-show="canEdit(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100" @click="open = false; $nextTick(() => edit(event))">
                                        <x-capstone::icon name="SquarePen" class="h-4 w-4 text-slate-400" />Ubah
                                    </button>
                                    <button type="button" x-show="canEdit(event)" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50" @click="open = false; confirmDelete(event)">
                                        <x-capstone::icon name="Trash2" class="h-4 w-4" />Hapus
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="!tableRows.length">
                    <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-400">Tidak ada hasil.</td>
                </tr>
            </tbody>
        </table>
    </div>
    @include('capstone::partials.table-footer', ['page' => 'currentPage', 'pageSize' => 'perPage', 'pageCount' => 'totalPages', 'total' => 'tableRows.length', 'from' => 'showingFrom', 'to' => 'showingTo', 'pageList' => 'pageList', 'prevAction' => 'goto(currentPage - 1)', 'nextAction' => 'goto(currentPage + 1)', 'goAction' => 'goto(p)'])
</div>
