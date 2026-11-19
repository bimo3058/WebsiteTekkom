@php
    $phaseSections = [
        ['key' => 'bidding', 'title' => 'Bidding', 'desc' => 'Set tanggal untuk jangka waktu Bidding', 'start' => 'bidding_start', 'end' => 'bidding_end', 'reminder' => 'bidding_reminder_at'],
        ['key' => 'pdc1', 'title' => 'PDC1', 'desc' => 'Set tanggal untuk jangka waktu PDC1', 'start' => 'pdc1_start', 'end' => 'pdc1_end', 'reminder' => 'pdc1_reminder_at'],
        ['key' => 'pdc2', 'title' => 'PDC2', 'desc' => 'Set tanggal untuk jangka waktu PDC2', 'start' => 'pdc2_start', 'end' => 'pdc2_end', 'reminder' => 'pdc2_reminder_at'],
        ['key' => 'expo', 'title' => 'EXPO TA', 'desc' => 'Set tanggal untuk jangka waktu Expo TA', 'start' => 'expo_date', 'end' => 'expo_date', 'reminder' => 'expo_reminder_at'],
        ['key' => 'ta', 'title' => 'Sidang TA', 'desc' => 'Set tanggal untuk jangka waktu Sidang TA', 'start' => 'ta_start', 'end' => 'ta_end', 'reminder' => 'ta_reminder_at'],
    ];
@endphp
<div class="divide-y divide-gray-100">
    @foreach($phaseSections as $section)
    <div class="grid grid-cols-1 gap-5 p-6 sm:p-8 lg:grid-cols-[280px_1fr]">
        <div>
            <h3 class="text-base font-bold text-gray-900">{{ $section['title'] }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $section['desc'] }}</p>
        </div>
        <div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="field-{{ $section['start'] }}-{{ $section['key'] }}" class="text-sm text-gray-600">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <div class="relative mt-2">
                        <x-capstone::icon name="Calendar" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" />
                        <input id="field-{{ $section['start'] }}-{{ $section['key'] }}" type="date" x-model="form.{{ $section['start'] }}" class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A]" />
                    </div>
                    <p class="mt-1 text-sm text-red-600" x-show="errors['{{ $section['start'] }}']" x-text="errors['{{ $section['start'] }}']?.[0]"></p>
                </div>
                <div>
                    <label for="field-{{ $section['end'] }}-{{ $section['key'] }}-end" class="text-sm text-gray-600">Tanggal Berakhir <span class="text-red-500">*</span></label>
                    <div class="relative mt-2">
                        <x-capstone::icon name="Calendar" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" />
                        <input id="field-{{ $section['end'] }}-{{ $section['key'] }}-end" type="date" x-model="form.{{ $section['end'] }}" class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A]" />
                    </div>
                    <p class="mt-1 text-sm text-red-600" x-show="errors['{{ $section['key'] }}_end']" x-text="errors['{{ $section['key'] }}_end']?.[0]"></p>
                </div>
            </div>
            <div class="mt-4">
                <label for="field-{{ $section['reminder'] }}" class="text-sm text-gray-600">Tanggal Pengingat <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <x-capstone::icon name="Calendar" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" />
                    <input id="field-{{ $section['reminder'] }}" type="date" x-model="form.{{ $section['reminder'] }}" class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A]" />
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
