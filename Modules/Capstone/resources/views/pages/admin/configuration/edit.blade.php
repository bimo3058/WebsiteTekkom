@php $peer = $peer ?? false; @endphp
@extends('capstone::layouts.app')
@section('title','Edit Konfigurasi Penilaian')
@section('content')
<div x-data="adminAssessmentConfig({{ $peer ? 'true' : 'false' }},true)" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4"><div><h1 class="text-3xl font-bold" x-text="type.replaceAll('_',' ')"></h1><p class="text-muted-foreground">Pilih dan urutkan komponen dari bank penilaian.</p></div><x-capstone::button :href="$peer ? '/admin/peer-review' : '/admin/period-assessment-config'" variant="outline">Kembali</x-capstone::button></div>
    @include('capstone::pages.admin.shared.toolbar')
    @include('capstone::partials.loading')
    <p x-show="period?.is_finalized" class="rounded-lg border bg-muted p-4 text-sm">Periode sudah difinalisasi. Konfigurasi hanya dapat dilihat.</p>
    <div x-show="!loading && !error && periodId" x-cloak class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-2">
            <x-capstone::card title="Bank Komponen"><div class="space-y-3 px-6"><template x-for="template in templates.filter(t=>(t.code+' '+t.name).toLowerCase().includes(search.toLowerCase()))" :key="template.id"><label class="flex items-start gap-3 rounded-lg border p-3"><input type="checkbox" :checked="selectedIds.includes(Number(template.id))" @change="toggle(template.id)" :disabled="saving || period?.is_finalized" class="mt-1"><div class="flex-1"><strong x-text="(template.code?template.code+' — ':'')+template.name"></strong><p class="mt-1 text-sm text-muted-foreground" x-text="template.description"></p></div><span class="text-sm" x-text="template.weight+'%'"></span></label></template><p x-show="!templates.length" class="text-muted-foreground">Bank komponen masih kosong.</p></div></x-capstone::card>
            <x-capstone::card title="Komponen Terpilih"><div class="space-y-3 px-6"><template x-for="(template,index) in selectedTemplates" :key="template.id"><div class="flex items-center gap-3 rounded-lg border p-3"><span x-text="index+1"></span><span class="flex-1" x-text="template.code || template.name"></span><span x-text="template.weight+'%'"></span><button @click="move(index,-1)" :disabled="index===0 || period?.is_finalized" aria-label="Pindah ke atas" class="rounded border p-1">↑</button><button @click="move(index,1)" :disabled="index===selectedIds.length-1 || period?.is_finalized" aria-label="Pindah ke bawah" class="rounded border p-1">↓</button></div></template><p x-text="'Total bobot: '+totalWeight+'%'"></p></div></x-capstone::card>
        </div>
        <div class="flex flex-wrap items-center gap-3"><select x-model="copyFrom" aria-label="Periode sumber" class="rounded border bg-background p-2"><option value="">Salin dari periode...</option><template x-for="p in periods.filter(p=>String(p.id)!==periodId)" :key="p.id"><option :value="p.id" x-text="p.name"></option></template></select><x-capstone::button variant="outline" @click="copy()" ::disabled="saving || !copyFrom || period?.is_finalized">Salin konfigurasi</x-capstone::button><x-capstone::button @click="save()" ::disabled="saving || period?.is_finalized">Simpan konfigurasi</x-capstone::button></div>
    </div>
</div>
@endsection
