<x-banksoal::layouts.gpm-master>
    <style>
        .gpm-bank-filter-btn,
        .gpm-bank-history-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 0.625rem 1.25rem;
            color: #334155;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .gpm-bank-filter-btn:hover {
            border-color: #0b266e;
            background: #f8fafc;
            color: #0b266e;
        }

        .gpm-bank-history-action {
            min-width: 6.25rem;
            border-color: #0b266e;
            background: #0b266e;
            color: #ffffff;
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .gpm-bank-history-action:hover {
            border-color: #081c52;
            background: #081c52;
        }
    </style>
    <x-banksoal::ui.page-header title="Riwayat Validasi Bank Soal" subtitle="Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi" />

    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-6 text-sm font-semibold">
            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center">
                Menunggu Validasi
                <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $counts->menunggu ?? 0 }}</span>
            </a>
            <a href="#" class="pb-3 border-b-2 border-primary text-primary flex items-center">
                Selesai Direview
                <span class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary border border-primary/20">{{ $counts->selesai ?? $riwayat_soal->count() }}</span>
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Paket Soal</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar paket soal yang telah selesai dievaluasi oleh GPM.</p>
        </div>

        <div class="mx-4 mt-4 mb-4 rounded-xl border border-slate-200 bg-slate-50 p-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form action="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" method="GET" class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" name="search" autocomplete="off" list="datalistRiwayat" value="{{ request('search') }}" placeholder="Cari mata kuliah..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" @change="$el.form.submit()">
            <datalist id="datalistRiwayat">
                @foreach($all_riwayat_soal as $item)
                    <option value="{{ $item->mk_nama }}"></option>
                    <option value="{{ $item->mk_kode }}"></option>
                @endforeach
            </datalist>
        </form>
        <button type="button" class="gpm-bank-filter-btn">
            Filter
        </button>
    </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[25%]">
                    <col class="w-[25%]">
                    <col class="w-[15%]">
                    <col class="w-[17%]">
                    <col class="w-[10%]">
                    <col class="w-[8%]">
                </colgroup>
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-y border-slate-200">
                    <tr>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Mata Kuliah</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Dosen Pengampu</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Jumlah Soal</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Tanggal Review Terakhir</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($riwayat_soal as $riwayat)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $riwayat->mk_nama }}</div>
                                <div class="text-xs text-slate-500">{{ $riwayat->mk_kode }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $dosenPengampu = collect(explode('|||', (string) ($riwayat->dosen_pengampu ?? '')))
                                        ->map(fn ($nama) => trim($nama))
                                        ->filter()
                                        ->values();
                                @endphp
                                <div class="flex flex-col gap-2">
                                    @forelse($dosenPengampu as $namaDosen)
                                        <div>
                                            <span class="text-sm font-medium text-slate-700">{{ $namaDosen }}</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-500">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $riwayat->jumlah_soal }} Butir Direview</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $riwayat->tanggal_review ? \Carbon\Carbon::parse($riwayat->tanggal_review)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($riwayat->jumlah_revisi > 0)
                                    <span class="inline-flex justify-center rounded-full border border-rose-200 bg-rose-50/80 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-rose-700">Revisi</span>
                                @else
                                    <span class="inline-flex justify-center rounded-full border border-emerald-200 bg-emerald-50/80 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Disetujui</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal.detail', $riwayat->mk_id) }}" class="gpm-bank-history-action">
                                    Lihat Detail
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
            <div class="px-6 py-4 border-t border-slate-200 bg-white flex items-center justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $riwayat_soal->count() }} item</span>
            </div>
        @endif
    </div>
</x-banksoal::layouts.gpm-master>