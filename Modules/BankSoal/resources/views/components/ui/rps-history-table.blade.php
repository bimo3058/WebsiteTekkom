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
                                $statusClass = match($item->status) {
                                    'approved' => 'badge-success',
                                    'rejected' => 'badge-danger',
                                    'pending' => 'badge-info',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="table-cell">
                            @if ($item->dokumen)
                                <button type="button" class="preview-dokumen-btn text-blue-600 hover:text-blue-700 font-medium text-sm" data-id="{{ $item->id }}" data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                            @else
                                <span class="text-slate-400 text-sm">-</span>
                            @endif
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
</div>
