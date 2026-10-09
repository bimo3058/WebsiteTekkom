<div class="grid grid-cols-1 gap-5 p-6 sm:p-8 lg:grid-cols-[280px_1fr]">
    <div>
        <h2 class="text-base font-bold text-gray-900">Konfigurasi Group</h2>
        <p class="mt-1 text-sm text-gray-500">Beban supervisi maksimum adalah jumlah maksimal kelompok yang dapat dibimbing oleh seorang dosen pada periode ini.</p>
    </div>
    <div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="field-min_group_size" class="text-sm text-gray-600">Minimal Jumlah Anggota <span class="text-red-500">*</span></label>
                <input id="field-min_group_size" type="number" min="1" max="10" x-model="form.min_group_size" placeholder="Masukkan min anggota" class="mt-2 h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none placeholder:text-gray-400 focus:border-[#1E2A5A]" />
                <p class="mt-1 text-sm text-red-600" x-show="errors['min_group_size']" x-text="errors['min_group_size']?.[0]"></p>
            </div>
            <div>
                <label for="field-max_group_size" class="text-sm text-gray-600">Maximal Jumlah Anggota <span class="text-red-500">*</span></label>
                <input id="field-max_group_size" type="number" min="1" max="10" x-model="form.max_group_size" placeholder="Masukkan max anggota" class="mt-2 h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none placeholder:text-gray-400 focus:border-[#1E2A5A]" />
                <p class="mt-1 text-sm text-red-600" x-show="errors['max_group_size']" x-text="errors['max_group_size']?.[0]"></p>
            </div>
        </div>
        <div class="mt-4">
            <label for="field-max_supervisor_load" class="text-sm text-gray-600">Maximal Dosen Pembimbing <span class="text-red-500">*</span></label>
            <input id="field-max_supervisor_load" type="number" min="1" max="50" x-model="form.max_supervisor_load" placeholder="Masukkan max dosbing" class="mt-2 h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none placeholder:text-gray-400 focus:border-[#1E2A5A]" />
            <p class="mt-1 text-sm text-red-600" x-show="errors['max_supervisor_load']" x-text="errors['max_supervisor_load']?.[0]"></p>
        </div>
    </div>
</div>
