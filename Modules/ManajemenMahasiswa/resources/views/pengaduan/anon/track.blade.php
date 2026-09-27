@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Pengaduan Konfidensial')

@section('content')
    {{-- Kotak yang sama dengan halaman Detail; tanpa pelapor, status, maupun aksi. --}}
    @include('manajemenmahasiswa::pengaduan.partials.detail-box', [
        'pengaduan'     => $pengaduan,
        'kategoriLabel' => $kategoriLabel ?? null,
        'title'         => 'Detail Pengaduan',
        'isStaff'       => false,
        'backUrl'       => null,
        'buktiToken'    => $pengaduan->anon_token,
    ])
@endsection
