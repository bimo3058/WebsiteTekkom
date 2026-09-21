{{-- Lightbox Modal Banner --}}
@if($proker->banner)
<div class="lightbox-modal" id="bannerLightboxModal">
    <button class="lightbox-close" onclick="closeBannerLightbox()" title="Tutup">&#10005;</button>
    <div class="lightbox-content">
        <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}">
    </div>
    <div class="lightbox-info">
        <div class="lightbox-title">{{ $proker->judul }}</div>
    </div>
</div>
@endif

{{-- ─── Lightbox JavaScript ──────────────────────────────────────────── --}}
<script>
// Photo gallery data
const galleryImages = [
    @if(isset($images) && $images->count() > 0)
        @foreach($images->values() as $img)
        {
            src: "{{ $img->url }}",
            title: "{{ addslashes($img->judul_file ?: $img->nama_file) }}"
        },
        @endforeach
    @endif
];

let currentImageIndex = 0;

function openLightbox(index) {
    currentImageIndex = index;
    updateLightboxImage();
    document.getElementById('lightboxModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('active');
    document.body.style.overflow = '';
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightboxImage();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateLightboxImage();
}

function updateLightboxImage() {
    if (galleryImages.length === 0) return;
    const img = galleryImages[currentImageIndex];
    document.getElementById('lightboxImage').src = img.src;
    document.getElementById('lightboxImage').alt = img.title;
    document.getElementById('lightboxTitle').textContent = img.title;
    document.getElementById('lightboxCounter').textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
}

// Banner Lightbox
function openBannerLightbox() {
    document.getElementById('bannerLightboxModal')?.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBannerLightbox() {
    document.getElementById('bannerLightboxModal')?.classList.remove('active');
    document.body.style.overflow = '';
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (modal && modal.classList.contains('active')) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
        return;
    }

    const bannerModal = document.getElementById('bannerLightboxModal');
    if (bannerModal && bannerModal.classList.contains('active')) {
        if (e.key === 'Escape') closeBannerLightbox();
    }
});

// Close lightbox on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (modal && e.target === modal) closeLightbox();

    const bannerModal = document.getElementById('bannerLightboxModal');
    if (bannerModal && e.target === bannerModal) closeBannerLightbox();
});
</script>
