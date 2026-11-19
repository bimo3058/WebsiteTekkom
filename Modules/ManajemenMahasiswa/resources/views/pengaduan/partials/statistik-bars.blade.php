{{--
    Batang horizontal satu warna untuk membandingkan besaran antar kelompok
    (dipakai halaman pengaduan/statistik; gaya .ps-bar-* ada di halaman itu).

    Param:
      $baris      [['label' => string, 'nilai' => int, 'url' => ?string], ...]
      $satuan     kata satuan untuk tooltip, mis. "pengaduan"
    Baris bernilai 0 tidak ditampilkan. Baris ber-url menjadi tautan ke daftarnya.
--}}
@php
    $baris = array_values(array_filter($baris, fn ($b) => $b['nilai'] > 0));
    $maks = max(1, max(array_map(fn ($b) => (int) $b['nilai'], $baris) ?: [0]));
@endphp

@if (empty($baris))
    <div class="ps-empty">Belum ada data.</div>
@else
    <div class="ps-bars">
        @foreach ($baris as $b)
            @php $psTag = !empty($b['url']) ? 'a' : 'div'; @endphp
            <{{ $psTag }} class="ps-bar-row" @if ($psTag === 'a') href="{{ $b['url'] }}" @else tabindex="0" @endif
                data-tip="{{ $b['label'] }}: {{ $b['nilai'] }} {{ $satuan }}{{ $psTag === 'a' ? ' · klik untuk melihat daftarnya' : '' }}">
                <span class="ps-bar-label">{{ $b['label'] }}</span>
                <span class="ps-bar-track">
                    @if ($b['nilai'] > 0)
                        <span class="ps-bar-fill" style="display: block; width: {{ $b['nilai'] / $maks * 100 }}%;"></span>
                    @endif
                </span>
                <span class="ps-bar-value">{{ $b['nilai'] }}</span>
            </{{ $psTag }}>
        @endforeach
    </div>
@endif
