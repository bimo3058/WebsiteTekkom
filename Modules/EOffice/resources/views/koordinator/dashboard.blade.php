<x-eoffice::layouts.koordinator title="Dashboard">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Dashboard</span>
    @endsection

    {{-- ── Page Header ── --}}
    <div class="mb-6 lg:mb-8 flex justify-between items-start">
        <div>
            {{-- H5: Semibold / 20px --}}
            <h1 style="font-family:'Inter Tight',sans-serif; font-size:20px; font-weight:600; line-height:1.35; color:#0D0D12;">
                Dashboard
            </h1>
            {{-- Body Medium / Regular / 16px --}}
            <p style="font-family:'Inter Tight',sans-serif; font-size:16px; font-weight:400; line-height:1.5; color:#666D80; margin-top:4px; letter-spacing:0.02em;">
                Ringkasan data pelaksanaan Kerja Praktik mahasiswa.
            </p>
        </div>
    </div>

    {{-- ── Stats Card ── --}}
    {{-- Shadow: Small | Border: Greyscale-100 #F1F1F3 | Rounded-16px --}}
    <div style="
        background:#ffffff;
        border-radius:16px;
        border:1px solid #F1F1F3;
        box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
        margin-bottom:32px;
        padding:24px;
    ">
        {{-- H6: Semibold / 18px --}}
        <h2 style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:600; line-height:1.35; color:#0D0D12; margin-bottom:24px;">
            Statistik Global KP
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Stat 1 — Total Pendaftar --}}
            <div style="padding:16px; background:#F8F5FF; border-radius:12px; border:1px solid #D3C4FC;">
                {{-- Body XSmall / Semibold / 12px — label --}}
                <p style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:#9774F7; margin-bottom:8px;">
                    Total Pendaftar
                </p>
                <div class="flex items-end gap-2">
                    {{-- H1: Semibold / 48px --}}
                    <span style="font-family:'Inter Tight',sans-serif; font-size:48px; font-weight:600; line-height:1; color:#0B266E; letter-spacing:-0.02em;">
                        {{ $stats['total_mahasiswa'] }}
                    </span>
                    {{-- Body Small / Regular / 14px --}}
                    <span style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:400; color:#7A4DF5; margin-bottom:6px;">
                        Mahasiswa
                    </span>
                </div>
            </div>

            {{-- Stat 2 — Menunggu Dosen (Warning) --}}
            <div style="padding:16px; background:#FFFBEB; border-radius:12px; border:1px solid #FDE68A;">
                <p style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:#B45309; margin-bottom:8px;">
                    Menunggu Dosen
                </p>
                <div class="flex items-end gap-2">
                    <span style="font-family:'Inter Tight',sans-serif; font-size:48px; font-weight:600; line-height:1; color:#92400E; letter-spacing:-0.02em;">
                        {{ $stats['menunggu_dosen'] }}
                    </span>
                    <span style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:400; color:#B45309; margin-bottom:6px;">
                        Perlu Balancing
                    </span>
                </div>
            </div>

            {{-- Stat 3 — Fase Pelaksanaan (Primary Sky / Additional) --}}
            <div style="padding:16px; background:#EFF6FF; border-radius:12px; border:1px solid #BFDBFE;">
                <p style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:#1D4ED8; margin-bottom:8px;">
                    Fase Pelaksanaan
                </p>
                <div class="flex items-end gap-2">
                    <span style="font-family:'Inter Tight',sans-serif; font-size:48px; font-weight:600; line-height:1; color:#1E3A8A; letter-spacing:-0.02em;">
                        {{ $stats['sedang_kp'] }}
                    </span>
                    <span style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:400; color:#1D4ED8; margin-bottom:6px;">
                        Sedang KP
                    </span>
                </div>
            </div>

            {{-- Stat 4 — Validasi Berkas (Success) --}}
            <div style="padding:16px; background:#F0FDF4; border-radius:12px; border:1px solid #BBF7D0;">
                <p style="font-family:'Inter Tight',sans-serif; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:#15803D; margin-bottom:8px;">
                    Validasi Berkas
                </p>
                <div class="flex items-end gap-2">
                    <span style="font-family:'Inter Tight',sans-serif; font-size:48px; font-weight:600; line-height:1; color:#14532D; letter-spacing:-0.02em;">
                        {{ $stats['menunggu_validasi'] }}
                    </span>
                    <span style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:400; color:#15803D; margin-bottom:6px;">
                        Dokumen Baru
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Action Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Action Card 1 — Balancing Dosen (Primary #0B266E) --}}
        <div class="group" style="
            background:#ffffff;
            border-radius:16px;
            border:1px solid #F1F1F3;
            box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
            padding:24px;
            display:flex;
            flex-direction:column;
            transition:box-shadow 0.2s, border-color 0.2s;
        "
        onmouseover="this.style.borderColor='#D3C4FC'; this.style.boxShadow='0px 4px 12px rgba(11,38,110,0.1), 0px 1px 3px rgba(11,38,110,0.06)';"
        onmouseout="this.style.borderColor='#F1F1F3'; this.style.boxShadow='0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04)';"
        >
            {{-- Icon badge — Primary 50 bg --}}
            <div style="
                width:48px; height:48px;
                background:#F8F5FF;
                border-radius:10px;
                border:1px solid #D3C4FC;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:20px;
            ">
                <svg width="22" height="22" fill="none" stroke="#7A4DF5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>

            {{-- H6: Semibold / 18px --}}
            <h3 style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:600; line-height:1.35; color:#0D0D12; margin-bottom:12px;">
                Balancing Dosen Pembimbing
            </h3>

            {{-- Body Small / Medium / 14px --}}
            <p style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; line-height:1.6; color:#666D80; letter-spacing:0.01em; flex:1; margin-bottom:24px;">
                Lihat daftar mahasiswa yang belum mendapatkan dosen pembimbing, atur kuota, dan lakukan pembagian secara merata sesuai keahlian dosen.
            </p>

            <a href="{{ route('eoffice.kp.koordinator.balancing') }}" style="
                display:inline-flex; align-items:center; justify-content:center;
                gap:8px;
                padding:10px 20px;
                background:#0B266E;
                color:#ffffff;
                font-family:'Inter Tight',sans-serif;
                font-size:14px;
                font-weight:600;
                border-radius:10px;
                text-decoration:none;
                width:fit-content;
                transition:background 0.2s;
                letter-spacing:0.01em;
            "
            onmouseover="this.style.background='#233C7D';"
            onmouseout="this.style.background='#0B266E';"
            >
                Lakukan Balancing
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Action Card 2 — Approval Berkas (Success #15803D) --}}
        <div style="
            background:#ffffff;
            border-radius:16px;
            border:1px solid #F1F1F3;
            box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
            padding:24px;
            display:flex;
            flex-direction:column;
            transition:box-shadow 0.2s, border-color 0.2s;
        "
        onmouseover="this.style.borderColor='#BBF7D0'; this.style.boxShadow='0px 4px 12px rgba(21,128,61,0.1), 0px 1px 3px rgba(21,128,61,0.06)';"
        onmouseout="this.style.borderColor='#F1F1F3'; this.style.boxShadow='0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04)';"
        >
            {{-- Icon badge — Success bg --}}
            <div style="
                width:48px; height:48px;
                background:#F0FDF4;
                border-radius:10px;
                border:1px solid #BBF7D0;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:20px;
            ">
                <svg width="22" height="22" fill="none" stroke="#15803D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>

            {{-- H6: Semibold / 18px --}}
            <h3 style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:600; line-height:1.35; color:#0D0D12; margin-bottom:12px;">
                Approval Berkas
            </h3>

            {{-- Body Small / Medium / 14px --}}
            <p style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; line-height:1.6; color:#666D80; letter-spacing:0.01em; flex:1; margin-bottom:24px;">
                Lakukan verifikasi administrasi seperti transkrip nilai, kartu hijau, surat balasan instansi, dan finalisasi nilai lapangan mahasiswa.
            </p>

            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" style="
                display:inline-flex; align-items:center; justify-content:center;
                gap:8px;
                padding:10px 20px;
                background:#ffffff;
                color:#15803D;
                font-family:'Inter Tight',sans-serif;
                font-size:14px;
                font-weight:600;
                border-radius:10px;
                border:1.5px solid #15803D;
                text-decoration:none;
                width:fit-content;
                transition:background 0.2s;
                letter-spacing:0.01em;
            "
            onmouseover="this.style.background='#F0FDF4';"
            onmouseout="this.style.background='#ffffff';"
            >
                Buka Halaman
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</x-eoffice::layouts.koordinator>