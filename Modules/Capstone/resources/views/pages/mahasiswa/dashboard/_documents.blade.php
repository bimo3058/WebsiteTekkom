<x-capstone::card class="min-w-0 gap-8 rounded-2xl border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between gap-4 px-6">
        <h3 class="flex items-center gap-2 text-base font-medium text-slate-900">
            <x-capstone::icon name="FileText" class="size-4" />Upload Document
        </h3>
        <x-capstone::feature-link href="/mahasiswa/documents" class="shrink-0 text-xs text-primary hover:underline">Lihat Semua</x-capstone::feature-link>
    </div>
    <div class="px-6">
        <p x-show="!dashboardDocuments.length" class="py-8 text-center text-sm text-slate-500">Belum ada dokumen yang perlu diunggah untuk kelompokmu.</p>
        <div x-show="dashboardDocuments.length" class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[560px] text-left text-sm">
                <caption class="sr-only">Ringkasan dokumen Capstone dan status unggah</caption>
                <thead class="border-b border-slate-200 text-xs text-slate-500">
                    <tr><th scope="col" class="w-[39%] px-2 py-3 font-normal">Nama Dokumen</th><th scope="col" class="px-2 py-3 font-normal">Tipe</th><th scope="col" class="px-2 py-3 font-normal">Status</th><th scope="col" class="px-2 py-3 text-right font-normal">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <template x-for="doc in pagedDocuments" :key="doc.phase+':'+doc.type">
                        <tr class="h-11 hover:bg-slate-50/50">
                            <td class="px-2 py-2"><span class="flex items-center gap-2 font-medium text-slate-900"><x-capstone::icon name="FileText" class="size-3.5 shrink-0 text-slate-400" /><span x-text="doc.name"></span></span></td>
                            <td class="px-2 py-2 text-xs text-slate-500" x-text="doc.phase"></td>
                            <td class="px-2 py-2"><span class="inline-flex whitespace-nowrap rounded-full border px-2 py-0.5 text-[10px] font-medium" :class="documentStatusClass(doc.status)" x-text="documentStatus(doc.status)"></span></td>
                            <td class="px-2 py-2 text-right">
                                <a x-show="doc.latest_document?.id || doc.can_upload" :href="documentHref(doc)" :aria-label="(doc.latest_document?.id ? 'Lihat ' : 'Upload ')+doc.type" :title="(doc.latest_document?.id ? 'Lihat ' : 'Upload ')+doc.type" class="inline-flex size-7 items-center justify-center rounded-md text-primary hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary">
                                    <x-capstone::icon name="Eye" class="size-4 text-slate-400" x-show="doc.latest_document?.id" />
                                    <x-capstone::icon name="Upload" class="size-4" x-show="!doc.latest_document?.id" />
                                </a>
                                <button x-show="!doc.latest_document?.id && !doc.can_upload" type="button" disabled :title="doc.locked_reason || 'Fase belum dibuka'" :aria-label="'Upload '+doc.type+' belum tersedia'" class="inline-flex size-7 items-center justify-center text-slate-400"><x-capstone::icon name="Upload" class="size-4" /></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="dashboardDocuments.length" class="flex flex-wrap items-center gap-3 px-6 py-3">
            <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs text-slate-500">Per page <span class="font-semibold text-slate-800">5</span></span>
            <span class="text-sm text-slate-800">Showing <span x-text="docFrom"></span> to <span x-text="docTo"></span> of, <span x-text="docTotal"></span> results</span>
            <div class="ml-auto flex items-center gap-1.5">
                <button type="button" @click="docPage=Math.max(1,docCurrentPage-1)" :disabled="docCurrentPage<=1" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:text-slate-300" aria-label="Previous page"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="p in docPageList" :key="'doc-'+p"><button type="button" @click="typeof p === 'number' && (docPage=p)" :disabled="typeof p !== 'number'" class="rounded-lg px-3 py-1.5 text-sm font-medium" :class="p===docCurrentPage ? 'bg-[#2f3d8a] text-white' : 'border border-slate-200 text-slate-600'" x-text="p"></button></template>
                <button type="button" @click="docPage=Math.min(docTotalPages,docCurrentPage+1)" :disabled="docCurrentPage>=docTotalPages" class="rounded-lg border border-slate-200 p-1.5 text-slate-500 disabled:text-slate-300" aria-label="Next page"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
</x-capstone::card>
