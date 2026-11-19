{{--
    Mode tampilan "Daftar" (list view) daftar kegiatan — pasangan kegiatan-list/_kartu
    (S-87, 11 Nov 2026). Hanya untuk admin (superadmin, admin_kemahasiswaan, gpm, dpm);
    role lain tetap kartu. Pola mengikuti list view aplikasi kerja (Linear, Notion):
    satu baris per proker dengan thumbnail kecil di kiri, kolom berlabel, seluruh baris
    bisa diklik, dan tetap dikelompokkan per status seperti mode kartu. Isi kolom sama
    dengan isi kartu supaya kedua mode tidak pelan-pelan berbeda.

    Variabel: $items (koleksi/paginator Kegiatan, relasi sama dengan _kartu), $routeShow.
    Opsional: $opsiKartu (tampilTahap, tandaiTanpaBidang — sama artinya dengan _kartu).
--}}
@php
    $opsiKartu = $opsiKartu ?? [];
    $tampilTahap = $opsiKartu['tampilTahap'] ?? false;
    $tandaiTanpaBidang = $opsiKartu['tandaiTanpaBidang'] ?? false;
    $K = \Modules\ManajemenMahasiswa\Models\Kegiatan::class;
@endphp
<div class="kegiatan-daftar" role="table" aria-label="Daftar proker">
    <div class="kegiatan-daftar__head" role="row">
        <span role="columnheader">Proker</span>
        <span role="columnheader">Status</span>
        <span role="columnheader" class="kd-col-bidang">Bidang &amp; Kategori</span>
        <span role="columnheader">Jadwal</span>
        <span role="columnheader" class="kd-col-lokasi">Lokasi</span>
        <span role="columnheader" class="kd-col-dosen">Dosen Pendamping</span>
        <span aria-hidden="true"></span>
    </div>

    @foreach($items as $item)
        @php
            $namaDosen = $item->dosenPendampings->map(fn ($d) => $d->user?->name)->filter()->values();
            $tooltipDosen = $item->dosenPendampings
                ->map(fn ($d) => ($d->user?->name ?? '-') . (filled($d->pivot->peran ?? null) ? ' (' . $d->pivot->peran . ')' : ''))
                ->implode(', ');
            $tahap = $tampilTahap ? $item->tahap_rencana : null;
            $tinjauan = $tampilTahap ? $item->tahap_tinjauan : null;
        @endphp
        <a href="{{ route($routeShow, $item->id) }}" class="kegiatan-daftar__row" role="row">
            {{-- Proker: thumbnail + judul + deskripsi satu baris --}}
            <div class="kd-proker" role="cell">
                <div class="kd-thumb">
                    @if($item->banner)
                        <img src="{{ $item->banner_url }}" alt="" loading="lazy">
                    @else
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary-border)" stroke-width="1.6"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                    @endif
                </div>
                <div class="kd-proker__teks">
                    <div class="kd-judul" title="{{ $item->judul }}">{{ $item->judul }}</div>
                    @if($item->deskripsi)
                        <div class="kd-desk">{{ Str::limit(html_entity_decode(strip_tags($item->deskripsi)), 160) }}</div>
                    @endif
                </div>
            </div>

            {{-- Status --}}
            <div class="kd-status" role="cell">
                @if($tinjauan)
                    <span class="mm-status mm-status--{{ $K::TINJAUAN_NADA[$tinjauan] }}">{{ $K::TINJAUAN_LABELS[$tinjauan] }}</span>
                @elseif($tahap)
                    <span class="mm-status mm-status--{{ $K::TAHAP_RENCANA_NADA[$tahap] }}">{{ $K::TAHAP_RENCANA_LABELS[$tahap] }}</span>
                @else
                    <span class="kd-kosong">—</span>
                @endif
            </div>

            {{-- Bidang & kategori: aturan sama dengan _kartu --}}
            <div class="kd-badges kd-col-bidang" role="cell">
                @if($tandaiTanpaBidang && $item->bidangs->isEmpty())
                    <span class="badge-bidang badge-bidang--peringatan" title="Lengkapi bidang/unit agar proker terlihat oleh bidang/unit pemegangnya">Belum ada bidang</span>
                @elseif($item->bidangs->isNotEmpty())
                    @foreach($item->bidangs as $b)
                        <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                    @endforeach
                @elseif($item->bidang)
                    <span class="badge-bidang">{{ $item->bidang->nama_bidang }}</span>
                @elseif($item->kategoris->isNotEmpty() || $item->kategoriKegiatan)
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

            {{-- Jadwal: tanggal + jam --}}
            <div class="kd-jadwal" role="cell">
                <div class="kd-utama">{{ $item->tanggal_mulai ? $item->tanggal_mulai->locale('id')->translatedFormat('d M Y') : ($item->tahun ?: 'Belum ditentukan') }}</div>
                @if($item->jam_mulai)
                    <div class="kd-sub">{{ $item->jam_mulai_formatted }}{{ $item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : '' }} WIB</div>
                @endif
            </div>

            {{-- Lokasi --}}
            <div class="kd-col-lokasi" role="cell">
                @if($item->lokasi)
                    <div class="kd-utama kd-potong" title="{{ $item->lokasi }}">{{ $item->lokasi }}</div>
                @else
                    <span class="kd-kosong">—</span>
                @endif
            </div>

            {{-- Dosen pendamping --}}
            <div class="kd-col-dosen" role="cell">
                @if($namaDosen->isNotEmpty())
                    <div class="kd-utama kd-potong" title="Dosen Pendamping: {{ $tooltipDosen }}">{{ $namaDosen->first() }}</div>
                    @if($namaDosen->count() > 1)
                        <div class="kd-sub kd-sub--primer">+{{ $namaDosen->count() - 1 }} lainnya</div>
                    @endif
                @else
                    <span class="kd-kosong">—</span>
                @endif
            </div>

            <div class="kd-chevron" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </a>
    @endforeach
</div>
