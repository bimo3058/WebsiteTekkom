<x-banksoal::layouts.gpm-master>
    <style>
        .gpm-rps-history-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #0b266e;
            border-radius: 8px;
            background: #0b266e;
            padding: 0.5rem 0.75rem;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .gpm-rps-history-action:hover {
            border-color: #081c52;
            background: #081c52;
        }
    </style>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Riwayat Validasi RPS" subtitle="Pantau riwayat dokumen RPS yang telah direview" />

    <div class="border-b border-slate-200 mb-4">
        <div class="inline-flex items-center gap-2 border-b-2 border-primary pb-3 text-sm font-semibold text-primary">
            Selesai Direview
            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">{{ $riwayat_rps->total() }}</span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan RPS</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar dokumen RPS yang telah selesai direview oleh GPM.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[25%]">
                    <col class="w-[30%]">
                    <col class="w-[17%]">
                    <col class="w-[13%]">
                    <col class="w-[15%]">
                </colgroup>
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-y border-slate-200">
                    <tr>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Mata Kuliah</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Dosen Pengampu</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Tanggal Disetujui</th>
                        <th class="whitespace-nowrap px-6 py-4 text-left">Status</th>
                        <th class="whitespace-nowrap px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($riwayat_rps as $rps)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama ?? '-' }} ({{ $rps->mataKuliah->kode ?? '-' }})</div>
                                <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $dosens = $rps->dosens->pluck('name');
                                @endphp
                                @if($dosens->isNotEmpty())
                                    <span class="text-sm font-medium text-slate-700">{{ $dosens->join(', ') }}</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $rps->updated_at ? $rps->updated_at->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Disetujui</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('banksoal.rps.gpm.validasi-rps.setuju', $rps->id) }}" class="gpm-rps-history-action">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-folder-open text-3xl text-slate-300"></i>
                                    <p class="text-sm font-semibold">Belum ada riwayat</p>
                                    <p class="text-xs">Belum ada RPS yang berstatus disetujui.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat_rps->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $riwayat_rps->firstItem() }}-{{ $riwayat_rps->lastItem() }} dari {{ $riwayat_rps->total() }} hasil</span>
                {{ $riwayat_rps->links('banksoal::components.ui.laravel-pagination') }}
            </div>
        @endif
    </div>
</x-banksoal::layouts.gpm-master>