<x-dynamic-component :component="'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            .init-wrapper {
                min-height: 70vh; display: flex; flex-direction: column;
                align-items: center; justify-content: center; padding: 32px 16px;
            }
            .success-card {
                background: #ffffff; border-radius: 12px; padding: 48px 40px;
                text-align: center; border: 1px solid #DDE1E8;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
                max-width: 600px; width: 100%;
            }
            .success-icon {
                width: 80px; height: 80px; background: #E7E8F0; color: #293C79;
                border-radius: 50%; display: flex; align-items: center;
                justify-content: center; margin: 0 auto 24px auto;
            }
            .link-box {
                background: #f8fafc; border: 2px dashed #DDE1E8;
                border-radius: 12px; padding: 24px; margin: 32px 0;
            }
            .link-url {
                font-family: monospace; font-size: 16px; font-weight: 600;
                color: #293C79; word-break: break-all; margin-bottom: 16px; display: block;
            }
            .btn-copy {
                background: #111827; color: white; border: none;
                border-radius: 12px; padding: 10px 24px; font-weight: 600;
                display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
            }
            .btn-copy:hover { background: #374151; }
        </style>
    @endpush

    <div class="init-wrapper">
        <div class="success-card">
            <div class="success-icon">
                <x-manajemenmahasiswa::ui.icon name="link-01" size="40" />
            </div>
            <h2 class="fw-bold text-dark mb-3" style="font-size: 1.5rem; color: #1e1b4b;">Magic Link Dibuat!</h2>
            <p class="text-muted" style="font-size: 15px; line-height: 1.6;">
                Sistem telah membuatkan Anda link khusus. Anda akan menggunakan link ini untuk <strong>mengisi form pengaduan</strong> dan <strong>melacak balasan</strong> dari admin.
            </p>

            <div class="link-box">
                <div class="text-danger fw-bold mb-2" style="font-size: 13px; text-transform: uppercase;">Simpan Tautan Ini!</div>
                @php $trackUrl = route('manajemenmahasiswa.pengaduan.track', ['token' => $pengaduan->anon_token]); @endphp
                <a href="{{ $trackUrl }}" target="_blank" class="link-url">{{ $trackUrl }}</a>

                <button class="btn-copy" onclick="copyLink()">
                    <x-manajemenmahasiswa::ui.icon name="files-01" size="16" />
                    Salin Tautan
                </button>
            </div>

            <p class="text-muted mb-4" style="font-size: 13px;">
                <span style="color: #f59e0b;"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" /></span>
                Tautan ini bersifat sangat rahasia. Jika hilang, Anda tidak dapat memulihkannya. Pastikan Anda menyalinnya sebelum membuka form.
            </p>

            <a href="{{ $trackUrl }}" target="_blank" class="btn btn-outline-secondary fw-bold px-4 py-2" style="border-radius: 12px;">
                Buka Form Pengaduan (Tab Baru)
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyLink() {
                const url = '{{ $trackUrl }}';
                navigator.clipboard.writeText(url).then(function() {
                    const btn = document.querySelector('.btn-copy');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:8px;">✓ Tersalin!</span>';
                    btn.style.background = '#16a34a';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '#111827';
                    }, 2000);
                });
            }
        </script>
    @endpush

</x-dynamic-component>
