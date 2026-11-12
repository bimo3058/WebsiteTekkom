@extends('capstone::layouts.app')
@section('title','Judul Saya')
@section('content')
<div x-data="lecturerTitles" class="space-y-6">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div><h1 class="text-3xl font-bold tracking-tight">Kelola Judul</h1><p class="text-muted-foreground">Buat dan kelola judul proyek akhir Anda.</p></div>
        <div class="flex flex-wrap items-center gap-3">@include('capstone::pages.dosen.shared.period')<x-btn @click="edit()"><x-capstone::icon name="Plus" class="mr-2 h-4 w-4" />Tambah Judul</x-btn></div>
    </div>
    <div class="flex flex-wrap items-center gap-4">
        <input type="search" x-model.debounce.200ms="search" @input="page=1" placeholder="Cari judul..." aria-label="Cari judul" class="file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-9 w-full min-w-0 rounded-radius border bg-surface px-3 py-1 text-sm text-on-surface shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20 min-w-48 flex-1" />
        <div class="flex flex-wrap gap-3"><template x-for="spec in specializations" :key="spec"><label class="flex items-center gap-1.5 text-xs"><input type="checkbox" x-model="filterSpecs" :value="spec" @change="page=1" class="accent-primary"><span x-text="spec"></span></label></template></div>
    </div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-4">
        <div class="overflow-x-auto rounded-lg border"><table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b bg-muted/40"><tr>
                @foreach(['title'=>'Judul','specializations'=>'Spesialisasi','quota'=>'Kuota','active_groups_count'=>'Kelompok','status'=>'Status'] as $key=>$label)<th class="p-4"><button type="button" @click="sort('{{ $key }}')">{{ $label }} <span x-show="sortKey==='{{ $key }}'" x-text="sortDirection===1?'↑':'↓'"></span></button></th>@endforeach
                <th class="p-4 text-right">Aksi</th>
            </tr></thead>
            <tbody><template x-for="title in visible" :key="title.id"><tr class="border-b last:border-0 hover:bg-muted/30">
                <td class="max-w-xs p-4 font-medium"><a :href="url('/dosen/titles/'+title.id)" class="hover:underline" x-text="title.title"></a></td>
                <td class="p-4"><div class="flex flex-wrap gap-1"><template x-for="spec in title.specializations||[]" :key="spec"><span class="rounded-md border px-2 py-0.5 text-xs" x-text="spec"></span></template></div></td>
                <td class="p-4" x-text="title.quota"></td><td class="p-4" x-text="title.active_groups_count||0"></td>
                <td class="p-4"><span class="rounded-full px-2 py-1 text-xs" :class="statusColor(title.status)" x-text="title.status"></span></td>
                <td class="p-4"><div class="flex justify-end gap-2"><x-btn style="outline" size="sm" @click="edit(title)">Ubah</x-btn><x-btn style="danger" size="sm" @click="confirmDelete(title)" x-bind:disabled="saving || title.active_groups_count>0">Hapus</x-btn></div></td>
            </tr></template></tbody>
        </table><p x-show="!filtered.length" class="p-12 text-center text-muted-foreground">Tidak ada judul ditemukan. Buat judul baru atau sesuaikan filter Anda.</p></div>
        @include('capstone::pages.dosen.shared.pagination')
    </div>
    <x-capstone::dialog id="title-form" width="max-w-xl">
        <h2 class="text-lg font-semibold" x-text="editing?'Ubah Judul':'Tambah Judul Baru'"></h2><p class="mt-2 text-sm text-muted-foreground">Tawarkan judul proyek untuk diperebutkan mahasiswa.</p>
        <form @submit.prevent="save()" class="mt-4 space-y-4"><fieldset :disabled="saving" class="space-y-4">
            <x-capstone::field name="title" label="Judul" placeholder="cth., Sistem Rekomendasi TA" maxlength="255" required />
            <x-capstone::field name="description" label="Deskripsi" type="textarea" placeholder="Masukkan deskripsi judul..." required />
            <x-capstone::field name="problem_statement" label="Rumusan Masalah" type="textarea" placeholder="Masukkan rumusan masalah..." required />
            <x-capstone::field name="scope" label="Ruang Lingkup" type="textarea" placeholder="Masukkan ruang lingkup..." required />
            <fieldset class="space-y-2"><legend class="text-sm font-medium">Spesialisasi</legend><div class="flex flex-wrap gap-3"><template x-for="spec in specializations" :key="spec"><label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.specializations" :value="spec" class="accent-primary"><span x-text="spec"></span></label></template></div><p class="text-sm text-destructive" x-text="errors.specializations?.[0]"></p></fieldset>
            <x-capstone::field name="quota" label="Kuota (Kelompok)" type="number" placeholder="cth., 3" min="1" step="1" required />
            <div x-show="editing"><x-capstone::field name="status" label="Status" type="select" :options="['open'=>'Buka','closed'=>'Tutup']" /></div>
            <p class="text-sm text-destructive" role="alert" x-text="errors.root"></p>
            <div class="flex justify-end gap-2"><x-btn style="outline" @click="close('title-form')">Batal</x-btn><x-btn type="submit" x-bind:disabled="saving"><span x-text="saving?'Menyimpan...':editing?'Perbarui Judul':'Buat Judul'"></span></x-btn></div>
        </fieldset></form>
    </x-capstone::dialog>
    <x-capstone::dialog id="title-delete" title="Hapus Judul"><p class="my-4 text-sm">Hapus <strong x-text="deleting?.title"></strong>?</p><p class="text-sm text-destructive" x-text="errors.root"></p><div class="flex justify-end gap-2"><x-btn style="outline" @click="close('title-delete')">Batal</x-btn><x-btn style="danger" @click="remove()" x-bind:disabled="saving">Hapus</x-btn></div></x-capstone::dialog>
</div>
@endsection
