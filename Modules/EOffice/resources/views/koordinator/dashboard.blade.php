<x-eoffice::layouts.koordinator title="Dashboard">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Dashboard</span>
    @endsection

    {{-- Alpine.js wrapper with reactive period selector --}}
    <div x-data="{
        selectedPeriode: '{{ $defaultPeriodeId }}',
        allStats: {{ \Illuminate\Support\Js::from($periodeStats) }},
        get stats() {
            return this.allStats[this.selectedPeriode] ?? this.allStats['all'];
        }
    }">

        {{-- ── Page Header ── --}}
        <div class="mb-6 lg:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 style="font-family:'Inter Tight',sans-serif; font-size:20px; font-weight:600; color:#0D0D12;">
                Dashboard
            </h1>

            {{-- Period Selector Dropdown --}}
            <div style="min-width:220px;">
                <select x-model="selectedPeriode" style="
                    width:100%; padding:8px 12px;
                    border:1px solid #E4E7EC; border-radius:8px;
                    font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#09090B;
                    background:#fff; cursor:pointer;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                ">
                    <option value="all">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">Semester {{ $p->semester }} {{ $p->tahun_ajaran }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ── Stats Cards ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- Stat 1 — Total Pendaftar --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Total Pendaftar</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;" x-text="stats.total_pendaftar"></div>
            </div>

            {{-- Stat 2 — Periode Aktif --}}
            <div class="bg-white border border-[#EAECF0] rounded-[10px] p-[18px] flex flex-col gap-3">
                <div class="flex items-center gap-[10px]">
                    <div class="w-9 h-9 rounded-full bg-[#EEF2FF] flex items-center justify-center">
                        <svg class="w-[18px] h-[18px] text-[#4338CA]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-[13px] font-semibold text-[#0D0D12]"
                        style="font-family:'Inter Tight',sans-serif;">Periode Aktif</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;" x-text="stats.periode_aktif"></div>
            </div>

            {{-- Stat 3 — Menunggu Balancing --}}
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
                        style="font-family:'Inter Tight',sans-serif;">Menunggu Balancing</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;" x-text="stats.menunggu_balancing"></div>
            </div>

            {{-- Stat 4 — Butuh Validasi --}}
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
                        style="font-family:'Inter Tight',sans-serif;">Butuh Validasi</span>
                </div>
                <div class="text-[26px] font-bold text-[#0D0D12] mt-2 leading-none"
                    style="font-family:'Inter Tight',sans-serif;" x-text="stats.butuh_validasi"></div>
            </div>
        </div>

        {{-- ── Action Cards ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Action Card 1 — Balancing Dosen --}}
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
                    Balancing Dosen Pembimbing
                </h3>
                <p
                    style="font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:500; line-height:1.6; color:#666D80; letter-spacing:0.01em; flex:1; margin-bottom:24px;">
                    Lihat daftar mahasiswa yang belum mendapatkan dosen pembimbing, atur kuota, dan lakukan pembagian
                    secara merata sesuai keahlian dosen.
                </p>
                <a href="{{ route('eoffice.kp.koordinator.balancing') }}" style="
                    display:inline-flex; align-items:center; justify-content:center;
                    gap:8px; padding:10px 20px; background:#0B266E; color:#ffffff;
                    font-family:'Inter Tight',sans-serif; font-size:14px; font-weight:600;
                    border-radius:10px; text-decoration:none; width:fit-content;
                    transition:background 0.2s; letter-spacing:0.01em;
                " onmouseover="this.style.background='#233C7D';" onmouseout="this.style.background='#0B266E';">
                    Lakukan Balancing
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>


        </div>

    </div>{{-- end Alpine wrapper --}}
</x-eoffice::layouts.koordinator>