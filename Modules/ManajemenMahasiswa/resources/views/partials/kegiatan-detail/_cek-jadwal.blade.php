{{--
    Peringatan jadwal & ruangan (S-81) di kartu info detail Rencana Proker, tepat di bawah
    kartu meta Tanggal/Lokasi — peringatan, bukan larangan. Sejak 11 Nov 2026 berada di dalam
    kartu info (sebelumnya panel terpisah di atas banner). Gaya kartu bentrok dipakai bersama
    form Lokasi (_bentrok-styles).

    Variabel: $proker, $cekJadwal (array{bentrok, ruangan, tanpaJam} dari ProkerController).
--}}
@php
    $ruanganBermasalah = $cekJadwal['ruangan'] && $cekJadwal['ruangan']['status'] !== 'tersedia';
@endphp

@if($cekJadwal['bentrok'] || $cekJadwal['tanpaJam'] || $ruanganBermasalah)
@include('manajemenmahasiswa::partials.kegiatan-form._bentrok-styles')
<div class="mk-bentrok mk-bentrok--detail" role="note" aria-label="Peringatan jadwal">
    <div class="mk-bentrok__kepala">
        <span class="mk-bentrok__ikon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </span>
        <span class="mk-bentrok__judul">Periksa jadwal</span>
    </div>

    @if($cekJadwal['tanpaJam'])
        <p class="mk-bentrok__isi">Jam mulai &amp; jam selesai belum diisi. Wajib sebelum dikirim untuk ditinjau, dan dibutuhkan untuk mengecek bentrok serta meminjam ruangan.</p>
    @endif
    @if($ruanganBermasalah)
        <p class="mk-bentrok__isi"><b>{{ $cekJadwal['ruangan']['nama'] }}:</b> {{ $cekJadwal['ruangan']['alasan'] ?? ucfirst($cekJadwal['ruangan']['status']) }}</p>
    @endif
    @if(count($cekJadwal['bentrok']))
        <p class="mk-bentrok__isi">Waktunya beririsan dengan {{ count($cekJadwal['bentrok']) }} proker lain:</p>
        <div class="mk-bentrok__daftar">
            @foreach($cekJadwal['bentrok'] as $b)
                <div @class(['mk-bentrok__item', 'is-ruang-sama' => $b['ruang_sama']])>
                    <span class="mk-bentrok__bidang">{{ $b['bidang'] }}</span>
                    <div class="mk-bentrok__badan">
                        <div class="mk-bentrok__nama">{{ $b['judul'] }}</div>
                        <div class="mk-bentrok__meta">
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>{{ $b['tanggal'] }}</span>
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>{{ $b['jam'] }}</span>
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>{{ $b['lokasi'] }}</span>
                        </div>
                    </div>
                    @if($b['ruang_sama'])<span class="mk-bentrok__sama">Ruang sama</span>@endif
                </div>
            @endforeach
        </div>
        <p class="mk-bentrok__catatan">Proker bidang lain hanya terlihat judul, bidang, jam &amp; lokasinya.</p>
    @endif
</div>

@once
<style>
    .mk-bentrok--detail { margin-top: 14px; }
    /* Oranye #F79009 di atas krem hanya ±2:1; judul di kartu putih memakai kuning tua token warning */
    .mk-bentrok--detail .mk-bentrok__judul { color: var(--c-warning); font-weight: 700; white-space: nowrap; }
    .mk-bentrok--detail .mk-bentrok__kepala { flex-wrap: wrap; row-gap: 6px; }
    .mk-bentrok--detail .mk-bentrok__isi { margin-top: 8px; }
    .mk-bentrok__catatan { margin: 8px 0 0; font-size: 12px; color: var(--c-fg-muted, #667085); }
</style>
@endonce
@endif
