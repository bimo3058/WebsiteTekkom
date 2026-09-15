@extends('capstone::layouts.app')
@section('title','Akses Terkunci')
@section('content')
<div class="flex flex-col items-center justify-center rounded-xl border bg-card py-16 px-6 text-center"><div class="rounded-full bg-muted p-4 mb-4"><x-capstone::icon name="Lock" class="h-8 w-8 text-muted-foreground" /></div><h1 class="text-xl font-semibold">Fitur belum tersedia</h1><p class="mt-2 text-sm text-muted-foreground">{{ $lockedReason }}</p><div class="mt-6"><x-capstone::button :href="empty($featureAccess['registered']) ? '/mahasiswa/registration' : '/mahasiswa/dashboard'">{{ empty($featureAccess['registered']) ? 'Registration' : 'Dashboard' }}</x-capstone::button></div></div>
@endsection
