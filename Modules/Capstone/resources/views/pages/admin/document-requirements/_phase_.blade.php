@extends('capstone::layouts.app')
@section('title','Edit Persyaratan Dokumen')
@section('content')
@php
    $phase = strtoupper($pageParams['phase'] ?? '');
    abort_unless(in_array($phase, ['PDC1','SEMPRO','PDC2','EXPO','TA','SIDANG'], true), 404);
@endphp
<div class="space-y-5" x-data="documentRequirements(@js($phase))">
    <div class="flex items-center justify-between gap-4">
        <a :href="phaseUrl()" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-input bg-white px-3.5 text-sm font-semibold text-muted-foreground shadow-xs hover:bg-accent"><x-capstone::icon name="ChevronLeft" class="size-4" />Kembali</a>
        <button type="button" @click="save()" :disabled="!editable" class="inline-flex h-9 items-center gap-2 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Loader2" x-show="saving" class="size-4 animate-spin" />Simpan</button>
    </div>

    <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Edit Persyaratan Dokumen</h1>
        <p class="mt-1 text-sm text-muted-foreground">Konfigurasikan dokumen yang diperlukan untuk setiap tahapan pada setiap periode.</p>
    </div>

    @include('capstone::pages.admin.document-requirements.finalized')
    @include('capstone::partials.loading')

    <div x-show="!loading && !error" x-cloak class="grid grid-cols-1 gap-6 rounded-xl border border-border bg-white p-5 shadow-xs lg:grid-cols-[280px_1fr]">
        <div>
            <h2 class="text-base font-semibold uppercase" x-text="labels[phase]"></h2>
            <p class="mt-1 text-sm text-muted-foreground" x-text="'Kelola jenis dokumen untuk '+labels[phase]+'.'"></p>
            <div class="mt-4">
                <label for="phase-period" class="text-sm font-medium text-muted-foreground">Periode</label>
                <select id="phase-period" x-model="selectedPeriod" @change="load()" :disabled="saving" class="mt-1 h-9 w-full rounded-lg border border-input bg-white px-3 text-sm font-medium outline-none focus:border-ring">
                    <option value="" disabled>Pilih periode</option>
                    <template x-for="period in periods" :key="period.id">
                        <option :value="String(period.id)" x-text="period.name+(period.is_active ? ' (Aktif)' : '')"></option>
                    </template>
                </select>
            </div>
            <div class="mt-4 border-t border-border pt-3">
                <div class="flex items-center justify-between"><span class="text-sm text-muted-foreground">Total Dokumen</span><span class="text-lg font-semibold" x-text="items.length"></span></div>
                <div class="mt-1 flex items-center justify-between"><span class="text-sm text-muted-foreground">Required</span><span class="text-lg font-semibold text-emerald-600" x-text="requiredCount"></span></div>
            </div>
        </div>

        <div class="min-w-0 overflow-hidden rounded-xl border border-border">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <h3 class="text-base font-semibold">Tipe Dokumen</h3>
                <button type="button" @click="add()" :disabled="!editable || !newName.trim()" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-[#1E2A5A] px-4 text-sm font-semibold text-white shadow-xs hover:bg-[#16204a] disabled:opacity-40"><x-capstone::icon name="Plus" class="size-4" />Tambah</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead>
                        <tr class="bg-[#F8F9FB] text-[13px] font-medium text-[#666D80]">
                            <th class="w-12 px-4 py-3 font-medium">Wajib</th>
                            <th class="px-4 py-3 font-medium">Nama Dokumen</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="w-16 px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <template x-for="item in items" :key="item._key">
                            <tr class="bg-white hover:bg-muted/30">
                                <td class="px-4 py-3"><input type="checkbox" x-model="item.is_required" :disabled="!editable" :aria-label="'Wajib: '+item.name" class="h-4 w-4 rounded border accent-primary" /></td>
                                <td class="px-4 py-3">
                                    <span x-show="finalized" class="font-medium" x-text="item.name"></span>
                                    <input x-show="!finalized" x-model="item.name" maxlength="255" aria-label="Nama Dokumen" :disabled="!editable" class="h-10 w-full min-w-36 rounded-lg border border-input bg-white px-3 text-sm outline-none focus:border-ring" />
                                </td>
                                <td class="px-4 py-3">
                                    <span x-show="finalized" class="text-muted-foreground" x-text="item.description || '-'"></span>
                                    <input x-show="!finalized" x-model="item.description" placeholder="(optional)" aria-label="Deskripsi" :disabled="!editable" class="h-10 w-full min-w-36 rounded-lg border border-input bg-white px-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring" />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="remove(item)" :disabled="!editable" aria-label="Hapus dokumen" class="rounded-md p-1.5 text-red-600 hover:bg-red-50 disabled:opacity-40"><x-capstone::icon name="Trash2" class="size-4" /></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!finalized" class="bg-white">
                            <td class="px-4 py-3"></td>
                            <td class="px-4 py-3"><input x-model="newName" @keydown.enter="add()" maxlength="255" placeholder="masukkan nama dokumen" aria-label="Nama dokumen baru" :disabled="!editable" class="h-10 w-full min-w-36 rounded-lg border border-input bg-white px-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring" /></td>
                            <td class="px-4 py-3"><input x-model="newDescription" @keydown.enter="add()" placeholder="(optional)" aria-label="Deskripsi dokumen baru" :disabled="!editable" class="h-10 w-full min-w-36 rounded-lg border border-input bg-white px-3 text-sm outline-none placeholder:text-muted-foreground focus:border-ring" /></td>
                            <td class="px-4 py-3 text-center"></td>
                        </tr>
                    </tbody>
                </table>
                <p x-show="!items.length && finalized" class="p-8 text-center text-sm text-muted-foreground">Belum ada dokumen yang dikonfigurasi.</p>
            </div>
        </div>
    </div>
    <p x-show="error" x-text="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></p>
</div>
@endsection
