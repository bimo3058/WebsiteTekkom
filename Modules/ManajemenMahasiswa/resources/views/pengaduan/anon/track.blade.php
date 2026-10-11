@extends('manajemenmahasiswa::pengaduan.anon.layout')

@section('title', 'Pengaduan Anonim')

@section('content')
    @if ($pengaduan)
        {{-- Kotak yang sama dengan halaman Detail; tanpa pelapor, status, maupun aksi. --}}
        @include('manajemenmahasiswa::pengaduan.partials.detail-box', [
            'pengaduan'     => $pengaduan,
            'kategoriLabel' => $kategoriLabel ?? null,
            'title'         => 'Detail Pengaduan',
            'isStaff'       => false,
            'backUrl'       => null,
            'buktiToken'    => $pengaduan->anon_token,
        ])
    @else
        {{-- Draft dari alur magic link lama yang tak pernah dikirim. --}}
        @include('manajemenmahasiswa::pengaduan.partials.box-styles')
        <div class="kf-box" style="max-width: 560px; margin: 40px auto 0; padding: 32px 28px; text-align: center;">
            <div class="kf-icon is-konfidensial" style="margin: 0 auto 16px;">
                <x-manajemenmahasiswa::ui.icon name="link-01" size="26" />
            </div>
            <h1 class="kf-subject" style="font-size: 18px; margin-bottom: 8px;">Tautan ini sudah tidak berlaku</h1>
            <p style="font-size: 13px; color: var(--c-fg-muted); line-height: 1.6; margin: 0;">
                Tautan ini dibuat untuk formulir pengaduan versi lama dan pengaduannya tidak pernah dikirim.
                Silakan buat pengaduan baru melalui menu <strong>Layanan Pengaduan</strong>; untuk merahasiakan
                identitas, centang <strong>Sembunyikan identitas saya</strong> di formulirnya.
            </p>
        </div>
    @endif
@endsection
