<!-- RPS History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan RPS</h2>
    </div>

    <div class="controls-section mx-4 mt-4">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="riwayatSearchInput"
                type="text"
                placeholder="Cari nama mata kuliah atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleRiwayatSearch()"
            >
        </div>

            <div class="filter-group">
                <label for="riwayatStatusSelect">Status:</label>
                <select id="riwayatStatusSelect" onchange="handleRiwayatFilterChange()">
                    <option value="">Semua</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="riwayatMkSelect">Mata Kuliah:</label>
                <select id="riwayatMkSelect" onchange="handleRiwayatFilterChange()">
                    <option value="">Semua</option>
                </select>
            </div>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell">Tahun/Semester</th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Tanggal Upload</th>
                    <th class="table-header-cell">Diunggah Oleh</th>
                    <th class="table-header-cell">Status</th>
                    <th class="table-header-cell w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayat as $item)
                        <tr class="table-row" data-mk="{{ $item->mataKuliah?->nama ?? '' }} {{ $item->mataKuliah?->kode ?? '' }}" data-status="{{ $item->status->value }}" data-year="{{ $item->tahun_ajaran }}">
                        <td class="table-cell-strong">{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>
                        <td class="table-cell">{{ $item->mataKuliah?->nama ?? 'N/A' }} <span class="text-xs text-slate-500">({{ $item->mataKuliah?->kode ?? 'N/A' }})</span></td>
                        <td class="table-cell">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="table-cell">
                            @php
                                $fallbackUser = null;
                                if (!$item->uploader_id) {
                                    $fallbackUser = DB::table('bs_rps_dosen')
                                        ->where('rps_id', $item->id)
                                        ->join('users', 'users.id', '=', 'bs_rps_dosen.dosen_id')
                                        ->orderBy('bs_rps_dosen.id', 'asc')
                                        ->select('users.id', 'users.name')
                                        ->first();
                                }
                                $uploaderId = $item->uploader_id ?? $fallbackUser?->id;
                                $uploaderName = $item->uploader_name ?? $fallbackUser?->name ?? 'Tidak diketahui';
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                    {{ substr($uploaderName, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $uploaderName }}</span>
                                @if($uploaderId == Auth::id())
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Anda</span>
                                @endif
                            </div>
                        </td>
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
                            <div class="dots-wrap" id="dots-{{ $item->id }}">
                                <button type="button" class="btn-dots" onclick="toggleMenu({{ $item->id }}, event)">⋮</button>
                                <div class="dots-menu" id="menu-{{ $item->id }}">
                                    @if ($item->dokumen)
                                        <button type="button"
                                                class="preview-dokumen-btn"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}">
                                            <i class="fas fa-eye w-4"></i> Preview
                                        </button>
                                    @endif
                                    
                                    @php
                                        $isUploader = $uploaderId == Auth::id();
                                        $canEdit = $isUploader && in_array($item->status->value, ['diajukan', 'revisi']);
                                        $canDelete = $isUploader && in_array($item->status->value, ['diajukan']);
                                    @endphp
                                    
                                    @if($canEdit)
                                        <a href="{{ route('banksoal.rps.dosen.edit', $item->id) }}"
                                           class="edit-rps-btn">
                                            <i class="fas fa-edit w-4"></i> Edit
                                        </a>
                                    @else
                                        <button type="button" class="cursor-not-allowed text-slate-400" disabled 
                                                title="{{ !$isUploader ? 'Hanya pengunggah yang dapat mengubah RPS ini' : 'RPS tidak dapat diedit dengan status ' . $item->status->label() }}">
                                            <i class="fas fa-edit w-4"></i> Edit
                                        </button>
                                    @endif

                                    @if($canDelete)
                                        <button type="button"
                                                class="delete-rps-btn menu-delete"
                                                data-id="{{ $item->id }}"
                                                data-mk="{{ e($item->mataKuliah?->nama ?? 'RPS') }}"
                                                data-destroy-url="{{ route('banksoal.rps.dosen.destroy', $item->id) }}">
                                            <i class="fas fa-trash w-4"></i> Hapus
                                        </button>
                                    @else
                                        <button type="button" class="cursor-not-allowed text-slate-400" disabled
                                                title="{{ !$isUploader ? 'Hanya pengunggah yang dapat menghapus RPS ini' : 'RPS tidak dapat dihapus dengan status ' . $item->status->label() }}">
                                            <i class="fas fa-trash w-4"></i> Hapus
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty-state="1">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-600">
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
            {{ $riwayat->onEachSide(1)->links('banksoal::components.ui.laravel-pagination') }}
        </div>
    @endif
</div>
