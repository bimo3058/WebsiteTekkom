<x-eoffice::layouts.koordinator title="Dashboard">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold">Dashboard</span>
    @endsection

    <!-- Page Header -->
    <div class="mb-6 lg:mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#081031] tracking-tight">Dashboard</h1>
            <p class="text-[14px] text-[#666D80] mt-1 max-w-2xl leading-relaxed">Ringkasan data pelaksanaan Kerja Praktik
                mahasiswa.</p>
        </div>
    </div>

    <!-- Stats Card ("kotak 1") -->
    <div class="bg-white rounded-[16px] border border-[#DFE1E7] mb-8 p-4">
        <h2 class="text-lg font-bold text-[#081031] mb-6">Statistik Global KP</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1 -->
            <div>
                <p class="text-xs font-bold text-[#A4ABB8] uppercase tracking-wider mb-2">Total Pendaftar</p>
                <div class="flex items-end gap-3">
                    <p class="text-5xl font-extrabold text-[#081031] tracking-tight">{{ $stats['total_mahasiswa'] }}</p>
                    <p class="text-sm text-[#666D80] font-medium pb-1.5">Mahasiswa</p>
                </div>
            </div>

            <!-- Stat 2 -->
            <div>
                <p class="text-xs font-bold text-[#A4ABB8] uppercase tracking-wider mb-2">Menunggu Dosen</p>
                <div class="flex items-end gap-3">
                    <p class="text-5xl font-extrabold text-amber-500 tracking-tight">{{ $stats['menunggu_dosen'] }}</p>
                    <p class="text-sm text-[#666D80] font-medium pb-1.5">Perlu Balancing</p>
                </div>
            </div>

            <!-- Stat 3 -->
            <div>
                <p class="text-xs font-bold text-[#A4ABB8] uppercase tracking-wider mb-2">Fase Pelaksanaan</p>
                <div class="flex items-end gap-3">
                    <p class="text-5xl font-extrabold text-[#0065FF] tracking-tight">{{ $stats['sedang_kp'] }}</p>
                    <p class="text-sm text-[#666D80] font-medium pb-1.5">Sedang KP</p>
                </div>
            </div>

            <!-- Stat 4 -->
            <div>
                <p class="text-xs font-bold text-[#A4ABB8] uppercase tracking-wider mb-2">Validasi Berkas</p>
                <div class="flex items-end gap-3">
                    <p class="text-5xl font-extrabold text-emerald-500 tracking-tight">{{ $stats['menunggu_validasi'] }}</p>
                    <p class="text-sm text-[#666D80] font-medium pb-1.5">Dokumen Baru</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards ("kotak 2 dan 3") -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Action Card 1 -->
        <div class="bg-white rounded-[16px] border border-[#DFE1E7] p-4 flex flex-col group hover:border-[#C1D0FF] hover:shadow-sm transition-all">
            <div class="w-12 h-12 bg-[#F0F2FA] text-[#0065FF] rounded-[10px] flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#081031] mb-3 group-hover:text-[#0065FF] transition-colors">Balancing
                Dosen Pembimbing</h3>
            <p class="text-sm text-[#666D80] flex-1 mb-8 leading-relaxed">Lihat daftar mahasiswa yang belum mendapatkan
                dosen pembimbing, atur kuota, dan lakukan pembagian secara merata sesuai keahlian dosen.</p>
            <a href="{{ route('eoffice.kp.koordinator.balancing') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-[#0065FF] text-white font-bold text-sm rounded-[10px] hover:bg-blue-700 transition-colors w-fit">
                Lakukan Balancing
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Action Card 2 -->
        <div class="bg-white rounded-[16px] border border-[#DFE1E7] p-4 flex flex-col group hover:border-emerald-200 hover:shadow-sm transition-all">
            <div class="w-12 h-12 bg-[#E6F4EA] text-[#0E8A38] rounded-[10px] flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#081031] mb-3 group-hover:text-[#0E8A38] transition-colors">Approval Berkas</h3>
            <p class="text-sm text-[#666D80] flex-1 mb-8 leading-relaxed">Lakukan verifikasi administrasi seperti
                transkrip nilai, kartu hijau, surat balasan instansi, dan finalisasi nilai lapangan mahasiswa.</p>
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-[#0E8A38] text-[#0E8A38] font-bold text-sm rounded-[10px] hover:bg-[#E6F4EA] transition-colors w-fit">
                Buka Halaman
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</x-eoffice::layouts.koordinator>