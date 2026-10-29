{{--
    Satu kartu kegiatan — dipakai bersama oleh daftar Rencana Proker, Pelaksanaan
    Kegiatan, dan Laporan & Arsip (keputusan 17 Okt 2026: isi kartu disamakan, versi
    lengkap). Badge mengikuti aturan halaman detail (kegiatan-detail/_body): bidang,
    atau "Prodi" bila tanpa bidang, lalu kategori. Isian yang kosong tidak ditampilkan,
    jadi kartu draft yang belum lengkap tetap rapi. Badge tahap (Draft / Di Pelaksanaan /
    Diarsipkan / Tidak terlaksana) tidak dipakai — dicabut 27 Okt 2026.

    Variabel: $item (Kegiatan — muat relasi bidang, bidangs, kategoriKegiatan,
    kategoris, dosenPendampings.user), $href.

    Baris terakhir kartu = Dosen Pendamping (bukan Ketua Pelaksana — diganti 27 Okt 2026
    atas permintaan Surya; ketua pelaksana tetap tampil di halaman detail).
--}}
@php
    $namaDosen = $item->dosenPendampings->map(fn ($d) => $d->user?->name)->filter()->values();
    $tooltipDosen = $item->dosenPendampings
        ->map(fn ($d) => ($d->user?->name ?? '-') . (filled($d->pivot->peran ?? null) ? ' (' . $d->pivot->peran . ')' : ''))
        ->implode(', ');
@endphp
<a href="{{ $href }}" class="kegiatan-card">
    <div class="kegiatan-card-image">
        @if($item->banner)
            <img src="{{ $item->banner_url }}" alt="{{ $item->judul }}" loading="lazy">
        @else
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary-border)" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
        @endif
    </div>

    <div class="kegiatan-card-body">
        <div class="kegiatan-badges">
            @if($item->bidangs->isNotEmpty())
                @foreach($item->bidangs as $b)
                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                @endforeach
            @elseif($item->bidang)
                <span class="badge-bidang">{{ $item->bidang->nama_bidang }}</span>
            @else
                <span class="badge-bidang">Prodi</span>
            @endif

            @if($item->kategoris->isNotEmpty())
                @foreach($item->kategoris as $kat)
                    <span class="badge-kategori">{{ $kat->nama_kategori }}</span>
                @endforeach
            @elseif($item->kategoriKegiatan)
                <span class="badge-kategori">{{ $item->kategoriKegiatan->nama_kategori }}</span>
            @endif
        </div>

        <div class="kegiatan-card-title">{{ $item->judul }}</div>

        @if($item->deskripsi)
            <div class="kegiatan-card-desc">{{ Str::limit(html_entity_decode(strip_tags($item->deskripsi)), 200) }}</div>
        @endif

        <div class="kegiatan-card-meta">
            <div>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                {{-- Bulan berbahasa Indonesia: .env memakai APP_LOCALE=en dan tidak diubah (S-44). --}}
                <span class="teks">{{ $item->tanggal_mulai ? $item->tanggal_mulai->locale('id')->translatedFormat('d M Y') : ($item->tahun ?: 'Belum ditentukan') }}</span>
            </div>
            @if($item->jam_mulai)
                <div>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="teks">{{ $item->jam_mulai_formatted }}{{ $item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : '' }} WIB</span>
                </div>
            @endif
            @if($item->lokasi)
                <div title="{{ $item->lokasi }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span class="teks">{{ $item->lokasi }}</span>
                </div>
            @endif
            @if($namaDosen->isNotEmpty())
                <div title="Dosen Pendamping: {{ $tooltipDosen }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    <span class="teks">{{ $namaDosen->first() }}@if($namaDosen->count() > 1) <span style="font-weight:700;color:var(--c-primary);">+{{ $namaDosen->count() - 1 }} lainnya</span>@endif</span>
                </div>
            @endif
        </div>
    </div>
</a>
