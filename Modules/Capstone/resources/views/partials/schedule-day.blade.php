<x-capstone::dialog id="schedule-day" class="sm:max-w-[440px]">
    <div class="py-4">
        <h3 class="text-base font-semibold text-slate-900" x-text="selectedLabel"></h3>
        <p class="mt-0.5 text-sm text-slate-400"><span x-text="dayEvents.length"></span> kegiatan</p>
        <div class="mt-4 max-h-[50vh] space-y-2 overflow-y-auto">
            <template x-for="event in dayEvents" :key="event._key || event.id">
                <button type="button" @click="$el.closest('dialog').close();detail(event)" class="flex w-full items-center gap-3 rounded-xl border border-slate-100 px-3 py-2.5 text-left transition-colors hover:bg-slate-50">
                    <span class="rounded px-2 py-0.5 text-[11px] font-semibold" :class="color(event.type)" x-text="label(event.type)"></span>
                    <span class="min-w-0 flex-1 truncate text-[13px] font-medium text-slate-800" x-text="title(event)"></span>
                    <span class="shrink-0 text-xs text-slate-400" x-text="(event.start_time || '').slice(0,5)"></span>
                </button>
            </template>
            <p x-show="!dayEvents.length" class="py-6 text-center text-sm text-slate-400">Tidak ada kegiatan pada tanggal ini.</p>
        </div>
        <div class="mt-4 flex justify-end"><x-capstone::button variant="outline" size="sm" @click="$el.closest('dialog').close()">Tutup</x-capstone::button></div>
    </div>
</x-capstone::dialog>
