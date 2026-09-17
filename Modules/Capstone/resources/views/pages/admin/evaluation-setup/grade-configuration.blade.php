@extends('capstone::layouts.app')
@section('title','Grade Configuration')
@section('content')
<div x-data="adminGradeConfig" class="space-y-6"><div><h1 class="text-3xl font-bold">Grade Configuration</h1><p class="text-muted-foreground">Atur bobot nilai PDC 1, PDC 2, dan Tugas Akhir per periode.</p></div>
@include('capstone::pages.admin.shared.toolbar')
@include('capstone::partials.loading')
<div x-show="!loading && !error && periodId" x-cloak class="space-y-6"><div class="grid gap-6 lg:grid-cols-3">
@foreach(['pdc1'=>'PDC 1','pdc2'=>'PDC 2','ta'=>'Tugas Akhir'] as $phase=>$label)
<x-capstone::card :title="$label"><div class="space-y-4 px-6"><template x-for="key in Object.keys(weights.{{ $phase }})" :key="key"><label class="block space-y-2"><span class="text-sm" x-text="key.replaceAll('_',' ')"></span><div class="flex items-center gap-2"><input type="number" min="0" max="100" step="0.01" x-model.number="weights.{{ $phase }}[key]" :disabled="saving" class="w-full rounded-md border bg-background p-2"><span>%</span></div></label></template><p :class="Math.abs(total('{{ $phase }}')-100)<0.001?'text-emerald-600':'text-red-600'" x-text="'Total: '+total('{{ $phase }}')+'%'" class="text-sm font-medium"></p></div></x-capstone::card>
@endforeach
</div><p x-show="!valid" class="text-sm text-red-600">Total bobot setiap fase harus 100%.</p><div class="flex gap-3"><x-capstone::button @click="save()" ::disabled="saving || !valid">Simpan</x-capstone::button><x-capstone::button variant="outline" @click="document.getElementById('reset-grades').showModal()" ::disabled="saving">Reset ke default</x-capstone::button></div></div>
<dialog id="reset-grades" class="max-w-md rounded-xl border bg-background p-6 backdrop:bg-black/40"><h2 class="text-lg font-semibold">Reset bobot nilai?</h2><p class="my-4 text-sm">Bobot pada periode yang dipilih akan diganti dengan bobot default sistem.</p><div class="flex gap-3"><x-capstone::button @click="reset()" ::disabled="saving">Reset</x-capstone::button><x-capstone::button variant="outline" @click="document.getElementById('reset-grades').close()">Batal</x-capstone::button></div></dialog>
</div>
@endsection
