<div class="grid grid-cols-1 gap-5 p-6 sm:p-8 lg:grid-cols-[280px_1fr]">
    <div>
        <h2 class="text-base font-bold text-gray-900">Informasi Dasar</h2>
        <p class="mt-1 text-sm text-gray-500">Masukkan informasi dasar untuk periode akademik baru.</p>
    </div>
    <div>
        <div>
            <label for="field-name" class="text-sm text-gray-600">Nama Periode <span class="text-red-500">*</span></label>
            <input id="field-name" type="text" x-model="form.name" placeholder="Contoh: Semester Ganjil 2025/2026" maxlength="255" class="mt-2 h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none placeholder:text-gray-400 focus:border-[#1E2A5A]" />
            <p class="mt-1 text-sm text-red-600" x-show="errors['name']" x-text="errors['name']?.[0]"></p>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="field-start_date" class="text-sm text-gray-600">Tanggal Mulai <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <x-capstone::icon name="Calendar" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" />
                    <input id="field-start_date" type="date" x-model="form.start_date" class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A]" />
                </div>
                <p class="mt-1 text-sm text-red-600" x-show="errors['start_date']" x-text="errors['start_date']?.[0]"></p>
            </div>
            <div>
                <label for="field-end_date" class="text-sm text-gray-600">Tanggal Berakhir <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <x-capstone::icon name="Calendar" class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500" />
                    <input id="field-end_date" type="date" x-model="form.end_date" class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-500 outline-none focus:border-[#1E2A5A]" />
                </div>
                <p class="mt-1 text-sm text-red-600" x-show="errors['end_date']" x-text="errors['end_date']?.[0]"></p>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between gap-4">
            <div>
                <label for="period-active" class="text-sm font-medium text-gray-700">Set Aktif <span class="text-red-500">*</span></label>
                <p class="mt-0.5 text-xs text-gray-500">Periode yang aktif akan digunakan untuk proses akademik saat ini</p>
            </div>
            <input id="period-active" type="checkbox" role="switch" x-model="form.is_active" class="h-5 w-9 accent-[#1E2A5A]" />
        </div>
    </div>
</div>
