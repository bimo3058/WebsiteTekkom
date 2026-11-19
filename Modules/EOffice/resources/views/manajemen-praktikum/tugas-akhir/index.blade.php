<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum" pageTitle="Tugas Akhir">
    @switch($role)
        @case('koor') <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" /> @break
        @case('dosen') <x-eoffice::manajemen-praktikum.dosen-header :praktikum="$praktikum" /> @break
        @case('asprak') <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" :activeTab="$section === 'nilai' ? 'absensi' : $section" /> @break
        @default <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$praktikum" />
    @endswitch
    @php
        $parent = $section === 'nilai' ? ($role === 'asprak' ? 'absensi' : 'nilai') : $section;
        $backUrl = $role === 'dosen' && $section !== 'tugas'
            ? route('eoffice.manprak.dosen.'.$parent.'.index',$praktikum->id)
            : route('eoffice.manprak.'.$role.'.'.$parent.'.index',['praktikum_id'=>$praktikum->id]);
    @endphp
    <div class="mp-page-header">
        <div><h1 class="mp-page-title">{{ match($section){'nilai'=>'Absensi & Nilai','tugas'=>'Tugas',default=>'Ketentuan'} }} — Tugas Akhir</h1><p class="mp-page-sub">{{ $praktikum->nama }}</p></div>
        <a class="mp-btn secondary md" href="{{ $backUrl }}" style="font-size:13px;text-decoration:none">Kembali</a>
    </div>
    @include('eoffice::manajemen-praktikum.tugas-akhir.content')
</x-eoffice::manajemen-praktikum.layout>
