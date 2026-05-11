<x-banksoal::layouts.admin>
    <!-- Header Title -->
    <div class="mb-6 lg:mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Cetak Soal Ujian (Offline)</h1>
            <p class="text-slate-500 text-sm mt-2">Daftar permintaan pencetakan lembar soal fisik dari Dosen</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" method="GET" class="mb-6 flex gap-3">
        <input type="text" name="searchSoal" value="{{ request('searchSoal') }}" placeholder="Cari agenda atau mata kuliah..." class="w-full rounded-xl border border-slate-300 text-sm px-4 py-2.5 outline-none focus:border-[#0f172a] focus:ring-1 focus:ring-[#0f172a]">
        
        <select name="filterStatus" class="w-full md:w-64 rounded-xl border border-slate-300 text-sm px-4 py-2.5 outline-none focus:border-[#0f172a] focus:ring-1 focus:ring-[#0f172a]">
            <option value="">-- Semua Status --</option>
            <option value="pending" @selected(request('filterStatus') == 'pending')>🕒 Menunggu Dicetak</option>
            <option value="diproses" @selected(request('filterStatus') == 'diproses')>⏳ Sedang Diproses</option>
            <option value="selesai" @selected(request('filterStatus') == 'selesai')>✅ Sudah Dicetak</option>
        </select>
        
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 transition lg:px-8 text-white px-5 py-2.5 rounded-xl font-medium">Filter</button>
    </form>

    @if (session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tgl / Waktu</th>
                        <th class="px-6 py-4 font-semibold">Agenda Ujian</th>
                        <th class="px-6 py-4 font-semibold">Mata Kuliah / Dosen</th>
                        <th class="px-6 py-4 font-semibold text-center">Metode</th>
                        <th class="px-6 py-4 font-semibold text-center">Status Cetak</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($antreanCetak as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-slate-500">
                            {{ $item->created_at->format('d M Y') }}<br>
                            <span class="text-[10px]">{{ $item->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">
                            {{ $item->nama_ekstraksi }}<br>
                            <span class="text-xs text-slate-500 font-normal">TA: {{ $item->tahun_akademik }} - Sem {{ ucfirst($item->semester) }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800 text-xs leading-relaxed">
                            <span class="font-bold text-[#0f172a]">{{ $item->mataKuliah ? $item->mataKuliah->nama : 'MK Tidak Ditemukan' }}</span><br>
                            <span class="text-slate-500 italic">{{ $item->dosen ? $item->dosen->name : 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(strtolower($item->metode_ujian) == 'offline')
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-200 whitespace-nowrap">Kertas (Offline)</span>
                            @else
                                <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-1 rounded-full border border-purple-200 whitespace-nowrap">Online</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status_cetak == 'pending')
                                <span class="bg-amber-100 text-amber-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-amber-200 uppercase tracking-wider"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                            @elseif($item->status_cetak == 'diproses')
                                <span class="bg-sky-100 text-sky-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-sky-200 uppercase tracking-wider"><i class="fas fa-spinner fa-spin mr-1"></i> Diproses</span>
                            @elseif($item->status_cetak == 'selesai')
                                <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-emerald-200 uppercase tracking-wider"><i class="fas fa-check mr-1"></i> Selesai</span>
                            @else
                                <span class="bg-slate-100 text-slate-700 text-[11px] font-bold px-3 py-1.5 rounded-lg border border-slate-200 uppercase tracking-wider">{{ $item->status_cetak }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('banksoal.admin.kontrol-banksoal.soal.cetak', $item->id) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition" title="Print Dokumen PDF">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                                @if($item->status_cetak != 'selesai')
                                <form action="{{ route('banksoal.admin.kontrol-banksoal.soal.tandai-selesai', $item->id) }}" method="POST" onsubmit="return confirm('Tandai bahwa berkas fisik soal ini sudah tercetak dan siap dibagikan?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-3 py-1.5 text-xs font-semibold transition shadow-sm" title="Tandai Selesai Dicetak">
                                        <i class="fas fa-check-double"></i> Selesai
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                            <p class="font-medium text-slate-500">Belum ada antrean cetak ujian fisik dari Dosen saat ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/80">
            {{ $antreanCetak->links() }}
        </div>
    </div>
</x-banksoal::layouts.admin>
