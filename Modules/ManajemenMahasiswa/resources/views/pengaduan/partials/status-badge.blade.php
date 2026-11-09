{{-- Badge status satu tiket. Param: $pengaduan. Butuh partials/palette. --}}
@php $pgdBadge = $pengaduan->statusBadge(); @endphp
<span class="pgd-status {{ $pgdBadge['tone'] }}">{{ $pgdBadge['label'] }}</span>
