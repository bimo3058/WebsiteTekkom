<x-banksoal::layouts.gpm-master>
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Riwayat Validasi RPS" subtitle="Pantau riwayat dokumen RPS yang telah direview" />

    <div class="border-b border-slate-200 mb-4">
        <div class="inline-flex items-center gap-2 border-b-2 border-primary pb-3 text-sm font-semibold text-primary">
            Selesai Direview
            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">{{ $riwayat_rps->total() }}</span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-primary text-xs uppercase text-white">
                    <tr>
                        <th class="px-6 py-4 text-left">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left">Dosen Pengampu</th>
                        <th class="px-6 py-4 text-left">Tanggal Disetujui</th>
                        <th class="px-6 py-4 text-left">Status Akhir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($riwayat_rps as $rps)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $rps->mataKuliah->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $rps->mataKuliah->kode ?? '-' }} &bull; {{ $rps->semester }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $dosens = $rps->dosens->pluck('name');
                                @endphp
                                @if($dosens->isNotEmpty())
                                    <span class="text-sm text-slate-600">{{ $dosens->join(', ') }}</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $rps->updated_at ? $rps->updated_at->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Disetujui</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/90">
                                    <i class="far fa-eye"></i> Lihat Detail
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