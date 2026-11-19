{{--
    Judul langkah CV Builder. Bila $info diisi, ikon ⓘ muncul di sampingnya dan
    keterangannya hanya tampil saat ikon di-hover, difokus (keyboard), atau ditekan
    (layar sentuh) — bukan kotak yang selalu terbuka.

    Parameter:
      $judul  teks judul
      $info   (opsional) keterangan; boleh memuat <strong>, ditulis oleh kita (bukan input pengguna).
              Token {kunci} diganti tautan ke submenu Manajemen Mahasiswa (SIMENMA) yang
              dikirim CvBuilderController@index lewat $tautanMm — mis. {prestasi},
              {kegiatan}, {arsip}, {direktori}. Tautan dibuka di tab baru supaya isian di
              wizard yang belum disimpan tidak hilang.

    Bubble dibungkus .cvb-info-pop yang menempel tepat di bawah baris judul (tanpa celah),
    sehingga pointer bisa pindah dari ikon ke tautan tanpa membuatnya menutup.
--}}
@php
    $infoHtml = $info ?? null;
    if ($infoHtml && isset($tautanMm)) {
        $infoHtml = preg_replace_callback('/\{(\w+)\}/', function ($m) use ($tautanMm) {
            if (!isset($tautanMm[$m[1]])) {
                return $m[0];
            }

            return '<a href="' . e($tautanMm[$m[1]]['url']) . '" target="_blank" rel="noopener">' . e($tautanMm[$m[1]]['label']) . '</a>';
        }, $infoHtml);
    }
@endphp
<div class="cvb-title-row"
     @if(!empty($infoHtml)) x-data="{ open: false }" @mouseleave="open = false" @click.outside="open = false"
     @keydown.escape="open = false" @focusout="if (!$el.contains($event.relatedTarget)) open = false" @endif>
    <h3 class="cvb-title">{{ $judul }}</h3>
    @if(!empty($infoHtml))
        <button type="button" class="cvb-info-btn" aria-label="Keterangan: {{ $judul }}" :aria-expanded="open"
                @mouseenter="open = true" @focus="open = true" @click.stop="open = true">
            <x-icon name="information-circle" size="18" />
        </button>
        <div class="cvb-info-pop" x-show="open" x-transition.opacity.duration.120ms style="display: none;">
            <div class="cvb-info-bubble" role="tooltip">{!! $infoHtml !!}</div>
        </div>
    @endif
</div>
