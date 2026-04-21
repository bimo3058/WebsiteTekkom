<x-banksoal::layouts.gpm-master>
    <x-banksoal::ui.page-header title="Riwayat Validasi Bank Soal" subtitle="Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi" />

    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-6 text-sm font-semibold">
            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center">
                Menunggu Validasi
                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $counts->menunggu ?? 0 }}</span>
            </a>
            <a href="#" class="pb-3 border-b-2 border-blue-600 text-blue-600 flex items-center">
                Selesai Direview
                <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700 border border-blue-200">{{ $counts->selesai ?? $riwayat_soal->count() }}</span>
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form action="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" method="GET" class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="search" autocomplete="off" list="datalistRiwayat" value="{{ request('search') }}" placeholder="Cari mata kuliah..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" onchange="this.form.submit()">
            <datalist id="datalistRiwayat">
                @foreach($all_riwayat_soal as $item)
                    <option value="{{ $item->mk_nama }}"></option>
                    <option value="{{ $item->mk_kode }}"></option>
                @endforeach
            </datalist>
        </form>
        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Review Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat_soal as $riwayat)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $riwayat->mk_nama }}</div>
                                <div class="text-xs text-slate-500">{{ $riwayat->mk_kode }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">Dosen Pengampu</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $riwayat->jumlah_soal }} Butir Direview</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $riwayat->tanggal_review ? \Carbon\Carbon::parse($riwayat->tanggal_review)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($riwayat->jumlah_revisi > 0)
                                    <span class="inline-flex w-full justify-center rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700 border border-rose-200">Dikembalikan</span>
                                @else
                                    <span class="inline-flex w-full justify-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal.detail', $riwayat->mk_id) }}" class="inline-flex flex-col items-center text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                    <span class="text-[11px] font-semibold">Lihat Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-history text-3xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Belum ada riwayat.</p>
                                    <p class="text-xs text-slate-500">Belum ada paket soal mata kuliah yang selesai divalidasi oleh GPM.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat_soal->count() > 0)
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $riwayat_soal->count() }} item</span>
            </div>
        @endif
    </div>
</x-banksoal::layouts.gpm-master>