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
                    <th class="table-header-cell">Aksi</th>
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
                        <td class="table-cell">
                            <div class="flex items-center gap-2">
                                @if ($item->dokumen)
                                    <button type="button" class="preview-dokumen-btn text-blue-600 hover:text-blue-700 font-medium text-sm" data-id="{{ $item->id }}" data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}" title="Preview dokumen">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                                
                                @php
                                    $canEdit = in_array($item->status->value, ['diajukan', 'revisi']);
                                    $canDelete = in_array($item->status->value, ['diajukan']);
                                @endphp
                                
                                @if($canEdit)
                                    <a href="{{ route('banksoal.rps.dosen.edit', $item->id) }}" class="text-amber-600 hover:text-amber-700 font-medium text-sm" title="Edit RPS">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                @else
                                    <button type="button" class="text-slate-300 cursor-not-allowed font-medium text-sm" disabled title="RPS tidak dapat diedit dengan status {{ $item->status->label() }}">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                @endif
                                
                                @if($canDelete)
                                    <button type="button" class="delete-rps-btn text-red-600 hover:text-red-700 font-medium text-sm" data-id="{{ $item->id }}" data-mk="{{ e($item->mataKuliah?->nama ?? 'RPS') }}" data-destroy-url="{{ route('banksoal.rps.dosen.destroy', $item->id) }}" title="Hapus RPS">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                @else
                                    <button type="button" class="text-slate-300 cursor-not-allowed font-medium text-sm" disabled title="RPS tidak dapat dihapus dengan status {{ $item->status->label() }}">
                                        <i class="fas fa-trash"></i> Hapus
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
