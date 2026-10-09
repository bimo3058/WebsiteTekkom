<div x-show="view === 'calendar'" x-cloak>
    <div class="overflow-hidden">
        <div class="grid grid-cols-7 border-b border-slate-200">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="py-3 text-center text-sm font-medium text-slate-500">{{ $day }}</div>
            @endforeach
        </div>
        <div class="grid grid-cols-7">
            <template x-for="(day, index) in days" :key="day.key">
                <div @click="selectedDate = day.key" @keydown.enter.self="selectedDate = day.key" @keydown.space.self.prevent="selectedDate = day.key"
                    tabindex="0" role="button" :aria-label="day.key + ', ' + eventsFor(day.key).length + ' events'" :aria-pressed="day.key === selectedDate"
                    class="min-h-[120px] cursor-pointer border-b border-r border-slate-200 p-2 text-right transition-colors hover:bg-slate-50 sm:min-h-[132px]"
                    :class="[!day.current && 'bg-[#F6F8FA]', (index + 1) % 7 === 0 && 'border-r-0', index >= days.length - 7 && 'border-b-0', (index % 7 === 0) && day.current && 'bg-[#F6F8FA]/60']">
                    <div class="mb-1 flex justify-end">
                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-md px-1 text-[13px]"
                            :class="day.key === selectedDate ? 'bg-[#0B266E] font-semibold text-white' : (day.current ? 'font-medium text-slate-900' : 'text-slate-400')"
                            x-text="day.number"></span>
                    </div>
                    <div class="space-y-1 text-left">
                        <template x-for="event in eventsFor(day.key).slice(0, 2)" :key="event._key">
                            <button type="button" @click.stop="detail(event)"
                                class="sched-pill block w-full truncate rounded-md px-2 py-1 text-left text-xs transition-opacity hover:opacity-80"
                                :class="pillClass(event.type)" :title="time(event) + ' ' + title(event)">
                                <span x-text="title(event)"></span>
                            </button>
                        </template>
                        <button type="button" x-show="eventsFor(day.key).length > 2" @click.stop="openDay(day.key)"
                            class="px-1 py-0.5 text-left text-xs font-medium text-slate-900 hover:underline"
                            x-text="'+' + (eventsFor(day.key).length - 2) + ' more'"></button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@include('capstone::partials.schedule-day')
@include('capstone::partials.schedule-detail')
@if($activeRole === 'admin')
<x-capstone::dialog id="schedule-reject" title="Reject Schedule">
    <form @submit.prevent="submitRejection" class="mt-4 space-y-4">
        <label for="schedule-reason" class="text-sm font-medium">Rejection Reason</label>
        <textarea id="schedule-reason" x-model="reason" required maxlength="1000" class="min-h-24 w-full rounded-md border p-3 text-sm" placeholder="Reason..."></textarea>
        <div class="flex justify-end gap-2">
            <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button>
            <x-capstone::button type="submit" variant="destructive" ::disabled="saving || !reason.trim()">Reject</x-capstone::button>
        </div>
    </form>
</x-capstone::dialog>
@endif
@if($activeRole === 'dosen')
<x-capstone::dialog id="schedule-delete" title="Delete Schedule" description="Are you sure you want to delete this schedule? This action cannot be undone.">
    <div class="mt-6 flex justify-end gap-2">
        <x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button>
        <x-capstone::button variant="destructive" @click="remove" ::disabled="saving">Delete</x-capstone::button>
    </div>
</x-capstone::dialog>
@endif
