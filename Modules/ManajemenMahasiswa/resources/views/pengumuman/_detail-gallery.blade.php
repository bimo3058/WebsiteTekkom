{{-- Galeri gambar pengumuman (maks. 5), dipakai halaman detail admin & mahasiswa.
     Butuh: $images (Collection RepoMulmed bertipe gambar) dan $judul.
     Urutan mengikuti relasi repoMulmed yang sudah di-orderBy('id'),
     sehingga gambar pertama adalah cover yang dipilih saat membuat pengumuman. --}}
@php
    $galeri = collect($images ?? [])
        ->map(fn ($f) => [
            'url'  => app(\App\Services\SupabaseStorage::class)->getPublicUrl($f->path_file),
            'nama' => $f->judul_file ?? $f->nama_file,
        ])
        ->values();
@endphp

@if($galeri->isNotEmpty())
    <div class="dt-gallery" data-gallery>
        <div class="dt-gallery-viewport">
            <div class="dt-gallery-track" data-gallery-track>
                @foreach($galeri as $g)
                    <div class="dt-gallery-slide">
                        <img src="{{ $g['url'] }}" alt="{{ $judul }}" draggable="false">
                    </div>
                @endforeach
            </div>

            @if($galeri->count() > 1)
                <button type="button" class="dt-gallery-nav prev" data-gallery-prev aria-label="Gambar sebelumnya">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <button type="button" class="dt-gallery-nav next" data-gallery-next aria-label="Gambar berikutnya">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
                <span class="dt-gallery-counter" data-gallery-counter>1 / {{ $galeri->count() }}</span>
            @endif
        </div>

        @if($galeri->count() > 1)
            <div class="dt-gallery-thumbs">
                @foreach($galeri as $i => $g)
                    <button type="button" class="dt-gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                        data-gallery-thumb aria-label="Lihat gambar {{ $i + 1 }}">
                        <img src="{{ $g['url'] }}" alt="" draggable="false">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
@endif

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-gallery]').forEach(function (galeri) {
                const track  = galeri.querySelector('[data-gallery-track]');
                const slides = track ? Array.from(track.children) : [];
                if (slides.length === 0) return;

                const tombolPrev = galeri.querySelector('[data-gallery-prev]');
                const tombolNext = galeri.querySelector('[data-gallery-next]');
                const penghitung = galeri.querySelector('[data-gallery-counter]');
                const thumbs     = Array.from(galeri.querySelectorAll('[data-gallery-thumb]'));
                const total      = slides.length;

                let index = 0;
                let mulaiX = null;
                let baruDigeser = false;   // menahan klik lightbox tepat setelah drag

                function tampilkan(i) {
                    index = Math.max(0, Math.min(total - 1, i));
                    track.style.transform = 'translateX(' + (-index * 100) + '%)';

                    if (penghitung) penghitung.textContent = (index + 1) + ' / ' + total;
                    if (tombolPrev) tombolPrev.disabled = index === 0;
                    if (tombolNext) tombolNext.disabled = index === total - 1;
                    thumbs.forEach((t, n) => t.classList.toggle('active', n === index));
                }

                tombolPrev?.addEventListener('click', () => tampilkan(index - 1));
                tombolNext?.addEventListener('click', () => tampilkan(index + 1));
                thumbs.forEach((t, n) => t.addEventListener('click', () => tampilkan(n)));

                // Klik gambar membuka lightbox — kecuali baru saja diseret
                slides.forEach(function (slide) {
                    const img = slide.querySelector('img');
                    if (!img) return;
                    img.addEventListener('click', function () {
                        if (baruDigeser) return;
                        openLightbox(img.src, img.alt);
                    });
                });

                // Geser: swipe di layar sentuh, drag dengan mouse
                function mulaiGeser(x) { mulaiX = x; baruDigeser = false; }
                function akhiriGeser(x) {
                    if (mulaiX === null) return;
                    const jarak = x - mulaiX;
                    mulaiX = null;
                    if (Math.abs(jarak) < 40) return;
                    baruDigeser = true;
                    setTimeout(() => { baruDigeser = false; }, 100);
                    tampilkan(jarak < 0 ? index + 1 : index - 1);
                }

                galeri.addEventListener('touchstart', e => mulaiGeser(e.touches[0].clientX), { passive: true });
                galeri.addEventListener('touchend',   e => akhiriGeser(e.changedTouches[0].clientX));
                galeri.addEventListener('mousedown',  e => mulaiGeser(e.clientX));
                galeri.addEventListener('mouseup',    e => akhiriGeser(e.clientX));
                galeri.addEventListener('mouseleave', () => { mulaiX = null; });

                // Panah kiri/kanan saat galeri sedang difokuskan
                galeri.setAttribute('tabindex', '0');
                galeri.addEventListener('keydown', function (e) {
                    if (e.key === 'ArrowLeft')  { e.preventDefault(); tampilkan(index - 1); }
                    if (e.key === 'ArrowRight') { e.preventDefault(); tampilkan(index + 1); }
                });

                tampilkan(0);
            });
        </script>
    @endpush
@endonce
