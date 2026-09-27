@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Lacak Pengaduan Konfidensial')

@section('content')
    {{-- Kotak yang sama dengan halaman Detail; tanpa pelapor, riwayat, maupun aksi. --}}
    @include('manajemenmahasiswa::pengaduan.partials.detail-box', [
        'pengaduan'     => $pengaduan,
        'kategoriLabel' => $kategoriLabel ?? null,
        'title'         => 'Status Pengaduan',
        'isStaff'       => false,
        'backUrl'       => null,
        'buktiToken'    => $pengaduan->anon_token,
    ])
@endsection
