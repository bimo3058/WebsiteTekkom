<x-dynamic-component :component="'manajemenmahasiswa::layouts.mahasiswa'">

    @include('manajemenmahasiswa::pengaduan.partials.palette')

    @push('styles')
        <style>
            /* Halaman ini memakai kartu sendiri di atas latar abu, jadi kotak bawaan
               .main-wrapper dimatikan dan hanya menyisakan area scroll bergutter. */
            .main-wrapper {
                /* Lebar bleed garis pemisah page-header bordered mengikuti padding di bawah. */
                --mm-ph-bleed: 10px;
                --mm-ph-bleed-top: 10px;
                --mm-ph-bleed-sm: 10px;
                --mm-ph-bleed-top-sm: 10px;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 10px !important;
                overflow-y: auto !important;
            }

            .init-wrapper {
                min-height: 70vh; display: flex; flex-direction: column;
                align-items: center; justify-content: center; padding: 32px 16px;
            }
            .success-card {
                background: #ffffff; border-radius: 12px; padding: 48px 40px;
                text-align: center; border: 1px solid var(--c-border);
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
                max-width: 600px; width: 100%;
            }
            .success-icon {
                width: 80px; height: 80px; background: var(--c-primary-subtle); color: var(--c-primary);
                border-radius: 50%; display: flex; align-items: center;
                justify-content: center; margin: 0 auto 24px auto;
            }
            .link-box {
                background: var(--c-bg); border: 2px dashed var(--c-border);
                border-radius: 12px; padding: 24px; margin: 32px 0;
            }
            .link-url {
                font-family: monospace; font-size: 16px; font-weight: 600;
                color: var(--c-primary); word-break: break-all; margin-bottom: 16px; display: block;
            }
            .btn-copy.is-done { border-color: var(--c-success); color: var(--c-success); }
        </style>
    @endpush

    <div class="init-wrapper">
        <div class="success-card">
            <div class="success-icon">
                <x-manajemenmahasiswa::ui.icon name="link-01" size="40" />
            </div>
            <h2 class="fw-bold mb-3" style="font-size: 24px; color: var(--c-fg);">Magic Link Dibuat!</h2>
            <p class="text-muted" style="font-size: 15px; line-height: 1.6;">
                Sistem telah membuatkan Anda link khusus. Anda akan menggunakan link ini untuk <strong>mengisi form pengaduan</strong> dan <strong>melacak balasan</strong> dari admin.
            </p>

            <div class="link-box">
                <div class="fw-bold mb-2" style="font-size: 13px; text-transform: uppercase; color: var(--c-error);">Simpan Tautan Ini!</div>
                @php $trackUrl = route('manajemenmahasiswa.pengaduan.track', ['token' => $pengaduan->anon_token]); @endphp
                <a href="{{ $trackUrl }}" target="_blank" class="link-url">{{ $trackUrl }}</a>

                <button type="button" class="mk-btn mk-btn--secondary btn-copy" onclick="copyLink()">
                    <x-manajemenmahasiswa::ui.icon name="files-01" size="16" />
                    <span>Salin Tautan</span>
                </button>
            </div>

            <p class="text-muted mb-4" style="font-size: 13px;">
                <span style="color: var(--c-warning);"><x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" /></span>
                Tautan ini bersifat sangat rahasia. Jika hilang, Anda tidak dapat memulihkannya. Pastikan Anda menyalinnya sebelum membuka form.
            </p>

            <a href="{{ $trackUrl }}" target="_blank" rel="noopener" class="mk-btn mk-btn--primary">
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
                    const label = btn.querySelector('span:last-child');
                    label.textContent = '✓ Tersalin!';
                    btn.classList.add('is-done');
                    setTimeout(() => {
                        label.textContent = 'Salin Tautan';
                        btn.classList.remove('is-done');
                    }, 2000);
                });
            }
        </script>
    @endpush

</x-dynamic-component>
