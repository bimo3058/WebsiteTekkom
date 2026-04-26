<!-- RPS History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengunggahan</h2>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell">Tahun/Semester</th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Tanggal Upload</th>
                    <th class="table-header-cell">Status</th>
                    <th class="table-header-cell w-[280px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayat as $item)
                    <tr class="table-row">
                        <td class="table-cell-strong">{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                        <td class="table-cell">{{ $item->mataKuliah?->nama ?? 'N/A' }} <span class="text-xs text-slate-500">({{ $item->mataKuliah?->kode ?? 'N/A' }})</span></td>
                        <td class="table-cell">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="table-cell">
                            @php
                                $status = $item->status->label() ?? 'Unknown';
                                $statusClass = match($item->status->value) {
                                    'disetujui' => 'badge-success',
                                    'revisi' => 'badge-danger',
                                    'diajukan' => 'badge-warning',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="table-cell align-top">
                            <div class="flex flex-wrap items-center gap-2 max-w-full">
                                @if ($item->dokumen)
                                    <button type="button"
                                            class="preview-dokumen-btn inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                            data-id="{{ $item->id }}"
                                            data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}"
                                            title="Preview dokumen">
                                        Preview
                                    </button>
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                                
                                @php
                                    $canEdit = in_array($item->status->value, ['diajukan', 'revisi']);
                                    $canDelete = in_array($item->status->value, ['diajukan']);
                                @endphp
                                
                                @if($canEdit)
                                    <a href="{{ route('banksoal.rps.dosen.edit', $item->id) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100"
                                       title="Edit RPS">
                                        Edit
                                    </a>
                                @else
                                    <button type="button"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed"
                                            disabled
                                            title="RPS tidak dapat diedit dengan status {{ $item->status->label() }}">
                                        Edit
                                    </button>
                                @endif

                                @if($canDelete)
                                    <button type="button"
                                            class="delete-rps-btn inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100"
                                            data-id="{{ $item->id }}"
                                            data-mk="{{ e($item->mataKuliah?->nama ?? 'RPS') }}"
                                            data-destroy-url="{{ route('banksoal.rps.dosen.destroy', $item->id) }}"
                                            title="Hapus RPS">
                                        Hapus
                                    </button>
                                @else
                                    <button type="button"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed"
                                            disabled
                                            title="RPS tidak dapat dihapus dengan status {{ $item->status->label() }}">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat pengunggahan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($riwayat, 'links'))
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $riwayat->onEachSide(1)->links() }}
        </div>
    @endif
</div>
