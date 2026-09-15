<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
               (resources/views/components/sidebar.blade.php). Layout modul ini tidak
               mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
            :root {
                --c-primary: #0B266E;
                --c-primary-hover: #091958;
                --c-primary-subtle: rgba(11, 38, 110, 0.08);
                --c-primary-border: #5C78B8;
                --c-bg: #F6F8FA;
                --c-fg: #0D0D12;
                --c-fg-sec: #353849;
                --c-fg-muted: #666D80;
                --c-fg-placeholder: #808897;
                --c-border: #DFE1E7;
                --c-border-strong: #C1C7CF;
                --c-success: #287F6E;
                --c-success-subtle: #DDF2EE;
                --c-error: #DF1C41;
                --c-error-subtle: #FADAE1;
                --c-warning: #956321;
                --c-warning-subtle: #F9ECCB;
                --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5)); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border, #DFE1E7); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong, #C1C7CF); border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
                .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Komposisi isi ──────────────────────────────────────────────
               Rata kiri mengikuti sumbu header, bukan kolom ter-center, supaya
               judul artikel sejajar dengan judul halaman di atasnya. */
            .detail-head {
                padding-bottom: 16px;
                margin-bottom: 20px;
                border-bottom: 1px solid var(--c-border, #DFE1E7);
            }

            /* Satu kolom baca: poster di atas, teks di bawahnya. Lebar kolom
               dibatasi agar baris teks tidak terlalu panjang saat layar lebar. */
            .detail-body { max-width: 760px; }

            .detail-head h5 {
                font-size: 24px; font-weight: 700; color: var(--c-fg, #0D0D12);
                margin: 0; line-height: 1.3; letter-spacing: -0.02em;
                word-break: break-word; overflow-wrap: break-word;
            }

            .pin-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
            .pin-status-badge {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 4px 9px; border-radius: 8px;
                font-size: 11px; font-weight: 700; letter-spacing: .02em;
            }
            .pin-status-global   { background: var(--c-warning-subtle, #F9ECCB); color: var(--c-warning, #956321); }
            .pin-status-personal { background: var(--c-primary-subtle, #EEF1F8); color: var(--c-primary, #0B266E); }

            /* ── Meta ───────────────────────────────────────────────────── */
            .meta-container { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
            .meta-item {
                display: inline-flex; align-items: center; gap: 6px;
                font-size: 12px; font-weight: 500; color: var(--c-fg-sec, #353849);
                background: var(--c-bg, #F6F8FA);
                border: 1px solid var(--c-border, #DFE1E7);
                padding: 6px 11px; border-radius: 8px;
            }
            .meta-item svg { color: var(--c-primary, #0B266E); flex-shrink: 0; }

            /* ── Poster ─────────────────────────────────────────────────────
               Tanpa aspect-ratio paksa: rasio asli gambar dipertahankan supaya
               poster tidak terpotong, dengan batas tinggi agar tidak dominan.
               Tetap bisa diklik untuk membuka lightbox (lihat .zoomable-thumbnail). */
            .detail-image-wrapper {
                /* inline-flex: kotak menyusut mengikuti gambar, jadi tidak ada
                   area kosong di sisi poster yang rasionya tidak pas. */
                display: inline-flex; max-width: 100%;
                margin: 0 0 22px;
                background: var(--c-bg, #F6F8FA);
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 12px;
                overflow: hidden; position: relative;
                box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
            }
            .detail-image-wrapper img {
                width: auto; max-width: 100%; max-height: 440px;
                display: block;
            }

            .zoomable-thumbnail { cursor: zoom-in; position: relative; }
            .zoomable-thumbnail::after {
                content: ''; position: absolute; inset: 0;
                background: rgba(13, 13, 18, 0.18);
                opacity: 0; transition: opacity .2s ease;
            }
            .zoomable-thumbnail:hover::after { opacity: 1; }

            /* ── Isi pengumuman ─────────────────────────────────────────── */
            .content-section {
                font-size: 14px; color: var(--c-fg-sec, #353849); line-height: 1.75;
                word-wrap: break-word; overflow-wrap: break-word;
            }
            .content-section p { margin-bottom: 1.1em; }
            .content-section img { max-width: 100%; height: auto; display: block; border-radius: 10px; margin: 16px 0; }
            .content-section a { color: var(--c-primary, #0B266E); text-decoration: underline; text-underline-offset: 3px; }
            .content-section a:hover { color: var(--c-primary-hover, #091958); }
            .content-section h1 { font-size: 19px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 18px 0 8px; }
            .content-section h2 { font-size: 16px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 16px 0 6px; }
            .content-section hr { border: none; border-top: 1px solid var(--c-border, #DFE1E7); margin: 18px 0; }
            .content-section table { width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 12.5px; }
            .content-section table td,
            .content-section table th {
                border: 1px solid var(--c-border, #DFE1E7); padding: 8px 12px; text-align: left;
            }
            .content-section table th { background: #FBFBFC; font-weight: 600; color: var(--c-fg, #0D0D12); }

            /* ── Section label: pola dashboard Super Admin ──────────────── */
            .section-heading { display: flex; align-items: center; gap: 8px; margin: 28px 0 12px; }
            .section-heading::before {
                content: ''; display: inline-block; width: 3px; height: 14px;
                border-radius: 2px; background: var(--c-primary, #0B266E);
            }
            .section-heading span { font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12); }

            /* ── Lampiran ───────────────────────────────────────────────── */
            .lampiran-list {
                display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 10px; margin-bottom: 24px; max-width: 900px;
            }
            .lampiran-item {
                display: flex; align-items: center; gap: 12px;
                padding: 12px 14px;
                background: #fff;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 10px;
                text-decoration: none; color: var(--c-fg, #0D0D12);
                box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
                transition: border-color .15s, box-shadow .15s;
            }
            .lampiran-item:hover {
                border-color: var(--c-primary-border, #5C78B8);
                box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
                color: var(--c-fg, #0D0D12);
            }
            .lampiran-icon {
                width: 32px; height: 32px; border-radius: 8px;
                background: var(--c-primary-subtle, #EEF1F8);
                color: var(--c-primary, #0B266E);
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .lampiran-info { flex-grow: 1; overflow: hidden; }
            .lampiran-name {
                font-weight: 600; font-size: 13px; color: var(--c-fg, #0D0D12);
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }
            .lampiran-action {
                color: var(--c-fg-muted, #666D80); font-size: 11.5px; font-weight: 500;
                display: flex; align-items: center; gap: 4px; margin-top: 2px;
                transition: color .15s;
            }
            .lampiran-item:hover .lampiran-action { color: var(--c-primary, #0B266E); }

            /* ── Tombol aksi ────────────────────────────────────────────── */
            .actions-container {
                display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
                padding-top: 16px;
                border-top: 1px solid var(--c-border, #DFE1E7);
            }
            .btn-action {
                display: inline-flex; align-items: center; justify-content: center; gap: 6px;
                padding: 8px 14px; border-radius: 8px;
                border: 1px solid var(--c-border, #DFE1E7); background: #fff;
                font-size: 12px; font-weight: 600; color: var(--c-fg-sec, #353849);
                text-decoration: none; cursor: pointer;
                transition: all .15s; font-family: inherit;
                box-shadow: 0 1px 2px rgba(0,0,0,.04);
            }
            .btn-action:hover {
                background: var(--c-bg, #F6F8FA);
                border-color: var(--c-border-strong, #C1C7CF);
                color: var(--c-fg-sec, #353849);
            }
            .btn-pin-personal { color: var(--c-primary, #0B266E); }
            .btn-pin-personal:hover {
                background: var(--c-primary-subtle, #EEF1F8);
                border-color: var(--c-primary-border, #5C78B8);
                color: var(--c-primary, #0B266E);
            }

            /* ── Lightbox ───────────────────────────────────────────────── */
            .lightbox-modal {
                display: none; position: fixed; inset: 0; z-index: 10000;
                background: rgba(13, 13, 18, 0.92);
                align-items: center; justify-content: center;
                animation: lightboxFadeIn .25s ease;
            }
            .lightbox-modal.active { display: flex; }
            @keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
            .lightbox-content {
                position: relative; max-width: 90vw; max-height: 85vh;
                display: flex; align-items: center; justify-content: center;
            }
            .lightbox-content img {
                max-width: 90vw; max-height: 82vh; object-fit: contain;
                border-radius: 8px; box-shadow: 0 25px 60px rgba(0,0,0,.4);
                animation: lightboxZoomIn .3s ease;
            }
            @keyframes lightboxZoomIn {
                from { transform: scale(.95); opacity: 0; }
                to   { transform: scale(1); opacity: 1; }
            }
            .lightbox-close {
                position: fixed; top: 20px; right: 24px; width: 40px; height: 40px;
                border-radius: 50%;
                background: rgba(255,255,255,.12); backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,.2);
                color: #fff; cursor: pointer;
                display: flex; align-items: center; justify-content: center;
                transition: background .15s; z-index: 10001;
            }
            .lightbox-close:hover { background: rgba(255,255,255,.22); }
            .lightbox-info {
                position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
                text-align: center; z-index: 10001;
            }
            .lightbox-info .lightbox-title { color: #fff; font-size: 13px; font-weight: 600; }

            @media (max-width: 640px) {
                .detail-head h5 { font-size: 20px; }
                .actions-container { flex-direction: column; align-items: stretch; }
                .btn-action { width: 100%; }
                .actions-container form { width: 100%; }
            }
        </style>
    @endpush

    @php
        $lampiran = collect($pengumuman->repoMulmed ?? []);

        // Try to find first image for the poster
        $images = $lampiran->filter(function ($file) {
            return in_array(strtolower(pathinfo($file->nama_file ?? '', PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });
        $posterUrl = $images->first() ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($images->first()->path_file) : null;

        $targetAudienceStr = $pengumuman->target_audience;
        if ($targetAudienceStr == 'all') {
            $targetAudienceStr = 'Semua Mahasiswa / Alumni';
        } else if ($targetAudienceStr == 'mahasiswa') {
            $targetAudienceStr = 'Mahasiswa Aktif';
        } else {
            $targetAudienceStr = ucfirst(str_replace('_', ' ', $targetAudienceStr));
        }
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">Detail Pengumuman</h1>
                            <span style="font-size:10px; font-weight:600; color:var(--c-primary, #0B266E); background:rgba(11,38,110,0.09); border:1px solid rgba(11,38,110,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Modul Mahasiswa</span>
                        </div>
                        <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                            Wadah informasi untuk mahasiswa dan alumni
                        </p>
                    </div>

                    <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-action">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="dash-box-body">

                    {{-- Judul + meta --}}
                    <div class="detail-head">
                        @if($pengumuman->is_pinned || $isPersonalPinned)
                            <div class="pin-badges">
                                @if($pengumuman->is_pinned)
                                    <span class="pin-status-badge pin-status-global">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/></svg>
                                        Pengumuman Penting
                                    </span>
                                @endif
                                @if($isPersonalPinned)
                                    <span class="pin-status-badge pin-status-personal">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                                        Pin Pribadi
                                    </span>
                                @endif
                            </div>
                        @endif
                        <h5>{{ $pengumuman->judul }}</h5>

                        <div class="meta-container">
                            <div class="meta-item" title="Target Audience">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                {{ $targetAudienceStr }}
                            </div>
                            <div class="meta-item" title="Tanggal">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ ($pengumuman->published_at ?? $pengumuman->created_at)->translatedFormat('d F Y') }}
                            </div>
                            <div class="meta-item" title="Waktu">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                {{ ($pengumuman->published_at ?? $pengumuman->created_at)->format('H:i') }} WIB
                            </div>
                            <div class="meta-item" title="Pembuat">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                {{ $pengumuman->author->name ?? 'Admin' }}
                            </div>
                        </div>
                    </div>

                    {{-- Poster di atas, isi pengumuman di bawahnya --}}
                    <div class="detail-body">
                        @if($posterUrl)
                            {{-- Klik poster untuk membuka lightbox gambar penuh --}}
                            <div class="detail-image-wrapper zoomable-thumbnail" onclick="openLightbox('{{ $posterUrl }}', '{{ addslashes($pengumuman->judul) }}')">
                                <img src="{{ $posterUrl }}" alt="{{ $pengumuman->judul }}">
                            </div>
                        @endif

                        <div class="content-section">
                            {!! $pengumuman->konten !!}
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    @if($lampiran->count() > 0)
                        <div class="section-heading">
                            <span>Lampiran File</span>
                        </div>
                        <div class="lampiran-list">
                            @foreach($lampiran as $item)
                                <a href="{{ route('manajemenmahasiswa.pengumuman.lampiran.download', $item->id) }}" class="lampiran-item"
                                    download>
                                    <div class="lampiran-icon">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                    <div class="lampiran-info">
                                        <div class="lampiran-name">{{ $item->judul_file ?? 'Lampiran' }}</div>
                                        <div class="lampiran-action">
                                            Unduh
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="actions-container">
                        {{-- Pin Pribadi --}}
                        <form action="{{ route('manajemenmahasiswa.pengumuman.personal_pin', $pengumuman->id) }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-action btn-pin-personal">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $isPersonalPinned ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                </svg>
                                {{ $isPersonalPinned ? 'Hapus Pin Pribadi' : 'Pin untuk Saya' }}
                            </button>
                        </form>
                    </div>

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    {{-- Lightbox Modal --}}
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
        // Lightbox functionality
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

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('lightboxModal');
            if (modal && modal.classList.contains('active') && e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Close on clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('lightboxModal');
            if (modal && e.target === modal) {
                closeLightbox();
            }
        });
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>
