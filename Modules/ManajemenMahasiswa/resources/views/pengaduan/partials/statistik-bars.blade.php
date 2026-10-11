{{--
    Batang horizontal satu warna untuk membandingkan besaran antar kelompok
    (dipakai halaman pengaduan/statistik; gaya .ps-bar-* ada di halaman itu).

    Param:
      $baris      [['label' => string, 'nilai' => int|null], ...]
                  nilai null = disamarkan (di bawah MIN_TAMPIL), tampil "< 3" tanpa batang.
      $satuan     kata satuan untuk tooltip, mis. "pengaduan"
      $tampilNol  true = baris bernilai 0 tetap ditampilkan (bawaan: disembunyikan)
--}}
@php
    $min = \Modules\ManajemenMahasiswa\Services\PengaduanStatistikService::MIN_TAMPIL;
    $tampilNol = $tampilNol ?? false;
    $baris = array_values(array_filter($baris, fn ($b) => $tampilNol || $b['nilai'] === null || $b['nilai'] > 0));
    $maks = max(1, max(array_map(fn ($b) => (int) $b['nilai'], $baris) ?: [0]));
@endphp

@if (empty($baris))
    <div class="ps-empty">Belum ada data.</div>
@else
    <div class="ps-bars">
        @foreach ($baris as $b)
            @php $disamarkan = $b['nilai'] === null; @endphp
            <div class="ps-bar-row" tabindex="0"
                 data-tip="{{ $b['label'] }}: {{ $disamarkan ? 'kurang dari ' . $min . ' (disamarkan)' : $b['nilai'] . ' ' . $satuan }}">
                <span class="ps-bar-label">{{ $b['label'] }}</span>
                <span class="ps-bar-track">
                    @unless ($disamarkan || $b['nilai'] === 0)
                        <span class="ps-bar-fill" style="display: block; width: {{ $b['nilai'] / $maks * 100 }}%;"></span>
                    @endunless
                </span>
                <span class="ps-bar-value {{ $disamarkan ? 'is-hidden' : '' }}">{{ $disamarkan ? '< ' . $min : $b['nilai'] }}</span>
            </div>
        @endforeach
    </div>
@endif
