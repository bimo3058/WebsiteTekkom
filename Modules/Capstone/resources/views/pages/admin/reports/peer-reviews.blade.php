@extends('capstone::layouts.app')
@section('title','Laporan Telaah Sejawat')
@section('content')
<div x-data="adminReports('peer-review-summary')" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Telaah Sejawat</h1>
            <p class="mt-1 text-sm text-muted-foreground">Ringkasan telaah sejawat per mahasiswa — klik baris untuk melihat siapa saja penilainya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-capstone::button href="/admin/reports" variant="outline">Laporan</x-capstone::button>
            <button type="button" @click="exportPeerReviews('summary')" :disabled="loading || saving || !periodId" class="inline-flex h-9 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-medium text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Download" class="size-4" />Ekspor Ringkasan</button>
            <button type="button" @click="exportPeerReviews('detail')" :disabled="loading || saving || !periodId" title="Ekspor semua baris per indikator" class="inline-flex h-9 items-center gap-2 rounded-lg border border-input bg-white px-4 text-sm font-medium text-foreground shadow-xs hover:bg-accent disabled:opacity-40"><x-capstone::icon name="Download" class="size-4" />Rincian CSV</button>
        </div>
    </div>
    @include('capstone::pages.admin.shared.toolbar')
    <div class="flex flex-wrap items-center gap-2.5 rounded-xl border border-border bg-white p-4 shadow-xs">
        <span class="text-sm text-muted-foreground">Urutkan:</span>
        <select x-model="summarySort" @change="page=1;load()" aria-label="Urutkan berdasarkan" class="h-9 rounded-lg border border-input bg-white px-2.5 text-sm outline-none">
            <option value="reviewee">Mahasiswa</option>
            <option value="group">Kelompok</option>
            <option value="avg_score">Rata-rata nilai</option>
            <option value="reviewers_count">Jumlah penilai</option>
        </select>
        <button type="button" @click="summaryDir=summaryDir==='asc'?'desc':'asc';page=1;load()" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-input bg-white px-3 text-sm font-medium hover:bg-accent" :aria-label="summaryDir==='asc'?'Urut menaik':'Urut menurun'">
            <span x-text="summaryDir==='asc'?'↑ A–Z / 0–100':'↓ Z–A / 100–0'"></span>
        </button>
    </div>
    @include('capstone::partials.loading')
    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>
    <div x-show="!loading && !error && !periodId" x-cloak class="rounded-xl border border-dashed border-border bg-white p-8 text-center text-sm text-muted-foreground">Pilih periode terlebih dahulu untuk menampilkan laporan.</div>
    <div x-show="!loading && !error && periodId" x-cloak class="overflow-hidden rounded-xl border border-border bg-white shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-5">
            <h3 class="text-base font-semibold">Tabel Telaah Sejawat per Mahasiswa</h3>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead>
                    <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                        <th class="w-10 px-4 py-3 font-medium"></th>
                        <th class="px-4 py-3 font-medium">Mahasiswa Dinilai</th>
                        <th class="px-4 py-3 font-medium">Kelompok</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Penilai</th>
                        <th class="px-4 py-3 text-right font-medium">Rata-rata</th>
                        <th class="w-24 px-4 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="(item,index) in items" :key="item.reviewee_id || index">
                        <tr class="cursor-pointer bg-white hover:bg-muted/30" @click="toggleExpand(item)">
                            <td class="px-4 py-4 text-muted-foreground">
                                <span x-show="expandedId!==item.reviewee_id"><x-capstone::icon name="ChevronRight" class="size-4" /></span>
                                <span x-show="expandedId===item.reviewee_id"><x-capstone::icon name="ChevronDown" class="size-4" /></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-foreground" x-text="item.reviewee_name"></div>
                                <div class="text-xs text-muted-foreground" x-text="item.reviewee_nim || '-'"></div>
                            </td>
                            <td class="px-4 py-4 font-medium text-foreground" x-text="item.group_name"></td>
                            <td class="px-4 py-4">
                                <span x-show="item.status==='DINILAI'" class="inline-flex items-center rounded-full bg-[#1E2A5A] px-2.5 py-0.5 text-[11px] font-semibold text-white">Dinilai</span>
                                <span x-show="item.status!=='DINILAI'" class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">Belum dinilai</span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="font-semibold text-foreground" x-text="item.reviewers_count+' orang'"></span>
                                <span class="block text-xs text-muted-foreground" x-text="item.reviews_count+' penilaian'"></span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="text-base font-bold" :class="scoreClass(item.avg_score)" x-text="fmtScore(item.avg_score)"></span>
                                <span class="block text-xs text-muted-foreground" x-text="'raw '+fmtScore(item.avg_raw_score)"></span>
                            </td>
                            <td class="px-4 py-4 text-right" @click.stop>
                                <a :href="peerDetailUrl(item)" class="inline-flex h-8 items-center rounded-lg border border-input bg-white px-3 text-[13px] font-medium text-foreground hover:bg-accent">Rincian</a>
                            </td>
                        </tr>
                        <template x-if="expandedId===item.reviewee_id">
                            <tr class="bg-muted/20">
                                <td></td>
                                <td colspan="6" class="px-4 py-4">
                                    <div x-show="detailLoading && !detailFor(item)" class="py-3 text-sm text-muted-foreground">Memuat rincian penilaian...</div>
                                    <div x-show="!detailLoading && (!detailFor(item) || !detailFor(item).reviewers || !detailFor(item).reviewers.length)" class="py-3 text-sm text-muted-foreground">Belum ada penilaian untuk mahasiswa ini.</div>
                                    <template x-if="detailFor(item) && detailFor(item).reviewers && detailFor(item).reviewers.length">
                                        <div class="space-y-3">
                                            <template x-for="reviewer in detailFor(item).reviewers" :key="reviewer.reviewer_id">
                                                <div class="rounded-lg border border-border bg-white p-3">
                                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                                        <div>
                                                            <p class="text-sm font-semibold text-foreground" x-text="reviewer.reviewer_name"></p>
                                                            <p class="text-xs text-muted-foreground" x-text="(reviewer.reviewer_nim || '-')+' · '+reviewer.reviews_count+' indikator'"></p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm font-bold" :class="scoreClass(reviewer.avg_score)" x-text="fmtScore(reviewer.avg_score)"></p>
                                                            <p class="text-xs text-muted-foreground" x-text="'raw '+fmtScore(reviewer.avg_raw_score)"></p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 overflow-x-auto">
                                                        <table class="w-full text-sm">
                                                            <thead>
                                                                <tr class="text-left text-xs text-muted-foreground">
                                                                    <th class="py-1 pr-3 font-medium">Indikator</th>
                                                                    <th class="py-1 pr-3 text-right font-medium">Raw</th>
                                                                    <th class="py-1 pr-3 text-right font-medium">Nilai</th>
                                                                    <th class="py-1 font-medium">Komentar</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <template x-for="(d,i) in reviewer.details" :key="i">
                                                                    <tr class="border-t border-border">
                                                                        <td class="py-1.5 pr-3"><span class="font-medium text-foreground" x-text="d.indicator_name"></span> <span class="text-xs text-muted-foreground" x-text="d.indicator_code"></span></td>
                                                                        <td class="py-1.5 pr-3 text-right font-semibold text-foreground" x-text="d.raw_score ?? '-'"></td>
                                                                        <td class="py-1.5 pr-3 text-right font-bold" :class="scoreClass(d.score)" x-text="d.score ?? '-'"></td>
                                                                        <td class="py-1.5 text-muted-foreground" x-text="d.comment || '-'"></td>
                                                                    </tr>
                                                                </template>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </template>
                                            <div>
                                                <a :href="peerDetailUrl(item)" class="text-sm font-medium text-[#1E2A5A] hover:underline">Buka halaman rincian &rarr;</a>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
            <p x-show="!items.length" class="p-8 text-center text-sm text-muted-foreground">Belum ada data pada periode ini.</p>
        </div>
        <div x-show="items.length" x-cloak class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-5 py-3.5 text-sm">
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-0 overflow-hidden rounded-lg border border-input bg-white text-[13px]">
                    <span class="px-2.5 text-muted-foreground">Per halaman</span>
                    <select x-model.number="pageSize" @change="page=1;load()" aria-label="Baris per halaman" class="border-l border-input bg-transparent px-2 py-1.5 font-medium outline-none"><option>10</option><option>25</option><option>50</option></select>
                </label>
                <span class="text-foreground"><span x-text="'Menampilkan '+showingFrom+' sampai '+showingTo+' dari '+(pagination.total ?? 0)+' data'"></span></span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="gotoPage(page-1)" :disabled="page<=1" aria-label="Halaman sebelumnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
                <template x-for="(n,i) in pageNumbers" :key="n">
                    <span class="flex items-center gap-1.5">
                        <span x-show="i>0 && n-pageNumbers[i-1]>1" class="px-1 text-muted-foreground">...</span>
                        <button type="button" @click="gotoPage(n)" x-text="n" class="inline-flex size-8 items-center justify-center rounded-lg border text-sm" :class="page===n?'border-[#1E2A5A] bg-[#1E2A5A] font-semibold text-white':'border-input bg-white text-foreground hover:bg-accent'"></button>
                    </span>
                </template>
                <button type="button" @click="gotoPage(page+1)" :disabled="page>=(pagination.last_page||1)" aria-label="Halaman berikutnya" class="inline-flex size-8 items-center justify-center rounded-lg border border-input bg-white text-muted-foreground disabled:opacity-40"><x-capstone::icon name="ChevronRight" class="size-4" /></button>
            </div>
        </div>
    </div>
</div>
@endsection
