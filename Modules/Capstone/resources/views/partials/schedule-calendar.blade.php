<div class="flex flex-col gap-6" x-show="view==='calendar'">
    <x-capstone::card class="w-full overflow-hidden pt-0 calendar">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b px-3 sm:px-6 py-4">
            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                <x-capstone::button variant="outline" size="sm" @click="today">Today</x-capstone::button>
                <div class="flex items-center gap-1">
                    <x-capstone::button variant="ghost" size="icon" class="h-8 w-8" @click="move(-1)" aria-label="Previous month"><x-capstone::icon name="ChevronLeft" /></x-capstone::button>
                    <x-capstone::button variant="ghost" size="icon" class="h-8 w-8" @click="move(1)" aria-label="Next month"><x-capstone::icon name="ChevronRight" /></x-capstone::button>
                </div>
                <h2 class="text-lg sm:text-xl font-semibold" aria-live="polite" x-text="monthLabel"></h2>
            </div>
            @if($activeRole==='dosen')<x-capstone::button size="sm" @click="edit()" class="bg-black text-white hover:bg-gray-800">+ New event</x-capstone::button>@endif
        </div>
        <div class="p-0">
            <div class="grid grid-cols-7 border-b">@foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)<div class="py-3 text-center text-xs sm:text-sm font-medium text-gray-500">{{ $day }}</div>@endforeach</div>
            <div class="grid grid-cols-7">
                <template x-for="(day,index) in days" :key="day.key">
                    <div @click="selectedDate=day.key" @keydown.enter.self="selectedDate=day.key" @keydown.space.self.prevent="selectedDate=day.key" tabindex="0" role="button"
                        :aria-label="day.key+', '+eventsFor(day.key).length+' events'" :aria-pressed="day.key===selectedDate"
                        class="min-h-[64px] sm:min-h-[100px] min-w-0 cursor-pointer border-r border-b p-1 sm:p-2 transition-colors hover:bg-gray-50"
                        :class="[!day.current && 'bg-gray-50/50',day.key===selectedDate && 'bg-accent/50',(index+1)%7===0 && 'border-r-0',index>=days.length-7 && 'border-b-0']">
                        <div class="mb-1 flex justify-center"><span class="inline-flex h-7 w-7 items-center justify-center text-sm font-medium" :class="day.today ? 'rounded-full bg-black text-white' : day.current ? 'text-gray-900' : 'text-gray-400'" x-text="day.number"></span></div>
                        <div class="space-y-1">
                            <template x-for="event in eventsFor(day.key).slice(0,3)" :key="event._key">
                                <button type="button" @click.stop="detail(event)" class="w-full cursor-pointer truncate rounded-md px-1 sm:px-2 py-1 text-[10px] sm:text-xs text-left transition-opacity border hover:opacity-80" :class="color(event.type)" :title="time(event)+' '+title(event)">
                                    <span class="hidden sm:inline mr-1 font-medium" x-text="(event.start_time || '').slice(0,5)"></span><span x-text="title(event)"></span>
                                </button>
                            </template>
                            <div x-show="eventsFor(day.key).length>3" class="px-1 py-0.5 text-[10px] sm:text-xs text-gray-500" x-text="'+'+(eventsFor(day.key).length-3)+' more'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </x-capstone::card>
    <x-capstone::card class="min-h-[300px]">
        <div class="border-b bg-gray-50/50 px-6 py-4"><h3 class="text-lg font-semibold" x-text="selectedLabel"></h3><p class="text-sm text-gray-500" x-text="dayEvents.length ? dayEvents.length+' event'+(dayEvents.length>1?'s':'')+' scheduled' : 'No events scheduled'"></p></div>
        <div class="p-0">
            <div x-show="!dayEvents.length" class="flex flex-col items-center justify-center py-12 text-gray-400"><x-capstone::icon name="Calendar" class="mb-3 h-12 w-12 opacity-20" /><p class="text-sm font-medium">No events on this day</p><p class="mt-1 text-xs opacity-70">Select a date with events to view details</p></div>
            <div class="divide-y"><template x-for="event in dayEvents" :key="event._key">
                <div class="group p-5 transition-colors hover:bg-gray-50">
                    <div class="mb-3 flex items-start justify-between gap-2"><div class="flex flex-wrap items-center gap-3"><span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="color(event.type)" x-text="label(event.type)"></span><span x-show="event.status" class="rounded-full px-2 py-0.5 text-xs font-medium" :class="statusColor(event.status)" x-text="event.status"></span></div><span class="text-xs text-gray-400" x-text="event.period_name"></span></div>
                    <button type="button" class="text-left mb-3 text-base font-semibold text-gray-900" @click="detail(event)" x-text="event.group?.title?.title || 'Untitled'"></button>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2 text-gray-600"><x-capstone::icon name="Clock" class="h-4 w-4 text-gray-400" /><span class="font-medium text-gray-900" x-text="time(event)"></span></div>
                        <div class="flex items-center gap-2 text-gray-600"><x-capstone::icon name="MapPin" class="h-4 w-4 text-gray-400" /><span class="font-medium text-gray-900 break-all" x-text="event.room || (event.mode==='online' ? 'Online' : 'Location not set')"></span></div>
                        <div x-show="students(event) || event.type==='TA_DEFENSE'" class="flex items-center gap-2 text-gray-600"><x-capstone::icon name="User" class="h-4 w-4 text-gray-400" /><span class="font-medium text-gray-900" x-text="students(event) || 'No student assigned'"></span></div>
                        <div x-show="event.mode" class="flex items-center gap-2 text-gray-600"><x-capstone::icon name="Video" x-show="event.mode==='online'" class="h-4 w-4 text-gray-400" /><x-capstone::icon name="Building" x-show="event.mode!=='online'" class="h-4 w-4 text-gray-400" /><span class="font-medium text-gray-900 capitalize" x-text="event.mode"></span></div>
                        <div x-show="examiners(event)" class="sm:col-span-2 flex items-start gap-2 text-gray-600"><x-capstone::icon name="Users" class="mt-0.5 h-4 w-4 text-gray-400" /><div><span class="mb-0.5 block text-xs text-gray-400">Examiners</span><span class="text-gray-900" x-text="examiners(event)"></span></div></div>
                        <div x-show="event.group?.members?.length" class="sm:col-span-2 flex items-start gap-2 text-gray-600"><x-capstone::icon name="Users" class="mt-0.5 h-4 w-4 text-gray-400" /><div><span class="mb-0.5 block text-xs text-gray-400">Group Members</span><span class="text-gray-900" x-text="event.group?.members?.map(m=>m.student?.name).join(', ')"></span></div></div>
                    </div>
                    <div x-show="event.notes" class="mt-3 border-t border-gray-100 pt-3 flex items-start gap-2"><x-capstone::icon name="StickyNote" class="mt-0.5 h-4 w-4 text-gray-400" /><p class="text-sm text-gray-600" x-text="event.notes"></p></div>
                    <div x-show="['REJECTED','CANCELLED'].includes(event.status) && event.rejection_reason" class="mt-3 rounded-md border border-red-100 bg-red-50 p-3 text-sm text-red-700"><span class="font-medium">Rejection Reason:</span> <span x-text="event.rejection_reason"></span></div>
                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3">@include('capstone::partials.schedule-actions')</div>
                </div>
            </template></div>
        </div>
    </x-capstone::card>
</div>
@if($activeRole==='admin')@include('capstone::partials.schedule-table')@endif
@include('capstone::partials.schedule-detail')
@if($activeRole==='admin')
<x-capstone::dialog id="schedule-reject" title="Reject Schedule"><form @submit.prevent="submitRejection" class="space-y-4 mt-4"><label for="schedule-reason" class="text-sm font-medium">Rejection Reason</label><textarea id="schedule-reason" x-model="reason" required maxlength="1000" class="w-full min-h-24 rounded-md border p-3 text-sm" placeholder="Reason..."></textarea><div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button type="submit" variant="destructive" ::disabled="saving || !reason.trim()">Reject</x-capstone::button></div></form></x-capstone::dialog>
@endif
@if($activeRole==='dosen')
<x-capstone::dialog id="schedule-delete" title="Delete Schedule" description="Are you sure you want to delete this schedule? This action cannot be undone."><div class="flex justify-end gap-2 mt-6"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button variant="destructive" @click="remove" ::disabled="saving">Delete</x-capstone::button></div></x-capstone::dialog>
@endif
