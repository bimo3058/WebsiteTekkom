<x-dynamic-component :component="$layout">

@push('styles')
<style>
    .main-wrapper {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .back-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .back-bar a, .back-bar .btn {
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-back {
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #374151;
    }
    .btn-back:hover { background: #f9fafb; color: #111827; }
    .btn-edit-top {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #78350f;
    }
    .btn-edit-top:hover { background: #fde68a; }

    /* ── Profile Card ── */
    .profile-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .profile-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%);
        height: 120px;
        position: relative;
    }
    .profile-avatar-wrap {
        position: absolute;
        bottom: -44px;
        left: 32px;
    }
    .profile-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: #4f46e5;
        overflow: hidden;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-body {
        padding: 56px 32px 32px;
    }
    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }
    .profile-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .profile-meta .nim {
        font-family: monospace;
        color: #4f46e5;
        font-weight: 700;
    }
    .profile-meta .dot {
        color: #d1d5db;
    }

    .status-badge-lg {
        font-size: 13px;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-badge-lg.bekerja { background: #dcfce7; color: #166534; }
    .status-badge-lg.wirausaha { background: #fef3c7; color: #92400e; }
    .status-badge-lg.studi_lanjut { background: #dbeafe; color: #1e40af; }
    .status-badge-lg.belum_bekerja { background: #fef2f2; color: #991b1b; }
    .status-badge-lg.belum_terdata { background: #f3f4f6; color: #4b5563; }

    /* ── Info Section ── */
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 16px;
        margin-top: 24px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }

    .info-item {
        background: #f8fafc;
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }
    .info-value.empty {
        color: #cbd5e1;
        font-style: italic;
        font-weight: 400;
    }
    .info-value a {
        color: #0077b5;
        text-decoration: none;
        font-weight: 600;
    }
    .info-value a:hover { text-decoration: underline; }
</style>
@endpush

<div class="back-bar">
    <a href="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('manajemenmahasiswa.direktori.alumni.cv', $alumni->id) }}" target="_blank" style="background: #4f46e5; border: 1px solid #4f46e5; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            Generate CV
        </a>
        @if($isAdmin)
            <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alumni->id) }}" class="btn-edit-top">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Data
            </a>
        @endif
    </div>
</div>

<div class="profile-card">
    <div class="profile-banner">
        <div class="profile-avatar-wrap">
            <div class="profile-avatar">
                @if($alumni->user && $alumni->user->avatar_url)
                    <img src="{{ $alumni->user->avatar_url }}" alt="{{ $alumni->user->name }}">
                @else
                    {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 1)) }}
                @endif
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="profile-name">{{ $alumni->user->name ?? 'Tanpa Nama' }}</div>
                <div class="profile-meta">
                    <span class="nim">{{ $alumni->nim }}</span>
                    <span class="dot">•</span>
                    <span>Angkatan {{ $alumni->angkatan }}</span>
                    <span class="dot">•</span>
                    <span>Lulus {{ $alumni->tahun_lulus }}</span>
                    @if($alumni->program_studi)
                        <span class="dot">•</span>
                        <span>{{ $alumni->program_studi }}</span>
                    @endif
                </div>
            </div>
            <span class="status-badge-lg {{ $alumni->status_karir ?? 'belum_terdata' }}">
                @if(in_array($alumni->status_karir, ['bekerja', 'wirausaha'])) <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">work</span>
                @elseif($alumni->status_karir == 'studi_lanjut') <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">school</span>
                @else <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">hourglass_empty</span>
                @endif
                {{ $alumni->status_karir_label }}
            </span>
        </div>

        <div class="section-title">Informasi Karir & Pekerjaan</div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Perusahaan / Instansi</div>
                <div class="info-value {{ !$alumni->perusahaan ? 'empty' : '' }}">
                    {{ $alumni->perusahaan ?: 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Posisi / Jabatan</div>
                <div class="info-value {{ !$alumni->jabatan ? 'empty' : '' }}">
                    {{ $alumni->jabatan ?: 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Bidang Industri</div>
                <div class="info-value {{ !$alumni->bidang_industri ? 'empty' : '' }}">
                    {{ $alumni->bidang_industri ? $alumni->bidang_industri_label : 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tahun Mulai Bekerja</div>
                <div class="info-value {{ !$alumni->tahun_mulai_bekerja ? 'empty' : '' }}">
                    {{ $alumni->tahun_mulai_bekerja ?: 'Belum diisi' }}
                    @if($alumni->waktu_tunggu !== null)
                        <span style="font-size: 11px; color: #6b7280; font-weight: 400;">({{ $alumni->waktu_tunggu }} tahun setelah lulus)</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-grid" style="margin-top: 14px;">
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">LinkedIn</div>
                @if($alumni->linkedin)
                    <div class="info-value">
                        <a href="{{ $alumni->linkedin }}" target="_blank">🔗 {{ $alumni->linkedin }}</a>
                    </div>
                @else
                    <div class="info-value empty">Belum ada tautan LinkedIn</div>
                @endif
            </div>
        </div>
    </div>
</div>

</x-dynamic-component>
