<div class="border-t border-gray-100 p-6 sm:p-8">
    <h3 class="text-base font-bold text-gray-900">Kumpulan Komponen Penilaian</h3>
    <p class="mt-1 text-sm text-gray-500">Pilih komponen yang akan digunakan dalam <span class="font-medium" x-text="step===2 ? 'PEER REVIEW' : typeLabel(evaluationType)"></span> untuk periode yang dipilih.</p>
    <div class="mt-5 grid grid-cols-1 gap-6 xl:grid-cols-[380px_1fr]">
        <div class="h-fit rounded-2xl border border-gray-200 p-5">
            <h4 class="text-base font-bold text-gray-900">Ringkasan</h4>
            <div class="mt-3 flex items-center justify-between text-sm"><span class="text-gray-500">Total Komponen</span><span class="font-medium text-gray-900" x-text="activeCount"></span></div>
            <div class="mt-2 flex items-center justify-between text-sm"><span class="text-gray-500">Total Bobot</span><span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium" :class="activeWeight===100 ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-amber-300 bg-amber-50 text-amber-700'" x-text="activeWeight+'%'"></span></div>
            <h4 class="mt-5 text-base font-bold text-gray-900">Komponen terpilih :</h4>
            <div class="mt-3 overflow-hidden rounded-lg border border-gray-100">
                <table class="w-full text-sm">
                    <thead><tr class="bg-slate-50 text-left text-gray-500"><th class="px-4 py-2.5 font-medium">Kode</th><th class="px-4 py-2.5 text-right font-medium">Bobot</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr x-show="!activeTemplates.length"><td colspan="2" class="px-4 py-5 text-center text-gray-400">Belum ada komponen dipilih.</td></tr>
                        <template x-for="item in activeTemplates" :key="item.id"><tr>
                            <td class="px-4 py-3"><span class="inline-flex items-center gap-2 text-gray-800"><input type="checkbox" checked @change="toggleActive(item.id)" :disabled="!editable" class="h-4 w-4 rounded accent-[#1E2A5A]" :aria-label="item.code" /><span x-text="item.code"></span></span></td>
                            <td class="px-4 py-3 text-right"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="pillClass(item.weight)" x-text="item.weight+'%'"></span></td>
                        </tr></template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="rounded-2xl border border-gray-200">
            <div class="flex flex-wrap items-center gap-2 p-4">
                <h4 class="mr-auto text-base font-bold text-gray-900">Pilih Komponen</h4>
                <div class="relative">
                    <x-capstone::icon name="Search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input x-model="search" @input="onSearch()" type="search" placeholder="Search" aria-label="Cari komponen" class="h-10 w-56 rounded-lg border border-gray-200 pl-9 pr-3 text-sm text-gray-700 outline-none placeholder:text-gray-400 focus:border-[#1E2A5A]" />
                </div>
                <div class="relative">
                    <x-capstone::icon name="ListFilter" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" />
                    <select x-model="filter" @change="page=1" aria-label="Filter komponen" class="h-10 appearance-none rounded-lg border border-gray-200 bg-white pl-9 pr-8 text-sm text-gray-600 outline-none focus:border-[#1E2A5A]">
                        <option value="all">Filter</option>
                        <option value="high">Bobot ≥ 50%</option>
                        <option value="mid">Bobot 20–49%</option>
                        <option value="low">Bobot &lt; 20%</option>
                    </select>
                </div>
                <div class="relative">
                    <x-capstone::icon name="ArrowUpDown" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" />
                    <select x-model="sortBy" @change="page=1" aria-label="Urutkan komponen" class="h-10 appearance-none rounded-lg border border-gray-200 bg-white pl-9 pr-8 text-sm text-gray-600 outline-none focus:border-[#1E2A5A]">
                        <option value="default">Sort by</option>
                        <option value="kode">Kode A–Z</option>
                        <option value="nama">Nama A–Z</option>
                        <option value="bobot_desc">Bobot terbesar</option>
                        <option value="bobot_asc">Bobot terkecil</option>
                    </select>
                </div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-slate-50 text-left text-gray-500"><th class="w-10 px-4 py-2.5"><input type="checkbox" :checked="activeAllChecked" @change="toggleAllActive($event.target.checked)" :disabled="!editable" class="h-4 w-4 rounded accent-[#1E2A5A]" aria-label="Pilih semua komponen" /></th><th class="px-2 py-2.5 font-medium">Kode</th><th class="px-2 py-2.5 font-medium">Deskripsi</th><th class="px-4 py-2.5 text-right font-medium">Bobot</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    <tr x-show="!pagedChoices.length"><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada komponen yang tersedia.</td></tr>
                    <template x-for="item in pagedChoices" :key="item.id"><tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3"><input type="checkbox" :checked="activeIds.includes(item.id)" @change="toggleActive(item.id)" :disabled="!editable" class="h-4 w-4 rounded accent-[#1E2A5A]" :aria-label="item.code" /></td>
                        <td class="whitespace-nowrap px-2 py-3 font-medium text-gray-800" x-text="item.code"></td>
                        <td class="px-2 py-3 text-gray-600" x-text="item.description || item.name"></td>
                        <td class="px-4 py-3 text-right"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="pillClass(item.weight)" x-text="item.weight+'%'"></span></td>
                    </tr></template>
                </tbody>
            </table>
            <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 px-4 py-3 text-sm">
                <span class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2 py-1 text-gray-600">Per page
                    <select x-model.number="perPage" @change="setPerPage(perPage)" aria-label="Baris per halaman" class="bg-transparent outline-none"><option :value="10">10</option><option :value="20">20</option><option :value="50">50</option></select>
                </span>
                <span class="mx-auto text-gray-800">Showing <span x-text="pageFrom"></span> to <span x-text="pageTo"></span> of, <span x-text="totalResults"></span> results</span>
                <span class="inline-flex items-center gap-1">
                    <button type="button" @click="prevPage()" :disabled="page<=1" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40" aria-label="Halaman sebelumnya"><x-capstone::icon name="ChevronLeft" class="h-4 w-4" /></button>
                    <template x-for="(n, i) in pageNumbers" :key="i">
                        <button type="button" @click="goPage(n)" :disabled="n==='...'" class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg border px-2 text-gray-700" :class="n===page ? 'border-[#1E2A5A] bg-[#1E2A5A] text-white' : 'border-gray-200 hover:bg-gray-50'" x-text="n"></button>
                    </template>
                    <button type="button" @click="nextPage()" :disabled="page>=totalPages" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-40" aria-label="Halaman berikutnya"><x-capstone::icon name="ChevronRight" class="h-4 w-4" /></button>
                </span>
            </div>
        </div>
    </div>
</div>

</div>
