{{--
    Daftar bukti yang SUDAH tersimpan di server (pending maupun tiket), satu baris per berkas.
    Param: $items (dari PengaduanBukti::toItems: url, kind pdf|image, size, label). Klik = pop-up penampil
    (lihat bukti-styles); "image" hanya untuk tiket lama.
--}}
@include('manajemenmahasiswa::pengaduan.partials.bukti-styles')
@php
    $fmtSize = fn (int $b) => $b >= 1048576
        ? number_format($b / 1048576, 1, ',', '.') . ' MB'
        : max(1, (int) round($b / 1024)) . ' KB';
@endphp
@if (!empty($items))
    <div class="bk-list">
        @foreach ($items as $item)
            <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" class="bk-row"
               data-bk-view="{{ $item['url'] }}" data-bk-title="{{ $item['label'] }}" title="Lihat {{ $item['label'] }}">
                <span class="bk-icon {{ $item['kind'] }}">{{ $item['kind'] === 'pdf' ? 'PDF' : 'IMG' }}</span>
                <span class="bk-text">
                    <span class="bk-name">{{ $item['label'] }}</span>
                    <span class="bk-sub">{{ $item['kind'] === 'pdf' ? 'Dokumen PDF' : 'Gambar' }}@if ($item['size'] > 0) · {{ $fmtSize($item['size']) }}@endif</span>
                </span>
                <span class="bk-open">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </span>
            </a>
        @endforeach
    </div>
@endif
