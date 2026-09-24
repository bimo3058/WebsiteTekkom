@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Buat Pengaduan Konfidensial')

@section('content')
    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.anon.confirm', ['token' => $token]) }}" enctype="multipart/form-data" style="position: relative; margin: 0;">
        @csrf
        @include('manajemenmahasiswa::pengaduan.partials.honeypot')

        @include('manajemenmahasiswa::pengaduan.partials.form-fields', [
            'jalur'             => 'konfidensial',
            'kategoriList'      => $kategoriList,
            'dosenList'         => $dosenList ?? [],
            'frekuensiList'     => $frekuensiList ?? [],
            'buktiPendingItems' => $buktiPendingItems ?? [],
            'backUrl'           => route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]),
        ])
    </form>
@endsection
