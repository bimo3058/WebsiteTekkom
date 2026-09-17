@extends('capstone::layouts.app')
@section('title','Halaman belum tersedia')
@section('content')
<div class="flex flex-col items-center justify-center rounded-xl border bg-card px-6 py-16 text-center"><div class="mb-4 rounded-full bg-muted p-4"><x-capstone::icon name="Info" class="h-8 w-8 text-muted-foreground" /></div><h1 class="text-xl font-semibold">Halaman belum tersedia</h1><p class="mt-2 text-sm text-muted-foreground">Halaman ini sedang disiapkan. Silakan gunakan menu yang tersedia.</p><div class="mt-6"><x-capstone::button href="/dashboard">Kembali ke Dashboard</x-capstone::button></div></div>
@endsection
