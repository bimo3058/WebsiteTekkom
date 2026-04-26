<!-- RPS Approved Course History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Riwayat RPS Mata Kuliah</h2>
            <p class="text-sm text-slate-500 mt-1">Menampilkan riwayat RPS berstatus disetujui untuk mata kuliah yang saat ini Anda ampu.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">Disetujui</span>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell">Tanggal Disetujui</th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Tahun/Semester</th>
                    <th class="table-header-cell">File</th>
                    <th class="table-header-cell w-[220px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayatMkDisetujui as $item)
                    <tr class="table-row">
                        <td class="table-cell">{{ $item->tanggal_disetujui ? \Carbon\Carbon::parse($item->tanggal_disetujui)->format('d M Y H:i') : '-' }}</td>
                        <td class="table-cell">
                            <div class="font-medium text-slate-900">{{ $item->mk_nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $item->mk_kode ?? '-' }}</div>
                        </td>
                        <td class="table-cell-strong">{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                        <td class="table-cell">{{ $item->dokumen ? basename((string) $item->dokumen) : '-' }}</td>
                        <td class="table-cell align-top">
                            <div class="flex flex-wrap items-center gap-2 max-w-full">
                                @if ($item->dokumen)
                                    <button type="button"
                                            class="preview-dokumen-btn inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ e($item->mk_nama ?? 'Dokumen') }}"
                                            title="Preview dokumen">
                                        Preview
                                    </button>
                                    <a href="{{ route('banksoal.rps.dosen.download', $item->id) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                                       title="Unduh RPS">
                                        Unduh
                                    </a>
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat RPS disetujui untuk mata kuliah yang Anda ampu saat ini</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($riwayatMkDisetujui, 'links'))
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $riwayatMkDisetujui->onEachSide(1)->links() }}
        </div>
    @endif
</div>
