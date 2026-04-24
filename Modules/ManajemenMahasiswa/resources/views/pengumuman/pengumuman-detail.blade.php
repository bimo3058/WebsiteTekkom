<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .page-header {
            margin-bottom: 30px;
        }
        .page-header h4 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 6px;
            letter-spacing: -0.025em;
        }
        .page-header p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .detail-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            margin: 0 auto;
            max-width: 900px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.01);
            border: 1px solid #f3f4f6;
        }

        .detail-title {
            margin-bottom: 24px;
            text-align: center;
        }
        .detail-title h5 {
            font-size: 2rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
            line-height: 1.3;
            letter-spacing: -0.025em;
        }

        .meta-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-bottom: 36px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #4b5563;
            background: #f9fafb;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid #f3f4f6;
        }
        .meta-item svg {
            color: #6366f1;
        }

        .detail-image-wrapper {
            background: #f9fafb;
            border-radius: 16px;
            width: 100%;
            max-width: 420px; /* Lebar maksimal agar tidak terlalu besar */
            aspect-ratio: 42 / 59.4; /* Ukuran poster standar 42 x 59.4 cm */
            margin: 0 auto 40px auto;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .detail-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .content-section {
            font-size: 1.05rem;
            color: #374151;
            line-height: 1.8;
            margin-bottom: 40px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .content-section img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 12px;
            margin: 20px 0;
        }
        .content-section p {
            margin-bottom: 1.2em;
        }
        .content-section a {
            color: #4f46e5;
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        .content-section a:hover {
            color: #4338ca;
        }

        .section-heading {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lampiran-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }

        .lampiran-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .lampiran-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .lampiran-icon {
            width: 40px;
            height: 40px;
            background: #e0e7ff;
            color: #4f46e5;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }
        .lampiran-info {
            flex-grow: 1;
            overflow: hidden;
        }
        .lampiran-name {
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #0f172a;
        }
        .lampiran-action {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s;
        }
        .lampiran-item:hover .lampiran-action {
            color: #4f46e5;
        }

        .actions-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: center;
            padding-top: 30px;
            border-top: 1px solid #f3f4f6;
        }
        
        .btn-action {
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }
        .btn-back {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-back:hover {
            background: #e5e7eb;
            color: #1f2937;
        }
        .btn-edit {
            background: #f59e0b;
            color: #fff;
        }
        .btn-edit:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        .btn-delete {
            background: #ef4444;
            color: #fff;
        }
        .btn-delete:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 640px) {
            .detail-card {
                padding: 24px 20px;
            }
            .detail-title h5 {
                font-size: 1.5rem;
            }
            .meta-container {
                flex-direction: column;
                align-items: stretch;
            }
            .meta-item {
                justify-content: center;
            }
            .actions-container {
                flex-direction: column;
                width: 100%;
            }
            .btn-action {
                width: 100%;
            }
        }
    </style>
    @endpush

    <div class="page-header">
        <h4>Pengumuman & Informasi</h4>
        <p>Wadah Informasi untuk Mahasiswa dan Alumni</p>
    </div>

    @php
        $lampiran = collect($pengumuman->repoMulmed ?? []);

        // Try to find first image for the poster
        $images = $lampiran->filter(function($file) {
            return in_array(strtolower(pathinfo($file->nama_file ?? '', PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });
        $posterUrl = $images->first() ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($images->first()->path_file) : null;

        $targetAudienceStr = $pengumuman->target_audience;
        if($targetAudienceStr == 'all') {
            $targetAudienceStr = 'Semua Mahasiswa / Alumni';
        } else if($targetAudienceStr == 'mahasiswa') {
            $targetAudienceStr = 'Mahasiswa Aktif';
        } else {
            $targetAudienceStr = ucfirst(str_replace('_', ' ', $targetAudienceStr));
        }

        $roles = $user->roles->pluck('name');
        $isAdminOrKoor = $roles->intersect(['superadmin', 'admin', 'dosen_koordinator'])->isNotEmpty();
        $canDelete = $user->id === $pengumuman->user_id || $isAdminOrKoor;
        $canEdit   = $user->id === $pengumuman->user_id || $isAdminOrKoor;
    @endphp

    <div class="detail-card">
        <!-- Title -->
        <div class="detail-title">
            <h5>{{ $pengumuman->judul }}</h5>
        </div>

        <!-- Meta Container -->
        <div class="meta-container">
            <div class="meta-item" title="Target Audience">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                {{ $targetAudienceStr }}
            </div>
            <div class="meta-item" title="Tanggal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                {{ ($pengumuman->published_at ?? $pengumuman->created_at)->translatedFormat('d F Y') }}
            </div>
            <div class="meta-item" title="Waktu">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                {{ ($pengumuman->published_at ?? $pengumuman->created_at)->format('H:i') }} WIB
            </div>
            <div class="meta-item" title="Pembuat">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                {{ $pengumuman->author->name ?? 'Admin' }}
            </div>
        </div>

        <!-- Image Poster -->
        @if($posterUrl)
        <div class="detail-image-wrapper">
            <img src="{{ $posterUrl }}" alt="{{ $pengumuman->judul }}">
        </div>
        @endif

        <!-- Content -->
        <div class="content-section">
            {!! $pengumuman->konten !!}
        </div>

        <!-- Lampiran -->
        @if($lampiran->count() > 0)
        <div class="section-heading">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
            Lampiran File
        </div>
        <div class="lampiran-list">
            @foreach($lampiran as $item)
            <a href="{{ route('manajemenmahasiswa.pengumuman.lampiran.download', $item->id) }}" class="lampiran-item" download>
                <div class="lampiran-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div class="lampiran-info">
                    <div class="lampiran-name">{{ $item->judul_file ?? 'Lampiran' }}</div>
                    <div class="lampiran-action">
                        Unduh
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <div class="actions-container">
            <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-action btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>
            @if($canEdit)
                <a href="{{ route('manajemenmahasiswa.pengumuman.edit', $pengumuman->id) }}" class="btn-action btn-edit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </a>
            @endif
            @if($canDelete)
                <form action="{{ route('manajemenmahasiswa.pengumuman.remove', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

</x-manajemenmahasiswa::layouts.admin>
