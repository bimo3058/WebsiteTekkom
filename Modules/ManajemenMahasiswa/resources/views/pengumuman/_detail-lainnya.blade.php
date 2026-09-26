{{--
    Bagian "Pengumuman Lainnya" — kartu full-width di bawah kolom artikel & sidebar
    pada halaman detail (versi admin maupun mahasiswa).

    Dipakai bersama supaya kedua halaman tidak pernah berbeda bentuk. Gayanya
    memakai kelas .dt-card yang sama dengan kartu Informasi/Aksi/Lampiran di
    halaman yang sama, jadi tidak ada kartu bergaya sendiri.

    Variabel include:
      $lainnya  Collection<Pengumuman> (wajib) — sudah disaring audiens oleh
                PengumumanService::lainnya()
--}}
@if($lainnya->isNotEmpty())
    <div class="dt-card dt-lainnya">
        <div class="dt-card-head">
            <span class="dt-card-title">Pengumuman Lainnya</span>
            <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="dt-lainnya-all">
                Lihat Semua
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>
        <div class="dt-card-body">
            <div class="dt-lainnya-grid">
                @foreach($lainnya as $item)
                    @php
                        // Gambar pertama dipakai sebagai sampul, sama seperti di daftar.
                        $sampul = collect($item->repoMulmed ?? [])
                            ->first(fn ($f) => $f->isGambar());

                        $tglItem = $item->published_at ?? $item->created_at;
                    @endphp

                    <a href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}" class="dt-lainnya-item">
                        <div class="dt-lainnya-thumb">
                            @if($sampul)
                                <img src="{{ $sampul->url }}" alt="{{ $item->judul }}" loading="lazy">
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            @endif
                        </div>

                        <div class="dt-lainnya-info">
                            <div class="dt-lainnya-judul">{{ $item->judul }}</div>
                            <div class="dt-lainnya-meta">
                                @if($item->is_pinned)
                                    <span class="dt-lainnya-pin">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/></svg>
                                        Penting
                                    </span>
                                @endif
                                <span>{{ $tglItem->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
