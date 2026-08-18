<x-eoffice::layouts.dosen title="Dashboard">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Dashboard</span>
    @endsection

    {{-- Wrapper (Optional, to mimic koordinator perfectly) --}}
    <div>

        {{-- ── Page Header ── --}}
        <div class="mb-6 lg:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 style="font-family:'Inter Tight',sans-serif; font-size:20px; font-weight:600; color:#0D0D12;">
                Dashboard
            </h1>
        </div>

        {{-- ── Stats Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- Stat 1 --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Total Bimbingan</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;">{{ $stats['total_bimbingan'] ?? 0 }}</div>
            </div>

            {{-- Stat 2 --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Menunggu ACC (Pra KP)</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;">{{ $stats['menunggu_acc'] ?? 0 }}</div>
            </div>

            {{-- Stat 3 --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Sedang KP</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;">{{ $stats['sedang_kp'] ?? 0 }}</div>
            </div>

            {{-- Stat 4 --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Selesai KP / Ternilai</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;">{{ $stats['selesai_kp'] ?? 0 }}</div>
            </div>
        </div>

        {{-- ── Action Cards ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Action Card 1 --}}
            <div style="
                background:#ffffff; border-radius:16px;
                border:1px solid #F1F1F3;
                box-shadow:0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
                padding:24px; display:flex; flex-direction:column;
                transition:box-shadow 0.2s, border-color 0.2s;
            " onmouseover="this.style.borderColor='#D3C4FC'; this.style.boxShadow='0px 4px 12px rgba(11,38,110,0.1), 0px 1px 3px rgba(11,38,110,0.06)';"
                onmouseout="this.style.borderColor='#F1F1F3'; this.style.boxShadow='0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04)';">
                <div
                    style="width:48px; height:48px; background:#F8F5FF; border-radius:10px; border:1px solid #D3C4FC; display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                    <svg width="22" height="22" fill="none" stroke="#7A4DF5" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <path
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3
                    style="font-family:'Inter Tight',sans-serif; font-size:18px; font-weight:600; line-height:1.35; color:#0D0D12; margin-bottom:12px;">
                    Bimbingan Mahasiswa
                </h3>
                <p
                    style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; line-height:1.6; color:#666D80; letter-spacing:0.01em; flex:1; margin-bottom:24px;">
                    Kelola daftar mahasiswa bimbingan, pantau progres, lihat riwayat bimbingan, dan berikan nilai
                    seminar akhir Kerja Praktik.
                </p>
                <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}" style="
                    display:inline-flex; align-items:center; justify-content:center;
                    gap:8px; padding:10px 20px; background:#0B266E; color:#ffffff;
                    font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600;
                    border-radius:10px; text-decoration:none; width:fit-content;
                    transition:background 0.2s; letter-spacing:0.01em;
                " onmouseover="this.style.background='#233C7D';" onmouseout="this.style.background='#0B266E';">
                    Kelola Bimbingan
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

        </div>

    </div>
</x-eoffice::layouts.dosen>