<!-- RPS History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan RPS</h2>
        <div class="flex items-center gap-2">
            @if(!($isUploadOpen ?? false))
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                    disabled title="Periode upload RPS saat ini tidak aktif">
                    Ajukan RPS
                </button>
            @else
                <a href="{{ route('banksoal.rps.dosen.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40"
                    title="Ajukan RPS baru">
                    Ajukan RPS
                </a>
            @endif
        </div>
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
                    <th class="table-header-cell">Tanggal</th>
                    <th class="table-header-cell">Diunggah Oleh</th>
                    <th class="table-header-cell">Status</th>
                    <th class="table-header-cell w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayat as $item)
                    @php
                        $isDraft = $item->is_draft ?? false;
                        $statusValue = $isDraft ? 'draft' : ($item->status?->value ?? '');

                        // Badge class
                        $statusClass = match($statusValue) {
                            'draft'     => 'bg-slate-100 text-slate-600 border-slate-200',
                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'revisi'    => 'bg-red-50 text-red-700 border-red-200',
                            'diajukan'  => 'bg-amber-50 text-amber-700 border-amber-200',
                            default     => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                        $statusLabel = $isDraft ? 'Draft' : ($item->status?->label() ?? 'Unknown');

                        // Uploader info (hanya untuk non-draft)
                        $fallbackUser = null;
                        if (!$isDraft && !$item->uploader_id) {
                            $fallbackUser = DB::table('bs_rps_dosen')
                                ->where('rps_id', $item->id)
                                ->join('users', 'users.id', '=', 'bs_rps_dosen.dosen_id')
                                ->orderBy('bs_rps_dosen.id', 'asc')
                                ->select('users.id', 'users.name')
                                ->first();
                        }
                        $uploaderId   = $isDraft ? ($item->uploader_id ?? null) : ($item->uploader_id ?? $fallbackUser?->id);
                        $uploaderName = $isDraft ? (Auth::user()->name) : ($item->uploader_name ?? $fallbackUser?->name ?? 'Tidak diketahui');

                        // Tanggal tampil
                        $tanggal = $isDraft
                            ? ($item->updated_at ? $item->updated_at->format('d M Y') : '-')
                            : ($item->created_at ? $item->created_at->format('d M Y') : '-');

                        // Step progress untuk draft generator
                        $stepLabels = ['Belum dimulai', 'Langkah 1/3', 'Langkah 2/3', 'Langkah 3/3'];
                        $stepReached = $isDraft ? ($item->step_reached ?? 0) : null;
                    @endphp

                    <tr class="table-row"
                        data-mk="{{ $item->mataKuliah?->nama ?? '' }} {{ $item->mataKuliah?->kode ?? '' }}"
                        data-status="{{ $statusValue }}"
                        data-year="{{ $item->tahun_ajaran }}">

                        <td class="table-cell-strong">{{ $item->tahun_ajaran }} - {{ $item->semester }}</td>

                        <td class="table-cell">
                            {{ $item->mataKuliah?->nama ?? 'N/A' }}
                            <span class="text-xs text-slate-500">({{ $item->mataKuliah?->kode ?? 'N/A' }})</span>
                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                @if($isDraft)
                                    @if(($item->creation_method ?? '') === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200">Form</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">Upload</span>
                                    @endif
                                    @if($stepReached !== null && ($item->creation_method ?? '') === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200">
                                            {{ $stepLabels[$stepReached] ?? '' }}
                                        </span>
                                    @endif
                                @else
                                    @if($item->creation_method === 'generator')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200">Form</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">Upload</span>
                                    @endif
                                @endif
                            </div>
                        </td>

                        <td class="table-cell">{{ $tanggal }}</td>

                        <td class="table-cell">
                            <div>
                                <span class="text-sm font-medium text-slate-700">{{ $uploaderName }}</span>
                                @if($uploaderId == Auth::id())
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Anda</span>
                                @endif
                            </div>
                        </td>

                        <td class="table-cell">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>

                        <td class="table-cell">
                            <div class="dots-wrap" id="dots-{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}">
                                <button type="button" class="btn-dots" onclick="toggleMenu('{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}', event)">⋮</button>
                                <div class="dots-menu" id="menu-{{ $isDraft ? 'draft-' . $item->draft_id : $item->id }}">

                                    @if($isDraft)
                                        {{-- Action untuk Draft --}}
                                        <a href="{{ route('banksoal.rps.dosen.create') }}?resume_draft={{ $item->draft_id }}&mk={{ $item->mk_id }}&method={{ $item->creation_method ?? 'generator' }}"
                                           class="inline-flex items-center gap-2">
                                            <span>Lanjutkan</span>
                                        </a>
                                        <button type="button"
                                                class="delete-draft-btn menu-delete"
                                                data-draft-id="{{ $item->draft_id }}"
                                                data-mk="{{ e($item->mataKuliah?->nama ?? 'Draft RPS') }}">
                                            Hapus Draft
                                        </button>

                                    @else
                                        {{-- Action untuk RPS yang sudah diajukan --}}
                                        @if ($item->dokumen)
                                            <button type="button"
                                                    class="preview-dokumen-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-title="{{ e($item->mataKuliah?->nama ?? 'Dokumen') }}">
                                                Preview
                                            </button>
                                        @endif

                                        @php
                                            $isUploader = $uploaderId == Auth::id();
                                            $canEdit    = $isUploader && in_array($item->status->value, ['diajukan', 'revisi']);
                                            $canDelete  = $isUploader && in_array($item->status->value, ['diajukan']);
                                        @endphp

                                        @if($canEdit)
                                            <a href="{{ route('banksoal.rps.dosen.edit', $item->id) }}" class="edit-rps-btn">
                                                Edit
                                            </a>
                                        @else
                                            <button type="button" class="cursor-not-allowed text-slate-400" disabled
                                                    title="{{ !$isUploader ? 'Hanya pengunggah yang dapat mengubah RPS ini' : 'RPS tidak dapat diedit dengan status ' . $item->status->label() }}">
                                                Edit
                                            </button>
                                        @endif

                                        @if($canDelete)
                                            <button type="button"
                                                    class="delete-rps-btn menu-delete"
                                                    data-id="{{ $item->id }}"
                                                    data-mk="{{ e($item->mataKuliah?->nama ?? 'RPS') }}"
                                                    data-destroy-url="{{ route('banksoal.rps.dosen.destroy', $item->id) }}">
                                                Hapus
                                            </button>
                                        @else
                                            <button type="button" class="cursor-not-allowed text-slate-400" disabled
                                                    title="{{ !$isUploader ? 'Hanya pengunggah yang dapat menghapus RPS ini' : 'RPS tidak dapat dihapus dengan status ' . $item->status->label() }}">
                                                Hapus
                                            </button>
                                        @endif
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
