<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .page-header h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 4px;
        }
        .page-header p {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .detail-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 32px;
            margin-top: 24px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .detail-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .detail-title h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .detail-image-wrapper {
            background: #e5e7eb;
            border-radius: 10px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .detail-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .detail-image-placeholder h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin: 0;
        }

        .meta-box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            background: #f9fafb;
        }

        .meta-table {
            width: 100%;
            max-width: 400px;
            font-size: 0.9rem;
            color: #111827;
        }
        .meta-table td {
            padding: 4px 8px 4px 0;
        }
        .meta-table td:first-child {
            width: 80px;
            color: #6b7280;
            font-weight: 500;
        }

        .section-container {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .section-title-left {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            position: relative;
            padding-right: 15px;
            display: inline-block;
            white-space: nowrap;
        }
        .section-container hr {
            flex-grow: 1;
            border-top: 1px solid #e5e7eb;
            margin: 0;
            opacity: 1;
        }

        .content-box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px;
            font-size: 0.95rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 30px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .lampiran-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
        }

        .lampiran-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px 20px;
            text-decoration: none;
            color: #111827;
            transition: all 0.2s;
        }
        .lampiran-item:hover {
            background: #f5f3ff;
            border-color: #818cf8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: #111827;
        }

        .lampiran-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .lampiran-right {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 0.85rem;
            color: #4f46e5;
            font-weight: 600;
        }

        .btn-kembali-wrapper {
            text-align: center;
            margin-top: 10px;
        }
        .btn-kembali {
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 30px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-kembali:hover {
            background: #4338ca;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
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
        $posterUrl = $images->first() ? asset('storage/' . $images->first()->path_file) : null;

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
    @endphp

    <div class="detail-card">
        <!-- Title -->
        <div class="detail-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 11 18-5v12L3 14v-3z"></path>
                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
            </svg>
            <h5>{{ $pengumuman->judul }}</h5>
        </div>

        <!-- Image Poster -->
        @if($posterUrl)
        <div class="detail-image-wrapper">
            <img src="{{ $posterUrl }}" alt="{{ $pengumuman->judul }}">
        </div>
        @endif

        <!-- Meta Box -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td>Target</td>
                    <td>: {{ $targetAudienceStr }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ ($pengumuman->published_at ?? $pengumuman->created_at)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Pembuat</td>
                    <td>: {{ $pengumuman->author->name ?? 'Admin' }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: {{ ($pengumuman->published_at ?? $pengumuman->created_at)->format('H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        <!-- Content & Divider -->
        <div class="section-container">
            <div class="section-title-left">Isi Pengumuman</div>
            <hr>
        </div>

        <div class="content-box">
            {!! $pengumuman->konten !!}
        </div>

        <!-- Lampiran & Divider -->
        <div class="section-container">
            <div class="section-title-left">Lampiran</div>
            <hr>
        </div>

        <div class="lampiran-list">
            @forelse($lampiran as $item)
            <a href="{{ route('manajemenmahasiswa.pengumuman.lampiran.download', $item->id) }}" class="lampiran-item">
                <div class="lampiran-left">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>{{ $item->judul_file ?? 'Lampiran' }}</span>
                </div>
                <div class="lampiran-right">
                    <span style="display:flex; align-items:center; gap:5px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Unduh
                    </span>
                </div>
            </a>
            @empty
            <div class="lampiran-item" style="justify-content:center; color:#6b7280; font-size: 0.95rem;">
                Tidak ada lampiran
            </div>
            @endforelse
        </div>

        <div class="btn-kembali-wrapper" style="display: flex; gap: 10px; justify-content: center; align-items: center; margin-top: 10px;">
            <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-kembali">Kembali</a>
            @if($canDelete)
                <form action="{{ route('manajemenmahasiswa.pengumuman.remove', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-kembali" style="background: #ef4444; box-shadow: none;">Hapus</button>
                </form>
            @endif
        </div>
    </div>

</x-manajemenmahasiswa::layouts.admin>
