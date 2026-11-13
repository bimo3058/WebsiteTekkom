<style>
    @media (max-width: 639px) {
        #ruangan-form-card .mp-card-body {
            padding: 16px !important;
            gap: 16px !important;
        }
    }

    /* Seragamkan ukuran placeholder input (Misal: A201, Gedung A, 2) dengan teks dropdown (13px) di semua layar */
    #ruangan-form-card input.mp-input::-webkit-input-placeholder {
        font-size: 13px;
        line-height: 1.4;
        color: #9CA3AF;
        opacity: 1;
    }

    #ruangan-form-card input.mp-input::placeholder {
        font-size: 13px;
        line-height: 1.4;
        color: #9CA3AF;
        opacity: 1;
    }

    /* Fallback anti-auto-zoom iOS tanpa :is() agar Safari lama (iPhone 5 / iOS 10-12) hingga HP modern terbaca aman */
    @media (max-width: 767px) {
        #ruangan-form-card input.mp-input[type="text"],
        #ruangan-form-card input.mp-input[type="number"],
        #ruangan-form-card input.mp-input[type="tel"],
        #ruangan-form-card input.mp-input[type="email"],
        #ruangan-form-card input.mp-input:not([type]) {
            font-size: 16px;
            line-height: 1.4;
            box-sizing: border-box;
            max-width: 100%;
        }
    }
</style>

<script>
    (function () {
        var card = document.getElementById('ruangan-form-card');
        if (!card) return;

        var TARGET_TYPES = {
            'text': true,
            'search': true,
            'number': true,
            'tel': true,
            'email': true,
            'url': true,
            'password': true
        };

        var isTouchPrimary = !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
        var lastTouchTime = 0;
        var touchStartX = 0;
        var touchStartY = 0;
        var trackingTouch = false;
        var touchStartedOnActive = false;

        function getActiveTargetInput() {
            var el = document.activeElement;
            if (!el || el.tagName !== 'INPUT') return null;
            if (!card.contains(el)) return null;
            var type = (el.getAttribute('type') || 'text').toLowerCase();
            if (!TARGET_TYPES[type]) return null;
            return el;
        }

        function blurActiveTarget() {
            var active = getActiveTargetInput();
            if (active && typeof active.blur === 'function') {
                active.blur();
            }
        }

        // Desktop & iPad (trackpad / Magic Keyboard): scroll roda mouse langsung menutup autocomplete
        window.addEventListener('wheel', function () {
            blurActiveTarget();
        }, { capture: true, passive: true });

        // Desktop drag scrollbar: aktif hanya jika bukan perangkat sentuh / bukan efek sentuhan baru-baru ini
        window.addEventListener('scroll', function (e) {
            if (isTouchPrimary) return;
            if (Date.now() - lastTouchTime < 1000) return;

            var active = getActiveTargetInput();
            if (!active) return;
            if (e && e.target === active) return;

            blurActiveTarget();
        }, { capture: true, passive: true });

        // Perangkat sentuh (iPhone 5, iPhone modern, Android, iPad Mini, iPad):
        // Abaikan auto-scroll bawaan keyboard naik; hanya tutup saat swipe nyata > 35px
        window.addEventListener('touchstart', function (e) {
            lastTouchTime = Date.now();
            trackingTouch = false;
            touchStartedOnActive = false;
            if (!e.touches || e.touches.length !== 1) return;

            var active = getActiveTargetInput();
            if (active) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchStartedOnActive = (e.target === active);
                trackingTouch = true;
            }
        }, { capture: true, passive: true });

        window.addEventListener('touchmove', function (e) {
            lastTouchTime = Date.now();
            if (!trackingTouch || !e.touches || e.touches.length !== 1) return;

            var dx = Math.abs(e.touches[0].clientX - touchStartX);
            var dy = Math.abs(e.touches[0].clientY - touchStartY);
            if (dy > 35 || (!touchStartedOnActive && dx > 35)) {
                trackingTouch = false;
                blurActiveTarget();
            }
        }, { capture: true, passive: true });
    })();
</script>
