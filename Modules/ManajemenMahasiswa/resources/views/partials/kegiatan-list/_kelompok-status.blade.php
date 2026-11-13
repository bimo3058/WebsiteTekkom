{{--
    Satu baris kelompok status di daftar Rencana Proker & Pelaksanaan Kegiatan (S-86,
    11 Nov 2026). Judul status di kiri, tautan di kanan ("View all →" membuka daftar
    tersaring status itu; "← Semua status" kembali ke tampilan kelompok).

    Variabel: $judul, $total, $tautan (URL), $teksTautan.
    Opsional: $items (koleksi kartu; tanpa ini hanya kepala baris yang dirender — dipakai
    mode tersaring yang gridnya ada di halaman), $routeShow (nama route detail),
    $opsiKartu (diteruskan ke kegiatan-list/_kartu), $teksKosong,
    $tampilan ('kartu' | 'daftar', S-87 — 'daftar' merender kegiatan-list/_daftar).
--}}
@php
    $items      = $items ?? null;
    $opsiKartu  = $opsiKartu ?? [];
    $teksKosong = $teksKosong ?? 'Belum ada proker dengan status ini.';
    // Baris kosong tidak perlu "View all →" — tidak ada yang bisa dilihat.
    $tampilTautan = $items === null || $total > 0;
@endphp
<section class="mk-kelompok">
    <div class="mk-kelompok__head">
        <h2 class="mk-kelompok__judul">
            {{ $judul }}
            <span class="mk-kelompok__jumlah">· {{ $total }}</span>
        </h2>
        @if($tampilTautan)
            {{-- rtrim: fullUrlWithQuery menyisakan "?" bila semua parameter dibuang --}}
            <a href="{{ rtrim($tautan, '?') }}" class="mk-kelompok__tautan">{{ $teksTautan }}</a>
        @endif
    </div>

    @if($items !== null)
        @if($items->count() > 0 && ($tampilan ?? 'kartu') === 'daftar')
            @include('manajemenmahasiswa::partials.kegiatan-list._daftar', [
                'items'     => $items,
                'routeShow' => $routeShow,
                'opsiKartu' => $opsiKartu,
            ])
        @elseif($items->count() > 0)
            <div class="row g-4">
                @foreach($items as $item)
                    <div class="col-md-6 col-lg-4 col-xxl-3">
                        @include('manajemenmahasiswa::partials.kegiatan-list._kartu', $opsiKartu + [
                            'item' => $item,
                            'href' => route($routeShow, $item->id),
                        ])
                    </div>
                @endforeach
            </div>
        @else
            <div class="mk-kelompok__kosong">{{ $teksKosong }}</div>
        @endif
    @endif
</section>
