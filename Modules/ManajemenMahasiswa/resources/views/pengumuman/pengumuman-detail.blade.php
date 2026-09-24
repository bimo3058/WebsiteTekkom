<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        @include('manajemenmahasiswa::pengumuman._detail-styles')
    @endpush

    @php
        $semuaFile = collect($pengumuman->repoMulmed ?? []);

        // Gambar tampil di galeri, dokumen di kartu lampiran — supaya tidak dobel.
        // Urutan galeri mengikuti relasi repoMulmed (orderBy id), jadi gambar
        // pertama adalah cover yang dipilih saat pengumuman dibuat.
        $adalahGambar = fn ($file) => in_array(
            strtolower(pathinfo($file->nama_file ?? '', PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png', 'gif', 'webp']
        );

        $images   = $semuaFile->filter($adalahGambar)->values();
        $lampiran = $semuaFile->reject($adalahGambar)->values();

        $targetAudienceStr = match ($pengumuman->target_audience) {
            'all'       => 'Semua Mahasiswa / Alumni',
            'mahasiswa' => 'Mahasiswa Aktif',
            default     => ucfirst(str_replace('_', ' ', $pengumuman->target_audience)),
        };

        $tanggal = $pengumuman->published_at ?? $pengumuman->created_at;
        $canPinGlobal = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="Detail Pengumuman"
                    badge="Modul Mahasiswa"
                    subtitle="Wadah informasi untuk mahasiswa dan alumni">
                    <x-slot:actions>
                        <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-action">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </x-slot:actions>
                </x-manajemenmahasiswa::ui.page-header>
            </div>

            <div class="dash-box-body">
                <div class="dt-layout">

                    {{-- ── Kolom utama: artikel ───────────────────── --}}
                    <div class="dt-main">
                        <div class="dt-card">
                            <div class="dt-card-body">
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

                                <h2 class="dt-title">{{ $pengumuman->judul }}</h2>

                                @include('manajemenmahasiswa::pengumuman._detail-gallery', [
                                    'images' => $images,
                                    'judul'  => $pengumuman->judul,
                                ])

                                <div class="content-section">
                                    {!! $pengumuman->konten !!}
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ── Sidebar: informasi + aksi ──────────────── --}}
                    <aside>
                        <div class="dt-card">
                            <div class="dt-card-head">
                                <span class="dt-card-title">Informasi</span>
                            </div>
                            <div class="dt-card-body">
                                <div class="dt-info-row">
                                    <div class="dt-info-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <div style="min-width:0;">
                                        <p class="dt-info-label">Target Audiens</p>
                                        <p class="dt-info-value">{{ $targetAudienceStr }}</p>
                                    </div>
                                </div>

                                <div class="dt-info-row">
                                    <div class="dt-info-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <div style="min-width:0;">
                                        <p class="dt-info-label">Dipublikasikan</p>
                                        <p class="dt-info-value">{{ $tanggal->translatedFormat('d F Y') }} · {{ $tanggal->format('H:i') }} WIB</p>
                                    </div>
                                </div>

                                <div class="dt-info-row">
                                    <div class="dt-info-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div style="min-width:0;">
                                        <p class="dt-info-label">Pembuat</p>
                                        <p class="dt-info-value">{{ $pengumuman->author->name ?? 'Admin' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-card">
                            <div class="dt-card-head">
                                <span class="dt-card-title">Aksi</span>
                            </div>
                            <div class="dt-card-body">
                                <div class="dt-actions">
                                    {{-- Pin Pribadi --}}
                                    <form action="{{ route('manajemenmahasiswa.pengumuman.personal_pin', $pengumuman->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-action btn-pin-personal">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $isPersonalPinned ? 'currentColor' : 'none' }}"
                                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                            </svg>
                                            {{ $isPersonalPinned ? 'Unpin Pribadi' : 'Pin Pribadi' }}
                                        </button>
                                    </form>

                                    {{-- Pin Global (admin only) --}}
                                    @if($canPinGlobal)
                                        <form action="{{ route('manajemenmahasiswa.pengumuman.pin', $pengumuman->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-action btn-pin-global">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $pengumuman->is_pinned ? 'currentColor' : 'none' }}"
                                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                                                </svg>
                                                {{ $pengumuman->is_pinned ? 'Unpin Global' : 'Pin Global' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ── Lampiran: berkas yang bisa diunduh ─────── --}}
                        @if($lampiran->count() > 0)
                            <div class="dt-card">
                                <div class="dt-card-head">
                                    <span class="dt-card-title">Lampiran File</span>
                                    <span class="dt-card-count">{{ $lampiran->count() }} berkas</span>
                                </div>
                                <div class="dt-card-body">
                                    <div class="lampiran-list">
                                        @foreach($lampiran as $item)
                                            <a href="{{ route('manajemenmahasiswa.pengumuman.lampiran.download', $item->id) }}"
                                                class="lampiran-item" download>
                                                <div class="lampiran-icon">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="lampiran-info">
                                                    <div class="lampiran-name">{{ $item->judul_file ?? 'Lampiran' }}</div>
                                                    <div class="lampiran-action">
                                                        Unduh
                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                                </div>
                            </div>
                        @endif
                    </aside>

                </div>
            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    @include('manajemenmahasiswa::pengumuman._detail-lightbox')

</x-manajemenmahasiswa::layouts.admin>
