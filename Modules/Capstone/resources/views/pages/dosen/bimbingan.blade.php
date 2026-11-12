@extends('capstone::layouts.app')
@section('title','Bimbingan')
@section('content')
<div x-data="@yield('document-controller','lecturerDocuments')" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><h1 class="text-3xl font-bold tracking-tight">@yield('document-heading','Bimbingan')</h1><p class="text-muted-foreground">Tinjau dokumen dan berikan umpan balik untuk kelompok bimbingan Anda.</p></div><div @change="selectedGroup='all';page=1">@include('capstone::pages.dosen.shared.period')</div></div>
    <div class="flex flex-wrap gap-3"><input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Cari berdasarkan mahasiswa, judul, atau fase..." aria-label="Cari dokumen" class="file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-9 w-full min-w-0 rounded-radius border bg-surface px-3 py-1 text-sm text-on-surface shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 min-w-48 flex-1" />
        <x-capstone::select-menu x-model="selectedGroup" @select-menu-change.window="page=1" :options="['all'=>'Semua Kelompok']" placeholder="Semua Kelompok" aria-label="Filter berdasarkan kelompok" class="max-w-full" />
        <x-capstone::select-menu x-model="phase" @select-menu-change.window="page=1" :options="['all'=>'Semua Fase']" placeholder="Semua Fase" aria-label="Filter berdasarkan fase" />
        <x-capstone::select-menu x-model="status" @select-menu-change.window="page=1" :options="['all'=>'Semua Status','SUBMITTED'=>'SUBMITTED','APPROVED'=>'APPROVED','REVISE'=>'REVISE','REJECTED'=>'REJECTED']" placeholder="Semua Status" aria-label="Filter berdasarkan status" />
    </div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <p x-show="!filtered.length" class="rounded-xl border border-dashed p-12 text-center text-muted-foreground">Tidak ada dokumen ditemukan untuk filter yang dipilih.</p>
        <template x-for="entry in grouped" :key="entry.id"><section class="overflow-hidden rounded-xl border bg-card shadow-sm"><div class="border-b bg-muted/30 p-4"><h2 class="font-semibold" x-text="entry.group?.title?.title||'Kelompok '+entry.id"></h2><p class="mt-1 text-xs text-muted-foreground" x-text="entry.group?.code||'Kelompok '+entry.id"></p></div>
            <div class="overflow-x-auto"><table class="w-full min-w-[760px] text-left text-sm"><thead class="border-b"><tr>@foreach(['Dokumen','Mahasiswa','Diunggah','Status','Aksi'] as $label)<th class="p-4">{{ $label }}</th>@endforeach</tr></thead><tbody><template x-for="doc in entry.documents" :key="doc.id"><tr class="border-b last:border-0">
                <td class="max-w-xs p-4"><p class="font-medium" x-text="doc.document_type||doc.phase"></p><p class="mt-1 text-xs text-muted-foreground" x-text="doc.phase+' · Versi '+doc.version"></p><details x-show="doc.feedback" class="mt-2"><summary class="cursor-pointer text-xs text-primary">Lihat Umpan Balik</summary><p class="mt-2 whitespace-pre-wrap text-sm text-muted-foreground" x-text="doc.feedback"></p></details></td>
                <td class="p-4"><p x-text="doc.student?.name||'-'"></p><p class="mt-1 text-xs text-muted-foreground" x-text="doc.student?.nim"></p></td><td class="p-4 text-xs" x-text="date(doc.created_at,true)"></td><td class="p-4"><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(doc.status)" x-text="doc.status"></span></td><td class="p-4"><div class="flex gap-2"><x-btn style="outline" size="sm" @click="downloadDocument(doc)" x-bind:disabled="downloading!==null">Unduh</x-btn><x-btn size="sm" @click="review(doc)" x-bind:disabled="saving">Tinjau</x-btn></div></td>
            </tr></template></tbody></table></div>
        </section></template>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="document-review" title="Tinjau Dokumen"><p class="mt-3 text-sm text-muted-foreground" x-text="selected?(selected.document_type||selected.phase)+' · Versi '+selected.version:''"></p><form @submit.prevent="submit()" class="mt-4 space-y-4">
        <label class="grid gap-2 text-sm font-medium">Status Tinjauan<x-capstone::select-menu x-model="reviewStatus" :options="['APPROVED'=>'Disetujui','REVISE'=>'Revisi — Perlu Perbaikan','REJECTED'=>'Ditolak']" placeholder="Pilih status" aria-label="Status Tinjauan" /></label>
        <label class="grid gap-2 text-sm font-medium">Umpan Balik<textarea x-model="feedback" maxlength="500" rows="5" class="rounded-md border bg-background p-3 font-normal" placeholder="Masukkan umpan balik Anda di sini..."></textarea></label><p class="text-right text-xs text-muted-foreground" x-text="feedback.length+' / 500'"></p><p class="text-sm text-destructive" role="alert" x-text="errors.root"></p>
        <div class="flex justify-end gap-2"><x-btn style="outline" @click="close('document-review')">Batal</x-btn><x-btn type="submit" x-bind:disabled="saving"><span x-text="saving?'Menyimpan...':'Kirim Tinjauan'"></span></x-btn></div>
    </form></x-capstone::dialog>
</div>
@endsection
