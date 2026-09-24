@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Konfirmasi Pengaduan Konfidensial')

@push('styles')
    <style>
        .anon-container { max-width: 1040px; }
    </style>
@endpush

@section('content')
    {{-- Tampilan konfirmasi dipakai bersama jalur Reguler & Konfidensial. --}}
    @include('manajemenmahasiswa::pengaduan.partials.konfirmasi', [
        'payload' => $payload,
        'isAnonim' => true,
        'backUrl' => route('manajemenmahasiswa.pengaduan.track', ['token' => $token]),
        'formAction' => route('manajemenmahasiswa.pengaduan.anon.store', ['token' => $token]),
    ])
@endsection
