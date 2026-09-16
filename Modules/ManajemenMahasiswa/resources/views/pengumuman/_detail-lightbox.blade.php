{{-- Lightbox poster, dipakai bersama halaman detail admin & mahasiswa. --}}
<div class="lightbox-modal" id="lightboxModal">
    <button class="lightbox-close" onclick="closeLightbox()" title="Tutup">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
    </button>
    <div class="lightbox-content">
        <img id="lightboxImage" src="" alt="">
    </div>
    <div class="lightbox-info">
        <div class="lightbox-title" id="lightboxTitle"></div>
    </div>
</div>

@push('scripts')
<script>
    function openLightbox(src, title) {
        document.getElementById('lightboxImage').src = src;
        document.getElementById('lightboxTitle').textContent = title;
        document.getElementById('lightboxModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('lightboxModal');
        if (modal && modal.classList.contains('active') && e.key === 'Escape') {
            closeLightbox();
        }
    });

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('lightboxModal');
        if (modal && e.target === modal) {
            closeLightbox();
        }
    });
</script>
@endpush
