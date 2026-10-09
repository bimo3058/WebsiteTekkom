<div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 lg:flex-row lg:items-center lg:justify-between">
    <div class="inline-flex self-start rounded-xl bg-slate-100 p-1" role="group" aria-label="Schedule view">
        <button type="button" @click="view = 'calendar'" :aria-pressed="view === 'calendar'"
            :class="view === 'calendar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="rounded-lg px-5 py-1.5 text-sm font-semibold transition-all">Calendar</button>
        <button type="button" @click="view = 'table'" :aria-pressed="view === 'table'"
            :class="view === 'table' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="rounded-lg px-5 py-1.5 text-sm font-semibold transition-all">Table</button>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <label class="relative block w-full sm:w-64">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                <x-capstone::icon name="Search" class="h-4 w-4" />
            </span>
            <input x-model.debounce.200ms="search" @input="page = 1" type="search" placeholder="Search" aria-label="Search schedules"
                class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#0B266E] focus:outline-none" />
        </label>
        <div x-data="{ open: false }" class="relative" @click.outside="open = false" @keydown.escape.window="open = false">
            <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Filter schedules" title="Filter schedules"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition-colors hover:bg-slate-50 hover:text-slate-700">
                <x-capstone::icon name="Funnel" class="h-4 w-4" />
            </button>
            <div x-show="open" x-cloak class="absolute right-0 top-full z-30 mt-2 w-64 space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="sched-filter-period">Period</label>
                    <select id="sched-filter-period" x-model="selectedPeriod" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-sm">
                        <option value="all">All Periods</option>
                        <template x-for="period in periods" :key="period.id">
                            <option :value="period.id" x-text="period.name + (period.is_active ? ' (Active)' : '')"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="sched-filter-type">Type</label>
                    <select id="sched-filter-type" x-model="typeFilter" @change="page = 1" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-sm">
                        <option value="all">All Types</option>
                        @foreach(['BIMBINGAN', 'SEMPRO', 'EXPO', 'TA_DEFENSE'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="sched-filter-status">Status</label>
                    <select id="sched-filter-status" x-model="statusFilter" @change="page = 1" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-sm">
                        <option value="all">All Statuses</option>
                        @foreach(['PENDING', 'PENDING_APPROVAL', 'SCHEDULED', 'APPROVED', 'COMPLETED', 'REJECTED', 'CANCELLED'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <span class="hidden text-sm font-medium text-slate-500 md:inline" aria-live="polite" x-text="monthLabel"></span>
        <button type="button" @click="move(-1)" aria-label="Previous month" :title="'Previous month (' + monthLabel + ')'"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50">
            <x-capstone::icon name="ChevronLeft" class="h-4 w-4" />
        </button>
        <button type="button" @click="move(1)" aria-label="Next month" :title="'Next month (' + monthLabel + ')'"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-slate-50">
            <x-capstone::icon name="ChevronRight" class="h-4 w-4" />
        </button>
    </div>
</div>
