<x-capstone::card class="h-full" x-data="capstoneCalendar">
    <div class="px-6 pt-5 pb-3 flex flex-row items-center justify-between border-b border-gray-100"><h3 class="text-base font-semibold text-gray-800">Schedule</h3><x-capstone::button variant="ghost" size="icon" class="h-8 w-8 text-gray-400 hover:text-gray-600" @click="$dispatch('refresh-dashboard')" aria-label="Refresh schedule"><x-capstone::icon name="RefreshCw" class="h-4 w-4" /></x-capstone::button></div>
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-3"><h3 class="text-lg font-bold text-gray-900" x-text="monthLabel"></h3><div class="flex items-center gap-1"><x-capstone::button variant="ghost" size="icon" class="h-7 w-7" @click="move(-1)" aria-label="Previous month"><x-capstone::icon name="ChevronLeft" /></x-capstone::button><x-capstone::button variant="ghost" size="icon" class="h-7 w-7" @click="move(1)" aria-label="Next month"><x-capstone::icon name="ChevronRight" /></x-capstone::button></div></div>
        <div class="grid grid-cols-7 mb-1">@foreach(['Mo','Tu','We','Th','Fr','Sa','Su'] as $day)<div class="text-center text-xs font-semibold text-gray-800 py-1">{{ $day }}</div>@endforeach</div>
        <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden"><template x-for="day in days" :key="day.key"><div class="min-h-[44px] p-1 flex flex-col items-center justify-center cursor-pointer transition-colors" :class="day.key===selectedDate ? 'bg-primary text-white' : (day.current ? 'bg-white hover:bg-gray-50 text-gray-800' : 'bg-gray-50 text-gray-300')" @click="openDay(day)" :title="eventsFor(day,schedules).map(e=>dayName(e)).join('\n')"><span class="text-sm font-medium" x-text="day.number"></span><div class="flex gap-0.5 mt-0.5 h-1.5"><template x-for="(event,index) in eventsFor(day,schedules).slice(0,3)" :key="index"><span class="w-1.5 h-1.5 rounded-full" :class="eventColor(event.type)"></span></template></div></div></template></div>
    </div>
    <x-capstone::dialog id="schedule-day" class="sm:max-w-[440px]">
        <div class="py-4">
            <h3 class="text-base font-semibold text-gray-900" x-text="dayTitle()"></h3>
            <p class="text-sm text-gray-400 mt-0.5"><span x-text="dayList().length"></span> kegiatan</p>
            <div class="mt-4 space-y-2 max-h-[50vh] overflow-y-auto">
                <template x-for="event in dayList()" :key="event.id || (event.type+(event.start_time||''))">
                    <button type="button" @click="$el.closest('dialog').close();$dispatch('open-schedule-detail',{event})" class="w-full text-left flex items-center gap-3 rounded-xl border border-gray-100 px-3 py-2.5 hover:bg-gray-50 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="eventColor(event.type)"></span>
                        <span class="flex-1 min-w-0"><span class="block text-sm font-medium text-gray-800 truncate" x-text="dayName(event)"></span><span class="block text-xs text-gray-400 mt-0.5" x-text="dayTime(event)+(event.room ? ' • '+event.room : '')"></span></span>
                        <span class="rounded-md px-2 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600 shrink-0" x-text="typeLabel(event.type)"></span>
                    </button>
                </template>
                <p x-show="!dayList().length" class="text-sm text-gray-400 text-center py-6">Tidak ada kegiatan pada tanggal ini.</p>
            </div>
            <div class="flex justify-end mt-4"><x-capstone::button variant="outline" size="sm" @click="$el.closest('dialog').close()">Tutup</x-capstone::button></div>
        </div>
    </x-capstone::dialog>
</x-capstone::card>
