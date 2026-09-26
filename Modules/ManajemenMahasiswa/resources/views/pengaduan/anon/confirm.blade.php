@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Konfirmasi Pengaduan Konfidensial')

@section('content')
    {{-- Tampilan konfirmasi dipakai bersama jalur Reguler & Konfidensial. --}}
    @include('manajemenmahasiswa::pengaduan.partials.konfirmasi', [
        'payload' => $payload,
        'isAnonim' => true,
        'backUrl' => route('manajemenmahasiswa.pengaduan.track', ['token' => $token]),
        'formAction' => route('manajemenmahasiswa.pengaduan.anon.store', ['token' => $token]),
    ])
@endsection
