{{--
    Panel bukti (kolom kiri) — dipakai modal Tinjau maupun modal Ajukan Reward,
    supaya sertifikat tetap terlihat sambil datanya dibaca atau formulirnya diisi.

    Elemennya ditandai data-attribute, bukan id, karena satu halaman bisa memuat
    lebih dari satu panel; pengisinya adalah tpRenderBukti(pane, bukti, idx) yang
    didefinisikan di partials/tinjau-modal.blade.php.
--}}
<div class="tp-pane-bukti" data-tp-bukti>
    <div data-tp-preview style="width: 100%; display: flex; align-items: center; justify-content: center;"></div>
    <div data-tp-thumbs class="tp-thumbs"></div>
    <a data-tp-opentab href="#" target="_blank" rel="noopener"
       style="font-size: 11.5px; font-weight: 600; color: var(--c-primary); text-decoration: none;">
        Buka bukti di tab baru &#8599;
    </a>
</div>
