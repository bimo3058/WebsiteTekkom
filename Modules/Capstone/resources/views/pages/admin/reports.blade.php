@extends('capstone::layouts.app')
@section('title','Reports')
@section('content')
<div x-data="adminReports('summary')" class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Reports</h1>
            <p class="mt-1 text-sm text-muted-foreground">Ringkasan laporan penilaian, peer review, kelompok, dan nilai akhir pada periode berjalan.</p>
        </div>
    </div>
    @include('capstone::pages.admin.shared.toolbar')
    @include('capstone::partials.loading')
    <div x-show="!loading && !error && periodId" x-cloak class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <a :href="link('/admin/reports/pdc1')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">PDC 1</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'assessments.pdc1_students')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'assessments.pdc1_average')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/pdc2')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">PDC 2</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'assessments.pdc2_students')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'assessments.pdc2_average')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/ta')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Tugas Akhir</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'assessments.ta_students')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'assessments.ta_average')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/assessments')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Penilaian Mahasiswa</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'assessments.total_students')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'assessments.average_score')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/peer-reviews')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Peer Reviews</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'peer_reviews.total_reviews')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'peer_reviews.average_score')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/final-grades')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Nilai Akhir</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'final_grades.total_students')"></p><p class="text-sm text-muted-foreground" x-text="'Rata-rata: '+value(summary,'final_grades.pdc1_average')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/groups')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Kelompok</h2><p class="my-3 text-3xl font-bold tabular-nums text-foreground" x-text="value(summary,'groups.total_groups')"></p><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
        <a :href="link('/admin/reports/grade-consistency')" class="block rounded-xl border border-border bg-white p-6 shadow-xs transition-colors hover:border-[#1E2A5A]"><h2 class="text-base font-semibold text-foreground">Konsistensi Nilai</h2><p class="mt-4 text-sm font-medium text-[#1E2A5A]">Buka laporan</p></a>
    </div>
</div>
@endsection
