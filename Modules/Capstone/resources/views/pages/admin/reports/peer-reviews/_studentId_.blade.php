@extends('capstone::layouts.app')
@section('title','Rincian Telaah Sejawat')
@section('content')
<div x-data="adminReports('peer-review-detail')" class="space-y-5">
    <div class="flex flex-wrap items-center gap-4">
        <a :href="link('/admin/reports/peer-reviews')" class="inline-flex size-9 items-center justify-center rounded-lg border border-input bg-white text-foreground hover:bg-accent" aria-label="Kembali ke laporan telaah sejawat"><x-capstone::icon name="ArrowLeft" class="size-4" /></a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Rincian Telaah Sejawat</h1>
            <p class="mt-1 text-sm text-muted-foreground">Dinilai oleh siapa saja dan nilainya berapa.</p>
        </div>
    </div>
    @include('capstone::partials.loading')
    <div x-show="error" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-text="error"></div>
    <div x-show="!loading && !error && !periodId" x-cloak class="rounded-xl border border-dashed border-border bg-white p-8 text-center text-sm text-muted-foreground">Buka halaman ini dari laporan Telaah Sejawat agar periode terisi.</div>
    <template x-if="!loading && !error && periodId && peerDetail">
        <div class="space-y-5">
            <div class="rounded-xl border border-border bg-white p-5 shadow-xs">
                <h3 class="text-base font-semibold text-foreground">Mahasiswa Dinilai</h3>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xl font-semibold text-foreground" x-text="peerDetail.reviewee.name"></p>
                        <p class="text-sm text-muted-foreground" x-text="(peerDetail.reviewee.nim || '-')+' · '+peerDetail.reviewee.group_name"></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span x-show="peerDetail.overall.status==='DINILAI'" class="inline-flex items-center rounded-full bg-[#1E2A5A] px-2.5 py-0.5 text-[11px] font-semibold text-white">Dinilai</span>
                        <span x-show="peerDetail.overall.status!=='DINILAI'" class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">Belum dinilai</span>
                        <div class="text-right">
                            <p class="text-2xl font-bold" :class="scoreClass(peerDetail.overall.avg_score)" x-text="fmtScore(peerDetail.overall.avg_score)"></p>
                            <p class="text-xs text-muted-foreground" x-text="peerDetail.overall.reviewers_count+' penilai · '+peerDetail.overall.reviews_count+' penilaian'"></p>
                        </div>
                    </div>
                </div>
            </div>
            <p x-show="!peerDetail.reviewers || !peerDetail.reviewers.length" class="rounded-xl border border-border bg-white p-8 text-center text-sm text-muted-foreground">Belum ada penilaian untuk mahasiswa ini.</p>
            <template x-for="reviewer in (peerDetail.reviewers || [])" :key="reviewer.reviewer_id">
                <div class="rounded-xl border border-border bg-white p-5 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="text-base font-semibold text-foreground" x-text="reviewer.reviewer_name"></p>
                            <p class="text-sm text-muted-foreground" x-text="reviewer.reviewer_nim || '-'"></p>
                        </div>
                        <p class="text-lg font-bold" :class="scoreClass(reviewer.avg_score)">
                            <span x-text="fmtScore(reviewer.avg_score)"></span>
                            <span class="ml-2 text-xs font-normal text-muted-foreground" x-text="'raw '+fmtScore(reviewer.avg_raw_score)"></span>
                        </p>
                    </div>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border text-left text-xs text-muted-foreground">
                                    <th class="py-2 pr-4 font-medium">Indikator</th>
                                    <th class="py-2 pr-4 font-medium">Bobot</th>
                                    <th class="py-2 pr-4 text-right font-medium">Raw (1-4)</th>
                                    <th class="py-2 pr-4 text-right font-medium">Nilai (0-100)</th>
                                    <th class="py-2 font-medium">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(d,i) in reviewer.details" :key="i">
                                    <tr class="border-b border-border last:border-0">
                                        <td class="py-2 pr-4"><span class="font-medium text-foreground" x-text="d.indicator_name"></span> <span class="text-xs text-muted-foreground" x-text="d.indicator_code"></span></td>
                                        <td class="py-2 pr-4 text-muted-foreground" x-text="d.weight ?? '-'"></td>
                                        <td class="py-2 pr-4 text-right font-semibold text-foreground" x-text="d.raw_score ?? '-'"></td>
                                        <td class="py-2 pr-4 text-right font-bold" :class="scoreClass(d.score)" x-text="d.score ?? '-'"></td>
                                        <td class="py-2 text-muted-foreground" x-text="d.comment || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </template>
    <div x-show="!loading && !error && periodId && !peerDetail" x-cloak class="rounded-xl border border-border bg-white p-8 text-center">
        <h3 class="text-base font-semibold text-foreground">Data tidak ditemukan</h3>
        <p class="mt-1 text-sm text-muted-foreground">Mahasiswa tidak ditemukan pada periode ini.</p>
    </div>
</div>
@endsection
