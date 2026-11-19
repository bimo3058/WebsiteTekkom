{{-- Badge status satu tiket. Param: $pengaduan, $pelapor (opsional, bawaan false —
     true = label versi pelapor: Terkirim/Dibaca/Tercatat). Butuh partials/palette. --}}
@php $pgdBadge = ($pelapor ?? false) ? $pengaduan->statusPelapor() : $pengaduan->statusBadge(); @endphp
<span class="pgd-status {{ $pgdBadge['tone'] }}" @isset($pgdBadge['keterangan']) title="{{ $pgdBadge['keterangan'] }}" @endisset>{{ $pgdBadge['label'] }}</span>
