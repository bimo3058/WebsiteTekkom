@extends('capstone::layouts.app')
@section('title','Usulkan Judul')
@section('content')
<div x-data="studentProposals" class="space-y-6">@include('capstone::partials.loading')
<div x-show="!loading && !error" x-cloak class="space-y-6"><div class="flex flex-wrap justify-between items-start gap-4"><div><h1 class="text-3xl font-bold tracking-tight">Usulkan Judul</h1><p class="text-muted-foreground">Kirim usulan judul capstone Anda sendiri ke pembimbing.</p></div><div class="flex items-center gap-3"><x-capstone::badge variant="outline" x-show="isLeader" class="text-sm px-3 py-1"><span x-text="total+'/3 slot terpakai'"></span></x-capstone::badge><x-capstone::button x-show="canCreate && !pending && !approved && !showForm" @click="edit()"><x-capstone::icon name="PenLine" />Usulan Baru</x-capstone::button></div></div>
@include('capstone::pages.mahasiswa.propose-title.constraints')
@include('capstone::pages.mahasiswa.propose-title.form')
@include('capstone::pages.mahasiswa.propose-title.history')
<div x-show="!proposals.length && !showForm && canCreate" class="text-center py-12 text-muted-foreground border rounded-lg border-dashed"><x-capstone::icon name="PenLine" class="h-12 w-12 mx-auto mb-4 opacity-50" /><p class="text-lg font-medium mb-1">Belum ada usulan</p><p class="text-sm">Klik "Usulan Baru" untuk mengirim judul capstone Anda sendiri.</p></div>
</div></div>
@endsection
